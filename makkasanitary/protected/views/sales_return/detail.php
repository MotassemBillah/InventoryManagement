<?php
$this->breadcrumbs = array(
    'Sale Returns' => array(AppUrl::URL_SALERETURN),
    'View'
);
?>
<?php
$balanceAmount = AppObject::customerBalanceAmount($model->customer_id);
$discountAmount = AppObject::getDiscountAmount($model->customer_id);
if ($balanceAmount > 0) {
    $balance = $balanceAmount - $discountAmount;
} elseif ($balanceAmount < 0) {
    $balance = $balanceAmount + $discountAmount;
} else {
    $balance = $balanceAmount;
}
$allInv = AppObject::sumInvoiceAmount($model->customer_id);
$allDue = AppObject::sumDueAmount($model->customer_id);
$allDueAmount = AppHelper::getFloat($allInv + $allDue);
$allInvPaid = AppObject::sumInvoicePaid($model->customer_id);
$allAdvPaid = AppObject::getAdvancePayments($model->customer_id);
$allPaidAmount = AppHelper::getFloat($allInvPaid + $allAdvPaid);
$invAmount = AppHelper::getFloat($model->sumPrice);
$otherCost = AppHelper::getFloat($payment->transport + $payment->labor);
$invPaid = AppHelper::getFloat($payment->invoice_paid);
$curDue = AppHelper::getFloat($payment->due_amount);
$prevDue = AppHelper::getFloat($allDue - $curDue);
$total = AppHelper::getFloat(($invAmount + $otherCost) - $payment->discount_amount);
$gTotal = AppHelper::getFloat($total - $invPaid);
$totalDues = AppHelper::getFloat($gTotal + $prevDue);
?>
<div id="printDiv">
    <div class="row form-group clearfix text-center txt_left_xs mp_center media_print">
        <?php if (!empty($this->settings->title)): ?>
            <h1 style="font-size: 30px;margin: 0;"><?php echo $this->settings->title; ?></h1>
        <?php endif; ?>
        <?php if (!empty($this->settings->author_email)): ?>
            <?php echo "<b>Email</b> : " . $this->settings->author_email; ?><br>
        <?php endif; ?>
        <?php if (!empty($this->settings->author_address)): ?>
            <?php echo "<b>Address</b> : " . $this->settings->author_address; ?><br>
        <?php endif; ?>
        <?php
        if (!empty($this->settings->author_contact)):
            echo "<b>Contacts</b> : " . $this->settings->author_contact . ", ";
        endif;
        if (!empty($this->settings->other_contacts)) {
            $other_contacts = explode(",", $this->settings->other_contacts);
            foreach ($other_contacts as $_k => $_v) {
                trim($other_contacts[$_k]);
                echo $_v . ", ";
            }
        }
        ?>
        <h4 class="text-uppercase"><u><?php echo Yii::t("strings", "Sale Return Invoice"); ?></u></h4>
    </div>
    <div class="row clearfix txt_left_xs media_print">
        <div class="col-sm-3 form-group mpw_33">
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
        <div class="col-sm-3 form-group mpw_22">
            <strong><u><?php echo Yii::t("strings", "Invoice No"); ?></u> : </strong><?php echo $model->return_invoice; ?><br/>
        </div>
        <div class="col-sm-3 form-group text-center mpw_22">
            <strong><u><?php echo Yii::t("strings", "Prepared By"); ?></u> : </strong><?php echo AppObject::displayNameByUser($model->created_by); ?><br/>
        </div>
        <div class="col-sm-3 form-group text-right mpw_22">
            <strong><u><?php echo Yii::t("strings", "Date"); ?></u> : </strong><?php echo date('j M Y', strtotime($model->created)); ?><br/>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered tbl_invoice_view">
            <tr class="bg_gray">
                <th class="text-center"><?php echo Yii::t("strings", "SL#"); ?></th>
                <th>
                    <?php echo Yii::t("strings", "Description Of Item"); ?>&nbsp;
                    <span>
                        <b>[</b><u>Category</u> - <u>Head</u> - <u>Color</u> - <u>Grade</u><b>]</b>
                    </span>
                </th>
                <th class="text-center"><?php echo Yii::t("strings", "Quantity"); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Rate"); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Total Amount"); ?></th>
            </tr>
            <?php
            $counter = 0;
            foreach ($model->items as $item):
                $counter++;
                $catName = "<u>" . AppObject::categoryName($item->product->category_id) . "</u>";
                $typeName = !empty($item->product->type) ? " - <u>" . AppObject::companyHeadName($item->product->type) . "</u>" : " - <u>n/a</u>";
                $colorName = !empty($item->product->color) ? " - <u>" . strtolower($item->product->color) . "</u>" : " - <u>n/a</u>";
                $gradeName = !empty($item->product->grade) ? " - <u>" . strtoupper($item->product->grade) . "</u>" : " - <u>n/a</u>";
                ?>
                <tr id="sale_view_row_<?php echo $item->id; ?>">
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td>
                        <?php echo AppObject::productName($item->product_id); ?>&nbsp;
                        <span>
                            <b>[</b><?php echo $catName . $typeName . $colorName . $gradeName; ?><b>]</b>
                        </span>
                    </td>
                    <td class="text-center"><?php echo $item->quantity; ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($item->price); ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($item->quantity * $item->price); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="bg_gray">
                <th colspan="2" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                <th class="text-center"><?php echo $model->sumQty; ?></th>
                <th class="text-right"><?php //echo AppHelper::getFloat($model->sumPrice);          ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat($model->sumTotal); ?></th>
            </tr>
        </table>
    </div>
    <div class="clearfix sale_info">
        <div class="form-group">
            <strong><u><?php echo Yii::t("strings", "Inword"); ?></u>&nbsp;:</strong>&nbsp;<span class="text-capitalize" style="width: auto !important"><?php echo AppHelper::int_to_words($model->sumTotal) . " Taka Only"; ?></span>
        </div>
    </div>
</div>
<div class="form-group text-center">
    <button type="button" class="btn btn-primary" onclick="printElem('#printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
</div>