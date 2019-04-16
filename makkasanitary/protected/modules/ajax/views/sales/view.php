<div class="row content-panel">
    <div class="col-md-12">
        <?php
        if ($hasContent == 1):
            $invoiceAmount = AppHelper::getFloat($model->sumPrice);
            //$vat = AppObject::vatCount($this->settings->vat, $invoiceAmount);
            $otherCost = AppHelper::getFloat($payment->transport + $payment->labor);
            $invoicePaid = AppHelper::getFloat($payment->invoice_paid);
            $invoiceDue = AppHelper::getFloat($payment->balance_amount);

            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'frmSaleReturn',
                'enableClientValidation' => true,
                'clientOptions' => array('validateOnSubmit' => true),
                'htmlOptions' => array('class' => 'ps_form')
            ));
            ?>
            <input type="hidden" name="customerID" value="<?php echo $model->customer->id; ?>">
            <input type="hidden" name="saleID" value="<?php echo $model->id; ?>">
            <input type="hidden" name="saleInvoiceNO" value="<?php echo $model->invoice_no; ?>">
            <div class="row clearfix">
                <div class="col-sm-3 form-group">
                    <strong><?php echo Yii::t("strings", "Customer Order : " . sprintf("00000%d", $model->id)); ?></strong><br>
                    <?php if (!empty($model->customer->name)): ?>
                        <span><?php echo $model->customer->name; ?></span><br>
                    <?php else: ?>
                        <strong><?php echo $model->customer->company; ?></strong><br/>
                    <?php endif; ?>
                    <?php if (!empty($model->customer->email)): ?>
                        <span><?php echo "E-mail : " . $model->customer->email; ?></span><br>
                    <?php endif; ?>
                    <?php if (!empty($model->customer->phone)): ?>
                        <span><?php echo "Phone : " . $model->customer->phone; ?></span><br>
                    <?php endif; ?>
                    <?php if (!empty($model->customer->address)): ?>
                        <span><?php echo "Address : " . $model->customer->address; ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-sm-3 form-group">
                    <strong><u><?php echo Yii::t("strings", "Invoice No"); ?></u> : </strong><?php echo $model->invoice_no; ?><br/>
                </div>
                <div class="col-sm-3 form-group text-center">
                    <strong><u><?php echo Yii::t("strings", "Prepared By"); ?></u> : </strong><?php echo AppObject::displayNameByUser($model->created_by); ?><br/>
                </div>
                <div class="col-sm-3 form-group text-right">
                    <strong><u><?php echo Yii::t("strings", "Date"); ?></u> : </strong><?php echo date('j M Y', strtotime($model->created)); ?><br/>
                </div>
            </div>
            <hr style="margin: 0 0 15px 0;">
            <div class="clearfix">
                <p>
                    <strong style="text-decoration: underline;"><?php echo Yii::t("strings", "Payment Status"); ?>&nbsp;:</strong>&nbsp;
                    <?php
                    if ($payment->payment_mode == "Cheque Payment") {
                        echo "<br>" . $payment->payment_mode . "<br>";
                        echo "<u>Bank</u> : " . $payment->bank_name . "<br>";
                        echo "<u>Check No</u> : " . $payment->check_no;
                    } else {
                        echo $payment->payment_mode . "<br>";
                    }
                    ?>
                    <span style="margin-right: 15px;"><u><?php echo Yii::t("strings", "Bill Amount"); ?></u>&nbsp;&nbsp;&nbsp;: <?php echo $invoiceAmount; ?></span>
                    <span style="margin-right: 15px;"><u><?php echo Yii::t("strings", "Paid Amount"); ?></u>&nbsp;: <?php echo $invoicePaid; ?></span>
                    <span style="margin-right: 15px;"><u><?php echo Yii::t("strings", "Due Amount"); ?></u>&nbsp;&nbsp;: <?php echo $invoiceDue; ?></span>
                </p>
            </div>
            <hr style="margin: 0 0 15px 0;">
            <div class="table-responsive">
                <table class="table table-bordered tbl_invoice_view">
                    <tr>
                        <th>
                            <?php echo Yii::t("strings", "Description Of Item"); ?>&nbsp;
                            <span>
                                <b>[</b><u>Category</u> - <u>Head</u> - <u>Color</u> - <u>Grade</u><b>]</b>
                            </span>
                        </th>
                        <th class="text-center" style="width: 16%"><?php echo Yii::t("strings", "Quantity"); ?></th>
                        <th class="text-center" style="width: 16%"><?php echo Yii::t("strings", "Price"); ?></th>
                    </tr>
                    <?php
                    foreach ($model->items as $item):
                        $_productName = AppObject::productName($item->product_id);
                        $catName = "<u>" . AppObject::categoryName($item->product->category_id) . "</u>";
                        $typeName = !empty($item->product->type) ? " - <u>" . AppObject::companyHeadName($item->product->type) . "</u>" : " - <u>n/a</u>";
                        $colorName = !empty($item->product->color) ? " - <u>" . strtolower($item->product->color) . "</u>" : " - <u>n/a</u>";
                        $gradeName = !empty($item->product->grade) ? " - <u>" . strtoupper($item->product->grade) . "</u>" : " - <u>n/a</u>";
                        ?>
                        <tr id="sale_view_row_<?php echo $item->id; ?>">
                            <td>
                                <label class="txt_np" for="product_<?php echo $item->product_id; ?>" data-info='<?php echo $_productName; ?>'>
                                    <input type="checkbox" id="product_<?php echo $item->product_id; ?>" name="products[<?php echo $item->product_id; ?>]" value="<?php echo $item->product_id; ?>">
                                    <?php echo $_productName; ?>&nbsp;
                                    <span>
                                        <b>[</b><?php echo $catName . $typeName . $colorName . $gradeName; ?><b>]</b>
                                    </span>
                                </label>
                            </td>
                            <td class="text-center" style="width: 16%">
                                <input type="number" class="form-control" name="quantity[<?php echo $item->product_id; ?>]" placeholder="quantity" min="0" value="<?php //echo $item->quantity;  ?>">
                            </td>
                            <td class="text-center" style="width: 16%">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="prices[<?php echo $item->product_id; ?>]" placeholder="price" min="0" step="any" value="<?php //echo AppHelper::getFloat($item->price);  ?>">
                                    <span class="input-group-addon">Tk</span>
                                </div>
                            </td>
                        </tr>
                        <?php
                        $sum[] = $item->price;
                        $total = array_sum($sum);
                    endforeach;
                    ?>
                    <tr>
                        <th colspan="2"><?php echo Yii::t("strings", "Total"); ?></th>
                        <th><?php echo $total; ?></th>
                    </tr>
                </table>
            </div>
            <div class="col-md-12 col-sm-12 form-group text-center">
                <?php echo CHtml::resetButton('Reset', array('class' => 'btn btn-info')); ?>
                <button type="button" class="btn btn-primary" name="btnSaleReturn" id="btnSaleReturn"><?php echo Yii::t("strings", "Save"); ?></button>
            </div>
            <?php $this->endWidget(); ?>
        <?php else: ?>
            <div class="alert alert-info">No record found!</div>
        <?php endif; ?>
    </div>
</div>