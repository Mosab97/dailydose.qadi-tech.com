<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Order</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                       <?= display_breadcrumbs(); ?>

                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="modal fade" id="add-ons-model" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">View Product Item Add Ons</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Id</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Add On</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $final_price_add_ons = 0;
                                        $i = 1;
                                        foreach ($items as $row) {
                                            if (isset($row['add_ons']) && !empty($row['add_ons']) && $row['add_ons'] != "" && $row['add_ons'] != "[]") {
                                                $add_ons = json_decode($row['add_ons'], true);
                                                foreach ($add_ons as $row1) {
                                                    $final_price_add_ons += intval($row1['qty']) * intval($row1['price']);
                                        ?>
                                                    <tr>
                                                        <th><?= $i ?></th>
                                                        <td><?= $row['pname'] ?></td>
                                                        <td><?= $row1['title'] ?></td>
                                                        <td><?= $row1['qty'] ?></td>
                                                        <td><?= intval($row1['price']) ?></td>
                                                        <td><?= intval($row1['qty']) * intval($row1['price']) ?></td>
                                                    </tr>
                                        <?php
                                                    $i++;
                                                }
                                            }
                                        } ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <!-- The time line -->
                    <section class="time-line-box text-center">
                        <div class="swiper-wrapper col-12">
                            <?php
                            $status = json_decode($order_detls[0]['status']);
                            $status_wise_class = [
                                'awaiting' => ['fa fa-clock-o'],
                                'pending' => ['fa fa-xs fa-history', 'bg-secondary'],
                                'confirmed' => ['fa fa-xs fa-level-down-alt', 'bg-indigo'],
                                'preparing' => ['fa fa-xs fa-people-carry ', 'bg-navy'],
                                'ready_for_pickup' => ['fa fa-xs fa-shipping-fast ', 'bg-yellow'],
                                'out_for_delivery' => ['fa fa-xs fa-shipping-fast ', 'bg-yellow'],
                                'delivered' => ['fa fa-xs fa-user-check ', 'bg-success'],
                                'cancelled' => ['fa fa-xs fa-times-circle ', 'bg-red'],
                            ];
                            foreach ($status as $row) {
                            ?>
                                <div class="swiper-slide">
                                    <div class="max-auto col-md-6 offset-md-3">
                                        <div class="<?= $status_wise_class[$row[0]][1] ?> pt-2 pb-2 rounded"> <span class="fa-lg"><i class="<?= $status_wise_class[$row[0]][0] ?>"></i></span></div>
                                    </div>
                                    <div class="timestamp m-1"><small class="date"><i class="fas fa-clock"></i>&nbsp;<?= strtoupper($row[1]) ?> </small> </div>
                                    <div class="status text-bold"><span> <?= strtoupper($row[0]) ?> </span></div>
                                </div>
                            <?php } ?>

                        </div>
                    </section>
                </div>
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-body">
                            <div class="card card-widget widget-user-2">
                                <div class="widget-user-header bg-navy">
                                    <h5 class="text-left"> Order Items</h5>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <p class="h5">Branch Name: <span class="text text-primary"><?= output_escaping($branch_data[0]['branch_name']); ?> </span></p>
                                    <p class="h6">Branch Address: <span class="text text-primary"><?= $branch_data[0]['address']; ?> </span></p>
                                    <?php
                                    if (isset($order_detls[0]['notes']) && !empty($order_detls[0]['notes'])) {
                                    ?>
                                        <p class="h6">Order Note: <span class="text text-info"><?= $order_detls[0]['notes']; ?> </span></p>

                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <a class="btn btn-danger" target="_BLANK" data-options="{&quot;iframe&quot; : {&quot;css&quot; : {&quot;width&quot; : &quot;80%&quot;, &quot;height&quot; : &quot;80%&quot;}}}" href="https://www.google.com/maps/search/?api=1&amp;query=<?= $branch_data[0]['latitude']; ?>,<?= $branch_data[0]['longitude']; ?>&hl=es;z=14&amp;output=embed">
                                        <i class="fas fa-map-marked-alt" aria-hidden="true"></i> Locate Branch</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="card-header">
                                    <h6 class="mb-0 text-left"><small><a href='javascript:void(0)' data-toggle='modal' id="view_add_on" data-target='#add-ons-model' class='btn btn-info' title='View Add Ons'>Add Ons</a></small></h6>
                                </div>
                            </div>
                            <?php $total = 0;
                            $tax_amount = 0;
                            foreach ($items as $item) {
                                $item['discounted_price'] = ($item['discounted_price'] == '') ? 0 : $item['discounted_price'];
                                $total += $subtotal = ($item['quantity'] != 0 && ($item['discounted_price'] != '' && $item['discounted_price'] > 0) && $item['price'] > $item['discounted_price']) ? ($item['price'] - $item['discounted_price']) : ($item['price'] * $item['quantity']);
                                $tax_amount += floatval($item['tax_amount']);
                                $total += $subtotal = $tax_amount;
                            ?>
                                <div class="row">
                                    <div class="col">
                                        <div class="card card-2">
                                            <div class="card-body">
                                                <div class="media">
                                                    <div class="sq align-self-center ">
                                                        <a href='<?= base_url() . $item['product_image'] ?>' data-toggle='lightbox' data-gallery='order-images' class="order-product-image mx-2">
                                                            <img src='<?= base_url() . $item['product_image'] ?>' class="img-fluid" />
                                                        </a>
                                                    </div>
                                                    <div class="media-body my-auto text-right">
                                                        <div class="row my-auto flex-column flex-md-row">
                                                            <div class="col my-auto mx-2">
                                                                <h6 class="mb-0 text-left"><?= (strlen($item['pname']) > 25) ? substr($item['pname'], 0, 25) . "..." : $item['pname'] ?></h6>
                                                                <?php if (isset($item['product_variants']) && !empty($item['product_variants'])) { ?>
                                                                    <h6 class="mb-0 text-left"><small><?= str_replace(',', ' | ', $item['product_variants'][0]['variant_values']) ?></small></h6>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="col-auto my-auto">
                                                                <div class="price mb-2 list-view-price">
                                                                    Price: <?= $settings['currency'] . number_format(floatval($item['price']) + floatval($item['tax_amount'])) ?>
                                                                    <?php if (isset($item['discounted_price']) && !empty($item['discounted_price'])) { ?>
                                                                        <span class="striped-price"><?= $settings['currency'] . number_format($item['discounted_price']) ?>
                                                                        </span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <div class="col my-auto"> Variant ID : <?= $item['product_variant_id'] ?> </div>
                                                            <div class="col my-auto"> Qty : <?= $item['quantity'] ?></div>
                                                            <div class="col my-auto"> Type: <?= ucwords(str_replace('_', ' ', $item['product_type'])); ?> </div>
                                                            <div class="col my-auto">
                                                                <h6 class="mb-0"><?= $settings['currency'] . number_format($item['price'] * $item['quantity']) ?></h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-info">
                                        <div class="card-header bg-navy border-0 h5">Customer Details</div>
                                        <div class="card-body">
                                            <div class="card card-widget widget-user-2">
                                                <div class="widget-user-header bg-info">
                                                    <input type="hidden" name="hidden" id="order_id" value="<?php echo $order_detls[0]['id']; ?>">

                                                    <div class="widget-user-image">
                                                        <img class="img-circle elevation-2" src="<?= base_url(AVTAR_IMAGE) ?>" alt="User Avatar">
                                                    </div>
                                                    <h5 class="widget-user-desc"><?= $order_detls[0]['uname']; ?></h5>
                                                    <h6 class="widget-user-desc"><?= $order_detls[0]['address']; ?></h6>
                                                </div>
                                                <div class="card-footer p-0">
                                                    <ul class="nav flex-column">
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Contact <span class="float-right text-primary"><?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['mobile']) - 3) . substr($order_detls[0]['mobile'], -3) : $order_detls[0]['mobile']; ?></span></a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Email <span class="float-right text-primary"><?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email']; ?></span></a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">User Wallet Balance(<?= $settings['currency'] ?>)<span class="float-right text-primary"><?= $order_detls[0]['user_balance']; ?></span>
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Order Date<span class="float-right text-primary"> <?= date('d-M-Y, g:i A - D', strtotime($order_detls[0]['date_added'])); ?></span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <a class="btn btn-danger" data-options="{&quot;iframe&quot; : {&quot;css&quot; : {&quot;width&quot; : &quot;80%&quot;, &quot;height&quot; : &quot;80%&quot;}}}" href="https://www.google.com/maps/search/?api=1&amp;query=<?= $order_detls[0]['user_lat']; ?>,<?= $order_detls[0]['user_lng']; ?>&hl=es;z=14&amp;output=embed"><i class="fas fa-map-marked-alt" aria-hidden="true"></i> Locate Customer</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-info">
                                        <div class="card-body">
                                            <div class="card card-widget widget-user-2">
                                                <div class="widget-user-header bg-navy">
                                                    <h5 class="text-center">Payment Details</h5>
                                                </div>
                                                <div class="card-footer p-0">
                                                    <ul class="nav flex-column">
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Payment Method<span class="float-right text-primary"><?= $order_detls[0]['payment_method']; ?></span></a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Total(<?= $settings['currency'] ?>)<span class="float-right text-primary" id='amount'>
                                                                    <?= '+ ' . number_format($order_detls[0]['order_total']);
                                                                    $total = $order_detls[0]['order_total']; ?></span>
                                                            </a>
                                                        </li>
                                                        <!-- tax amount -->
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Tax Amount<span class="float-right text-primary"><?= $order_detls[0]['tax_amount']; ?></span></a>
                                                        </li>
                                                        <!-- end -->
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Delivery Charge(<?= $settings['currency'] ?>)<span class="float-right text-primary"><?php echo '+ ' . $order_detls[0]['delivery_charge'];
                                                                                                                                                                                                                $total = $total + $order_detls[0]['delivery_charge']; ?></span></a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Delivery Tip(<?= $settings['currency'] ?>)<span class="float-right text-primary"><?php echo '+ ' . $order_detls[0]['delivery_tip'];
                                                                                                                                                                                                            $total = $total + $order_detls[0]['delivery_tip']; ?></span></a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Wallet Balance(<?= $settings['currency'] ?>) <span class="float-right text-primary"><?php echo  '- ' . $order_detls[0]['wallet_balance'];
                                                                                                                                                                                                                $total = $total - $order_detls[0]['wallet_balance'];  ?></span></a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link payment-details">Promo Code Discount (<?= $settings['currency'] ?>)<span class="float-right text-primary">
                                                                    <?php echo '- ' . $order_detls[0]['promo_discount'];
                                                                    $total = floatval($total - $order_detls[0]['promo_discount']); ?></span>
                                                            </a>
                                                        </li>
                                                        <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $order_detls[0]['order_total'] + $order_detls[0]['delivery_charge'] ?>">
                                                        <input type="hidden" name="final_amount" id="final_amount" value="<?php echo $order_detls[0]['final_total']; ?>">
                                                        <input type="hidden" id="final_total" name="final_total" value="<?= $total; ?>">
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0)" class="nav-link bg-info">
                                                                <?php

                                                                $total_final = $total + floatval($order_detls[0]['tax_amount']);
                                                                ?>
                                                                Payable Total(<?= $settings['currency'] ?>) <span class="float-right"><?= $total_final; ?></span>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.widget-user -->
                                </div>
                            </div>
                            <?php

                            $rider_cancel_status = fetch_details(['id' => $order_detls[0]['rider_id']], 'users', 'rider_cancel_order');

                            ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th class="col-2">Status <span class='text-danger text-sm'>*</span></th>
                                                <td>
                                                    <select name="status" id="status" class="form-control" data-isjson="true" data-orderid="<?= $order_detls[0]['id']; ?>">
                                                        <option value="">Select</option>
                                                        <option value="out_for_delivery" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'out_for_delivery') ? 'selected' : '' ?>>Out For Delivery</option>
                                                        <option value="delivered" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'delivered') ? 'selected'  : '' ?>>Delivered</option>
                                                        <?php if ($rider_cancel_status[0]['rider_cancel_order'] == 1) { ?>
                                                            <option value="cancelled" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'cancelled') ? 'selected'  : '' ?>>Cancel</option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr
                                                    class="reason-to-cancel <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'cancelled') ? '' : "d-none" ?>">
                                                    <th class="col-3">Reason to Cancel <span
                                                            class='text-danger text-sm'>*</span></th>
                                                    <td>
                                                        <input type="text" class="form-control" id="reason" name="reason"
                                                            value="<?= (isset($order_detls[0]['reason']) && !empty($order_detls[0]['reason'])) ? $order_detls[0]['reason'] : "" ?>"
                                                            placeholder="Reason">
                                                        <?php
                                                        $username = "";
                                                        $cancel_by_user_id = (isset($order_detls[0]['cancel_by']) && !empty($order_detls[0]['cancel_by'])) ? $order_detls[0]['cancel_by'] : "0";
                                                        if (isset($cancel_by_user_id) && !empty($cancel_by_user_id)) {
                                                            $username = fetch_details(['id' => $cancel_by_user_id], "users", "username");
                                                        }
                                                        if ($username != "") {
                                                        ?>
                                                            <!-- <label for="cancel_by">Cancel By
                                                                <?php
                                                                //  $username[0]['username'] . "( " . $this->ion_auth->get_users_groups($cancel_by_user_id)->row()->description . " )" 
                                                                 ?></label> -->
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="form-group">
                                                   
                                                        <button type="submit" class="btn btn-info update_status_rider" data-id="<?= $order_detls[0]['order_id']; ?>" data-otp-system='<?= ($order_detls[0]['item_otp'] != 0) ? '1' : '0' ?>' id="submit_btn">Update Order</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
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