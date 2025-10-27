<div class="login-box container-fluid">
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row">
            <!-- Left Text - Image -->
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 rider_login_background">
                <div class="d-flex h-100 rider_login_image">
                    <img src="<?= BASE_URL() . $rider_cover_image ?>" class="img-fluid" alt="Login image" data-app-dark-img="illustrations/boy-with-rocket-dark.png" data-app-light-img="illustrations/boy-with-rocket-light.png">

                    <div class="dark-overlay"></div>

                </div>
            </div>
            <!-- Login Form -->
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg login-background-color">
                <div class="w-px-400 mx-auto">
                    <!-- ... Rest of the login form ... -->
                    <div class="w-px-400 mx-auto">
                        <?php if (ALLOW_MODIFICATION == 0) { ?>
                            <div class="alert alert-warning">
                                Note: If you cannot login here, please close the codecanyon frame by clicking on x Remove Frame button from top right corner on the page or <a href="<?= base_url('/admin') ?>" target="_blank" class="text-danger"> >> Click here << </a>
                            </div>
                        <?php } ?>

                        <div class="login-logo">
                            <a href="<?= base_url() . 'rider/login' ?>"><img src="<?= base_url() . $rider_logo ?>"></a>


                        </div>


                        <h4>
                            <p class="login-box-msg">Sign in to start your session</p>
                        </h4>

                        <form action="<?= base_url('rider/login/auth') ?>" class='form-submit-event' method="post">
                            <input type='hidden' name='<?= $this->security->get_csrf_token_name() ?>' value='<?= $this->security->get_csrf_hash() ?>'>
                            <div class="input-group mb-3">
                                <input type="<?= $identity_column ?>" id="numberInput" oninput="validateNumberInput(this)" class="form-control" name="identity" placeholder="<?= ucfirst($identity_column)  ?>" <?= (ALLOW_MODIFICATION == 0) ? 'value="9987654321"' : 'value="9987654321"'; ?>>

                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas <?= ($identity_column == 'email') ? 'fa-envelope' : 'fa-mobile' ?> "></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">

                                <input type="password" id="loginPassword" class="form-control" name="password" placeholder="Password" <?= (ALLOW_MODIFICATION == 0) ? 'value="12345678"' : 'value="12345678"'; ?>>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="password-toggle"><i id="passwordVisible" class="far fa-eye"></i></span>
                                        <!-- <span class="fas fa-lock"></span> -->
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

                            </div>
                        </form><br>

                        <div>
                            <a href="<?= base_url('rider/auth/sign_up') ?>" class="text text-danger font-weight-bold">Don't have any account?</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>