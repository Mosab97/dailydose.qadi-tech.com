<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header mt-2">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-8">
                    <h4>Settings</h4>
                </div>
                <div class="col-sm-4 d-flex justify-content-end">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="text text-info"
                                href="<?= base_url('admin/home') ?>"><?= display_breadcrumbs(); ?></a></li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="row col-12 d-flex">

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/setting') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">
                                            <i class="fas fa-store nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>System Setting </b><i class='fas fa-arrow-circle-right main_color'></i></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/setting/system-status') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fas fa-heartbeat nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>
                                        </div>

                                        <div class="d-flex flex-column ">
                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>System Health </b><i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/email-settings') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fas fa-envelope-open-text nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>Email Settings</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/payment-settings') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fas fa-rupee-sign nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>Payment Methods </b><i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/notification-settings') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fa fa-bell nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>Notification Settings</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/contact-us') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fa fa-phone-alt nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>Contact Us</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/about-us') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fas fa-info-circle nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>About Us</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/privacy-policy') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fa fa-user-secret nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>Privacy Policy</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/rider-privacy-policy') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fa fa-exclamation-triangle nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>Rider Policies</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/updater') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fas fa-sync nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>System Updater</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="<?= base_url('admin/purchase-code') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fas fa-check nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>System Registration</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- custom notification -->
                        <div class="col-md-3">
                            <a href="<?= base_url('admin/custom_notification') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fa fa-bell nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>Custom Notification</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- end -->

                        <!-- authetication mode -->
                        <div class="col-md-3">
                            <a href="<?= base_url('admin/authentication-settings') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fa fa-bell nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>Authentication Mode</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- end -->

                        <!-- SMS Gateway -->
                        <div class="col-md-3">
                            <a href="<?= base_url('admin/sms-gateway-settings') ?>">
                                <div class="card border border-secondary-secondary-secondary-secondary mt-4 settings-card hover-card">
                                    <div class="card-body card-hover">
                                        <div class="d-flex flex-column justify-content-center rounded bg-secondary square-icon">

                                            <i class="fa fa-sms nav-icon link-color fa-lg d-flex justify-content-center text-white"></i>

                                        </div>

                                        <div class="d-flex flex-column ">

                                            <span class="fw-semibold d-block col-md-12 h7 mt-4 main_color"><b>SMS Gateway Settings</b> <i class='fas fa-arrow-circle-right'></i></span>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- end -->

                    </div>

                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>