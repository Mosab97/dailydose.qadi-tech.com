<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid p-3">
            <div class="row">

                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning rounded-circle home_cardss">
                            <i class="ion-ios-cart-outline display-4"></i>
                        </span>
                        <div class="info-box-content">
                            <h3><?= number_format($order_counter) ?></h3>
                            <p>Orders</p>
                            <a href="<?= base_url('admin/orders/') ?>"
                                class="text-info">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary rounded-circle home_cardss">
                            <i class="ion-ios-personadd-outline display-4"></i></span>
                        <div class="info-box-content">
                            <h3><?= number_format($user_counter) ?></h3>
                            <p>New Signups</p>
                            <a href="<?= base_url('admin/customer/') ?>" class="text-info">More info
                                <i class="fas fa-arrow-circle-right"></i></a>
                        </div>

                    </div>
                </div>
                <!-- <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-success rounded-circle home_cardss">
                            <i class="ion-ios-people-outline display-4"></i></span>
                        <div class="info-box-content">
                            <h3><?= number_format($rider_counter) ?></h3>
                            <p>Riders</p>
                            <a href="<?= base_url('admin/riders/manage-rider') ?>"
                                class="text-info">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>

                    </div>
                </div> -->
                <!-- <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger rounded-circle home_cardss">
                            <i class="fas fa-hamburger"></i>
                        </span>
                        <div class="info-box-content">
                            <h3><?= number_format($branch_counter) ?></h3>
                            <p>Branches</p>
                            <a href="<?= base_url('admin/branch/manage-branch') ?>"
                                class="text-info">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div> -->

                <div class="col-xl-6 col-12" id="ecommerceChartView">
                    <div class="card card-shadow chart-height">
                        <div class="m-3">Sales Analytics</div>
                        <div class="card-header card-header-transparent py-20 border-0">
                            <ul class="nav nav-pills nav-pills-rounded chart-action float-right btn-group" role="group">
                                <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                        href="#scoreLineToDay">Day</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab"
                                        href="#scoreLineToWeek">Week</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab"
                                        href="#scoreLineToMonth">Month</a></li>
                            </ul>
                        </div>
                        <div class="widget-content tab-content bg-white p-20">
                            <div class="ct-chart tab-pane active scoreLineShadow" id="scoreLineToDay"></div>
                            <div class="ct-chart tab-pane scoreLineShadow" id="scoreLineToWeek"></div>
                            <div class="ct-chart tab-pane scoreLineShadow" id="scoreLineToMonth"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-12">
                    <!-- Category Wise Product's Sales -->
                    <div class="card ">
                        <h3 class="card-title m-3">Category Wise Product's Count</h3>
                        <div class="card-body">
                            <div id="piechart_3d" class='piechat_height'></div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-md-6 col-xs-12">
                    <div class="alert alert-dismissible products_stock_background">
                        <button type="button" class="close product_close_button" data-dismiss="alert"
                            aria-hidden="true">×</button>
                        <h6 class="main_color"><i class="icon fa fa-info"></i>
                            <?= $count_products_availability_status ?> Product(s) sold out!</h6>
                        <a href="<?= base_url('admin/product/?flag=sold') ?>"
                            class="text-decoration-none small-box-footer">More info <i
                                class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <?php $settings = get_settings('system_settings', true); ?>
                <div class="col-md-6 col-xs-12">
                    <div class="alert alert-dismissible products_stock_background">
                        <button type="button" class="close product_close_button" data-dismiss="alert"
                            aria-hidden="true">×</button>
                        <h6 class="main_color"><i class="icon fa fa-info"></i> <?= $count_products_low_status ?>
                            Product(s) low in stock!<small> (Low stock limit
                                <?= isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : '5' ?>)</small>
                        </h6>
                        <a href="<?= base_url('admin/product/?flag=low') ?>"
                            class="text-decoration-none small-box-footer">More info <i
                                class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- branch wise earning & overall earning -->
                <?php if ($is_super_admin[0]['role'] == '0') { ?>
                    <div class="col-md-4 col-xs-4 mb-4">
                        <div class="earnings-card">
                            <div class="header">
                                <h5>Overall Earnings</h5>
                            </div>
                            <div class="content">

                                <span class="amount text-dark"><?= $curreny . number_format($total_earnings, 2) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php $branch_name = fetch_details(['id' => $_SESSION['branch_id']], 'branch', 'branch_name') ?>
                    <div class="col-md-4 col-xs-4 mb-4">
                        <div class="earnings-card">
                            <div class="header">
                                <h5><?= strtoupper(stripslashes($branch_name[0]['branch_name'])) ?> Branch Earnings</h5>
                            </div>
                            <div class="content">
                                <span class="amount text-dark"><?= $curreny . number_format($branch_total_earnings, 2) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xs-4 mb-4">
                        <div class="earnings-card">
                            <div class="header">
                                <h5>Top Earning Branch</h5>
                            </div>
                            <div class="content">
                                <span class="amount text-dark"> <?= isset($top_earning_branch) ? strtoupper(stripslashes($top_earning_branch)) : "-" ?></span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <!-- end -->
                <h5 class="col">Order Outlines</h5>
                <div class="row col-12 d-flex">
                    <!-- awaiting -->
                    <div class="col-sm-3">
                        <div class="small-box">
                            <div class="inner">
                                <h3><?= $status_counts['awaiting'] ?></h3>
                                <p>Awaiting</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fas fa-clock home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                    <!-- end -->
                    <div class="col-sm-3">
                        <div class="small-box">
                            <div class="inner">
                                <h3><?= $status_counts['pending'] ?></h3>
                                <p>Pending</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-history home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="small-box">
                            <div class="inner">
                                <h3><?= $status_counts['confirmed'] ?></h3>
                                <p>Confirmed</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-level-down-alt home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="small-box">
                            <div class="inner">
                                <h3><?= $status_counts['preparing'] ?></h3>
                                <p>Preparing</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fas fa-concierge-bell home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ready for pickup -->
                    <div class="col-sm-3">
                        <div class="small-box">
                            <div class="inner">
                                <h3><?= $status_counts['ready_for_pickup'] ?></h3>
                                <p>Ready For Pickup</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-people-carry home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                    <!-- end -->
                    <div class="col-sm-3">
                        <div class="small-box">
                            <div class="inner">
                                <h3><?= $status_counts['out_for_delivery'] ?></h3>
                                <p>Out For Delivery</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-shipping-fast home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="small-box">
                            <div class="inner">
                                <h3><?= $status_counts['delivered'] ?></h3>
                                <p>Delivered</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-user-check home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                    <!-- draft -->
                    <div class="col-sm-3">
                        <div class="small-box">
                            <div class="inner">
                                <h3><?= $status_counts['draft'] ?></h3>
                                <p>Draft</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fas fa-layer-group home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                    <!-- end -->
                    <div class="col-sm-3">
                        <div class="small-box ">
                            <div class="inner">
                                <h3><?= $status_counts['cancelled'] ?></h3>
                                <p>Cancelled</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-times-circle home_page_order_icones"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- latest orders -->
                <div class="col-md-6 main-content">
                    <div class="row">
                        <div class="card col-md-12">
                            <div class="card-innr">


                                <label class="mt-3 ml-3 mb-2">Latest Orders</label>
                                <table class='table-hover no-border' data-toggle="table"
                                    data-url="<?= base_url('admin/orders/view_latest_orders') ?>"
                                    data-click-to-select="true" data-side-pagination="server" data-pagination="false"
                                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false"
                                    data-show-footer="false" data-show-columns="false" data-show-refresh="false"
                                    data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                                    data-mobile-responsive="true" data-toolbar="" data-show-export="false"
                                    data-maintain-selected="true" data-export-types='["txt","excel","csv"]'
                                    data-export-options='{
                                           "fileName": "order-list"}' data-query-params="home_query_params">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable='false'>Order ID</th>
                                            <th data-field="qty" data-sortable='false' data-visible="false">Qty</th>
                                            <th data-field="name" data-sortable='false'>User Name</th>
                                            <th data-field="mobile" data-sortable='false' data-visible="false">Mobile
                                            </th>
                                            <th data-field="final_total" data-sortable='false'>Final
                                                Total(<?= $curreny ?>)</th>
                                            <th data-field="payment_method" data-sortable='false' data-visible="true">
                                                Payment Method</th>
                                            <th data-field="active_status" data-sortable='false' data-visible='true'>
                                                Status</th>
                                            <th data-field="operate" data-sortable='false'></th>
                                        </tr>
                                    </thead>
                                </table>

                            </div><!-- .card-innr -->
                        </div><!-- .card -->
                    </div>
                    <div class="row">
                        <div class="card col-md-12">
                            <div class="card-innr">
                                <label class="mt-3 ml-3 mb-2">Top Selling Foods</label>
                                <div class="row mb-2">
                                    <?php


                                    if (!empty($top_foods)) {
                                        foreach ($top_foods as $product) { ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="panel panel-default food-card">
                                                    <div class="panel-body top-food-card">
                                                        <!-- Top section with background color and image -->
                                                        <div class="food-cards-top">
                                                            <div class="image-container food-card-image">
                                                                <a href="<?php echo base_url() . $product['image']; ?>" data-toggle="lightbox"
                                                                    data-gallery="gallery">
                                                                    <img src="<?php echo base_url() . $product['image']; ?>" alt="Food Image"
                                                                        class="img-responsive img-responsive-home center-block">
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <!-- Middle dividing line -->
                                                        <div class="food-card-middel"></div>

                                                        <!-- Bottom section with product details -->
                                                        <div class="text-center food-card-buttom">
                                                            <h5 class="product-name">
                                                                <?php echo htmlspecialchars($product['name']); ?>
                                                            </h5>
                                                            <p class="text-success food-card-total-sale">
                                                                <strong>Total Sales:</strong> <?php echo htmlspecialchars($product['total_sales']); ?>
                                                            </p>
                                                            <a href="<?= 'product/view-product?edit_id=' . $product['id'] ?>" class="product-view btn btn-info btn-sm">View Details</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    <?php } else { ?>
                                        <div class="col-md-12 mb-3 text-center">
                                            <p>Currently no data available</p>
                                        </div>
                                    <?php  } ?>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>


                <div class="col-md-6 main-content">
                    <div class="card content-area">
                        <div class="card-innr">

                            <label class="mt-3 ml-3 mb-2">Top Customers</label>
                            <table class='table-hover no-border' data-toggle="table"
                                data-url="<?= base_url('admin/customer/top_customers') ?>" data-side-pagination="server"
                                data-click-to-select="true" data-pagination="false" data-id-field="id"
                                data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false" data-show-columns="false"
                                data-show-refresh="false" data-trim-on-search="false" data-sort-name="id"
                                data-sort-order="desc" data-mobile-responsive="true" data-toolbar=""
                                data-show-export="false" data-maintain-selected="true"
                                data-export-types='["txt","excel"]' data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="name" data-sortable="false">Name</th>
                                        <th data-field="profile" data-sortable="false">Profile</th>
                                        <th data-field="total_orders" data-sortable="false">Total Oredrs</th>
                                        <th data-field="balance" data-sortable="false">Balance</th>
                                        <th data-field="mobile" data-sortable="false">Mobile No</th>
                                        <th data-field="email" data-sortable="false">Email</th>
                                    </tr>
                                </thead>
                            </table>

                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div>
            </div>
        </div>

    </section>
</div>