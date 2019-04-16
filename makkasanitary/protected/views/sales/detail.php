<?php
$this->breadcrumbs = array(
    'Sale' => array(AppUrl::URL_SALE),
    'View'
);
?>
<?php
$vat = !empty($this->settings->vat) ? $this->settings->vat : "";
$vatPrice = AppObject::getVat($vat, $model->sumPrice);
$invAmount = AppHelper::getFloat($model->sumPrice);
$otherCost = AppHelper::getFloat($payment->transport + $payment->labor);
$invPaid = AppHelper::getFloat($payment->invoice_paid);
$curDue = AppHelper::getFloat($payment->due_amount);
if (!empty($this->settings->vat)) {
    $total = AppHelper::getFloat(($invAmount + $otherCost) - $payment->discount_amount) + $vatPrice;
} else {
    $total = AppHelper::getFloat(($invAmount + $otherCost) - $payment->discount_amount);
}
$_sumBalance = AppObject::sumBalanceAmount($model->customer_id);
?>
<div id="printDiv">
    <div class="row form-group clearfix text-center txt_left_xs mp_center media_print mp_mt">
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
        <h3 class="inv_title"><u><?php echo Yii::t("strings", "Sale Invoice"); ?></u></h3>
    </div>
    <div class="row clearfix txt_left_xs media_print">
        <div class="col-sm-4 form-group mpw_50">
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
        <div class="col-sm-4 form-group pull-right text-right mpw_50">
            <?php echo Yii::t("strings", "Date"); ?>&nbsp;:&nbsp;<?php echo date('j M Y', strtotime($model->created)); ?><br/>
            <?php if (!empty($model->on_time)) : ?>
                <?php echo Yii::t("strings", "Time"); ?>&nbsp;:&nbsp;<?php echo date('h:i:s A', strtotime($model->on_time)); ?><br/>
            <?php endif; ?>
            <?php echo Yii::t("strings", "Invoice No"); ?>&nbsp;:&nbsp;<?php echo $model->invoice_no; ?><br/>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr>
                <th class="text-center"><?php echo Yii::t("strings", "SL#"); ?></th>
                <th><?php echo Yii::t("strings", "Description Of Items"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Quantity"); ?></th>
                <?php if (Yii::app()->user->role == AppConstant::ROLE_SUPERADMIN) : ?>
                    <th class="text-right bg-info"><?php echo Yii::t("strings", "P Rate"); ?></th>
                    <th class="text-right bg-info"><?php echo Yii::t("strings", "P Total"); ?></th>
                <?php endif; ?>
                <th class="text-right"><?php echo Yii::t("strings", "Rate"); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
            </tr>
            <?php
            $ppTotal = '';
            $counter = 0;
            foreach ($model->items as $item):
                $pp = AppObject::purchasePrice($item->product_id);
                $counter++;
                $catName = !empty($item->product->category_id) ? "<u>" . AppObject::categoryName($item->product->category_id) . "</u>" : "<u>n/a</u>";
                $typeName = !empty($item->product->type) ? " - <u>" . AppObject::companyHeadName($item->product->type) . "</u>" : " - <u>n/a</u>";
                $colorName = !empty($item->product->color) ? " - <u>" . strtolower($item->product->color) . "</u>" : " - <u>n/a</u>";
                $codeName = !empty($item->product->code) ? " - <u>" . strtoupper($item->product->code) . "</u>" : " - <u>n/a</u>";
                ?>
                <tr id="sale_view_row_<?php echo $item->id; ?>">
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td>
                        <?php echo AppObject::productName($item->product_id); ?>&nbsp;
                        <span>
                            <b>[</b><?php echo $catName . $colorName . $codeName; ?><b>]</b>
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
                    <?php if (Yii::app()->user->role == AppConstant::ROLE_SUPERADMIN) : ?>
                        <td class="text-right bg-info"><?php echo $pp; ?></td>
                        <td class="text-right bg-info"><?php echo AppHelper::getFloat($item->quantity * $pp); ?></td>
                    <?php endif; ?>
                    <td class="text-right"<?php if ($item->price < $pp) echo ' style = "color:red"'; ?>><?php echo AppHelper::getFloat($item->price); ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($item->quantity * $item->price); ?></td>
                </tr>
                <?php
                $sum[] = ($item->quantity * $pp);
                $ppTotal = AppHelper::getFloat(array_sum($sum));
            endforeach;
            $saleTotal = AppObject::sumSaleTotalById('total', $model->id);
            ?>
            <tr class="bg_gray">
                <th colspan="2" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                <th class="text-center"><?php echo AppObject::sumSaleQtyById($model->id); ?></th>
                <?php if (Yii::app()->user->role == AppConstant::ROLE_SUPERADMIN) : ?>
                    <th class="text-right bg-info"></th>
                    <th class="text-right bg-info"><?php echo!empty($ppTotal) ? $ppTotal : ""; ?></th>
                <?php endif; ?>
                <th class="text-right"></th>
                <th class="text-right"><?php echo $saleTotal; ?></th>
            </tr>
            <?php if (Yii::app()->user->role == AppConstant::ROLE_SUPERADMIN) : ?>
                <tr class="bg_gray">
                    <th colspan="6" class="text-right"><?php echo Yii::t("strings", "Invoice Profit"); ?></th>
                    <th class="text-right"><?php echo AppHelper::getFloat(($saleTotal - $ppTotal) - $payment->discount_amount); ?></th>
                </tr>
            <?php endif; ?>
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
                        if ($payment->payment_mode == "Cheque Payment") {
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
                                    echo "[invoice No: <a class='txt_ul' href=' {$invoiceUrl}' target='_blank' title='View Invoice'>{$payment->sale_invoice_no}</a>]";
                                } elseif ($payment->discount_type == AppConstant::PRODUCT_RETURN) {
                                    echo "[<span class='txt_ul color_blue' style='width:auto!important;'>" . AppConstant::PRODUCT_RETURN . "</span>]";
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
                            <td><?php echo Yii::t("strings", "Total Paid"); ?></td>
                            <td class="text-right"><?php echo AppHelper::getFloat($payment->total_paid); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Invoice Paid"); ?></td>
                            <td class="text-right"><?php echo $invPaid; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Due Amount"); ?></td>
                            <td class="text-right"><?php echo $curDue; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("strings", "Due Collection"); ?></td>
                            <td class="text-right"><?php echo AppHelper::getFloat($payment->advance_amount); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
		
        <div class="form-group clearfix">
            <div class="col-md-6 mp_50 pull-left" style="padding:0;">
                <?php if ($model->has_transport == 1) : ?>
                    <h4 class="mp_no_mrgn"><u><?php echo Yii::t("strings", "Transport Options"); ?></u></h4>
                    <form action="" method="post" id="formSaleInfo">
                        <input type="hidden" name="infoID" value="<?php echo!empty($model->info->id) ? $model->info->id : ''; ?>">
                        <input type="hidden" name="saleID" value="<?php echo $model->id; ?>">
                        <p class="">
                            <strong style="display:inline-block;width:150px;">Transport Name </strong>:<input type="text" name="info_transport_name" value="<?php echo!empty($model->info->transport_name) ? $model->info->transport_name : ''; ?>">
                        </p>
                        <p>
                            <strong style="display:inline-block;width:150px;">Driver Name </strong>:<input type="text" name="info_transport_driver" value="<?php echo!empty($model->info->driver_name) ? $model->info->driver_name : ''; ?>">
                        </p>
                        <p>
                            <strong style="display:inline-block;width:150px;">Driver Phone </strong>:<input type="text" name="info_transport_driver_phone" value="<?php echo!empty($model->info->driver_phone) ? $model->info->driver_phone : ''; ?>">
                        </p>
                        <p>
                            <strong style="display:inline-block;width:150px;">Transport No </strong>:<input type="text" name="info_viechel_no" value="<?php echo!empty($model->info->viechel_no) ? $model->info->viechel_no : ''; ?>">
                        </p>
                        <p>
                            <strong style="display:inline-block;width:150px;">Supervisor Name </strong>:<input type="text" name="info_supervisor_name" value="<?php echo!empty($model->info->supervisor_name) ? $model->info->supervisor_name : ''; ?>">
                        </p>
                        <p>
                            <strong style="display:inline-block;width:150px;">Supervisor Phone </strong>:<input type="text" name="info_supervisor_phone" value="<?php echo!empty($model->info->supervisor_phone) ? $model->info->supervisor_phone : ''; ?>">
                        </p>
                        <p class="dis_print" style="text-align: center;"><input type="button" id="submitInfo" value="Save" class="btn btn-info btn-xs"></p>
                    </form>
                <?php endif; ?>
            </div>            
        </div>
		<div class="form-group clearfix">
			<div class="col-md-8 mpw_65 pull-left" style="padding:0 15px 0 0;">
				<div class="form-group clearfix">
					<strong><u><?php echo Yii::t("strings", "Inword"); ?></u>&nbsp;:</strong>&nbsp;<span class="text-capitalize" style="width: auto !important"><?php echo AppHelper::int_to_words($total) . " Taka Only"; ?></span>
				</div>
				<div class="form-group clearfix">
					<strong><u><?php echo Yii::t("strings", "Prepared By"); ?></u>&nbsp;:</strong>&nbsp;<?php echo AppObject::displayNameByUser($model->created_by); ?>
				</div>
			</div>
			<div class="col-md-4 mpw_33 pull-right" style="padding:50px 0 0;">
				<div style="border-top:1px solid #000000;text-align:center;">Authorized Signature and Seal</div>
			</div>
		</div>
    </div>
</div>
<div class="form-group text-center">
    <form id="frmPayInfo" action="" method="post">
        <input type="hidden" name="paymentID" value="<?php echo $model->_key; ?>">
        <input type="hidden" name="purchaseTotal" value="<?php echo $ppTotal; ?>">
        <input type="hidden" name="saleTotal" value="<?php echo $saleTotal; ?>">
    </form>
    <button type="button" class="btn btn-primary" onclick="printDiv('printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
    <a class="btn btn-success" id="getPaidBtn" href="javascript:void(0);" data-info="<?php echo $model->_key; ?>"><i class="fa fa-dollar"></i>&nbsp;<?php echo Yii::t("strings", "Get Paid"); ?></a>
</div>
<div class="modal fade" id="containerForPaymentInfo" tabindex="-1" role="dialog" aria-labelledby="containerForPaymentInfoLabel"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", "#getPaidBtn", function(e) {
            showLoader("Processing...", true);
            var _form = $("#frmPayInfo");
            var _url = ajaxUrl + '/sales/payment?' + _form.serialize();

            $("#containerForPaymentInfo").load(_url, function() {
                $("#containerForPaymentInfo").modal({backdrop: 'static', keyboard: false
                });
                showLoader("", false);
            });
            e.preventDefault();
        });

        $(document).on("click", "#submitInfo", function(e) {
            showLoader("Processing...", true);
            var _form = $("#formSaleInfo");
            var _url = ajaxUrl + '/sales/save_info';

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