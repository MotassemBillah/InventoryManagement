<?php
$this->breadcrumbs = array(
    'Sales' => array(AppUrl::URL_SALE),
    'Cart'
);
?>
<div class="row content-panel">
    <div class="clearfix">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <form action="" method="post" id="saveCart">
                <div class="clearfix">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="existing_customer"><?php echo Yii::t("strings", "Customer Type"); ?>:</label>&nbsp;
                            <label class="txt_np" for="existing_customer"><input type="radio" id="existing_customer" class="customer_toggle" name="customer_type" value="exist" data-target="#exist_customer_form">&nbsp;Existing</label>
                            <label class="txt_np" for="new_customer"><input type="radio" id="new_customer" class="customer_toggle" name="customer_type" value="new" data-target="#new_customer_form">&nbsp;New</label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="search" id="customer" name="customer" class="form-control" placeholder="search customer">
                                <span class="input-group-addon" id="customerSearch" style="padding: 5px 10px;">
                                    <button class="btn btn-info btn-xs" type="button">Search</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="col-md-4 col-sm-4" id="exist_customer_form" style="">
                        <div class="form-group">
                            <label for="customer_id">Customer :</label>
                            <?php
                            $customerList = Customer::model()->getList();
                            $custList = CHtml::listData($customerList, 'id', 'name');
                            echo CHtml::dropDownList('customer_id', '', $custList, array('empty' => 'Select', 'class' => 'form-control', 'style' => 'display:inline-block;width:260px'));
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <div class="form-group">
                            <input type="text" class="form-control" name="invoice_no" placeholder="Invoice Number">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <div class="form-group">
                            <label for="transport">
                                <input type="checkbox" id="transport" name="transport" value="1" class="chk_no_mvam">&nbsp;
                                <?php echo Yii::t("strings", "Has Transport"); ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 customer_div" id="new_customer_form" style="display: none;">
                    <div class="row clearfix">
                        <div class="col-md-3 col-sm-4">
                            <div class="form-group">
                                <label for="Customer_name" class="required">Name <span class="required">*</span></label>
                                <input class="form-control" name="Customer[name]" id="Customer_name" type="text">
                            </div>
                            <div class="form-group">
                                <label for="Customer_phone" class="required">Phone <span class="required">*</span></label>
                                <input class="form-control" name="Customer[phone]" id="Customer_phone" type="text">
                            </div>
                        </div>
                        <div class="col-md-9 col-sm-8">
                            <div class="form-group">
                                <label for="Customer_address">Address</label>
                                <textarea class="form-control" name="Customer[address]" id="Customer_address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 clearfix">
                    <div class="table-responsive">
                        <table class="table table-bordered tbl_invoice_view">
                            <tr>
                                <th class="text-center"><?php echo Yii::t("strings", "SL#"); ?></th>
                                <th><?php echo Yii::t("strings", "Description Of Item"); ?></th>
                                <th class="text-center"><?php echo Yii::t("strings", "Quantity"); ?></th>
                                <th class="text-right"><?php echo Yii::t("strings", "Price"); ?></th>
                                <th class="text-right"><?php echo Yii::t("strings", "Total Amount"); ?></th>
                            </tr>
                            <?php
                            $counter = 0;
                            foreach ($dataset as $data):
                                $counter++;
                                ?>
                                <tr id="sale_view_row_<?php echo $data->id; ?>">
                                    <td class="text-center"><?php echo $counter; ?></td>
                                    <td>
                                        <input type="hidden" name="products[<?php echo $data->product_id; ?>]" value="<?php echo $data->product_id; ?>">
                                        <?php echo AppObject::productName($data->product_id) . " ( " . Yii::t("strings", AppObject::categoryName($data->product->category_id)) . " )"; ?>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" name="quantity[<?php echo $data->product_id; ?>]" value="<?php echo $data->qty; ?>">
                                        <?php echo $data->qty; ?>
                                    </td>
                                    <td class="text-right">
                                        <input type="hidden" name="prices[<?php echo $data->product_id; ?>]" value="<?php echo $data->price; ?>">
                                        <?php echo AppHelper::getFloat($data->price); ?>
                                    </td>
                                    <td class="text-right"><?php echo AppHelper::getFloat($data->qty * $data->price); ?></td>
                                </tr>
                                <?php
                                $_rate[] = $data->price;
                                $_qty[] = $data->qty;
                                $_trq[] = $data->qty * $data->price;
                            endforeach;
                            $_totalRate = array_sum($_rate);
                            $_totalQty = array_sum($_qty);
                            $_totalAmount = array_sum($_trq);
                            ?>
                            <tr>
                                <th colspan="2" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                                <th class="text-center"><?php echo $_totalQty; ?></th>
                                <th class="text-right"><?php echo AppHelper::getFloat($_totalRate); ?></th>
                                <th class="text-right"><?php echo AppHelper::getFloat($_totalAmount); ?></th>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" name="create_invoice" id="create_invoice" value="Create Invoice">
                    <a class="btn btn-warning" href="<?php echo Yii::app()->createUrl('/sales/empty_cart'); ?>"><?php echo Yii::t("strings", "Empty Cart"); ?></a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info">No records found! <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_SALE_CREATE); ?>" class="btn btn-link"><u><?php echo Yii::t("strings", "Create Cart"); ?></u></a></div>
                    <?php endif; ?>
    </div>
</div>