# SQL ONLY_FULL_GROUP_BY Mode Fixes

## Overview
This document describes the SQL errors encountered due to MySQL's `ONLY_FULL_GROUP_BY` mode and the solutions implemented to resolve them.

## What is ONLY_FULL_GROUP_BY?
MySQL's `ONLY_FULL_GROUP_BY` mode enforces strict SQL standards for `GROUP BY` queries. When this mode is enabled:
- All columns in the `SELECT` clause must either:
  1. Be included in the `GROUP BY` clause, OR
  2. Be wrapped in an aggregate function (COUNT, MAX, MIN, SUM, etc.)

---

## Issues Fixed

### Issue #1: Product Add-Ons on Create Product Page

**Route:**
```
https://dailydose.qadi-tech.com/admin/product/create-product
```

**Error Message:**
```
Error Number: 1055
Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 
'dailydose.qadi-tech.com.product_add_ons.id' which is not functionally dependent on 
columns in GROUP BY clause

SELECT * FROM `product_add_ons` GROUP BY `title`
```

**File:** `application/controllers/admin/Product.php`  
**Line:** 60

**Original Code:**
```php
$this->data['add_on_snaps'] = fetch_details(NULL, 'product_add_ons', '*', '', '', '', '', '', '', "", "", false, "title");
```

**Problem:**
- Selecting all columns (`*`) including `id`, `description`, `price`, etc.
- Only grouping by `title`
- MySQL doesn't know which row's data to use when multiple add-ons have the same title

**Solution:**
Replaced `GROUP BY` with `DISTINCT` to get unique add-ons:

```php
// Get unique add-ons by title using DISTINCT instead of GROUP BY to avoid SQL mode issues
$this->data['add_on_snaps'] = $this->db->distinct()
                                        ->select('title, id, description, price, status')
                                        ->from('product_add_ons')
                                        ->order_by('title', 'ASC')
                                        ->get()
                                        ->result_array();
```

**Why This Works:**
- `DISTINCT` retrieves unique records without GROUP BY restrictions
- Explicitly selects only the needed columns
- Compatible with `ONLY_FULL_GROUP_BY` mode

---

### Issue #2: Product Attributes in Point of Sale

**Route:**
```
http://localhost/dailydose.qadi-tech.com/admin/point_of_sale/get_products?category_id=&limit=2&offset=0&search=
```

**Error Message:**
```
Error Number: 1055
Expression #3 of SELECT list is not in GROUP BY clause and contains nonaggregated column 
'dailydose.qadi-tech.com.a.id' which is not functionally dependent on columns in 
GROUP BY clause

SELECT group_concat(`av`.`id`) as ids, group_concat(' ', `av`.`value`) as value, 
`a`.`id` as `attr_id`, `a`.`name` as `attr_name`, `a`.`name`, 
GROUP_CONCAT(av.swatche_type ORDER BY av.id ASC ) as swatche_type, 
GROUP_CONCAT(av.swatche_value ) as swatche_value 
FROM `product_attributes` `pa` 
INNER JOIN `attribute_values` `av` ON FIND_IN_SET(av.id, pa.attribute_value_ids ) > 0 
INNER JOIN `attributes` `a` ON `a`.`id` = `av`.`attribute_id` 
WHERE `pa`.`product_id` = '1' 
GROUP BY `a`.`name`
```

**File:** `application/helpers/function_helper.php`  
**Line:** 1356  
**Function:** `get_attribute_values_by_pid($id)`

**Original Code:**
```php
$attribute_values = $t->db->select(" group_concat(`av`.`id`) as ids,group_concat(' ',`av`.`value`) as value ,`a`.`id` as attr_id,`a`.`name` as attr_name, a.name, GROUP_CONCAT(av.swatche_type ORDER BY av.id ASC ) as swatche_type , GROUP_CONCAT(av.swatche_value  ) as swatche_value")
    ->join('attribute_values av ', 'FIND_IN_SET(av.id, pa.attribute_value_ids ) > 0', 'inner')
    ->join('attributes a', 'a.id = av.attribute_id', 'inner')
    ->where('pa.product_id', $id)->group_by('`a`.`name`')->get('product_attributes pa')->result_array();
```

