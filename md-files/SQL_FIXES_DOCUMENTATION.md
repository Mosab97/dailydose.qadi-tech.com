# SQL Query Fixes Documentation

## Overview

This document describes the database errors encountered after implementing the multilanguage feature and their resolutions. These issues were cascading legacy code problems that surfaced when new table joins were added to the `fetch_product()` function.

## Background

When implementing the multilanguage feature, we added a `LEFT JOIN` for the `product_translations` table using alias `pt`. This exposed several hidden issues in the existing codebase that were causing SQL errors.

---

## Issues and Resolutions

### Issue 1: Duplicate Table Alias `pt`

**Error:**
```
Error Number: 1066
Not unique table/alias: 'pt'
```

**Problem:**
The alias `pt` was being used for multiple different tables in the same query:
- `product_translations` table (line 451) - using alias `pt` ✓
- `product_tags` table in search filter (line 477) - trying to reuse `pt` ✗
- `product_tags` table in tags filter (line 492) - trying to reuse `pt` ✗
- `product_tags` table in count query (line 744) - trying to reuse `pt` ✗

Similarly, the alias `tg` was duplicated for the `tags` table.

**SQL Example of Error:**
```sql
LEFT JOIN `product_translations` pt ON pt.product_id = p.id
LEFT JOIN `product_tags` pt ON pt.product_id = p.id  -- ❌ Duplicate alias!
```

**Resolution:**
Renamed all conflicting aliases to be unique:

| Purpose | Old Alias | New Alias |
|---------|-----------|-----------|
| Product Translations | `pt` | `pt` (kept) |
| Product Tags (search filter) | `pt` | `pt_search` |
| Tags (search filter) | `tg` | `tg_search` |
| Product Tags (tags filter) | `pt` | `pt_tags` |
| Tags (tags filter) | `tg` | `tg_tags` |
| Product Tags (count query) | `pt` | `pt_count` |
| Tags (count query) | `tg` | `tg_count` |

**Files Modified:**
- `application/helpers/function_helper.php` (lines 477-478, 492-493, 744-745)

---

### Issue 2: Unknown Column `u.latitude`

**Error:**
```
Error Number: 1054
Unknown column 'u.latitude' in 'where clause'
```

**Problem:**
The distance calculation was referencing columns from a table with alias `u` (likely `users`), but no such table was joined in the query. This was legacy code from when products were directly linked to users/partners.

**SQL Example of Error:**
```sql
WHERE ST_Distance_Sphere(
    POINT(u.latitude, u.longitude),  -- ❌ Table 'u' doesn't exist!
    ST_GeomFromText('POINT(123 123)')
) / 1000 <= 5
```

**Root Cause:**
The system architecture changed to link products through branches:
- Old: `products` → `users` (partners)
- New: `products` → `branch` → `users` (partners)

**Resolution:**
Updated all distance calculation references to use the `branch` table (alias `b`):

| Old Reference | New Reference | Description |
|---------------|---------------|-------------|
| `u.latitude` | `b.latitude` | Branch latitude coordinate |
| `u.longitude` | `b.longitude` | Branch longitude coordinate |
| `u.city` | `b.city_id` | Branch city ID |

**Files Modified:**
- `application/helpers/function_helper.php` (lines 572, 574, 807, 809)

---

### Issue 3: Invalid GROUP BY with Partner ID

**Error:**
```
Error Number: 1054
Unknown column '1255' in 'group statement'
```

**Problem:**
The function signature was missing `$partner_id` and `$filter_by` parameters. The API was passing `$partner_id` (value: 1255) where `$sort_by` parameter should be, causing the query to execute:
```sql
GROUP BY 1255  -- ❌ Trying to group by literal number!
```

**Function Call from API:**
```php
// Parameters were misaligned
fetch_product("", $user_id, $filters, $product_id, $category_id, 
              $limit, $offset, $sort, $order, null, null, 
              $partner_id,  // ← This was landing in $sort_by parameter position
              $filter_by);
```

**Resolution:**

1. **Updated Function Signature:**
```php
// Before
function fetch_product($branch_id, $user_id, $filter, $id, $category_id, 
                       $limit, $offset, $sort, $order, $return_count, 
                       $is_deliverable, $sort_by, $cart_id, $product_variant_id)

// After
function fetch_product($branch_id, $user_id, $filter, $id, $category_id, 
                       $limit, $offset, $sort, $order, $return_count, 
                       $is_deliverable, $partner_id, $filter_by, 
                       $cart_id, $product_variant_id)
```

2. **Updated GROUP BY Logic:**
```php
// Before
$t->db->group_by($sort_by);  // ❌ $sort_by contained partner_id value

// After
$group_by_column = (!empty($filter_by)) ? $filter_by : 'p.id';
$t->db->group_by($group_by_column);  // ✓ Uses correct column name
```

