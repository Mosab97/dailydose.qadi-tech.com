<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <?php if (isset($rider_request) && ($rider_request == 1)) { ?>
                        <h4>Accept / Reject Rider Request</h4>
                    <?php } else { ?>
                        <h4>Add Rider</h4>

                    <?php }  ?>
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

                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/riders/add_rider'); ?>" method="POST" id="add_product_form">
                            <?php if (isset($fetched_data[0]['id'])) { ?>
                            <?php
                            }
                            if (isset($rider_request) && ($rider_request == 1)) { ?>
                                <input type="hidden" name="edit_rider_request" value="<?= $fetched_data[0]['id'] ?>">

                                <?php } else {
                                if (isset($fetched_data[0]['id'])) { ?>
                                    <input type="hidden" name="edit_rider" value="<?= $fetched_data[0]['id'] ?>">

                            <?php }
                            } ?>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Name <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" placeholder="Rider Name" name="name" value="<?= @$fetched_data[0]['username'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-2 col-form-label">Mobile <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <?php if (isset($rider_request) && ($rider_request == 1)) { ?>
                                            <input type="text" readonly id="numberInput" oninput="validateNumberInput(this)" class="form-control" id="mobile" placeholder="Enter Mobile" name="mobile" value="<?= @$fetched_data[0]['mobile'] ?>" min="0">
                                        <?php } else { ?>
                                            <input type="text" id="numberInput" oninput="validateNumberInput(this)" class="form-control" id="mobile" placeholder="Enter Mobile" name="mobile" value="<?= @$fetched_data[0]['mobile'] ?>" min="0">
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <?php if (isset($rider_request) && ($rider_request == 1)) { ?>
                                            <input type="email" readonly class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= @$fetched_data[0]['email'] ?>">
                                        <?php } else { ?>
                                            <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= @$fetched_data[0]['email'] ?>">
                                        <?php } ?>

                                    </div>
                                </div>
                                <?php
                                if (!isset($fetched_data[0]['id'])) {
                                ?>
                                    <div class="form-group row ">
                                        <label for="password" class="col-sm-2 col-form-label">Password <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="password" placeholder="Enter Passsword" name="password" value="<?= @$fetched_data[0]['password'] ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label for="confirm_password" class="col-sm-2 col-form-label">Confirm Password <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="confirm_password" placeholder="Enter Confirm Password" name="confirm_password">
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Address <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="address" placeholder="Enter Address" name="address" value="<?= @$fetched_data[0]['address'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="commission_method" class="col-sm-4 col-form-label">Commission Methods <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-12">
                                        <select class='form-control' name="commission_method" id="commission_method">
                                            <option value=''>Select Method</option>
                                            <option value='percentage_on_delivery_charges' <?= (isset($fetched_data[0]['commission_method']) && $fetched_data[0]['commission_method'] == 'percentage_on_delivery_charges') ? 'selected' : '' ?>>Percentage on Delivery Charges</option>
                                            <option value='fixed_commission_per_order' <?= (isset($fetched_data[0]['commission_method']) && $fetched_data[0]['commission_method'] == 'fixed_commission_per_order') ? 'selected' : '' ?>>Fixed Commission per Order</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group d-none row" id="percentage_on_delivery_charges_input">
                                    <label for="percentage">Percentage on Delivery Charges(%) <span class='text-danger text-sm'>*</span></label>
                                    <input type="number" class="form-control" name="percentage" id="percentage" value="<?= @$fetched_data[0]['commission'] ?>" placeholder="Percentage on Delivery Charges applied on perticular order" min="0" step="0.000000000001">
                                </div>
                                <!-- only for percentage on delivery method -->
                                <div class="form-group d-none" id="max_commission_per_order_input">
                                    <label for="max_commission">Max Commission Per Order <span class='text-danger text-sm'>*</span> </label>
                                    <input type="number" class="form-control" oninput="validateNumberInput(this)" name="max_commission" id="max_commission" value="<?= @$fetched_data[0]['max_commission'] ?>" placeholder="Max Commission per Order" min="0">
                                </div>
                                <div class="form-group d-none" id="fixed_commission_per_order_input">
                                    <label for="commission">Fixed Commission per Order(<?= $currency ?>) <span class='text-danger text-sm'>*</span> </label>
                                    <input type="number" class="form-control" oninput="validateNumberInput(this)" name="commission" id="commission" value="<?= @$fetched_data[0]['commission'] ?>" placeholder="Amount will be transfered to wallet of rider per order" min="0">
                                </div>

                                <?php
                                $city = (isset($fetched_data[0]['serviceable_city']) &&  $fetched_data[0]['serviceable_city'] != NULL) ?  $fetched_data[0]['serviceable_city'] : "";

                                ?>
                                <div class="form-group row">
                                    <label for="cities" class="col-sm-2 col-form-label">Serviceable City <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <select name="serviceable_city[]" id="serviceable_cities" class="serviceable_cities search_city w-100" multiple onload="multiselect()">
                                            <option value="">Select Serviceable City</option>
                                            <?php

                                            $this->db->select('id,name');
                                            $this->db->from('cities');
                                            $this->db->where("FIND_IN_SET(id, '$city')");
                                            $city_name = $this->db->get()->result_array();

                                            foreach ($city_name as $row) {
                                            ?>
                                                <option value=<?= $row['id'] ?> selected> <?= output_escaping($row['name']) ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <?php if (isset($rider_request) && ($rider_request == 1)) { ?>

                                    <!-- choose branch -->
                                    <div class="form-group row">
                                        <label for="cities" class="col-sm-2 col-form-label">Select Branch <span
                                                class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <?php
                                            $branch_selection = (isset($fetched_data[0]['branch_id']) && !empty($fetched_data[0]['branch_id'])) ? 'disabled' : '';
                                            ?>
                                            <select <?= $branch_selection ?> name="branch"
                                                class="search_branch form-control" id="rider_branch">
                                                <option value="">Choose Branch</option>
                                                <?php foreach ($branch as $row) { ?>
                                                    <?php
                                                    $selectedBranches = explode(',', $fetched_data[0]['branch_id']);
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
                                    <!-- end -->


                                <?php } ?>

                                <?php if (isset($fetched_data[0]['id']) && !empty($fetched_data[0]['id'])) { ?>
                                    <!-- <div class="row"> -->
                                    <div class="form-group">
                                        <label class="col-sm-3 col-form-label">Status <span class='text-danger text-sm'>*</span></label>
                                        <div id="active" class="btn-group col-sm-8">
                                            <label class="btn btn-default" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                                <input type="radio" name="active" value="0" <?= (isset($fetched_data[0]['active']) && $fetched_data[0]['active'] == '0') ? 'Checked' : '' ?>> Not-Approved
                                            </label>
                                            <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                                <input type="radio" name="active" value="1" <?= (isset($fetched_data[0]['active']) && $fetched_data[0]['active'] == '1') ? 'Checked' : '' ?>> Approved
                                            </label>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="form-group row">
                                    <label for="profile" class="col-sm-4 col-form-label">Rider Profile</label>
                                    <div class="col-sm-10">
                                        <?php if (isset($fetched_data[0]['image']) && !empty($fetched_data[0]['image'])) { ?>
                                            <span class="text-danger">*Leave blank if there is no change</span>
                                        <?php } ?>
                                        <input type="file" class="form-control" name="profile" id="profile" accept="image/*" />
                                    </div>
                                </div>
                                <?php if (isset($fetched_data[0]['image']) && !empty($fetched_data[0]['image'])) { ?>
                                    <div class="form-group">
                                        <div class="mx-auto product-image"><a
                                                href="<?= base_url($fetched_data[0]['image']); ?>"
                                                data-toggle="lightbox" data-gallery="gallery_restro"><img
                                                    src="<?= base_url($fetched_data[0]['image']); ?>"
                                                    class="img-fluid rounded"></a></div>
                                    </div>
                                <?php } ?>

                                <!-- ------------------ -->
                                <?php if (!isset($fetched_data[0]['id']) && empty($fetched_data[0]['id'])) { ?>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="col-sm-3 col-form-label">Status <span class='text-danger text-sm'>*</span></label>
                                            <div id="active" class="btn-group col-sm-8">
                                                <label class="btn btn-default" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                                    <input type="radio" name="active" value="0" <?= (isset($fetched_data[0]['active']) && $fetched_data[0]['active'] == '0') ? 'Checked' : '' ?>> Not-Approved
                                                </label>
                                                <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                                    <input type="radio" name="active" value="1" <?= (isset($fetched_data[0]['active']) && $fetched_data[0]['active'] == '1') ? 'Checked' : '' ?>> Approved
                                                </label>
                                            </div>
                                        </div>

                                    <?php } ?>


                                    <!-- ------------------ -->

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-6 col-form-label">Can Cancel Order ?</label>
                                        <div id="active" class="btn-group col-sm-8">
                                            <input type="checkbox" name="rider_cancel_order" <?= (isset($fetched_data[0]['rider_cancel_order']) && $fetched_data[0]['rider_cancel_order'] == '1') ? 'Checked' : ''  ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="reset" class="btn btn-warning">Reset</button>
                                        <button type="submit" class="btn btn-info" id="submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Rider' : 'Add Rider' ?></button>
                                    </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
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