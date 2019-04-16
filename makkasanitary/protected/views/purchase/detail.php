<?php
$this->breadcrumbs = array(
    'Purchase' => array(AppUrl::URL_PURCHASE),
    'View'
);
?>
<?php
$vat = !empty($this->settings->vat) ? $this->settings->vat : "";
$vatPrice = AppObject::getVat($vat, $model->sumTotal);
$invAmount = AppHelper::getFloat($model->sumTotal);
$otherCost = AppHelper::getFloat($payment->transport + $payment->labor);
$invPaid = AppHelper::getFloat($payment->invoice_paid);
$curDue = AppHelper::getFloat($payment->due_amount);
if (!empty($this->settings->vat)) {
    $total = AppHelper::getFloat(($invAmount + $otherCost) - $payment->discount_amount) + $vatPrice;
} else {
    $total = AppHelper::getFloat(($invAmount + $otherCost) - $payment->discount_amount);
}
$_sumBalance = AppObject::sumSelfBalanceAmount($model->company_id);
?>
<div id="printDiv">
    <div class="row form-group clearfix text-center txt_left_xs mp_center media_print">
        <?php if (!empty($this->settings->title)): ?>
            <h1 style="font-size: 30px;margin: 0;"><?php echo $this->settings->title; ?></h1>
        <?php endif; ?>
        <?php if (!empty($this->settings->author_address)): ?>
            <?php echo $this->settings->author_address; ?><br>
        <?php endif; ?>
        <?php
        if (!empty($this->settings->other_contacts)) {
            $other_contacts = explode(",", $this->settings->other_contacts);
            $_str = "<b>Cell</b> : ";
            foreach ($other_contacts as $_k => $_v) {
                trim($other_contacts[$_k]);
                $_str .= $_v . " | ";
            }
            echo $_str;
        }
        if (!empty($this->settings->author_mobile)):
            echo $this->settings->author_mobile . " | ";
        endif;
        if (!empty($this->settings->author_phone)):
            echo "<b>Tel</b> : " . $this->settings->author_phone . " | ";
        endif;
        if (!empty($this->settings->author_email)):
            echo "<b>E-mail</b> : " . $this->settings->author_email;
        endif;
        ?>
        <h3 class="inv_title text-uppercase"><u><?php echo Yii::t("strings", "Purchase Invoice"); ?></u></h3>
    </div>
    <div class="mp_gap"></div>
    <div class="row clearfix txt_left_xs media_print">
        <div class="col-sm-4 form-group mpw_50">
            <strong><?php echo Yii::t("strings", "Order : " . sprintf("00000%d", $model->id)); ?></strong><br>
            <?php if (!empty($model->company_id)): ?>
                <span><?php echo AppObject::companyName($model->company_id); ?></span><br>
            <?php endif; ?>
            <?php if (!empty($model->company_id)): ?>
                <span><?php echo "Phone : " . AppObject::companyPhone($model->company_id); ?></span><br>
            <?php endif; ?>
        </div>
        <div class="col-sm-4 form-group pull-right text-right mpw_50">
            <?php echo Yii::t("strings", "Date"); ?>&nbsp;:&nbsp;<?php echo date('j M Y', strtotime($model->created)); ?><br/>
            <?php echo Yii::t("strings", "Invoice No"); ?>&nbsp;:&nbsp;<?php echo $model->invoice_no; ?><br/>
        </div>
    </div>
    <div style="height:10px;"></div>
    <div class="table-responsive">
        <table class="table table-bordered tbl_invoice_view">
            <tr>
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
                    <td class="text-center">
                        <?php
                        if (!empty($item->product->unit)) {
                            echo $item->quantity . " <em>" . $item->product->unit . "</em>";
                        } else {
                            echo $item->quantity;
                        }
                        ?>
                    </td>
                    <td class="text-right"><?php echo AppHelper::getFloat($item->price); ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($item->quantity * $item->price); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="bg_gray">
                <th colspan="2" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                <th class="text-center"><?php echo AppObject::sumPurchaseQtyById($model->id); ?></th>
                <th class="text-right"></th>
                <th class="text-right"><?php echo AppObject::sumPurchaseTotalById('total', $model->id); ?></th>
            </tr>
        </table>
    </div>
    <div class="clearfix sale_info">
        <div class="form-group clearfix">
            <div class="col-md-3 col-sm-4 mp_30 pull-left" style="padding:0;">
                <div class="clearfix media_print form-group">
                    <p><strong><u><?php echo Yii::t("strings", "Dues Status"); ?></u>&nbsp;:</strong></p>
                    <table class="table tbl_invoice_view no_mrgn">
                        <tr>
                            <td><?php echo Yii::t("strings", "Current Due"); ?></td>
                            <td class="text-right"><?php echo $curDue; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Previous Due"); ?></td>
                            <td class="text-right">
                                <?php
                                if ($_sumBalance < 0) {
                                    echo AppHelper::getFloat($_sumBalance + $curDue);
                                } else {
                                    echo AppHelper::getFloat($_sumBalance - $curDue);
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Total Due"); ?></td>
                            <td class="text-right<?php echo (!empty($_sumBalance) && $_sumBalance > 0) ? " color_green" : " color_red"; ?>"><?php echo AppHelper::getFloat($_sumBalance); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="clearfix media_print">
                    <p>
                        <strong><u><?php echo Yii::t("strings", "Payment Status"); ?></u>&nbsp;:</strong><br>
                        <?php
                        if ($payment->payment_mode == AppConstant::PAYMENT_CHECK) {
                            echo $payment->payment_mode . "<br>";
                            echo "<u>Bank</u> : " . $payment->bank_name . "<br>";
                            echo "<u>Check No</u> : " . $payment->check_no;
                        } else {
                            echo $payment->payment_mode;
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="col-md-5 col-sm-4 mpw_40 pull-right" style="padding:0;">
                <div class="clearfix media_print">
                    <table class="table tbl_invoice_view no_mrgn">
                        <tr>
                            <td><?php echo Yii::t("strings", "Sub Total"); ?></td>
                            <td class="text-right"><?php echo $invAmount; ?></td>
                        </tr>
                        <?php if (!empty($this->settings->vat)): ?>
                            <tr>
                                <td><?php echo Yii::t("strings", "Vat ({$vat}%)"); ?></td>
                                <td class="text-right"><?php echo $vatPrice; ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td><?php echo Yii::t("strings", "Other Cost"); ?></td>
                            <td class="text-right"><?php echo $otherCost; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo Yii::t("strings", "(-) Discount"); ?>
                                <?php
                                if (!empty($payment->sale_invoice_no)) {
                                    $invoiceUrl = Yii::app()->createUrl(AppUrl::URL_SALE_VIEW, array('id' => $payment->sale_invoice_no));
                                    echo "[invoice No: <a class='txt_ul' href='{$invoiceUrl}' target='_blank' title='View Invoice'>{$payment->sale_invoice_no}</a>]";
                                } elseif ($payment->discount_type == AppConstant::PRODUCT_RETURN) {
                                    echo "[<span class='txt_ul color_blue' style='width:auto !important;'>" . AppConstant::PRODUCT_RETURN . "</span>]";
                                }
                                ?>
                            </td>
                            <td class="text-right"><?php echo AppHelper::getFloat($payment->discount_amount); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Net Payable"); ?></td>
                            <td class="text-right"><?php echo $total; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Invoice Paid"); ?></td>
                            <td class="text-right"><?php echo $invPaid; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Due Collection"); ?></td>
                            <td class="text-right"><?php echo AppHelper::getFloat($payment->advance_amount); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Total Paid"); ?></td>
                            <td class="text-right"><?php echo AppHelper::getFloat($payment->total_paid); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Due Amount"); ?></td>
                            <td class="text-right"><?php echo $curDue; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="form-group clearfix">
            <strong><u><?php echo Yii::t("strings", "Inword"); ?></u>&nbsp;:</strong>&nbsp;<span class="text-capitalize" style="width: auto !important"><?php echo AppHelper::int_to_words($total) . " Taka Only"; ?></span>
        </div>
        <div class="form-group clearfix">
            <strong><u><?php echo Yii::t("strings", "Prepared By"); ?></u>&nbsp;:</strong>&nbsp;<?php echo AppObject::displayNameByUser($model->created_by); ?>
        </div>

        <div class="form-group clearfix">
            <div class="col-md-6 mp_50 pull-left" style="padding:0;">
                <?php if ($model->has_transport == 1) : ?>
                    <h4 class="mp_no_mrgn"><u><?php echo Yii::t("strings", "Transport Options"); ?></u></h4>
                    <form action="" method="post" id="formPurchaseInfo">
                        <input type="hidden" name="infoID" value="<?php echo!empty($model->info->id) ? $model->info->id : ''; ?>">
                        <input type="hidden" name="purchaseID" value="<?php echo $model->id; ?>">
                        <p>
                            <span style="display:inline-block;width:170px;">Transport Name </span>: <input type="text" name="info_transport_name" value="<?php echo!empty($model->info->transport_name) ? $model->info->transport_name : ''; ?>">
                        </p>
                        <p>
                            <span style="display:inline-block;width:170px;">Transport Driver Name </span>: <input type="text" name="info_transport_driver" value="<?php echo!empty($model->info->driver_name) ? $model->info->driver_name : ''; ?>">
                        </p>
                        <p>
                            <span style="display:inline-block;width:170px;">Phone </span>: <input type="text" name="info_transport_driver_phone" value="<?php echo!empty($model->info->driver_phone) ? $model->info->driver_phone : ''; ?>">
                        </p>
                        <p>
                            <span style="display:inline-block;width:170px;">Viechle No </span>: <input type="text" name="info_viechel_no" value="<?php echo!empty($model->info->viechel_no) ? $model->info->viechel_no : ''; ?>">
                        </p>
                        <p>
                            <span style="display:inline-block;width:170px;">Supervisor Name </span>: <input type="text" name="info_supervisor_name" value="<?php echo!empty($model->info->supervisor_name) ? $model->info->supervisor_name : ''; ?>">
                        </p>
                        <p>
                            <span style="display:inline-block;width:170px;">Supervisor Phone </span>: <input type="text" name="info_supervisor_phone" value="<?php echo!empty($model->info->supervisor_phone) ? $model->info->supervisor_phone : ''; ?>">
                        </p>
                        <p class="dis_print" style="text-align: center;"><input type="button" id="submitInfo" value="Save" class="btn btn-info btn-xs"></p>
                    </form>
                <?php endif; ?>
            </div>
            <div class="col-md-4 mp_30 pull-right" style="<?php echo ($model->has_transport == 1) ? 'padding-top:225px' : 'padding-top:50px'; ?>">
                <div style="border-top:1px solid #000000;text-align:center;">Authorized Signature and Seal</div>
            </div>
        </div>
    </div>
</div>
<div class="form-group text-center">
    <button type="button" class="btn btn-primary" onclick="printElem('#printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
    <a class="btn btn-info" id="getPaidBtn" href="javascript:void(0);" data-info="<?php echo $model->_key; ?>"><i class="fa fa-dollar"></i>&nbsp;<?php echo Yii::t("strings", "Get Paid"); ?></a>
</div>
<div class="modal fade" id="containerForPaymentInfo" tabindex="-1" role="dialog" aria-labelledby="containerForPaymentInfoLabel"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", "#getPaidBtn", function(e) {
            showLoader("Processing...", true);
            var _invoice = $(this).attr("data-info");
            var _url = ajaxUrl + '/purchase/payment?id=' + _invoice;

            $("#containerForPaymentInfo").load(_url, function() {
                $("#containerForPaymentInfo").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                showLoader("", false);
            });
            e.preventDefault();
        });

        $(document).on("click", "#submitInfo", function(e) {
            showLoader("Processing...", true);
            var _form = $("#formSaleInfo");
            var _url = ajaxUrl + '/purchase/save_info';

            $.post(_url, _form.serialize(), function(response) {
                if (response.success === true) {
                    $("#popup").html(response.message).show();
                    setTimeout(location.reload(), 3000);
                } else {
                    $("#popup").html(response.message).show();
                }
                showLoader("", false);
            }, "json");
            e.preventDefault();
        });

        $(document).on("input", "#txtLess", function(e) {
            var $this = $(this);
            var mainAmount = $(this).attr('data-info');
            var lessType = $('input[name="lessType"]:checked').val();
            var _less = '';
            var _total = '';

            if (lessType === "amount") {
                _less = parseFloat($this.val()).toFixed(2);
                _total = parseFloat(mainAmount - $this.val()).toFixed(2);
                $("#lessAmount").val(_less);
                $("#totalAmount").val(_total);
            } else {
                _less = parseFloat((mainAmount * $this.val()) / 100).toFixed(2);
                _total = parseFloat((mainAmount - _less)).toFixed(2);
                $("#lessAmount").val(_less);
                $("#totalAmount").val(_total);
            }
            e.preventDefault();
        });

        $(document).on("change", 'input[name="lessType"]', function(e) {
            var mainAmount = $("#txtLess").attr('data-info');
            var lessAmount = $("#txtLess").val();
            var _less = '';
            var _total = '';

            if ($(this).val() == "amount") {
                _less = parseFloat(lessAmount).toFixed(2);
                _total = parseFloat(mainAmount - lessAmount).toFixed(2);
                $("#lessAmount").val(_less);
                $("#totalAmount").val(_total);
            } else {
                _less = parseFloat((mainAmount * lessAmount) / 100).toFixed(2);
                _total = parseFloat(mainAmount - _less).toFixed(2);
                $("#lessAmount").val(_less);
                $("#totalAmount").val(_total);
            }
            e.preventDefault();
        });
    });
</script>