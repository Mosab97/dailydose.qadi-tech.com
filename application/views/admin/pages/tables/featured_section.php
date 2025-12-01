<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Featured Section (Show Foods Exclusively)</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="text text-info"
                                href="<?= base_url('admin/home') ?>"><?= display_breadcrumbs(); ?></a></li>
                        <!-- <li class="breadcrumb-item active">Orders</li> -->
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <!-- form start -->
                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/Featured_sections/add_featured_section'); ?>" method="POST" enctype="multipart/form-data">
                            <?php if (isset($fetched_data[0]['id'])) { ?>
                                <input type="hidden" id="edit_featured_section" name="edit_featured_section" value="<?= @$fetched_data[0]['id'] ?>">
                                <input type="hidden" id="update_id" name="update_id" value="1">
                            <?php } ?>
                            <div class="card-body">
                                <!-- Language Tabs for Title -->
                                <div class="form-group row">
                                    <label for="title" class="control-label col">Title for section <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-md-12">
                                        <?php 
                                        // Use unique IDs for modal context when edit_id is present (modal edit mode)
                                        // For modal: use modal- prefix, for main page: use regular IDs
                                        $is_edit_mode = isset($_GET['edit_id']) && !empty($_GET['edit_id']);
                                        $title_tabs_id = $is_edit_mode ? 'modal-sectionTitleTabs' : 'sectionTitleTabs';
                                        $title_tab_content_id = $is_edit_mode ? 'modal-sectionTitleTabContent' : 'sectionTitleTabContent';
                                        $title_en_tab_id = $is_edit_mode ? 'modal-section-title-en-tab' : 'section-title-en-tab';
                                        $title_en_pane_id = $is_edit_mode ? 'modal-section-title-en' : 'section-title-en';
                                        $title_ar_tab_id = $is_edit_mode ? 'modal-section-title-ar-tab' : 'section-title-ar-tab';
                                        $title_ar_pane_id = $is_edit_mode ? 'modal-section-title-ar' : 'section-title-ar';
                                        $title_he_tab_id = $is_edit_mode ? 'modal-section-title-he-tab' : 'section-title-he-tab';
                                        $title_he_pane_id = $is_edit_mode ? 'modal-section-title-he' : 'section-title-he';
                                        ?>
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
                                <!-- Language Tabs for Short Description -->
                                <div class="form-group row">
                                    <label for="short_description" class="control-label col">Short description <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-md-12">
                                        <?php 
                                        $desc_tabs_id = $is_edit_mode ? 'modal-sectionDescTabs' : 'sectionDescTabs';
                                        $desc_tab_content_id = $is_edit_mode ? 'modal-sectionDescTabContent' : 'sectionDescTabContent';
                                        $desc_en_tab_id = $is_edit_mode ? 'modal-section-desc-en-tab' : 'section-desc-en-tab';
                                        $desc_en_pane_id = $is_edit_mode ? 'modal-section-desc-en' : 'section-desc-en';
                                        $desc_ar_tab_id = $is_edit_mode ? 'modal-section-desc-ar-tab' : 'section-desc-ar-tab';
                                        $desc_ar_pane_id = $is_edit_mode ? 'modal-section-desc-ar' : 'section-desc-ar';
                                        $desc_he_tab_id = $is_edit_mode ? 'modal-section-desc-he-tab' : 'section-desc-he-tab';
                                        $desc_he_pane_id = $is_edit_mode ? 'modal-section-desc-he' : 'section-desc-he';
                                        ?>
                                        <ul class="nav nav-tabs" id="<?= $desc_tabs_id ?>" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="<?= $desc_en_tab_id ?>" data-toggle="tab" href="#<?= $desc_en_pane_id ?>" role="tab">English</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="<?= $desc_ar_tab_id ?>" data-toggle="tab" href="#<?= $desc_ar_pane_id ?>" role="tab">Arabic</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="<?= $desc_he_tab_id ?>" data-toggle="tab" href="#<?= $desc_he_pane_id ?>" role="tab">Hebrew</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content mt-2" id="<?= $desc_tab_content_id ?>">
                                            <div class="tab-pane fade show active" id="<?= $desc_en_pane_id ?>" role="tabpanel">
                                                <input type="text" class="form-control" id="section_short_description" placeholder="Short Description (English)" name="short_description" value="<?= isset($section_translations['en']['short_description']) ? $section_translations['en']['short_description'] : (isset($fetched_data[0]['short_description']) ? $fetched_data[0]['short_description'] : '') ?>">
                                                <input type="hidden" id="section_short_description_en" name="section_translations[en][short_description]" value="<?= isset($section_translations['en']['short_description']) ? $section_translations['en']['short_description'] : (isset($fetched_data[0]['short_description']) ? $fetched_data[0]['short_description'] : '') ?>">
                                            </div>
                                            <div class="tab-pane fade" id="<?= $desc_ar_pane_id ?>" role="tabpanel">
                                                <input type="text" class="form-control" dir="rtl" placeholder="وصف قصير (Arabic)" name="section_translations[ar][short_description]" id="section_short_description_ar" value="<?= isset($section_translations['ar']['short_description']) ? $section_translations['ar']['short_description'] : '' ?>">
                                            </div>
                                            <div class="tab-pane fade" id="<?= $desc_he_pane_id ?>" role="tabpanel">
                                                <input type="text" class="form-control" dir="rtl" placeholder="תיאור קצר (Hebrew)" name="section_translations[he][short_description]" id="section_short_description_he" value="<?= isset($section_translations['he']['short_description']) ? $section_translations['he']['short_description'] : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row feactured_section_categories<?= (isset($fetched_data[0]['id'])  && $fetched_data[0]['product_type'] == 'custom_foods') ? 'd-none' : '' ?>" id = "feactured_section_categories">
                                    <label for="categories" class="control-label col">Categories</label>
                                    <div class="col-md-12">
                                        <?php if (isset($permissions_message)) { ?>
                                            <span class='text-danger text-sm mb-3'>(<?= $permissions_message ?>)</span><br>
                                        <?php } ?>
                                        <select name="categories[]" class=" select_multiple w-100" multiple data-placeholder=" Type to search and select categories">
                                            <option value=""><?= (isset($categories) && empty($categories)) ? 'No Categories Exist' : 'Select Categories' ?>
                                            </option>
                                            <?php
                                            $selected_val = (isset($fetched_data[0]['id']) &&  !empty($fetched_data[0]['id'])) ? $fetched_data[0]['categories'] : '';
                                            $selected_vals = explode(',', $selected_val);
                                            echo get_categories_option_html($categories, $selected_vals);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <?php
                                    $product_type = ['new_added_foods', 'food_on_offer', 'top_rated_foods', 'most_ordered_foods', 'custom_foods'];
                                    ?>
                                    <label for="product_type" class="control-label col">Product Types <span class='text-danger text-sm'> * </span></label>
                                    <div class="col-md-12">
                                        <select name="product_type" class="form-control product_type">
                                            <option value=" ">Select Types</option>
                                            <?php foreach ($product_type as $row) { ?>
                                                <option value="<?= $row ?>" <?= (isset($fetched_data[0]['id']) &&  $fetched_data[0]['product_type'] == $row) ? "Selected" : "" ?>><?= ucwords(str_replace('_', ' ', $row)) ?></option>
                                            <?php
                                            } ?>
                                        </select>
                                        <?php ?>
                                    </div>
                                </div>

                                <div class="form-group row custom_foods <?= (isset($fetched_data[0]['id'])  && $fetched_data[0]['product_type'] == 'custom_foods') ? '' : 'd-none' ?>">
                                    <label for="product_ids" class="control-label col">Foods <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-md-12">
                                        <select name="product_ids[]" class="search_product w-100" multiple data-placeholder=" Type to search and select products" onload="multiselect()">
                                            <?php
                                            if (isset($fetched_data[0]['id'])) {
                                                $product_id = explode(",", $fetched_data[0]['product_ids']);

                                                foreach ($product_details as $row) {
                                            ?>
                                                    <option value="<?= $row['id'] ?>" selected><?= $row['name'] ?></option>
                                            <?php
                                                }
                                            }

                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-info" id="submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Fetured Section' : 'Add Fetured Section' ?></button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <!--/.card-->
            </div>
            <div class="modal fade edit-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Edit Fetured Section Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 main-content">
                <div class="card content-area p-4">
                    <div class="card-head">
                        <h4 class="card-title">Featured Section</h4>
                    </div>
                    <div class="card-innr">
                        <div class="gaps-1-5x"></div>
                        <table class='table-striped' data-toggle="table" data-url="<?= base_url('admin/Featured_sections/get_section_list') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-query-params="queryParams">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="title" data-sortable="true">Title</th>
                                    <th data-field="short_description" data-sortable="false">Short description</th>
                                    <th data-field="branch" data-sortable="false">Branch</th>
                                    <th data-field="categories" data-sortable="true">Categories</th>
                                    <th data-field="product_ids" data-sortable="true">Product ids</th>
                                    <th data-field="product_type" data-sortable="true">Product Type</th>
                                    <th data-field="date_added" data-sortable="true">Date</th>
                                    <th data-field="operate" data-sortable="false">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div><!-- .card-innr -->
                </div><!-- .card -->
            </div>
        </div>
        <!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>