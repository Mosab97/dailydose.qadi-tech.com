<!-- Adjusted HTML and CSS for the Login Page -->
<div class="login-box container-fluid">
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row">
            <!-- Left Text - Image -->
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 login-image-container">
                <div class="d-flex h-100 admin_login_image">
                    <img src="<?= BASE_URL() . $cover_image ?>" class="img-fluid" alt="Login image">
                    <div class="dark-overlay"></div>
                </div>
            </div>
            <!-- Login Form -->
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg login-background-color">
                <div class="w-px-400 mx-auto">
                    <?php if (ALLOW_MODIFICATION == 0) { ?>
                        <div class="alert alert-warning">
                            Note: If you cannot login here, please close the codecanyon frame by clicking on x Remove Frame button from top right corner on the page or <a href="<?= base_url('/admin') ?>" target="_blank" class="text-danger"> >> Click here << </a>
                        </div>
                    <?php } ?>
                    <div class="login-logo">
                        <a href="<?= base_url() . 'admin/login' ?>"><img src="<?= base_url() . $logo ?>"></a>
                    </div>
                    <h4>
                        <p class="login-box-msg">Sign in to start your session</p>
                    </h4>
                    <form action="<?= base_url('auth/login') ?>" class='form-submit-event' method="post">
                        <input type='hidden' name='<?= $this->security->get_csrf_token_name() ?>' value='<?= $this->security->get_csrf_hash() ?>'>
                        <div class="input-group mb-3">
                            <input type="<?= $identity_column ?>" id="numberInput" oninput="validateNumberInput(this)" class="form-control login_creds_mobile" name="identity" placeholder="<?= ucfirst($identity_column) ?>" <?= (ALLOW_MODIFICATION == 0) ? 'value="9876543210"' : ""; ?>>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas <?= ($identity_column == 'email') ? 'fa-envelope' : 'fa-mobile' ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" id="loginPassword" class="form-control login_creds_password" name="password" placeholder="Password" <?= (ALLOW_MODIFICATION == 0) ? 'value="12345678"' : ""; ?>>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="password-toggle"><i id="passwordVisible" class="far fa-eye"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-info">
                                    <input type="checkbox" name="remember" id="remember">
                                    <label for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" id="submit_btn" class="btn btn-block main_color_background">Sign In</button>
                            </div>
                        </div><br><br>
                         <div class="row">
                             <?php if (ALLOW_MODIFICATION == 0) { ?>

                                <div class="col-md-6" onclick="copyBranchCreds()">
                                    <div class="card border border-info mt-4 settings-card bg-info hover-card">
                                        <div class="card-body card-hover">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold h7 mt-2">
                                                    <b class="text-warning">Branch Login</b><br>
                                                    <small class="mb-0">Mobile : 8548548548</small>
                                                    <small class="mb-0">Password : 12345678</small>
                                                </span>
                                                <div class="bg-dark rounded-circle p-3 hover-copy-icon">
                                                    <i class="fa fa-copy text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" onclick="copyAdminCreds()">
                                    <div class="card border border-info mt-4 settings-card bg-info hover-card">
                                        <div class="card-body card-hover">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold h7 mt-2">
                                                    <b class="text-warning">Super Admin Login</b><br>
                                                    <small class="mb-0">Mobile : 9876543210</small>
                                                    <small class="mb-0">Password : 12345678</small>
                                                </span>
                                                <div class="bg-dark rounded-circle p-3 hover-copy-icon">
                                                    <i class="fa fa-copy text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                             <?php } ?>
                         </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .login-box {
        max-width: 100%;
    }
    .authentication-inner {
        flex-direction: column-reverse;
    }
    .authentication-cover .login-background-color {
        background-color: #ffffff;
    }
    .authentication-bg {
        background-color: #ffffff;
    }
    @media (min-width: 768px) {
        .authentication-inner {
            flex-direction: row;
        }
        .d-none.d-lg-flex {
            display: flex !important;
        }
    }
    @media (max-width: 767px) {
        .authentication-inner {
            margin-top: 20px;
        }
        .d-none.d-lg-flex {
            display: none !important;
        }
        .login-box {
            padding: 10px;
        }
    }
</style>
