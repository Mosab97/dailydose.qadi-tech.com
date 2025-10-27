<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>eRestro Purchase Code Validator</h4>
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
    <section class=" content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <?php $doctor_brown = get_settings('doctor_brown', true);
                        if (empty($doctor_brown) && !isset($doctor_brown['code_bravo'])) { ?>
                            <form class="form-horizontal form-submit-event"
                                action="<?= base_url('admin/purchase-code/validator'); ?>" method="POST"
                                enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="form-group row"> <label for="purchase_code"
                                            class="col-sm-2 col-form-label">eRestro Purchase Code<span
                                                class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="purchase_code"
                                                placeholder="Enter your purchase code here" name="purchase_code" value="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="reset" class="btn btn-warning">Reset</button>
                                        <button type="submit" class="btn btn-info" id="submit_btn">
                                            <?= (isset($fetched_data[0]['id'])) ? 'Register' : 'Register Now' ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php } ?>
                        <?php $doctor_brown = get_settings('doctor_brown', true);
                        if (!empty($doctor_brown) && isset($doctor_brown['code_bravo'])) { ?>
                            <div class="row">
                                <div class="col-md-6 mt-2 pl-5">

                                    <div class="alert alert-success">
                                        Your system is successfully registered with us! Enjoy selling online!
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary" name="erestro_deregister"
                                            id="erestro_deregister"
                                            value="<?= $doctor_brown['code_bravo']; ?>">De-register</button>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
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