<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Time Slots</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="text text-info"
                                href="<?= base_url('admin/home') ?>"><?= display_breadcrumbs(); ?></a></li>
                        <!-- <li class="breadcrumb-item active">Orders</li> -->
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/time_slots/add_time_slots'); ?>" method="POST">
                            <div class="card-body">
                                <?php if (isset($fetched_data[0]['id'])) { ?>
                                    <input type="hidden" name="edit_tag" value="<?= @$fetched_data[0]['id'] ?>">
                                <?php } ?>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <div class="card-body">
                                            <label for="is_time_slot_enable"> Enable Time Slot </label>
                                            <input type="checkbox" name="is_time_slot_enable" <?= (isset($fetched_data[0]['is_time_slot_enable']) && $fetched_data[0]['is_time_slot_enable'] == '1') ? 'Checked' : ''  ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="time_slots_type">Days <span class='text-danger text-sm'>*</span> </label>
                                        <select name="time_slots_type" id="time_slots_type" class="form-control type_event_trigger" required="">
                                            <option value=" ">Select Days</option>
                                            <option value="1" <?= (@$fetched_data[0]['time_slots_type'] == "1") ? 'selected' : ' ' ?>>Till Today</option>
                                            <option value="2" <?= (@$fetched_data[0]['time_slots_type'] == "2") ? 'selected' : ' ' ?>>Till Tomorrow</option>
                                            <option value="3" <?= (@$fetched_data[0]['time_slots_type'] == "3") ? 'selected' : ' ' ?>>Till Three Days</option>
                                        </select><br><br>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="card-body">
                                            <label for="time_slot_intervals"> Time Slot Intervals (in minutes)</label>
                                            <input type="text" oninput="validateNumberInput(this)"
                                                class="form-control" name="time_slot_intervals" id="time_slot_intervals"
                                                value="<?= @$fetched_details[0]['time_slot_intervals'] ?>" min="0">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-info" id="submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Slots' : 'Add Slots' ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>