<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Combo Product</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="text text-info" href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Combo Product</li>
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
                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/combo_product/add_combo'); ?>" method="POST">

                            <div class="card-body">
                                <?php
                                if (isset($fetched_details[0]['id']) && !empty($fetched_details[0]['id'])) {
                                ?>
                                    <input type="hidden" name="edit_combo_product" value="<?= $fetched_details[0]['id'] ?>">
                                <?php
                                }
                                ?>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="">Title <span class='text-danger text-sm'>*</span></label>
                                        <input type="text" class="form-control" name="title" value="<?= @$fetched_details[0]['title'] ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="image">Main Image <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <div class='col-md-5'><a class="uploadFile img btn btn-info text-white btn-sm" data-input='image' data-isremovable='0' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a></div>
                                            <?php
                                            if (file_exists(FCPATH . @$fetched_details[0]['image']) && !empty(@$fetched_details[0]['image'])) {
                                            ?>
                                                <label class="text-danger mt-3">*Only Choose When Update is necessary</label>
                                                <div class="container-fluid row image-upload-section">
                                                    <div class="col-md-12 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                        <div class='image-upload-div'><img class="img-fluid mb-2" src="<?= BASE_URL() . $fetched_details[0]['image'] ?>" alt="Image Not Found"></div>
                                                        <input type="hidden" name="image" value='<?= $fetched_details[0]['image'] ?>'>
                                                    </div>
                                                </div>
                                            <?php
                                            } else { ?>
                                                <div class="container-fluid row image-upload-section">
                                                    <div class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none"></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="">Group Name<span class='text-danger text-sm'>*</span></label>
                                        <input type="text" class="form-control" name="group_name" value="<?= @$fetched_details[0]['group_name'] ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Items <span class='text-danger text-sm'>*</span></label>
                                        <select name="items" id="items" class="form-control">
                                            <option value="">Select</option>
                                            <option value="1" <?= (isset($fetched_details[0]['items']) && $fetched_details[0]['items'] == '1') ? 'selected' : '' ?>>Single Item</option>
                                            <option value="0" <?= (isset($fetched_details[0]['items']) && $fetched_details[0]['items'] == '0') ? 'selected' : '' ?>>Multiple Item</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Allowed Selection <span class='text-danger text-sm'>*</span></label>
                                        <input type="text" class="form-control" name="allowed_selection" id="allowed_selection" value="<?= @$fetched_details[0]['allowed_selection'] ?>" min="0">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Products<span class='text-danger text-sm'>*</span></label>
                                        <select name="discount_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="percentage" <?= (isset($fetched_details[0]['discount_type']) && $fetched_details[0]['discount_type'] == 'percentage') ? 'selected' : '' ?>>Percentage</option>
                                            <option value="amount" <?= (isset($fetched_details[0]['discount_type']) && $fetched_details[0]['discount_type'] == 'amount') ? 'selected' : '' ?>>Amount</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="">Price<span class='text-danger text-sm'>*</span></label>
                                        <input type="number" class="form-control" name="price" id="price" value="<?= @$fetched_details[0]['price'] ?>" min="0">
                                    </div>

                                    <?php
                                    
                                    ?>
                                    <!-- select multiple branch -->
                                    <div class="form-group col-md-6">
                                        <label for="cities">Select Branch <span class='text-danger text-sm'>*</span></label>

                                       
                                        <?php
                                        
                                        $branch_selection = (isset($fetched_details[0]['branch_id']) && !empty($fetched_details[0]['branch_id'])) ? 'disabled' : '';
                                        ?>
                                        <select <?= $branch_selection ?> name="branch[]" class="search_branch w-100" multiple onload="multiselect()">
                                            <option value="">Select Branch for Product</option>
                                            <?php foreach ($branch as $row) { ?>
                                                <?php
                                                $selectedBranches = explode(',', $fetched_details[0]['branch_id']);
                                                if (in_array($row['id'], $selectedBranches)) {
                                                ?>
                                                    <option value="<?= $row['id'] ?>" selected><?= output_escaping($row['branch_name']) ?></option>
                                                <?php } else { ?>
                                                    <option value="<?= $row['id'] ?>"><?= output_escaping($row['branch_name']) ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">Status <span class='text-danger text-sm'>*</span></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Select</option>
                                            <option value="1" <?= (isset($fetched_details[0]['status']) && $fetched_details[0]['status'] == '1') ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= (isset($fetched_details[0]['status']) && $fetched_details[0]['status'] == '0') ? 'selected' : '' ?>>Deactive</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6 <?= (isset($fetched_details[0]['repeat_usage']) && $fetched_details[0]['repeat_usage'] == '1') ? '' : 'd-none' ?>" id="repeat_usage_html">
                                        <label for=""> No of repeat usage </label>
                                        <input type="number" class="form-control" name="no_of_repeat_usage" id="no_of_repeat_usage" value="<?= @$fetched_details[0]['no_of_repeat_usage'] ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-info" id="submit_btn"><?= (isset($fetched_details[0]['id'])) ? 'Update Combo Product' : 'Add Combo Product' ?></button>
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