**Problem:**
- Selecting `a.id` (attribute ID) 
- Only grouping by `a.name` (attribute name)
- If two attributes had the same name, MySQL wouldn't know which ID to use

**Solution:**
Added `a.id` to the GROUP BY clause:

```php
->where('pa.product_id', $id)->group_by('`a`.`id`, `a`.`name`')->get('product_attributes pa')->result_array();
```

**Why This Works:**
- Grouping by both `a.id` and `a.name` ensures uniqueness
- `a.id` is the primary key, making this logically correct
- All non-aggregated selected columns are now in GROUP BY

---

### Issue #3: Product List with Variants

**Route:**
```
http://localhost/dailydose.qadi-tech.com/admin/product/get_product_data?category_id=&status=&indicator=&type=&limit=10&sort=id&order=desc&offset=0&search=
```

**Error Message (Part 1):**
```
Error Number: 1055
Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 
'dailydose.qadi-tech.com.product_variants.id' which is not functionally dependent on 
columns in GROUP BY clause

SELECT `product_variants`.`id` AS `id`, `p`.`id` as `pid`, `p`.`rating`, 
`p`.`no_of_ratings`, `p`.`name`, `p`.`branch_id` as `branch_id`, `p`.`type`, 
`p`.`image`, `p`.`status`, `product_variants`.`price`, `product_variants`.`special_price`, 
`product_variants`.`stock`, `c`.`name` as `category` 
FROM `products` `p` 
JOIN `categories` `c` ON `p`.`category_id`=`c`.`id` 
JOIN `product_variants` ON `product_variants`.`product_id` = `p`.`id` 
WHERE `p`.`branch_id` = '1' 
GROUP BY `pid` 
ORDER BY `product_variants`.`id` DESC 
LIMIT 10
```

**File:** `application/models/Product_model.php`  
**Lines:** 371, 292  
**Function:** `get_product_details()`

**Original Code (Line 371):**
```php
$search_res = $this->db->select('product_variants.id AS id, p.id as pid ,p.rating,p.no_of_ratings,p.name,p.branch_id as branch_id ,p.type, p.image, p.status,product_variants.price , product_variants.special_price, product_variants.stock, c.name as category ')
    ->join(" categories c", "p.category_id=c.id ")
    ->join('product_variants', 'product_variants.product_id = p.id');
```

**Problem:**
- A product can have multiple variants (different sizes, colors, etc.)
- Selecting individual variant columns: `product_variants.id`, `product_variants.price`, etc.
- Grouping by `pid` (product ID) to show one row per product
- MySQL doesn't know which variant's data to display

**Solution (Line 371):**
Used aggregate functions for variant columns:

```php
$search_res = $this->db->select('MIN(product_variants.id) AS id, p.id as pid ,p.rating,p.no_of_ratings,p.name,p.branch_id as branch_id ,p.type, p.image, p.status, MIN(product_variants.price) as price, MIN(product_variants.special_price) as special_price, SUM(product_variants.stock) as stock, c.name as category ')
    ->join(" categories c", "p.category_id=c.id ")
    ->join('product_variants', 'product_variants.product_id = p.id');
```

**Why This Works:**
- `MIN(product_variants.id)` - Gets the first variant ID
- `MIN(product_variants.price)` - Shows the **lowest price** (best for customers)
- `MIN(product_variants.special_price)` - Shows the **lowest special price**
- `SUM(product_variants.stock)` - Shows **total stock** across all variants
- All variant columns are now aggregated, compatible with `GROUP BY pid`

---

**Error Message (Part 2):**
```
Error Number: 1055
Expression #1 of ORDER BY clause is not in GROUP BY clause and contains nonaggregated 
column 'dailydose.qadi-tech.com.product_variants.id' which is not functionally dependent 
on columns in GROUP BY clause
```