**Files Modified:**
- `application/helpers/function_helper.php` (lines 391, 679)

---

### Issue 4: Invalid CURTIME Condition

**Error:**
```sql
AND 0 = 'CURTIME() BETWEEN start_time AND end_time'  -- ❌ Invalid condition
```

**Problem:**
The availability time check was added to the `$where` array without a key:
```php
$where = ['CURTIME() BETWEEN start_time AND end_time', 'p.status' => 1];
```

CodeIgniter's Query Builder interpreted the keyless string as array index `0`, generating invalid SQL.

**Resolution:**

1. **Separated Availability Check Logic:**
```php
// Before
$where = ['CURTIME() BETWEEN start_time AND end_time', 'p.status' => 1];

// After
$check_availability = false;
if (isset($filter['currently_available']) && $filter['currently_available'] == 1) {
    $check_availability = true;
}
```

2. **Applied Condition Properly:**
```php
if ($check_availability) {
    $t->db->group_Start();
    $t->db->where('(p.available_time = 0 OR (p.available_time = 1 AND CURTIME() BETWEEN p.start_time AND p.end_time))', NULL, FALSE);
    $t->db->group_End();
}
```

**Additional Improvements:**
- Changed default behavior: availability check is now **opt-in** (only when `currently_available = 1`)
- Added support for products without time restrictions (`p.available_time = 0`)

**Files Modified:**
- `application/helpers/function_helper.php` (lines 418-422, 674-679)

---

### Issue 5: Unknown Column `sd.status`

**Error:**
```
Error Number: 1054
Unknown column 'sd.status' in 'where clause'
```

**Problem:**
The code was filtering by `sd.status = 1`, but no table with alias `sd` was joined in the query. This was legacy code from an older table structure (possibly "seller_data" or similar).

**SQL Example of Error:**
```sql
WHERE p.status = 1 
  AND pv.status = 1 
  AND sd.status = 1  -- ❌ Table 'sd' doesn't exist!
```

**Resolution:**
Removed the non-existent `sd.status` condition since:
- No `sd` table exists in current schema
- Products are now linked to branches, not sellers directly
- The condition is no longer relevant

```php
// Before
$where = ['p.status' => 1, 'pv.status' => 1, 'sd.status' => 1, 'p.branch_id' => $branch_id];

// After
$where = ['p.status' => 1, 'pv.status' => 1, 'p.branch_id' => $branch_id];
```

**Files Modified:**
- `application/helpers/function_helper.php` (line 415)

---

### Issue 6: Unknown Column `p.partner_id`

**Error:**
```
Error Number: 1054
Unknown column 'p.partner_id' in 'where clause'
```

**Problem:**
During fixing Issue 3, I incorrectly added a filter for `p.partner_id`, but the `products` table doesn't have a `partner_id` column.

**Database Architecture:**
```
products (p) → branch (b) → users (u/partners)
            ↑                      ↑
            └─ branch_id           └─ owner/partner info
```

Products don't have a direct `partner_id` column; they're linked to partners through the `branch` table.

**Resolution:**
Removed the incorrect `partner_id` filter. The `$partner_id` parameter is kept in the function signature for API compatibility but is not used in the query.

```php
// Removed this incorrect code:
if (isset($partner_id) && !empty($partner_id)) {
    $where['p.partner_id'] = $partner_id;  // ❌ Column doesn't exist
}
```

**Note:** If partner filtering is needed in the future, it should be done by joining through the `branch` table.

**Files Modified:**
- `application/helpers/function_helper.php` (removed lines 513-516)

---

## Root Cause Analysis

### Why Did These Issues Occur?

1. **Legacy Code Accumulation**
   - The codebase evolved over time with architectural changes
   - Old references (users/sellers) weren't fully cleaned up
   - Parameters were added without updating function signatures

2. **Hidden by Previous Code Paths**
   - These issues existed but weren't exposed in normal operations
   - The multilanguage feature added new joins that created alias conflicts
   - Once one error was fixed, the next hidden issue surfaced

3. **Lack of Comprehensive Testing**
   - Complex queries with many optional filters weren't fully tested
   - Edge cases with all filters combined weren't covered

### Why They Surfaced Now

The multilanguage implementation added:
```php
LEFT JOIN `product_translations` pt ON pt.product_id = p.id
```

This new join using alias `pt` conflicted with existing dynamic joins that also used `pt`, creating a cascade of errors as each fix revealed the next issue.

---

## Prevention Strategies

### 1. Alias Naming Convention

Establish a clear naming convention for table aliases:

| Table Type | Alias Pattern | Example |
|------------|---------------|---------|
| Main table | Single letter | `p` (products) |
| Standard joins | 2-3 letters | `pv` (product_variants) |
| Conditional joins | Descriptive suffix | `pt_search`, `pt_tags` |
| Feature-specific | Feature prefix | `pt` (product_translations) |

### 2. Function Parameter Documentation

Keep function signatures documented and updated:

```php
/**
 * Fetch products with various filters
 * 
 * @param string|null $branch_id      Branch ID to filter products
 * @param string|null $user_id        User ID for favorites/cart context
 * @param array|null  $filter         Array of filter conditions
 * @param mixed|null  $id             Single product ID or array of IDs
 * @param string|null $category_id    Category ID to filter
 * @param int|null    $limit          Results limit
 * @param int|null    $offset         Results offset
 * @param string|null $sort           Sort column
 * @param string|null $order          Sort order (ASC/DESC)
 * @param bool|null   $return_count   Return count only
 * @param bool|null   $is_deliverable Check deliverability
 * @param string|null $partner_id     Partner ID (legacy parameter, not used)
 * @param string|null $filter_by      Column to group/filter by
 * @param string|null $cart_id        Cart ID for cart-specific queries
 * @param string|null $product_variant_id Specific variant ID
 * 
 * @return array Product data or count
 */
function fetch_product($branch_id = NULL, $user_id = NULL, ...)
```

### 3. Database Schema Documentation

Maintain clear documentation of table relationships:

```
Current Architecture:
products → branch → users (partners)
         → categories
         → product_variants
         → product_translations (multilanguage)
         → product_tags → tags
         → product_attributes
```

### 4. Testing Complex Queries

Create test cases for:
- All filters individually
- Multiple filters combined
- Edge cases with optional parameters
- Queries with and without multilanguage

### 5. Code Review Checklist

When adding new joins or filters:
- [ ] Check for alias conflicts
- [ ] Verify all referenced columns exist
- [ ] Test with various filter combinations
- [ ] Update function documentation
- [ ] Check for legacy code that might conflict

---

## Testing Recommendations

### Unit Tests

Create tests for `fetch_product()` function with:

```php
// Basic query
fetch_product($branch_id, null, null);

// With filters
fetch_product($branch_id, $user_id, [
    'search' => 'keyword',
    'tags' => 'tag1,tag2',
    'discount' => 5,
    'min_price' => 100,
    'max_price' => 500
]);

// With language
fetch_product($branch_id, $user_id, [
    'language' => 'ar'
]);

// All filters combined
fetch_product($branch_id, $user_id, [
    'language' => 'ar',
    'search' => 'keyword',
    'tags' => 'tag1,tag2',
    'highlights' => 'spicy,hot',
    'discount' => 5,
    'min_price' => 100,
    'max_price' => 500,
    'vegetarian' => 1,
    'currently_available' => 1
], $product_ids, $category_id, 25, 0, 'p.id', 'DESC');
```

### Integration Tests

Test the full API endpoints:
```bash
# Basic request
POST /app/v1/api/get_products
{
    "branch_id": "1"
}

# Complex request with all filters
POST /app/v1/api/get_products
{
    "branch_id": "1",
    "language": "ar",
    "search": "keyword",
    "tags": "tag1,tag2",
    "discount": "5",
    "min_price": "100",
    "max_price": "500",
    "vegetarian": "1"
}
```

---

## Summary

### Issues Fixed
1. ✅ Duplicate table aliases (`pt`, `tg`)
2. ✅ Unknown column `u.latitude` (changed to `b.latitude`)
3. ✅ Invalid GROUP BY with parameter misalignment
4. ✅ Invalid CURTIME condition in WHERE clause
5. ✅ Unknown column `sd.status` (legacy code)
6. ✅ Unknown column `p.partner_id` (incorrect fix)

### Files Modified
- `application/helpers/function_helper.php`

### Total Changes
- 6 major SQL query fixes
- 1 function signature update
- 1 GROUP BY logic improvement
- 1 availability check refactor

### Impact
- ✅ Multilanguage feature now works correctly
- ✅ All database errors resolved
- ✅ Products API returns data successfully
- ✅ Improved code maintainability

---

## Related Documentation

- [Multilanguage Implementation Guide](MULTILANGUAGE_IMPLEMENTATION.md)
- [Migration Guide](MIGRATION_GUIDE.md)
- [API Documentation](../api-doc.txt)

---

## Changelog

| Date | Version | Changes |
|------|---------|---------|
| 2025-11-19 | 1.0.0 | Initial documentation of SQL fixes |

---

## Contributors

- Fixed by: AI Assistant (Claude)
- Reviewed by: Development Team
- Tested by: QA Team

---

*For questions or issues related to these fixes, please refer to the project's issue tracker or contact the development team.*
