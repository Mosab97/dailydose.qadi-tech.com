<?php $settings = get_settings('system_settings', true);
$doctor_brown_for_app = get_settings('doctor_brown');

$authentication_settings = get_settings('authentication_settings');
$sms_gateway_settings = get_settings('sms_gateway_settings');


if ($sms_gateway_settings !== null && is_string($sms_gateway_settings)) {
    $sms_gateway_data = get_settings('sms_gateway_settings');
} else {
    $sms_gateway_data = [];
}



if ($authentication_settings !== null && is_string($authentication_settings)) {
    $authentication = json_decode(get_settings('authentication_settings'), true);
} else {
    $authentication = [];
}
?>
<input type="hidden" id="sms_gateway_data" value='<?= isset($sms_gateway_data) ? json_encode($sms_gateway_data) : '' ?>' />
<aside class="main-sidebar elevation-2 sidebar-dark-info" id="admin-sidebar">
    <!-- Brand Logo -->
    <a href="<?= base_url('admin/home') ?>" class="brand-link">
        <img src="<?= base_url() . get_settings('favicon') ?>" alt="<?= $settings['app_name']; ?>"
            title="<?= $settings['app_name']; ?>" class="brand-image">
        <span class="brand-text font-weight-light small"><?= $settings['app_name']; ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <!-- <div class="ps-2 pe-2">
            <input type="text" class="form-control menuSearch" placeholder="Search Menu...">
        </div> -->

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu"
                data-accordion="false">


                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item has-treeview">
                    <a href="<?= base_url('/admin/home') ?>" class="nav-link">
                        <i class="nav-icon fas fa-th-large text-primary"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <?php if (has_permissions('read', 'orders')) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/orders/') ?>" class="nav-link <?= is_url_active('admin/orders') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-shopping-cart text-warning"></i>
                            <p>
                                Orders
                            </p>
                        </a>
                    </li>
                <?php } ?>

                <?php if (has_permissions('read', 'categories')) { ?>
                    <li class="nav-item has-treeview <?= is_url_active('admin/category') ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= is_url_active('admin/category') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-bullseye text-success"></i>
                            <p>
                                Categories
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?= is_url_active('admin/category') ? 'd-block;' : 'd-none;' ?>">
                            <?php if (has_permissions('read', 'categories')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/category/') ?>" class="nav-link">
                                        <i class="fa fa-bullseye nav-icon"></i>
                                        <p>Categories</p>
                                    </a>
                                </li>
                            <?php } ?>
                            <!-- <?php if (has_permissions('read', 'categories')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/category/bulk-upload') ?>" class="nav-link">
                                        <i class="fas fa-upload nav-icon"></i>
                                        <p>Bulk upload</p>
                                    </a>
                                </li>
                            <?php } ?> -->
                            <?php if (has_permissions('read', 'category_order')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/category/category-order') ?>" class="nav-link">
                                        <i class="fa fa-bars nav-icon"></i>
                                        <p>Category Order</p>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if (has_permissions('read', 'tags')) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/tag/manage-tag') ?>" class="nav-link <?= is_url_active('admin/tag') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tag text-info"></i>
                            <p>
                                Tags
                            </p>
                        </a>
                    </li>
                <?php } ?>

                <?php if (has_permissions('read', 'branch')) { ?>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/branch/manage-branch') ?>" class="nav-link <?= is_url_active('admin/branch') ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-code-branch text-danger"></i>

                            <p>
                                Branch
                            </p>
                        </a>
                    </li>

                <?php } ?>

                <!-- <li class="nav-item">
                    <a href="<?= base_url('admin/time_slots/') ?>" class="nav-link <?= is_url_active('admin/time_slots') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar text-success"></i>
                        <p>
                            Time Slots
                        </p>
                    </a>
                </li> -->

                <?php if (has_permissions('read', 'product') || has_permissions('read', 'attribute') || has_permissions('read', 'attribute_set') || has_permissions('read', 'attribute_value') || has_permissions('read', 'tax') || has_permissions('read', 'product_order')) { ?>
                    <li class="nav-item has-treeview <?= is_url_active('admin/taxes', 'admin/products', 'admin/product', 'admin/product_faqs', 'admin/attributes') ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link menu-open <?= is_url_active('admin/taxes', "admin/products", "admin/product", "admin/product_faqs", "admin/attributes") ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cubes text-primary"></i>
                            <p>
                                Products
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview <?= is_url_active('admin/taxes', 'admin/products', 'admin/product', 'admin/product_faqs', 'admin/attributes') ? 'd-block;' : 'd-none;' ?>">

                            <?php if (has_permissions('read', 'attribute')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/attributes/') ?>" class="nav-link">
                                        <i class="fas fa-sliders-h nav-icon"></i>
                                        <p>Attributes</p>
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if (has_permissions('read', 'tax')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/taxes/manage-taxes') ?>" class="nav-link">
                                        <i class="fas fa-percentage nav-icon"></i>
                                        <p>Tax</p>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (has_permissions('read', 'product')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/product/create-product') ?>" class="nav-link">
                                        <i class="fas fa-plus-square nav-icon"></i>
                                        <p>Add Products</p>
                                    </a>
                                </li>
                            <?php } ?>
                            <!-- <?php if (has_permissions('read', 'product')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/product/bulk-upload') ?>" class="nav-link">
                                        <i class="fas fa-upload nav-icon"></i>
                                        <p>Bulk upload</p>
                                    </a>
                                </li>
                            <?php } ?> -->
                            <?php if (has_permissions('read', 'product')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/product/') ?>" class="nav-link">
                                        <i class="fas fa-boxes nav-icon"></i>
                                        <p>Manage Products</p>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (has_permissions('read', 'product_order')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/product/product-order') ?>" class="nav-link">
                                        <i class="fa fa-bars nav-icon"></i>
                                        <p>Products Order</p>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'media')) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/media/') ?>" class="nav-link <?= is_url_active('admin/media') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-icons text-danger"></i>
                            <p>
                                Media
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'point_of_sale')) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/point_of_sale/') ?>" class="nav-link <?= is_url_active('admin/point_of_sale') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calculator text-info"></i>
                            <p>
                                Point of sale
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'home_slider_images')) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/slider/manage-slider') ?>" class="nav-link <?= is_url_active('admin/slider') ? 'active' : '' ?>">
                            <i class="nav-icon far fa-image text-success"></i>
                            <p>
                                Sliders
                            </p>
                        </a>
                    </li>
                <?php } ?>

                <?php if (has_permissions('read', 'new_offer_images')) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/offer/manage-offer') ?>" class="nav-link <?= is_url_active('admin/offer') ? 'active' : '' ?>">
                            <i class="nav-icon fa fa-gift text-primary"></i>
                            <p>
                                Offers
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'support_tickets')) { ?>
                    <li class="nav-item has-treeview <?= is_url_active('admin/tickets') ? 'menu-open' : '' ?>">

                        <a href="#" class="nav-link <?= is_url_active('admin/tickets') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-ticket-alt text-danger"></i>
                            <p>
                                Support Tickets
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview <?= is_url_active('admin/tickets') ? 'd-block;' : 'd-none;' ?>">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/tickets/ticket-types') ?>" class="nav-link">
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Ticket Types</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/tickets') ?>" class="nav-link">
                                    <i class="fas fa-ticket-alt nav-icon"></i>
                                    <p>Tickets</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'promo_code')) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/promo-code/manage-promo-code') ?>" class="nav-link <?= is_url_active('admin/promo-code') ? 'active' : '' ?>">
                            <i class="nav-icon fa fa-puzzle-piece text-warning"></i>
                            <p>
                                Promo code
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'featured_section')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link menu-open">
                            <i class="nav-icon fas fa-layer-group text-danger"></i>
                            <p>
                                Featured Sections
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/featured-sections/') ?>" class="nav-link">
                                    <i class="fas fa-folder-plus nav-icon"></i>
                                    <p>Manage Sections</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/featured-sections/section-order') ?>" class="nav-link">
                                    <i class="fa fa-bars nav-icon"></i>
                                    <p>Sections Order</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'customers')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa fa-user text-success"></i>
                            <p>
                                Customer
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/customer/') ?>" class="nav-link">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p> View Customers </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/customer/addresses') ?>" class="nav-link">
                                    <i class="far fa-address-book nav-icon"></i>
                                    <p> Addresses </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/transaction/view-transaction') ?>" class="nav-link">
                                    <i class="fas fa-money-bill-wave nav-icon "></i>
                                    <p> Transactions </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/transaction/customer-wallet') ?>" class="nav-link">
                                    <i class="fas fa-wallet nav-icon "></i>
                                    <p>Wallet Transactions</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'rider')) { ?>
                    <li class="nav-item has-treeview <?= is_url_active('admin/riders', 'admin/fund-transfer') ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= is_url_active('admin/riders', 'admin/fund-transfer') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-motorcycle text-info"></i>
                            <p>
                                Riders
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?= is_url_active('admin/riders', 'admin/fund-transfer') ? 'd-block;' : 'd-none;' ?>">
                            <?php if (has_permissions('read', 'rider')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/riders/manage-rider') ?>" class="nav-link ">
                                        <i class="fas fa-motorcycle nav-icon "></i>
                                        <p> Riders </p>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (has_permissions('read', 'rider_requests')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/riders/rider_registration_request') ?>" class="nav-link ">
                                        <i class="fa fa-envelope nav-icon "></i>
                                        <p> Rider Registration Requests </p>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (has_permissions('read', 'fund_transfer')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/fund-transfer/') ?>" class="nav-link">
                                        <i class="fa fa-rupee-sign nav-icon "></i>
                                        <p>Fund Transfer</p>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (has_permissions('read', 'rider')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/riders/manage-cash') ?>" class="nav-link text-sm">
                                        <i class="fas fa-money-bill-alt nav-icon "></i>
                                        <p> Cash Collection </p>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if (has_permissions('read', 'payment_request')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="<?= base_url('admin/payment-request') ?>" class="nav-link <?= is_url_active('admin/payment-request') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-money-bill-wave text-danger"></i>
                            <p>Payment Request</p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'send_notification')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="<?= base_url('admin/Notification-settings/manage-notifications') ?>" class="nav-link <?= is_url_active('admin/Notification-settings') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-paper-plane text-success"></i>
                            <p>
                                Send Notification
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'settings')) { ?>

                    <li class="nav-item has-treeview">
                        <a href="<?= base_url('admin/setting/system-page') ?>" class="nav-link <?= is_url_active('admin/setting', 'admin/email-settings', 'admin/payment-settings', 'admin/notification-settings', 'admin/contact-us', 'admin/about-us', 'admin/privacy-policy', 'admin/rider-privacy-policy', 'admin/updater', 'admin/purchase-code', 'admin/custom_notification', 'admin/authentication-settings', 'admin/sms-gateway-settings') ? 'active' : '' ?>">
                            <i class="nav-icon fa fa-wrench text-primary"></i>
                            <p>
                                System
                            </p>
                        </a>
                    </li>

                <?php } ?>
                <?php if (has_permissions('read', 'web_settings')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa fa-globe-asia text-warning"></i>
                            <p>
                                Web Settings
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/web-setting') ?>" class="nav-link">
                                    <i class="fa fa-laptop nav-icon "></i>
                                    <p>General Settings</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/language') ?>" class="nav-link">
                                    <i class="fa fa-language nav-icon "></i>
                                    <p>Languages</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/web-setting/firebase') ?>" class="nav-link">
                                    <i class="fa fa-fire nav-icon "></i>
                                    <p>Firebase</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'city')) { ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-map-marked-alt text-danger"></i>
                            <p>
                                Location
                                <i class="right fas fa-angle-left "></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if (has_permissions('read', 'city')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/area/manage-cities') ?>" class="nav-link">
                                        <i class="fa fa-location-arrow nav-icon "></i>
                                        <p>City</p>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (has_permissions('read', 'city')) { ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('admin/area/manage-city-outlines') ?>" class="nav-link">
                                        <i class="fas fa-chart-area nav-icon "></i>
                                        <p>Deliverable Area</p>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (has_permissions('read', 'sales_inventory_report')) { ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-pie nav-icon text-primary"></i>
                            <p>Reports
                                <i class="right fas fa-angle-left "></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/sales-inventory') ?>" class="nav-link">
                                    <i class="fa fa-chart-line nav-icon "></i>
                                    <p>Sales And Inventory Reports</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if (has_permissions('read', 'faq')) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/faq/') ?>" class="nav-link">
                            <i class="nav-icon fas fa-question-circle text-warning"></i>
                            <p class="text">FAQ</p>
                        </a>
                    </li>
                    <?php }
                $userData = get_user_permissions($this->session->userdata('user_id'));
                if (!empty($userData)) {
                    if ($userData[0]['role'] == 0 || $userData[0]['role'] == 1) {
                    ?>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/system-users/') ?>" class="nav-link <?= is_url_active('admin/system-users') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-user-tie text-danger"></i>
                                <p class="text">System Users</p>
                            </a>
                        </li>
                    <?php
                    }
                    if ($userData[0]['role'] == 0) { ?>
                        <li class="nav-item mb-4">
                            <a href="<?= base_url('admin/database_bakup/') ?>" class="nav-link">
                                <i class="nav-icon fa fa-database text-success"></i>
                                <p class="text">Database Backup</p>
                            </a>
                        </li>
                        
                <?php  }
                } ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>


    <!-- /.sidebar -->
</aside>