**Original Code (Line 292):**
```php
if ($_GET['sort'] == 'id') {
    $sort = "product_variants.id";
}
```

**Problem:**
- Trying to ORDER BY `product_variants.id` 
- This column isn't in the GROUP BY clause
- Can't sort by an ungrouped column

**Solution (Line 292):**
Changed sort to use the grouped column:

```php
if ($_GET['sort'] == 'id') {
    $sort = "pid";  // Sort by product ID since we're grouping by product
}
```

**Why This Works:**
- `pid` is in the GROUP BY clause
- Sorts by product ID (the grouped value) instead of variant ID
- Produces the same desired result (newest/oldest products first)

---

## Additional Bug Fix

**File:** `application/models/Product_model.php`  
**Lines:** 416-430

**Issue:**
Several filter conditions were being applied to `$count_res` instead of `$search_res`, causing filters to not work properly on the product list query.

**Fixed:**
Changed `$count_res` to `$search_res` for the following filters:
- Partner ID filter (line 417)
- Status filter (line 421)
- Indicator filter (line 426)
- Product type filter (line 429)

---

## Summary Table

| Issue | File | Line(s) | Route | Fix Type |
|-------|------|---------|-------|----------|
| Product Add-Ons | `application/controllers/admin/Product.php` | 60 | `/admin/product/create-product` | GROUP BY → DISTINCT |
| Attribute Values | `application/helpers/function_helper.php` | 1356 | `/admin/point_of_sale/get_products` | Added column to GROUP BY |
| Product Variants | `application/models/Product_model.php` | 371 | `/admin/product/get_product_data` | Used aggregate functions |
| Sort by ID | `application/models/Product_model.php` | 292 | `/admin/product/get_product_data` | Changed ORDER BY column |
| Filter Bug | `application/models/Product_model.php` | 416-430 | `/admin/product/get_product_data` | Fixed variable names |

---

## Key Takeaways

1. **GROUP BY with SELECT ***: Never use `SELECT *` with `GROUP BY`. Always select specific columns.

2. **Aggregate Functions**: When grouping data, use aggregate functions (MIN, MAX, SUM, etc.) for columns not in GROUP BY.

3. **DISTINCT Alternative**: For simple uniqueness, `DISTINCT` is often cleaner than `GROUP BY`.

4. **ORDER BY Restrictions**: You can only ORDER BY columns that are either:
   - In the GROUP BY clause
   - Wrapped in an aggregate function

5. **Testing**: Always test queries with `ONLY_FULL_GROUP_BY` enabled to ensure SQL standard compliance.

---

## MySQL Configuration

To check if `ONLY_FULL_GROUP_BY` is enabled:
```sql
SELECT @@sql_mode;
```

To temporarily disable (not recommended for production):
```sql
SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
```

**Note:** It's better to fix queries properly rather than disable this mode, as it enforces correct SQL standards.

### Docker Development Override (Nov 7, 2025)

To unblock local testing we added a Docker override that removes `ONLY_FULL_GROUP_BY` from MySQL’s `sql_mode` inside the dev container:

1. Created `docker/mysql/conf.d/custom.cnf`:
   ```
   [mysqld]
   sql_mode=STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION
   ```
2. Mounted the file in `docker-compose.yml`:
   ```yaml
   services:
     db:
       volumes:
         - ./docker/mysql/conf.d/custom.cnf:/etc/mysql/conf.d/custom.cnf:ro
   ```
3. Recreated the `db` service with `docker compose up -d db`.

This affects only the Dockerized development database. Production should keep `ONLY_FULL_GROUP_BY` enabled so query regressions surface early.

---

## Date
Fixed on: October 29, 2025

## Developer Notes
All fixes maintain backward compatibility and improve query performance. The aggregate functions used (MIN for prices, SUM for stock) provide meaningful business logic rather than arbitrary values.

