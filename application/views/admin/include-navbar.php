<?php $current_version = get_current_version(); ?>
<nav class="main-header navbar navbar-expand navbar-dark navbar-info">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item my-auto">
            <span class="badge badge-light h5">v
                <?= (isset($current_version) && !empty($current_version)) ? $current_version : '1.0' ?></span>
        </li>
        <li class="nav-item my-auto ml-3">
            <a href="<?= base_url('admin/setting/system-status') ?>"><i
                    class="fas fa-heartbeat fa-lg main_color"></i></a>
        </li>

        <?php
        $users_branch = fetch_details(['user_id' => $_SESSION['user_id']], 'user_permissions', 'branch_ids,role');
        
        if ($users_branch[0]['role'] == '0') {
        ?>
            <ul class="navbar-nav navbar-right">
                <li class="dropdown d-inline">
                    <?php if (isset($_SESSION['branch_id'])) {
                        $selected_branch = fetch_details(['id' => $_SESSION['branch_id']], 'branch', '*');
                    ?>
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user"
                            id="dropdownMenuButton">
                            <?= isset($selected_branch[0]['branch_name']) ? stripslashes($selected_branch[0]['branch_name']) : ''; ?>
                        </a>
                    <?php } else { ?>
                    <?php }
                    $branch = fetch_details(['status' => 1], 'branch', '*');
                    ?>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <div class="dropdown-title text-center"><b>Branches</b></div>
                        <?php foreach ($branch as $key) { ?>
                            <a class="dropdown-item hover-pointer save-branch branch_listss" data-id="<?= $key['id'] ?>">
                                <!-- <img src="<?= isset($key['image']) && !empty($key['image']) ? base_url() . $key['image'] : base_url() . NO_IMAGE ?>"
                                    class="branch_list_images"> -->
                                    <img src="<?= isset($key['image']) && !empty($key['image']) ? base_url() . $key['image'] : base_url() . NO_IMAGE ?>"
     class="branch_list_images">

                                <span class="branch_lists_name">
                                    <?= stripslashes($key['branch_name']) ?>
                                </span>
                            </a>
                        <?php } ?>
                    </div>
                </li>
            </ul>

        <?php

        } else {

            $allowed_branch = explode(",", $users_branch[0]['branch_ids']);
            save_branch($allowed_branch[0]);

        ?>
            <ul class="navbar-nav navbar-right">
                <li class="dropdown d-inline">
                    <?php if (isset($_SESSION['branch_id'])) {
                        $selected_branch = fetch_details(['id' => $_SESSION['branch_id']], 'branch', '*');
                    ?>
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user"
                            id="dropdownMenuButton">
                            <?= isset($selected_branch[0]['branch_name']) ? stripslashes($selected_branch[0]['branch_name']) : ''; ?>
                        </a>
                    <?php } else { ?>

                    <?php }
                    $branch = fetch_details(NULL, 'branch', '*', '', '', '', '', 'id', $allowed_branch);

                    ?>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <div class="dropdown-title text-center"><b>Branches</b></div>
                        <?php foreach ($branch as $key) {
                         
                        ?>
                            <a class="dropdown-item hover-pointer save-branch branch_listss" data-id="<?= $key['id'] ?>">
                                <img src="<?= isset($key['image']) && !empty($key['image']) ? base_url() . $key['image'] : base_url() . NO_IMAGE ?>"
                                    class="branch_list_images">
                                <span class="branch_lists_name">
                                    <?= stripslashes($key['branch_name']) ?>
                                </span>
                            </a>
                        <?php } ?>
                    </div>
                </li>
            </ul>
        <?php
        }
        ?>

        <?php
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
        ?>
            <li class="nav-item my-auto ml-2">
                <span class="badge badge-success">Demo mode</span>
            </li>
        <?php } ?>
    </ul>


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <div id="google_translate_element"></div>

        <li class="nav-item">
            <a class="nav-link" id="panel_dark_mode" data-slide="true" href="#" role="button">
                <i id="dark-mode-icon" class="fas fa-moon main_color "></i>
            </a>
        </li>
        <!-- audio sound start -->
        <audio id="audioplay" class="d-none" controls>
            <source src="<?= base_url('assets/order_notofication.mp3'); ?>" type="audio/mp3">
            Your browser does not support the audio element.
        </audio>
        <!-- end -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-user main_color"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php if ($this->ion_auth->is_admin()) { ?>
                    <a href="<?= base_url('admin/home/profile') ?>" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <a href="<?= base_url('admin/home/logout') ?>" class="dropdown-item">
                        <i class="fa fa-sign-out-alt mr-2"></i> Log Out
                    </a>
                <?php } else { ?>
                    <a href="#" class="dropdown-item">Welcome <b><?= ucfirst($this->ion_auth->user()->row()->username) ?>
                        </b>! </a>
                    <a href="<?= base_url('rider/home/profile') ?>" class="dropdown-item"><i class="fas fa-user mr-2"></i>
                        Profile </a>
                    <a href="<?= base_url('rider/home/logout') ?>" class="dropdown-item "><i
                            class="fa fa-sign-out-alt mr-2"></i> Log Out </a>
                <?php } ?>
            </div>
        </li>
    </ul>
</nav>