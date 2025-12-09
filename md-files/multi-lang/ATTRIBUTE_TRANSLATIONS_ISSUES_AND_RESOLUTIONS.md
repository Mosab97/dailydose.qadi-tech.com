# Attribute Translations Issues and Resolutions

## Overview

This document describes the issues encountered during the implementation of multilingual support for Attributes and Attribute Values, and how they were resolved. The implementation allows administrators to manage attribute names and attribute values in multiple languages (English, Arabic, Hebrew).

## Table of Contents

1. [Issue 1: Translations Not Loading in Modal](#issue-1-translations-not-loading-in-modal)
2. [Issue 2: Translations Not Being Stored](#issue-2-translations-not-being-stored)
3. [Issue 3: Tab ID Conflicts When Adding New Values](#issue-3-tab-id-conflicts-when-adding-new-values)
4. [Issue 4: Duplicate English Value Fields](#issue-4-duplicate-english-value-fields)
5. [Issue 5: Values Being Stored Twice in Database](#issue-5-values-being-stored-twice-in-database)

---

## Issue 1: Translations Not Loading in Modal

### Problem

When clicking the edit button in the attributes table, the modal would open but translations (Arabic and Hebrew) were not being displayed in the form fields, even though translations existed in the database.

### Root Cause

1. **Missing API Endpoints**: The `Attributes` controller (plural) was missing the API methods `get_attribute_translations()` and `get_attribute_value_translations()` that the JavaScript was calling.

2. **Timing Issue**: The JavaScript was trying to load translations and populate form fields before the HTML was fully appended to the DOM, causing the selectors to fail.

### Resolution

#### 1. Added Missing API Methods to Controller

**File**: `application/controllers/admin/Attributes.php`

Added two new methods:

```php
public function get_attribute_translations()
{
    if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
        $attribute_id = $this->input->get('attribute_id', true);
        
        if (empty($attribute_id)) {
            $this->response['error'] = true;
            $this->response['message'] = "Attribute ID is required";
            $this->response['data'] = [];
        } else {
            $translations = $this->attribute_model->get_attribute_translations($attribute_id);
            
            $this->response['error'] = false;
            $this->response['message'] = "Translations retrieved successfully";
            $this->response['data'] = $translations;
        }
        
        print_r(json_encode($this->response));
    } else {
        redirect('admin/login', 'refresh');
    }
}

public function get_attribute_value_translations()
{
    if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
        $attribute_value_id = $this->input->get('attribute_value_id', true);
        
        if (empty($attribute_value_id)) {
            $this->response['error'] = true;
            $this->response['message'] = "Attribute Value ID is required";
            $this->response['data'] = [];
        } else {
            $translations = $this->attribute_model->get_attribute_value_translations($attribute_value_id);
            
            $this->response['error'] = false;
            $this->response['message'] = "Translations retrieved successfully";
            $this->response['data'] = $translations;
        }
        
        print_r(json_encode($this->response));
    } else {
        redirect('admin/login', 'refresh');
    }
}
```

#### 2. Fixed Translation Loading Timing

**File**: `assets/admin/custom/custom.js`

**Before**:
```javascript
$.each(attribute_values, function (key, val) {
    // ... HTML generation ...
    
    // Load translations immediately (before HTML is appended)
    if (value_id && value_id !== '') {
        load_attribute_value_translations(value_id, value_index);
    }
});

$("#attribute_values_html").append(html);
```

**After**:
```javascript
var translations_to_load = [];

$.each(attribute_values, function (key, val) {
    // ... HTML generation ...
    
    // Store translation loading info for after HTML is appended
    if (value_id && value_id !== '') {
        translations_to_load.push({ value_id: value_id, value_index: value_index });
    }
});

$("#attribute_values_html").append(html);

// Load translations after HTML is in the DOM
if (translations_to_load.length > 0) {
    translations_to_load.forEach(function(item) {
        setTimeout(function() {
            load_attribute_value_translations(item.value_id, item.value_index);
        }, 100);
    });
}
```

### Result

- Translations now load correctly when the modal opens
- Attribute name translations (English, Arabic, Hebrew) populate correctly
- Attribute value translations populate correctly for each value

---

## Issue 2: Translations Not Being Stored

### Problem

When adding or editing attributes, translations were not being saved to the database, even though the form fields were filled in.

### Root Cause

1. **Controller Not Loading Translations**: The `Attributes` controller's `index()` method didn't load translations when `edit_id` was present, so the view had no translation data to display.

2. **Controller Not Processing Translations**: The `add_attributes()` method didn't handle translation data from POST requests - it wasn't parsing JSON or ensuring English was included.

3. **Main Form Not Collecting Translations**: The main page form submission didn't collect and send translation data like the modal form did.

### Resolution

#### 1. Updated Controller to Load Translations

**File**: `application/controllers/admin/Attributes.php`

**In `index()` method**:
```php
if (isset($_GET['edit_id'])) {
    $this->data['fetched_data'] = $this->db->select(' attr.* ,GROUP_CONCAT(av.value) as attribute_values')
        ->join('attribute_values av', 'av.attribute_id = attr.id')
        ->where(['attr.id' => $_GET['edit_id']])
        ->group_by('attr.id')
        ->get('attributes attr')
        ->result_array();
    
    // Load attribute translations
    $this->data['attribute_translations'] = $this->attribute_model->get_attribute_translations($_GET['edit_id']);
    
    // Load attribute value translations
    $attribute_values = $this->db->where('attribute_id', $_GET['edit_id'])->get('attribute_values')->result_array();
    $this->data['attribute_value_translations'] = [];
    foreach ($attribute_values as $av) {
        $this->data['attribute_value_translations'][$av['id']] = $this->attribute_model->get_attribute_value_translations($av['id']);
    }
} else {
    // Initialize empty translations arrays for new attributes
    $this->data['attribute_translations'] = [];
    $this->data['attribute_value_translations'] = [];
}
```

#### 2. Updated Controller to Process Translations

**File**: `application/controllers/admin/Attributes.php`

**In `add_attributes()` method**:
```php
// Collect and process attribute translations
$attribute_translations = [];
if (isset($_POST['attribute_translations']) && is_array($_POST['attribute_translations'])) {
    $attribute_translations = $_POST['attribute_translations'];
} elseif (isset($_POST['attribute_translations']) && is_string($_POST['attribute_translations']) && !empty($_POST['attribute_translations'])) {
    // If it's a JSON string, parse it
    $parsed = json_decode($_POST['attribute_translations'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
        $attribute_translations = $parsed;
    }
}

// Always ensure English translation is included from main field
if (!isset($attribute_translations['en'])) {
    $attribute_translations['en'] = [];
}
if (empty($attribute_translations['en']['name']) && !empty($_POST['name'])) {
    $attribute_translations['en']['name'] = $_POST['name'];
}

// Set the processed translations back to POST
$_POST['attribute_translations'] = $attribute_translations;

// Handle attribute value translations if provided
if (isset($_POST['attribute_value_translations'])) {
    if (is_string($_POST['attribute_value_translations']) && !empty($_POST['attribute_value_translations'])) {
        $parsed = json_decode($_POST['attribute_value_translations'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
            $_POST['attribute_value_translations'] = $parsed;
        } else {
            unset($_POST['attribute_value_translations']);
        }
    } elseif (!is_array($_POST['attribute_value_translations'])) {
        unset($_POST['attribute_value_translations']);
    }
}
```

#### 3. Updated JavaScript to Collect Translations

**File**: `assets/admin/custom/custom.js`

**In form submission handler**:
```javascript
// Collect attribute translations if this is an attribute form
if ($("#attribute_name").length) {
    // Sync English field first
    var name_en = $("#attribute_name").val();
    $("#attribute_name_en").val(name_en);
    
    // Collect all attribute translations
    var attribute_translations = {};
    var en_name = $("#attribute_name_en").val() || $("#attribute_name").val();
    var ar_name = $("#attribute_name_ar").val();
    var he_name = $("#attribute_name_he").val();
    
    if (en_name) {
        attribute_translations['en'] = { name: en_name };
    }
    if (ar_name && ar_name.trim() !== '') {
        attribute_translations['ar'] = { name: ar_name };
    }
    if (he_name && he_name.trim() !== '') {
        attribute_translations['he'] = { name: he_name };
    }
    
    // Add to form data as JSON string
    if (Object.keys(attribute_translations).length > 0) {
        formData.append('attribute_translations', JSON.stringify(attribute_translations));
    }
}
```

### Result

- Translations are now properly loaded when editing
- Translations are collected from form fields
- Translations are saved to the database correctly

---

## Issue 3: Tab ID Conflicts When Adding New Values

### Problem

When clicking "Add Attribute Value" button in the modal and then clicking tabs, the tab clicks would affect the original item's tabs instead of the newly added value's tabs. This was because multiple rows had the same tab IDs.

### Root Cause

When new attribute values were added dynamically, they always used index `0` for tab IDs, causing conflicts with existing rows that also had index-based IDs.

### Resolution

**File**: `assets/admin/custom/custom.js`

**Before**:
```javascript
// Always used index 0 for new values
html = `<div class="form-group row attribute-value-row" data-value-index="0">
    <ul class="nav nav-tabs mt-2" id="valueTabs0" role="tablist">
        <!-- tabs with id="value-0-en-tab", etc. -->
    </ul>
</div>`;
```

**After**:
```javascript
// Calculate next available index to avoid ID conflicts
var max_index = -1;
$('.attribute-value-row').each(function() {
    var current_index = parseInt($(this).data('value-index')) || 0;
    if (current_index > max_index) {
        max_index = current_index;
    }
});
var next_index = max_index + 1;
var unique_id = 'new_' + Date.now() + '_' + next_index;

html = `<div class="form-group row attribute-value-row" data-value-index="${next_index}" data-unique-id="${unique_id}">
    <ul class="nav nav-tabs mt-2" id="valueTabs${unique_id}" role="tablist">
        <!-- tabs with unique IDs -->
    </ul>
</div>`;
```

**Note**: This solution was later simplified to use the form's array index directly, which matches how JavaScript sends the data.

### Result

- Each attribute value row has unique tab IDs
- Tabs work independently for each row
- No conflicts when adding multiple new values

---

## Issue 4: Duplicate English Value Fields

### Problem

There were two fields for the English value:
1. Main field: `value_name[]` (for the main table)
2. Translation field: `attribute_value_translations[${value_index}][en][value]` (in English tab)

This caused confusion and potential duplication issues.

### Root Cause

The English value was being stored in both:
- The main `attribute_values.value` field (for backward compatibility)
- The `attribute_value_translations` table with `language_code='en'`

But the form had both a visible English field AND an English tab with another field, which was redundant.

### Resolution

**File**: `assets/admin/custom/custom.js`

**Removed English Tab**:
```javascript
// Before: Had English tab with input field
<div class="tab-pane fade show active" id="value-${value_index}-en" role="tabpanel">
    <input type="text" name="attribute_value_translations[${value_index}][en][value]" value="${val}">
</div>

// After: Only Arabic and Hebrew tabs, English is handled via hidden field synced with main field
<!-- Hidden field for English translation (synced with value_name) -->
<input type="hidden" class="attribute-value-en-translation" name="attribute_value_translations[${value_index}][en][value]" value="${val}">

<!-- Language Tabs for Attribute Value Translations (Arabic & Hebrew only) -->
<ul class="nav nav-tabs mt-2" id="valueTabs${value_index}" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="value-${value_index}-ar-tab" data-toggle="tab" href="#value-${value_index}-ar" role="tab">Arabic</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="value-${value_index}-he-tab" data-toggle="tab" href="#value-${value_index}-he" role="tab">Hebrew</a>
    </li>
</ul>
```

**Updated Form Submission**:
```javascript
// Collect attribute value translations
// English comes from value_name[], Arabic and Hebrew from translation fields
var attribute_value_translations = {};
$('.attribute-value-row').each(function() {
    var value_index = $(this).data('value-index');
    var value_id = $(this).data('value-id');
    // Get English value from main field (value_name[])
    var en_value = $(this).find('input[name="value_name[]"]').val();
    var ar_value = $(this).find(`input.attribute-value-ar[data-value-index="${value_index}"]`).val();
    var he_value = $(this).find(`input.attribute-value-he[data-value-index="${value_index}"]`).val();
    
    // Always include English translation from main field
    var translations = {};
    if (en_value && en_value.trim() !== '') {
        translations['en'] = { value: en_value };
    }
    // ... add Arabic and Hebrew ...
});
```

### Result

- Only one English field visible (`value_name[]`)
- English value automatically synced to translations
- Arabic and Hebrew have their own tabs
- Cleaner UI with no duplication

---

## Issue 5: Values Being Stored Twice in Database

### Problem

When adding a new attribute value (e.g., "test 4") and clicking "Update Attribute", the value was being inserted twice into the `attribute_values` table, creating duplicate records.

### Root Cause

1. **Duplicate Processing Logic**: The model had two separate sections processing new values:
   - Main loop: Processed all values including new ones
   - BlankKeys section: Processed new values again → causing duplicate inserts

2. **Index Mismatch**: The model was using a separate counter (`$value_index`) that didn't match the form's array index, causing translation matching issues.

3. **Form Fields Being Submitted**: Translation fields had `name` attributes, so they were being submitted as form data AND as JSON, potentially causing duplication.

### Resolution

#### 1. Removed Form Field Names for Translations

**File**: `assets/admin/custom/custom.js`

**Before**:
```javascript
<input type="text" name="attribute_value_translations[${value_index}][ar][value]" value="">
```

**After**:
```javascript
<input type="text" class="attribute-value-ar" data-value-index="${value_index}" value="">
```

Removed all `name` attributes from translation fields to prevent form submission. Translations are now only sent via JSON.

#### 2. Fixed Model to Process Values in Form Order

**File**: `application/models/Attribute_model.php`

**Before**:
```php
$data['value_id'] = array_map('intval', $data['value_id']);
$attribute_values = array_combine($data['value_id'], $data['value_name']);

$value_index = 0; // Separate counter
foreach ($attribute_values as $key => $val) {
    // Process using $key and separate $value_index counter
    // Mismatch between form index and counter
}
```

**After**:
```php
// Process values maintaining the form's index order
$value_ids = isset($data['value_id']) ? $data['value_id'] : [];
$value_names = isset($data['value_name']) ? $data['value_name'] : [];

// Process each value in the order they appear in the form (by array index)
foreach ($value_names as $form_index => $val) {
    $value_id = isset($value_ids[$form_index]) ? intval($value_ids[$form_index]) : 0;
    
    if (empty($value_id) || $value_id === 0) {
        // New value - use form_index to find translations
        $this->db->insert('attribute_values', $tempRow);
        $new_value_id = $this->db->insert_id();
        
        // Get translations using form index (matches JavaScript)
        $translations = isset($data['attribute_value_translations'][$form_index]) 
            ? $data['attribute_value_translations'][$form_index] 
            : [];
        // ... save translations ...
    } else {
        // Existing value - use value_id to find translations
        $this->db->set($tempRow)->where('id', $value_id)->update('attribute_values');
        
        // Get translations using value_id (matches JavaScript)
        $translations = isset($data['attribute_value_translations'][$value_id]) 
            ? $data['attribute_value_translations'][$value_id] 
            : [];
        // ... save translations ...
    }
}
```

#### 3. Removed Duplicate BlankKeys Section

**File**: `application/models/Attribute_model.php`

**Removed**:
```php
// This section was processing new values again, causing duplicates
if (isset($data['edit_attribute_id'])) {
    if(isset($data['value_id']) && !empty($data['value_id']) && isset($data['value_name']) && !empty($data['value_name'])){
        $blankKeys = array_keys($value_id, null);
        foreach ($blankKeys as $blankKey) {
            // Inserting new values again - DUPLICATE!
            $this->db->insert('attribute_values', $tempRow);
        }
    }
}
```

**Replaced with**:
```php
// Note: Blank keys (new values) are now handled in the main loop above
// This section is removed to prevent duplicate inserts
```

### Result

- Values are now inserted only once
- Translations are correctly matched using form index for new values and value_id for existing values
- No duplication in the database

---

## Key Learnings

### 1. Form Index vs. Database ID

When processing form data with mixed existing and new records:
- **Existing records**: Use database ID as the key for translations
- **New records**: Use form array index as the key for translations
- **JavaScript must match**: Send translations with the same key structure

### 2. Prevent Form Field Duplication

When sending data as JSON:
- Remove `name` attributes from fields that should only be sent via JSON
- Use `data-*` attributes or classes for JavaScript selection
- Prevents both form submission AND JSON from sending the same data

### 3. DOM Timing Issues

When dynamically adding HTML and then manipulating it:
- Append HTML to DOM first
- Use `setTimeout` or callbacks to ensure DOM is ready
- Store references/IDs during generation, use them after append

### 4. Avoid Duplicate Processing

When refactoring code:
- Check for duplicate sections that process the same data
- Remove redundant processing loops
- Ensure each record is processed exactly once

---

## Testing Checklist

After implementing fixes, verify:

- [ ] Modal opens and loads all translations correctly
- [ ] Adding new attribute values works without ID conflicts
- [ ] Tabs work independently for each attribute value row
- [ ] Only one English field is visible per value
- [ ] Translations are saved correctly (check database)
- [ ] No duplicate values in `attribute_values` table
- [ ] No duplicate translations in `attribute_value_translations` table
- [ ] English value appears in both main table and translations table
- [ ] Arabic and Hebrew translations appear only in translations table

---

## Related Files Modified

1. **`application/controllers/admin/Attributes.php`**
   - Added `get_attribute_translations()` method
   - Added `get_attribute_value_translations()` method
   - Updated `index()` to load translations
   - Updated `add_attributes()` to process translations

2. **`application/models/Attribute_model.php`**
   - Updated `add_attributes()` to process values in form order
   - Removed duplicate blankKeys processing section
   - Ensured English translation is always included

3. **`assets/admin/custom/custom.js`**
   - Fixed translation loading timing
   - Removed form field names from translation inputs
   - Updated form submission to collect translations correctly
   - Removed English tab, kept only Arabic/Hebrew tabs

---

## Related Documentation

- [Featured Sections Translations Implementation](./FEATURED_SECTIONS_TRANSLATIONS_IMPLEMENTATION.md)
- [Add-Ons Translations Implementation](./ADD_ONS_TRANSLATIONS_IMPLEMENTATION.md)

---

*Last Updated: 2025-12-09*



