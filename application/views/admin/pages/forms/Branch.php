<?php $system_settings = get_settings("system_settings", true);
// $javascript_map_key = $system_settings['google_map_javascript_api_key'];
// $map_url = "https://maps.googleapis.com/maps/api/js?key=$javascript_map_key&libraries=drawing,places&v=weekly"
?>
<script async defer src="<?= $map_url ?>" >
</script>
<!-- <script>
    $(document).ready(function() {
        initMap();
    });
</script> -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Add Branch</h4>
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
                        <form class="form-horizontal form-submit-event"
                            action="<?= base_url('admin/branch/add_branch'); ?>" method="POST">

                            <div class="card-body">
                                <?php if (isset($fetched_details[0]['id']) && !empty($fetched_details[0]['id'])) { ?>
                                    <input type="hidden" name="edit_branch" value="<?= $fetched_details[0]['id'] ?>">
                                <?php } ?>
                                <!-- <div class="row">
                                    <div class="col-md-4 map-div">
                                        <label for="city_name">Search Location</label>
                                        <input id="city-input" type="text" class="form-control"
                                            placeholder="Enter a location" />
                                        </br>
                                        <span class="text text-primary">Search your Branch location.</span>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="map"></div>
                                        <div id="infowindow-content">
                                            <span id="place-name" class="title"></span><br />
                                            <span id="place-address"></span>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- hidden fields for long lat -->

                                <!-- end -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="latitude">Latitude <span
                                                    class='text-danger text-sm'>*</span></label>
                                            <input type="number" min="0" step="0.000000000000000001" readonly
                                                class="form-control" name="latitude_show" id="city_lat"
                                                value="<?= (isset($fetched_details[0]['latitude']) ? $fetched_details[0]['latitude'] : '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="longitude">Longitude <span
                                                    class='text-danger text-sm'>*</span></label>
                                            <input type="number" min="0" step="0.000000000000000001" readonly
                                                class="form-control" name="longitude_show" id="city_long"
                                                value="<?= (isset($fetched_details[0]['longitude']) ? $fetched_details[0]['longitude'] : '') ?>">
                                        </div>
                                    </div>
                                    <input type="hidden" min="0" step="0.000000000000000001" readonly
                                        class="form-control" name="latitude" id="city_lat"
                                        value="<?= (isset($fetched_details[0]['latitude']) ? $fetched_details[0]['latitude'] : '') ?>">

                                    <input type="hidden" min="0" step="0.000000000000000001" readonly
                                        class="form-control" name="longitude" id="city_long"
                                        value="<?= (isset($fetched_details[0]['longitude']) ? $fetched_details[0]['longitude'] : '') ?>">
                                </div>
                                <!-- othert branch details  -->
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="">Branch Name <span class='text-danger text-sm'>*</span></label>
                                        <input type="text" class="form-control" name="branch_name"
                                            value="<?= @$fetched_details[0]['branch_name'] ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Description</label>
                                        <textarea type="text" class="form-control"
                                            name="description"><?= @$fetched_details[0]['description'] ?></textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Address <span class='text-danger text-sm'>*</span></label>
                                        <textarea type="text" class="form-control" name="address"
                                            min="0"><?= @$fetched_details[0]['address'] ?></textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Email <span class='text-danger text-sm'>*</span></label>
                                        <input type="text" class="form-control" name="email"
                                            value="<?= @$fetched_details[0]['email'] ?>" min="0">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Contact <span class='text-danger text-sm'>*</span></label>
                                        <input type="text" id="numberInput" oninput="validateNumberInput(this)"
                                            class="form-control" name="contact" id="contact"
                                            value="<?= @$fetched_details[0]['contact'] ?>" min="0">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Status <span class='text-danger text-sm'>*</span></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Select</option>
                                            <option value="1" <?= (isset($fetched_details[0]['status']) && $fetched_details[0]['status'] == '1') ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= (isset($fetched_details[0]['status']) && $fetched_details[0]['status'] == '0') ? 'selected' : '' ?>>Deactive
                                            </option>
                                        </select>
                                    </div>
                                    <?php
                                    $city = (isset($fetched_details[0]['city_id']) && $fetched_details[0]['city_id'] != NULL) ? $fetched_details[0]['city_id'] : "";
                                    ?>
                                    <div class="form-group col-md-6">
                                        <label for="cities" class="col-sm-2 col-form-label">City <span
                                                class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <select name="city" class="search_city w-100" id="deliverable_zipcodes">
                                                <option value="">Select City</option>
                                                <?php
                                                $city_name = fetch_details("", 'cities', 'name,id', "", "", "", "", "id", $city);
                                                foreach ($city_name as $row) {
                                                ?>
                                                    <option value=<?= $row['id'] ?> <?= ($row['id'] == $city) ? 'selected' : ''; ?>> <?= $row['name'] ?></option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <h4 class="h4 col-md-12">Set Default</h4>
                                        <div class="form-group col-md-4 col-sm-3">
                                            <label for="default_mode"> Enable / Disable</label>
                                            <div class="card-body">
                                                <input type="checkbox" name="default_mode"
                                                    <?= (isset($fetched_details[0]['default_branch']) && $fetched_details[0]['default_branch'] == '1') ? 'Checked' : '' ?>
                                                    data-bootstrap-switch data-off-color="danger"
                                                    data-on-color="success">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <h4 class="h4 col-md-12">Self Pickup</h4>
                                        <div class="form-group col-md-4 col-sm-3">
                                            <label for="self_pickup"> Enable / Disable</label>
                                            <div class="card-body">
                                                <input type="checkbox" name="self_pickup"
                                                    <?= (isset($fetched_details[0]['self_pickup']) && $fetched_details[0]['self_pickup'] == '1') ? 'Checked' : '' ?>
                                                    data-bootstrap-switch data-off-color="danger"
                                                    data-on-color="success">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <h4 class="h4 col-md-12">Deliver Orders</h4>
                                        <div class="form-group col-md-4 col-sm-3">
                                            <label for="delivery_orders"> Enable / Disable</label>
                                            <div class="card-body">
                                                <input type="checkbox" name="deliver_orders"
                                                    <?= (isset($fetched_details[0]['deliver_orders']) && $fetched_details[0]['deliver_orders'] == '1') ? 'Checked' : '' ?>
                                                    data-bootstrap-switch data-off-color="danger"
                                                    data-on-color="success">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <h4 class="h4 col-md-12">Global Branch Time</h4>
                                        <div class="form-group col-md-4 col-sm-3">
                                            <label for="global_branch_time"> Enable / Disable</label>
                                            <div class="card-body">
                                                <input type="checkbox" name="global_branch_time"
                                                    <?= (isset($fetched_details[0]['global_branch_time']) && $fetched_details[0]['global_branch_time'] == '1') ? 'Checked' : '' ?>
                                                    data-bootstrap-switch data-off-color="danger"
                                                    data-on-color="success">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- branch timing -->
                                    <input type="hidden" name="working_time" id="working_time" value="">

                                    <div class="col-md-6">
                                        <!-- Your PHP code for working hours -->
                                        <?php
                                        $branch_id = (isset($fetched_details[0]['id']) && !empty($fetched_details[0]['id'])) ? $fetched_details[0]['id'] : "";
                                        $timing = get_working_hour_html($branch_id);
                                        ?>
                                        <div class="form-group">
                                            <label for="address" class="col-sm-4 col-form-label">Working Days <span
                                                    class='text-danger text-sm'>*</span></label>
                                            <div id="hourForm" class="ml-3">
                                                <?= $timing ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="image">Image <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <div class='col-md-3'><a
                                                    class="uploadFile img btn btn-info text-white btn-sm"
                                                    data-input='branch_image' data-isremovable='0'
                                                    data-is-multiple-uploads-allowed='0' data-toggle="modal"
                                                    data-target="#media-upload-modal" value="Upload Photo"><i
                                                        class='fa fa-upload'></i> Upload</a></div>
                                            <?php
                                            if (file_exists(FCPATH . @$fetched_details[0]['image']) && !empty(@$fetched_details[0]['image'])) {
                                            ?>
                                                <label class="text-danger mt-3">*Only Choose When Update is
                                                    necessary</label>
                                                <div class="container-fluid row image-upload-section">
                                                    <div
                                                        class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                        <div class='image-upload-div'><img class="img-fluid mb-2"
                                                                src="<?= BASE_URL() . $fetched_details[0]['image'] ?>"
                                                                alt="Image Not Found"></div>
                                                        <input type="hidden" name="branch_image"
                                                            value='<?= $fetched_details[0]['image'] ?>'>
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


                                </div>
                                <hr>
                                <!-- <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-info submit_branch"
                                        id="submit_btn"><?= (isset($fetched_details[0]['id'])) ? 'Update Branch' : 'Add Branch' ?></button>
                                </div> -->
                            </div>

                    </div>

                    </form>
                </div>
                <!--/.card-->
            </div>

        </div>
        <!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>