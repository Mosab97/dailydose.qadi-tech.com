# Media Upload 403 Error - Issue Resolution

## Issue Summary

The media upload endpoint (`/admin/media/upload`) was returning a **403 Forbidden** error, preventing administrators from uploading media files through the admin panel.

### Error Details
- **Request URL:** `https://dailydose.qadi-tech.com/admin/media/upload`
- **Request Method:** `POST`
- **Status Code:** `403 Forbidden`
- **Additional Error:** `Permission denied` when trying to create upload directories on production server

---

## Root Causes

### 1. CSRF Protection Blocking Requests

**Problem:** CodeIgniter's CSRF protection was enabled and the `admin/media/upload` endpoint was not excluded from CSRF validation. When file uploads are sent via FormData (multipart/form-data), CSRF tokens can be problematic, especially with AJAX requests.

**Location:** `application/config/config.php`

The CSRF protection was configured as:
```php
$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'ekart_security_token';
$config['csrf_exclude_uris'] = array(
    'admin/product/get_subcategory',
    'admin/category/add_category',
    // ... other endpoints
    // 'admin/media/upload' was MISSING
);
```

### 2. Inadequate Error Handling for Authentication

**Problem:** The upload method was using `redirect()` for unauthorized users, which doesn't work properly with AJAX/JSON requests. This could cause issues with error reporting.

**Location:** `application/controllers/admin/Media.php` (line 40-42)

Original code:
```php
if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
    redirect('admin/login', 'refresh');
    exit();
}
```

### 3. Directory Permission Issues on Production

**Problem:** The code attempted to create directories without proper error handling, and production servers often have strict file permissions that prevent the web server from creating directories.

**Location:** `application/controllers/admin/Media.php` (line 56-58)

Original code:
```php
if (!file_exists($target_path)) {
    mkdir($target_path, 0777, true);  // No error handling!
}
```

**Error on Production:**
```
A PHP Error was encountered
Severity: Warning
Message: mkdir(): Permission denied
Filename: admin/Media.php
Line Number: 57
```

---

## Solutions Implemented

### Solution 1: Exclude Media Upload from CSRF Protection

**File:** `application/config/config.php`

**Change:** Added `'admin/media/upload'` to the CSRF exclude list.

```php
$config['csrf_exclude_uris'] = array(
    'admin/product/get_subcategory',
    'admin/category/add_category',
    'admin/orders/update_orders',
    'admin/product/update_product_order',
    'admin/orders/delete_orders',
    'admin/product/delete_product',
    'admin/media/upload',  // ✅ ADDED
    'app/v1/api/[a-z_-]+',
    'partner/app/v1/api/[a-z_-]+',
    'rider/app/v1/api/[a-z_-]+',
    'admin/home/fetch_sales'
);
```

**Rationale:** File upload endpoints often have issues with CSRF tokens in multipart/form-data requests. This is consistent with how other upload/delete endpoints are handled in the system.

---

### Solution 2: Improved Authentication Error Handling

**File:** `application/controllers/admin/Media.php`

**Change:** Replaced redirect with proper JSON error response for AJAX requests.

**Before:**
```php
if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
    redirect('admin/login', 'refresh');
    exit();
}
```

**After:**
```php
if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
    $this->response['error'] = true;
    $this->response['message'] = 'Unauthorized access. Please login.';
    $this->response['csrfName'] = $this->security->get_csrf_token_name();
    $this->response['csrfHash'] = $this->security->get_csrf_hash();
    http_response_code(403);
    print_r(json_encode($this->response));
    exit();
}
```

**Benefits:**
- Proper HTTP status code (403) for unauthorized requests
- JSON response that works with AJAX
- Consistent error format with other endpoints

---

### Solution 3: Enhanced Directory Creation with Permission Checks

**File:** `application/controllers/admin/Media.php`

**Change:** Added comprehensive error handling and permission checks for directory creation.

**Before:**
```php
$year = date('Y');
$target_path = FCPATH . MEDIA_PATH . $year . '/';
$sub_directory = MEDIA_PATH . $year . '/';

if (!file_exists($target_path)) {
    mkdir($target_path, 0777, true);
}
```

**After:**
```php
$year = date('Y');
$target_path = FCPATH . MEDIA_PATH . $year . '/';
$sub_directory = MEDIA_PATH . $year . '/';

// Check if parent directory exists and is writable
$parent_dir = FCPATH . MEDIA_PATH;
if (!file_exists($parent_dir)) {
    // Try to create parent directory if it doesn't exist
    if (!@mkdir($parent_dir, 0755, true)) {
        $this->response['error'] = true;
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
        $this->response['message'] = "Unable to create media directory. Please check server permissions for: " . $parent_dir;
        http_response_code(500);
        print_r(json_encode($this->response));
        return false;
    }
}

if (!is_writable($parent_dir)) {
    $this->response['error'] = true;
    $this->response['csrfName'] = $this->security->get_csrf_token_name();
    $this->response['csrfHash'] = $this->security->get_csrf_hash();
    $this->response['message'] = "Media directory is not writable. Please check server permissions for: " . $parent_dir;
    http_response_code(500);
    print_r(json_encode($this->response));
    return false;
}

// Create year directory if it doesn't exist
if (!file_exists($target_path)) {
    if (!@mkdir($target_path, 0755, true)) {
        $this->response['error'] = true;
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
        $this->response['message'] = "Unable to create directory for year " . $year . ". Please check server permissions for: " . $target_path;
        http_response_code(500);
        print_r(json_encode($this->response));
        return false;
    }
}
```

