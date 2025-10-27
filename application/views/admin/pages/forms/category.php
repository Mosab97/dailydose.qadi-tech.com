<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><?= (isset($fetched_data[0]['id'])) ? "Update" : "Add" ?> Category</h4>
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
                        <form class="form-horizontal form-submit-event" id="add_product_form"
                            action="<?= base_url('admin/category/add_category'); ?>" method="POST"
                            enctype="multipart/form-data">
                            <?php if (isset($fetched_data[0]['id'])) { ?>
                                <input type="hidden" name="edit_category" value="<?= @$fetched_data[0]['id'] ?>">
                            <?php } ?>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="category_input_name" class="col-sm-2 col-form-label">Name <span
                                            class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="category_input_name"
                                            placeholder="Category Name" name="category_input_name"
                                            value="<?= isset($fetched_data[0]['name']) ? output_escaping($fetched_data[0]['name']) : "" ?>">
                                    </div>
                                </div>
                                <?php if (!isset($fetched_data[0]['id']) && empty($fetched_data[0]['id'])) { ?>
                                    <?php if (isset($permissions_message)) { ?>
                                        <span class='text-danger text-sm'>(<?= $permissions_message ?>)</span><br>
                                    <?php } ?>
                                    <div class="form-group row">
                                        <label for="cities" class="col-sm-2 col-form-label">Select Branch <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <?php

                                            $branch_selection = (isset($fetched_data[0]['branch_id']) && !empty($fetched_data[0]['branch_id'])) ? 'disabled' : '';
                                            ?>
                                            <select <?= $branch_selection ?> name="branch[]"
                                                class="search_branch form-control" multiple onload="multiselect()">
                                                <option value="">Select Branch for Product</option>
                                                <?php foreach ($branch as $row) { ?>
                                                    <?php
                                                    $selectedBranches = isset($fetched_data[0]['branch_id']) ? explode(',', $fetched_data[0]['branch_id']) : [];
                                                    if (in_array($row['id'], $selectedBranches)) {
                                                    ?>
                                                        <option value="<?= $row['id'] ?>" selected>
                                                            <?= output_escaping($row['branch_name']) ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?= $row['id'] ?>">
                                                            <?= output_escaping($row['branch_name']) ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="image">Main Image <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class='col-md-3'><a class="uploadFile img btn btn-info text-white btn-sm"
                                                data-input='category_input_image' data-isremovable='0'
                                                data-is-multiple-uploads-allowed='0' data-toggle="modal"
                                                data-target="#media-upload-modal" value="Upload Photo"><i
                                                    class='fa fa-upload'></i> Upload</a></div>
                                        <?php
                                        if (file_exists(FCPATH . @$fetched_data[0]['image']) && !empty(@$fetched_data[0]['image'])) {
                                        ?>
                                            <label class="text-danger mt-3">*Only Choose When Update is necessary</label>
                                            <div class="container-fluid row image-upload-section">
                                                <div
                                                    class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                    <div class='image-upload-div'><img class="img-fluid mb-2"
                                                            src="<?= BASE_URL() . $fetched_data[0]['image'] ?>"
                                                            alt="Image Not Found"></div>
                                                    <input type="hidden" name="category_input_image"
                                                        value='<?= $fetched_data[0]['image'] ?>'>
                                                </div>
                                            </div>
                                        <?php
                                        } else { ?>
                                            <div class="container-fluid row image-upload-section">
                                                <div
                                                    class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-info category-submit"
                                        id="submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Category' : 'Add Category' ?></button>
                                </div>
                            </div>
                    </div>
                    <!-- /.card-footer -->
                    </form>
                </div>
                <!--/.card-->
            </div>
            <!--/.col-md-12-->
        </div>
        <!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>