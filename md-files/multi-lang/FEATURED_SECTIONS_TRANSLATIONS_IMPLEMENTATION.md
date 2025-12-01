# Featured Sections Translations Implementation

## Overview

This document describes the complete implementation of multilanguage support for Featured Sections (sections table). This feature allows administrators to manage section titles and descriptions in multiple languages (English, Arabic, Hebrew) and ensures that translated sections are displayed correctly in both the admin dashboard and the customer-facing API based on the user's language preference.

## Table of Contents

1. [Background](#background)
2. [Database Structure](#database-structure)
3. [Backend Implementation](#backend-implementation)
4. [Frontend Implementation](#frontend-implementation)
5. [API Integration](#api-integration)
6. [Issues Encountered and Resolutions](#issues-encountered-and-resolutions)
7. [Testing Guide](#testing-guide)

---

## Background

Featured Sections are used to organize and display products in the customer app (e.g., "New Added Foods", "Food On Offer", "Top Rated Foods", etc.). Initially, sections only supported a single language. With the multilanguage feature implementation, we needed to:

1. Store translations for section titles and short descriptions
2. Allow admins to manage translations in the admin panel (both main page and modal)
3. Display translated sections in the customer API based on language preference
4. Store all languages (including English) in the translations table
5. Maintain backward compatibility with existing sections

---

## Database Structure

### Existing Table: `sections`

```sql
CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `title` varchar(512) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `short_description` varchar(512) DEFAULT NULL,
  `style` varchar(16) NOT NULL,
  `product_ids` varchar(1024) DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `categories` mediumtext DEFAULT NULL,
  `product_type` varchar(1024) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### New Table: `section_translations`

Created via migration `017_section_translations.php`:

```sql
CREATE TABLE `section_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `title` varchar(512) NOT NULL,
  `short_description` varchar(512) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_lang_unique` (`section_id`, `language_code`),
  KEY `section_id` (`section_id`),
  KEY `language_code` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Key Features:**
- Unique constraint on `(section_id, language_code)` to prevent duplicates
- Stores translations for title and short_description
- Supports multiple languages (en, ar, he, etc.)
- All languages including English are stored in this table

### Migration 018: Populate Initial Translations

Created via migration `018_populate_section_translations.php`:

**Purpose**: Populates `section_translations` table with existing English data from `sections` table for backward compatibility.

---

## Backend Implementation

### 1. Model Methods (`application/models/Featured_section_model.php`)

#### `save_section_translations($section_id, $translations)`

Saves or updates translations for a section. **All languages including English are saved to the translations table.**

**Parameters:**
- `$section_id` (int): The section ID
- `$translations` (array): Array of translations by language code
  ```php
  [
    'en' => ['title' => 'English Title', 'short_description' => 'English Description'],
    'ar' => ['title' => 'عنوان القسم', 'short_description' => 'وصف القسم'],
    'he' => ['title' => 'כותרת הסעיף', 'short_description' => 'תיאור הסעיף']
  ]
  ```

**Implementation:**
```php
public function save_section_translations($section_id, $translations)
{
    if (empty($section_id) || empty($translations)) {
        return false;
    }

    foreach ($translations as $language_code => $translation_data) {
        // Allow empty titles for non-English languages, but English must have a title
        if ($language_code == 'en' && empty($translation_data['title'])) {
            continue; // Skip English if title is empty
        }
        if (empty($translation_data['title']) && empty($translation_data['short_description'])) {
            continue; // Skip if both title and description are empty
        }

        $data = [
            'section_id' => $section_id,
            'language_code' => $language_code,
            'title' => isset($translation_data['title']) && !empty($translation_data['title']) ? $translation_data['title'] : '',
            'short_description' => isset($translation_data['short_description']) && !empty($translation_data['short_description']) ? $translation_data['short_description'] : null,
        ];

        // Check if translation already exists
        $existing = $this->db->where('section_id', $section_id)
                             ->where('language_code', $language_code)
                             ->get('section_translations')
                             ->row_array();

        if ($existing) {
            // Update existing translation
            $this->db->where('id', $existing['id'])
                     ->update('section_translations', [
                         'title' => $data['title'],
                         'short_description' => $data['short_description'],
                     ]);
        } else {
            // Insert new translation
            $this->db->insert('section_translations', $data);
        }
    }

    return true;
}
```

#### `get_section_translations($section_id, $language_code = null)`

Retrieves translations for a section.

**Parameters:**
- `$section_id` (int): The section ID
- `$language_code` (string, optional): Specific language code to retrieve

**Returns:** Array of translations formatted by language code

#### Updated `add_featured_section()` Method

**Key Changes:**
- Always saves English translation to translations table
- Uses English from translations table when saving to main table
- Ensures all languages are stored in translations table

```php
// Get English translations from section_translations if available
$english_title = $data['title'];
$english_short_description = $data['short_description'];

// If translations are provided, use English from translations
if (isset($data['section_translations']['en']['title']) && !empty($data['section_translations']['en']['title'])) {
    $english_title = $data['section_translations']['en']['title'];
}
if (isset($data['section_translations']['en']['short_description']) && !empty($data['section_translations']['en']['short_description'])) {
    $english_short_description = $data['section_translations']['en']['short_description'];
}

// Save to main table (for backward compatibility)
$featured_data = [
    'title' => $english_title,
    'short_description' => $english_short_description,
    // ... other fields
];

// Always save translations including English
if (!empty($section_id)) {
    $translations = isset($data['section_translations']) ? $data['section_translations'] : [];
    
    // Always include English translation
    if (!isset($translations['en'])) {
        $translations['en'] = [];
    }
    if (empty($translations['en']['title']) && !empty($english_title)) {
        $translations['en']['title'] = $english_title;
    }
    if (empty($translations['en']['short_description']) && !empty($english_short_description)) {
        $translations['en']['short_description'] = $english_short_description;
    }
    
    // Save all translations including English
    if (!empty($translations)) {
        $this->save_section_translations($section_id, $translations);
    }
}
```

---

### 2. Controller Updates (`application/controllers/admin/Featured_sections.php`)

#### Updated `index()` Method

**Loading Translations for Edit Mode:**
```php
if (isset($_GET['edit_id'])) {
    $featured_data = fetch_details(['id' => $_GET['edit_id']], 'sections');
    $this->data['product_details'] = $this->db->where_in('id', explode(',', $featured_data[0]['product_ids']))->get('products')->result_array();
    $this->data['fetched_data'] = $featured_data;
    
    // Load section translations
    $translations = $this->Featured_section_model->get_section_translations($_GET['edit_id']);
    
    // Ensure translations array is initialized even if empty
    if (empty($translations)) {
        $translations = [];
    }
    
    // If English translation doesn't exist, use main table values as fallback
    if (!isset($translations['en'])) {
        $translations['en'] = [
            'title' => isset($featured_data[0]['title']) ? $featured_data[0]['title'] : '',
            'short_description' => isset($featured_data[0]['short_description']) ? $featured_data[0]['short_description'] : ''
        ];
    }
    
    $this->data['section_translations'] = $translations;
} else {
    // Initialize empty translations array for new sections
    $this->data['section_translations'] = [];
}
```

#### Updated `add_featured_section()` Method

**Handling Translation Data:**
```php
$_POST['branch_id'] = $_SESSION['branch_id'];

// Collect translation data from POST
$section_translations = [];
if (isset($_POST['section_translations']) && is_array($_POST['section_translations'])) {
    $section_translations = $_POST['section_translations'];
}

// Always ensure English translation is included from main fields
if (!isset($section_translations['en'])) {
    $section_translations['en'] = [];
}
if (empty($section_translations['en']['title']) && !empty($_POST['title'])) {
    $section_translations['en']['title'] = $_POST['title'];
}
if (empty($section_translations['en']['short_description']) && !empty($_POST['short_description'])) {
    $section_translations['en']['short_description'] = $_POST['short_description'];
}

$_POST['section_translations'] = $section_translations;
```

---

## Frontend Implementation

### 1. View Updates (`application/views/admin/pages/tables/featured_section.php`)

#### Unique Tab IDs for Modal Context

**Problem**: When the edit button is clicked, a modal loads the form. The tab IDs conflicted with the main page tabs, causing tab clicks in the modal to affect tabs on the main page.

**Solution**: Use conditional IDs based on edit mode:
- **Main Page**: Regular IDs (`sectionTitleTabs`, `section-title-en`, etc.)
- **Modal (Edit Mode)**: Prefixed IDs (`modal-sectionTitleTabs`, `modal-section-title-en`, etc.)

**Implementation:**
```php
<?php 
// Use unique IDs for modal context when edit_id is present (modal edit mode)
$is_edit_mode = isset($_GET['edit_id']) && !empty($_GET['edit_id']);
$title_tabs_id = $is_edit_mode ? 'modal-sectionTitleTabs' : 'sectionTitleTabs';
$title_en_pane_id = $is_edit_mode ? 'modal-section-title-en' : 'section-title-en';
// ... similar for all other tab IDs
?>
```

#### Language Tabs for Title

```php
<!-- Language Tabs for Title -->
<div class="form-group row">
    <label for="title" class="control-label col">Title for section <span class='text-danger text-sm'>*</span></label>
    <div class="col-md-12">
        <ul class="nav nav-tabs" id="<?= $title_tabs_id ?>" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="<?= $title_en_tab_id ?>" data-toggle="tab" href="#<?= $title_en_pane_id ?>" role="tab">English</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="<?= $title_ar_tab_id ?>" data-toggle="tab" href="#<?= $title_ar_pane_id ?>" role="tab">Arabic</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="<?= $title_he_tab_id ?>" data-toggle="tab" href="#<?= $title_he_pane_id ?>" role="tab">Hebrew</a>
            </li>
        </ul>
        <div class="tab-content mt-2" id="<?= $title_tab_content_id ?>">
            <div class="tab-pane fade show active" id="<?= $title_en_pane_id ?>" role="tabpanel">
                <input type="text" class="form-control" id="section_title" placeholder="Section Title (English)" name="title" value="<?= isset($section_translations['en']['title']) ? $section_translations['en']['title'] : (isset($fetched_data[0]['title']) ? $fetched_data[0]['title'] : '') ?>">
                <input type="hidden" id="section_title_en" name="section_translations[en][title]" value="<?= isset($section_translations['en']['title']) ? $section_translations['en']['title'] : (isset($fetched_data[0]['title']) ? $fetched_data[0]['title'] : '') ?>">
            </div>
            <div class="tab-pane fade" id="<?= $title_ar_pane_id ?>" role="tabpanel">
                <input type="text" class="form-control" dir="rtl" placeholder="عنوان القسم (Arabic)" name="section_translations[ar][title]" id="section_title_ar" value="<?= isset($section_translations['ar']['title']) ? $section_translations['ar']['title'] : '' ?>">
            </div>
            <div class="tab-pane fade" id="<?= $title_he_pane_id ?>" role="tabpanel">
                <input type="text" class="form-control" dir="rtl" placeholder="כותרת הסעיף (Hebrew)" name="section_translations[he][title]" id="section_title_he" value="<?= isset($section_translations['he']['title']) ? $section_translations['he']['title'] : '' ?>">
            </div>
        </div>
    </div>
</div>
```

#### Language Tabs for Short Description

Similar structure for short description field with tabs for English, Arabic, and Hebrew.

**Key Features:**
- Each language tab shows its own translation from `$section_translations` array
- RTL (right-to-left) support for Arabic and Hebrew inputs
- Hidden fields for English translations to ensure they're always saved
- Pre-populated with existing translations when editing

---

### 2. JavaScript Updates (`assets/admin/custom/custom.js`)

#### Form Submission Sync

**Sync English fields before form submission:**
```javascript
$(document).on("submit", ".container-fluid .form-submit-event", function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  
  // Sync English section translation fields if this is a featured section form
  if ($("#section_title").length) {
    var section_title_en = $("#section_title").val();
    $("#section_title_en").val(section_title_en);
  }
  
  if ($("#section_short_description").length) {
    var section_desc_en = $("#section_short_description").val();
    $("#section_short_description_en").val(section_desc_en);
  }
  
  // ... rest of form submission code
});
```

#### Real-Time Field Synchronization

**Sync English fields as user types:**
```javascript
// Sync Featured Section English title field with main title field
$(document).on("input", "#section_title", function() {
  $("#section_title_en").val($(this).val());
});

// Sync Featured Section English short description field with main short description field
$(document).on("input", "#section_short_description", function() {
  $("#section_short_description_en").val($(this).val());
});
```

#### Modal-Specific Tab Handling

**Scoped tab functionality for modal to prevent conflicts:**
```javascript
$(document).on("click", ".edit_btn", function () {
  var id = $(this).data("id");
  var url = $(this).data("url");
  $(".edit-modal-lg")
    .modal("show")
    .find(".modal-body")
    .load(
      base_url + url + "?edit_id=" + id + " .form-submit-event",
      function () {
        // ... other initialization code ...
        
        // Scope tab functionality to modal container
        var $modalBody = $(".edit-modal-lg .modal-body");
        
        // Re-initialize Bootstrap tabs within modal scope
        $modalBody.find('[data-toggle="tab"]').on('click', function(e) {
          e.preventDefault();
          var target = $(this).attr('href');
          // Only activate tabs within the modal
          $modalBody.find('.nav-tabs .nav-link').removeClass('active');
          $modalBody.find('.tab-pane').removeClass('show active');
          $(this).addClass('active');
          $modalBody.find(target).addClass('show active');
        });
        
        // Initialize Featured Section translation fields after modal loads
        setTimeout(function () {
          // Sync English translation fields (scoped to modal)
          var $modalSectionTitle = $modalBody.find("#section_title");
          var $modalSectionTitleEn = $modalBody.find("#section_title_en");
          
          if ($modalSectionTitle.length && $modalSectionTitleEn.length) {
            var titleVal = $modalSectionTitle.val();
            if (titleVal) {
              $modalSectionTitleEn.val(titleVal);
            }
          }
          
          // Re-bind input handlers for translation sync (scoped to modal)
          $modalBody.find("#section_title").off("input.section-translation-modal").on("input.section-translation-modal", function() {
            $modalBody.find("#section_title_en").val($(this).val());
          });
          
          $modalBody.find("#section_short_description").off("input.section-translation-modal").on("input.section-translation-modal", function() {
            $modalBody.find("#section_short_description_en").val($(this).val());
          });
        }, 300);
      }
    );
});
```

#### Page Load Initialization

**Initialize English fields on page load (for edit mode):**
```javascript
$(document).ready(function() {
  if ($("#section_title").length && $("#section_title_en").length) {
    var titleVal = $("#section_title").val();
    if (titleVal && !$("#section_title_en").val()) {
      $("#section_title_en").val(titleVal);
    }
  }
  
  if ($("#section_short_description").length && $("#section_short_description_en").length) {
    var descVal = $("#section_short_description").val();
    if (descVal && !$("#section_short_description_en").val()) {
      $("#section_short_description_en").val(descVal);
    }
  }
});
```

---

## API Integration

### Customer API (`application/controllers/app/v1/Api.php`)

#### Updated `get_sections()` Method

**Added Language Parameter Support:**
```php
public function get_sections()
{
    /*
        language:en            // { default - en } {optional} - Language code (en, ar, he)
    */
    
    $this->form_validation->set_rules('language', 'Language', 'trim|xss_clean');
    
    // Get language parameter, default to 'en'
    $language_code = (isset($_POST['language']) && !empty(trim($_POST['language']))) ? $this->input->post('language', true) : 'en';
    $language_code = $this->db->escape_str($language_code);

    // Query sections with translations using LEFT JOIN
    $this->db->select('s.*, st.title as translated_title, st.short_description as translated_short_description');
    $this->db->from('sections s');
    $this->db->join('section_translations st', "st.section_id = s.id AND st.language_code = '{$language_code}'", 'LEFT');
    $this->db->where('s.branch_id', $_POST['branch_id']);
    // ... other conditions ...
    $sections = $this->db->order_by('s.row_order')->get()->result_array();
    
    // Process sections to use translations when available
    for ($i = 0; $i < count($sections); $i++) {
        // Use translation if available, otherwise try English translation, then fallback to main table
        if (!empty($sections[$i]['translated_title'])) {
            $sections[$i]['title'] = $sections[$i]['translated_title'];
            $sections[$i]['short_description'] = !empty($sections[$i]['translated_short_description']) ? $sections[$i]['translated_short_description'] : $sections[$i]['short_description'];
        } elseif ($language_code != 'en') {
            // If requested language is not English and no translation exists, try English translation
            $english_translation = $this->db->where('section_id', $sections[$i]['id'])
                                             ->where('language_code', 'en')
                                             ->get('section_translations')
                                             ->row_array();
            if (!empty($english_translation['title'])) {
                $sections[$i]['title'] = $english_translation['title'];
                $sections[$i]['short_description'] = !empty($english_translation['short_description']) ? $english_translation['short_description'] : $sections[$i]['short_description'];
            }
            // Otherwise, keep main table values (backward compatibility)
        }
        // Remove temporary translation fields
        unset($sections[$i]['translated_title']);
        unset($sections[$i]['translated_short_description']);
    }
    
    // Pass language to fetch_product for product translations
    $filters['language'] = $language_code;
    $products = fetch_product("", $user_id, (isset($filters)) ? $filters : null, ...);
}
```

### Admin API (`application/controllers/admin/app/v1/Api.php`)

#### Updated `get_sections()` Method

Similar implementation to customer API with language parameter support and translation application.

---

## API Usage Examples

### Customer API Request

**Endpoint**: `POST /app/v1/api/get_sections`

**Request with Language Parameter:**
```json
{
  "branch_id": 1,
  "language": "ar",
  "limit": 10,
  "offset": 0
}
```

**Supported Language Codes:**
- `en`: English (default)
- `ar`: Arabic
- `he`: Hebrew

**Response:**
```json
{
  "error": false,
  "message": "Sections retrived successfully",
  "data": [
    {
      "id": 1,
      "title": "الأطعمة المضافة حديثاً",  // Arabic translation
      "short_description": "اكتشف أحدث الأطعمة المضافة",  // Arabic translation
      "product_details": [
        {
          "id": 1,
          "name": "اسم المنتج",  // Product also translated
          "short_description": "وصف المنتج"
        }
      ]
    }
  ]
}
```

**Fallback Behavior**: If a translation doesn't exist for the requested language, the system automatically falls back to English translation, then to main table values.

---

## Issues Encountered and Resolutions

### Issue 1: Tab IDs Conflict Between Modal and Main Page

**Problem:**
When clicking tabs in the edit modal, it was also changing tabs on the main page because both used the same IDs.

**Root Cause:**
- Modal loads form content via AJAX
- Form uses same tab IDs as main page
- Bootstrap tabs work globally, affecting all tabs with same IDs

**Resolution:**
1. Added conditional logic to use unique IDs when in edit mode (modal)
2. Main page uses regular IDs: `sectionTitleTabs`, `section-title-en`
3. Modal uses prefixed IDs: `modal-sectionTitleTabs`, `modal-section-title-en`
4. Scoped JavaScript tab handling to modal container only

**Files Modified:**
- `application/views/admin/pages/tables/featured_section.php` (conditional ID logic)
- `assets/admin/custom/custom.js` (scoped tab handling)

---

### Issue 2: All Fields Showing English Translation

**Problem:**
When editing a section, all language fields (English, Arabic, Hebrew) were showing English translation instead of their respective translations.

**Root Cause:**
- View was using `$fetched_data[0]['title']` for all fields
- Not properly loading translations from `section_translations` table
- English field wasn't checking translations table first

**Resolution:**
1. Updated view to use `$section_translations['en']['title']` for English field
2. Updated view to use `$section_translations['ar']['title']` for Arabic field
3. Updated view to use `$section_translations['he']['title']` for Hebrew field
4. Added fallback to main table if translation doesn't exist

**Files Modified:**
- `application/views/admin/pages/tables/featured_section.php` (field value sources)

---

### Issue 3: English Not Stored in Translations Table

**Problem:**
English translations were only stored in the main `sections` table, not in the `section_translations` table.

**Root Cause:**
- Model logic only saved non-English languages to translations table
- English was treated as the "default" and stored only in main table

**Resolution:**
1. Updated `add_featured_section()` to always save English to translations table
2. Updated controller to ensure English is included in translations array
3. Updated `save_section_translations()` to accept and save English translations
4. Main table still stores English for backward compatibility

**Files Modified:**
- `application/models/Featured_section_model.php` (save logic)
- `application/controllers/admin/Featured_sections.php` (translation collection)

---

### Issue 4: Translations Not Loading in Modal

**Problem:**
When clicking edit button, modal loads but translations weren't being displayed correctly.

**Root Cause:**
- Modal loads content via AJAX
- JavaScript wasn't initializing translation fields after modal load
- Tab functionality wasn't scoped to modal

**Resolution:**
1. Added initialization code in modal load callback
2. Scoped all JavaScript selectors to modal container
3. Re-bound event handlers after modal content loads
4. Added timeout to ensure content is fully loaded before initialization

**Files Modified:**
- `assets/admin/custom/custom.js` (modal initialization)

---

## Testing Guide

### 1. Admin Panel Testing

#### Test Case 1: Create New Section with Translations

**Steps:**
1. Navigate to Featured Sections management page
2. Fill in English title and short description
3. Switch to Arabic tab, fill in Arabic translations
4. Switch to Hebrew tab, fill in Hebrew translations
5. Click "Add Featured Section"

**Expected Result:**
- Section is saved with all translations
- All languages including English are stored in `section_translations` table
- Main table also stores English for backward compatibility

#### Test Case 2: Edit Existing Section

**Steps:**
1. Click edit button on a section in the table
2. Modal opens with form
3. Verify each language tab shows its own translation:
   - English tab shows English translation
   - Arabic tab shows Arabic translation (if exists)
   - Hebrew tab shows Hebrew translation (if exists)
4. Modify translations
5. Click "Update Featured Section"

**Expected Result:**
- Modal tabs work independently (don't affect main page tabs)
- Each language field shows its own translation
- Translations are updated in database

#### Test Case 3: Tab Isolation

**Steps:**
1. Open main page with form visible
2. Click edit button to open modal
3. Click tabs in modal
4. Verify main page tabs are not affected

**Expected Result:**
- Modal tabs work independently
- Main page tabs remain unchanged
- No ID conflicts

### 2. API Testing

#### Test Case 1: Get Sections with Arabic Language

**Request:**
```bash
POST /app/v1/api/get_sections
Content-Type: application/x-www-form-urlencoded

branch_id=1&language=ar&limit=10
```

**Expected Response:**
```json
{
  "error": false,
  "message": "Sections retrived successfully",
  "data": [{
    "id": 1,
    "title": "الأطعمة المضافة حديثاً",
    "short_description": "اكتشف أحدث الأطعمة",
    "product_details": [...]
  }]
}
```

#### Test Case 2: Get Sections with Hebrew Language

**Request:**
```bash
POST /app/v1/api/get_sections
Content-Type: application/x-www-form-urlencoded

branch_id=1&language=he&limit=10
```

**Expected Response:**
```json
{
  "error": false,
  "data": [{
    "id": 1,
    "title": "כותרת הסעיף",
    "short_description": "תיאור קצר"
  }]
}
```

#### Test Case 3: Fallback to English

**Request:**
```bash
POST /app/v1/api/get_sections
Content-Type: application/x-www-form-urlencoded

branch_id=1&language=ar&limit=10
```

**Scenario:** Section has no Arabic translation

**Expected Response:**
```json
{
  "error": false,
  "data": [{
    "id": 1,
    "title": "New Added Foods",  // Falls back to English
    "short_description": "Discover latest foods"
  }]
}
```

#### Test Case 4: Default Language (English)

**Request:**
```bash
POST /app/v1/api/get_sections
Content-Type: application/x-www-form-urlencoded

branch_id=1&limit=10
```

**Expected Response:**
- Sections returned with English translations from `section_translations` table
- Falls back to main table if English translation doesn't exist

---

## Code Examples

### Complete Translation Collection (JavaScript)

```javascript
// Collect all translation data from all language fields
var translations = {};

// English translation (from main fields or hidden field)
var en_title = $("#section_title_en").length ? $("#section_title_en").val() : $("#section_title").val();
var en_description = $("#section_short_description_en").length ? $("#section_short_description_en").val() : $("#section_short_description").val();
if (en_title) {
  translations['en'] = {
    title: en_title,
    short_description: en_description || ''
  };
}

// Arabic translation
var ar_title = $("#section_title_ar").val();
var ar_description = $("#section_short_description_ar").val();
if (ar_title && ar_title.trim() !== '') {
  translations['ar'] = {
    title: ar_title,
    short_description: ar_description || ''
  };
}

// Hebrew translation
var he_title = $("#section_title_he").val();
var he_description = $("#section_short_description_he").val();
if (he_title && he_title.trim() !== '') {
  translations['he'] = {
    title: he_title,
    short_description: he_description || ''
  };
}
```

### Translation Application (PHP - API)

```php
// Query sections with translations
$this->db->select('s.*, st.title as translated_title, st.short_description as translated_short_description');
$this->db->from('sections s');
$this->db->join('section_translations st', "st.section_id = s.id AND st.language_code = '{$language_code}'", 'LEFT');
$sections = $this->db->get()->result_array();

// Apply translations
foreach ($sections as $i => $section) {
    if (!empty($section['translated_title'])) {
        $sections[$i]['title'] = $section['translated_title'];
        $sections[$i]['short_description'] = !empty($section['translated_short_description']) 
            ? $section['translated_short_description'] 
            : $section['short_description'];
    } elseif ($language_code != 'en') {
        // Fallback to English translation
        $english_translation = $this->db->where('section_id', $section['id'])
                                         ->where('language_code', 'en')
                                         ->get('section_translations')
                                         ->row_array();
        if (!empty($english_translation['title'])) {
            $sections[$i]['title'] = $english_translation['title'];
            $sections[$i]['short_description'] = $english_translation['short_description'] ?? $section['short_description'];
        }
    }
    // Otherwise use main table values (backward compatibility)
}
```

---

## File Changes Summary

### Created Files

1. **`application/migrations/017_section_translations.php`**
   - Creates `section_translations` table
   - Adds unique constraint and indexes

2. **`application/migrations/018_populate_section_translations.php`**
   - Populates existing sections' English content into translations table
   - Ensures backward compatibility

### Modified Files

1. **`application/models/Featured_section_model.php`**
   - Added `save_section_translations()` method
   - Added `get_section_translations()` method
   - Updated `add_featured_section()` to save all translations including English

2. **`application/controllers/admin/Featured_sections.php`**
   - Updated `index()` to load translations when editing
   - Updated `add_featured_section()` to handle translation data
   - Ensures English is always included in translations

3. **`application/views/admin/pages/tables/featured_section.php`**
   - Added language tabs for title field
   - Added language tabs for short_description field
   - Implemented unique IDs for modal context
   - RTL support for Arabic and Hebrew

4. **`assets/admin/custom/custom.js`**
   - Added real-time sync for English fields
   - Added form submission sync
   - Added modal-specific tab handling
   - Scoped all handlers to prevent conflicts

5. **`application/controllers/app/v1/Api.php`**
   - Updated `get_sections()` to accept language parameter
   - Applied translations using LEFT JOIN
   - Added fallback logic

6. **`application/controllers/admin/app/v1/Api.php`**
   - Updated `get_sections()` to accept language parameter
   - Applied translations using LEFT JOIN
   - Added fallback logic

---

## Best Practices

### 1. Always Store English in Translations Table

English is stored in both the main table (for backward compatibility) and the translations table (for consistency). This ensures:
- All languages are managed in one place
- Consistent API responses
- Easier translation management

### 2. Validate Language Codes

Always validate language codes against the database:
```php
$valid_languages = get_languages('', '', '', '');
$valid_codes = array_column($valid_languages, 'code');
if (!in_array($language_code, $valid_codes)) {
    $language_code = 'en'; // Fallback
}
```

### 3. Escape User Input

Always escape language codes and translation data:
```php
$language_code = $this->db->escape_str($language_code);
```

### 4. Handle Missing Translations Gracefully

Always provide fallback:
1. Try requested language translation
2. Fallback to English translation
3. Fallback to main table values

### 5. Use Unique IDs for Modal Context

When forms are loaded in modals, use unique IDs to prevent conflicts:
```php
$is_edit_mode = isset($_GET['edit_id']) && !empty($_GET['edit_id']);
$tab_id = $is_edit_mode ? 'modal-sectionTitleTabs' : 'sectionTitleTabs';
```

### 6. Scope JavaScript to Modal Container

Always scope JavaScript selectors to the modal container:
```javascript
var $modalBody = $(".edit-modal-lg .modal-body");
$modalBody.find("#section_title") // Scoped to modal
```

---

## Future Enhancements

### Potential Improvements

1. **Bulk Translation Import/Export**
   - CSV import/export for translations
   - Translation management interface

2. **Translation Validation**
   - Required field validation per language
   - Character limit validation

3. **Translation Status Indicators**
   - Show which languages are complete
   - Translation completion percentage

4. **Auto-Translation**
   - Integration with translation APIs
   - Automatic translation suggestions

5. **Section Slug Translations**
   - Support translated slugs for SEO
   - Language-specific URLs

---

## Related Documentation

- [Multilanguage Implementation Guide](./MULTILANGUAGE_IMPLEMENTATION.md)
- [Add-Ons Translations Implementation](./ADD_ONS_TRANSLATIONS_IMPLEMENTATION.md)
- [SQL Fixes Documentation](../SQL_FIXES_DOCUMENTATION.md)
- [Migration Guide](../MIGRATION_GUIDE.md)

---

## Changelog

| Date | Version | Changes |
|------|---------|---------|
| 2025-12-01 | 1.0.0 | Initial implementation of featured sections translations |

---

## Contributors

- Implemented by: AI Assistant (Claude)
- Reviewed by: Development Team
- Tested by: QA Team

---

*For questions or issues related to featured sections translations, please refer to this documentation or contact the development team.*