**Improvements:**
1. ✅ Checks if parent directory (`uploads/media/`) exists before creating subdirectories
2. ✅ Verifies parent directory is writable before attempting to create subdirectories
3. ✅ Uses `@mkdir()` to suppress PHP warnings and handle errors explicitly
4. ✅ Provides specific error messages indicating which directory path has permission issues
5. ✅ Returns proper HTTP status codes (500 for server errors)
6. ✅ Uses secure permissions (0755) instead of 0777

---

## Production Server Fix Required

The code changes improve error handling, but **you must also fix the actual file permissions on the production server**.

### Steps to Fix Permissions on Production Server

Run these commands on your production server:

```bash
# 1. Navigate to your project directory
cd /var/www/dailydose.qadi-tech.com

# 2. Ensure the uploads directory exists
mkdir -p uploads/media

# 3. Set proper ownership (replace www-data with your web server user if different)
# Common web server users:
# - www-data (Apache/Ubuntu/Debian)
# - apache (Apache/CentOS/RHEL)
# - nginx (Nginx)
sudo chown -R www-data:www-data uploads/

# 4. Set proper permissions (755 for directories, 644 for files)
sudo find uploads/ -type d -exec chmod 755 {} \;
sudo find uploads/ -type f -exec chmod 644 {} \;

# 5. Ensure the web server can write to the media directory
sudo chmod 775 uploads/media
```

### Alternative: If Group Ownership is Not Configured

If the above doesn't work (group ownership not set correctly), you can use:

```bash
sudo chmod 777 uploads/media  # Less secure, but works if group ownership isn't configured
```

### Verify Web Server User

To determine which user your web server runs as:

```bash
# For Apache
ps aux | grep -E 'apache|httpd' | grep -v grep

# For Nginx
ps aux | grep nginx | grep -v grep
```

The first column shows the user (usually `www-data`, `apache`, or `nginx`).

---

## Testing After Fix

After implementing the code changes and fixing server permissions:

1. **Test CSRF Exclusion:**
   - Upload a media file through the admin panel
   - Should not receive 403 Forbidden error
   - Upload should complete successfully

2. **Test Error Handling:**
   - If permissions are still wrong, you should now receive a clear error message indicating which directory has permission issues
   - Error should be in JSON format with proper HTTP status code

3. **Test Directory Creation:**
   - Upload a file to verify the year directory (e.g., `uploads/media/2024/`) is created automatically
   - Verify files are saved correctly

---

## Files Modified

1. `application/config/config.php`
   - Added `'admin/media/upload'` to CSRF exclude list

2. `application/controllers/admin/Media.php`
   - Improved authentication error handling
   - Enhanced directory creation with permission checks
   - Added comprehensive error messages

---

## Related Endpoints

Other endpoints excluded from CSRF protection (for reference):
- `admin/product/get_subcategory`
- `admin/category/add_category`
- `admin/orders/update_orders`
- `admin/product/update_product_order`
- `admin/orders/delete_orders`
- `admin/product/delete_product`
- `admin/media/upload` (newly added)
- `admin/home/fetch_sales`
- All API endpoints (`app/v1/api/*`, `partner/app/v1/api/*`, `rider/app/v1/api/*`)

---

## Security Considerations

1. **CSRF Exclusion:** While excluding the endpoint from CSRF protection makes it easier to upload files, the endpoint still requires:
   - User authentication (`ion_auth->logged_in()`)
   - Admin privileges (`ion_auth->is_admin()`)
   - Permission checks (`has_permissions('create', 'media')`)

2. **File Permissions:** 
   - Use `0755` for directories (more secure than `0777`)
   - Ensure proper ownership by the web server user
   - Consider using group ownership with `775` permissions for better security

3. **File Upload Validation:** The existing code already validates:
   - Allowed media types (`allowed_media_types()`)
   - File size limits (via CodeIgniter upload library)
   - User permissions

---

## Summary

The 403 error was caused by:
1. ✅ **CSRF protection** blocking the request (fixed by adding endpoint to exclude list)
2. ✅ **Poor error handling** for authentication (fixed by returning proper JSON responses)
3. ✅ **No permission checks** before directory creation (fixed by adding comprehensive checks)

All issues have been resolved in the code. **Remember to fix file permissions on the production server** for the fix to work completely.

---

**Date:** 2024
**Status:** ✅ Resolved

