<?php
$vat = !empty($this->settings->vat) ? $this->settings->vat : "";
$vatPrice = AppObject::getVat($vat, $model->sumTotal);
$invAmount = AppHelper::getFloat($model->sumTotal);
$otherCost = AppHelper::getFloat($payment->transport + $payment->labor);
$invPaid = AppHelper::getFloat($payment->invoice_paid);
$curDue = AppHelper::getFloat($payment->due_amount);
if (!empty($this->settings->vat)) {
    $total = AppHelper::getFloat(($invAmount + $otherCost)) + $vatPrice;
} else {
    $total = AppHelper::getFloat(($invAmount + $otherCost));
}
$_sumBalance = AppObject::sumSelfBalanceAmount($model->company_id);
if ($_sumBalance < 0) {
    $prevDue = AppHelper::getFloat($_sumBalance + $curDue);
} else {
    $prevDue = AppHelper::getFloat($_sumBalance - $curDue);
}
?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close"><span aria-hidden="true">x</span></button>
            <div class="modal-title">
                <strong><u><?php echo Yii::t("strings", "Company"); ?></u>:</strong>&nbsp;<?php echo AppObject::companyName($model->company_id); ?>
                <strong style="margin-left: 10px;"><u><?php echo Yii::t("strings", "Invoice No"); ?></u>:</strong>&nbsp;<?php echo $model->invoice_no; ?>
                <strong style="margin-left: 10px;"><u><?php echo Yii::t("strings", "Previous Due"); ?></u>:</strong>&nbsp;<?php echo $prevDue; ?>
            </div>
            <div id="ajaxModalMessage" class="alert" style="display: none"></div>
        </div>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmPayment',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>
        <input type="hidden" name="company_id" value="<?php echo $payment->company_id; ?>">
        <input type="hidden" name="paymentID" value="<?php echo!empty($payment->id) ? $payment->id : ''; ?>">
        <input type="hidden" name="infoID" value="<?php echo!empty($model->info->id) ? $model->info->id : ''; ?>">
        <input type="hidden" name="previousDue" value="<?php echo $prevDue; ?>">
        <div class="modal-body" style="height:440px;overflow-y:auto;">
            <div class="clearfix">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <td><strong><?php echo Yii::t('strings', 'Company Name'); ?></strong></td>
                            <td class="text-right"><?php echo AppObject::companyName($payment->company_id); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Payment Method"); ?></strong></td>
                            <td class="text-right">
                                <?php foreach ($payModes as $k => $v): ?>
                                    <label class="txt_np no_mrgn" for="payment_mode_<?php echo $k; ?>">
                                        <input type="radio" id="payment_mode_<?php echo $k; ?>" class="pay_mode" name="payment_mode"<?php if ($payment->payment_mode == $payModes[$k]) echo ' checked="checked"'; ?> value="<?php echo $v; ?>">&nbsp;<?php echo $v; ?>
                                    </label>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr class="bank_option">
                            <td><strong><?php echo $form->labelEx($payment, 'bank_name'); ?></strong></td>
                            <td class="text-right">
                                <?php
                                $accountList = Account::model()->getList();
                                $accList = CHtml::listData($accountList, 'id', function($c) {
                                            return $c->account_name . " (" . AppObject::getBankName($c->bank_id) . ")";
                                        });
                                echo $form->dropDownList($payment, 'account_id', $accList, array('empty' => 'Select', 'class' => 'form-control', 'style' => 'float:right;width:60%'));
                                ?>
                            </td>
                        </tr>
                        <tr class="bank_option">
                            <td><strong><?php echo $form->labelEx($payment, 'check_no'); ?></strong></td>
                            <td class="text-right"><?php echo $form->textField($payment, 'check_no', array('class' => 'text-right')); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Invoice Amount"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" id="txtInvoiceAmount" name="txtInvoiceAmount" value="<?php echo $invAmount; ?>"></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Transport Cost"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" id="txtTransportCost" name="txtTransportCost" value="<?php echo $payment->transport; ?>"></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Labor Cost"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" id="txtLaborCost" name="txtLaborCost" value="<?php echo $payment->labor; ?>"></td>
                        </tr>
                        <?php if (!empty($this->settings->vat)): ?>
                            <tr>
                                <td><strong><?php echo Yii::t("strings", "Vat ({$vat}%)"); ?></strong></td>
                                <td class="text-right"><input type="text" class="text-right" id="txtVatPrice" name="txtVatPrice" value="<?php echo $vatPrice; ?>"></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Total Payable"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" id="txtTotalAmount" name="txtTotalAmount" value="<?php echo $total; ?>"></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Discount Type"); ?></strong></td>
                            <td class="text-right">
                                <label class="txt_np no_mrgn" for="none"><input type="radio" id="none" class="chk_no_mvam" name="lessType" value=""<?php if ($model->payment->discount_type == "") echo ' checked="checked"'; ?>>&nbsp;<?php echo Yii::t("strings", "None"); ?></label>
                                <label class="txt_np no_mrgn" for="amount"><input type="radio" id="amount" class="chk_no_mvam" name="lessType" value="amount"<?php if ($model->payment->discount_type == "amount") echo ' checked="checked"'; ?>>&nbsp;<?php echo Yii::t("strings", "Amount"); ?></label>
                                <label class="txt_np no_mrgn" for="commission"><input type="radio" id="commission" class="chk_no_mvam" name="lessType" value="commission"<?php if ($model->payment->discount_type == "commission") echo ' checked="checked"'; ?>>&nbsp;<?php echo Yii::t("strings", "Commission"); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Discount Amount"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" name="txtLessAmount" id="txtLessAmount" value="<?php echo!empty($payment->discount_amount) ? AppHelper::getFloat($payment->discount_amount) : ""; ?>"<?php if (empty($payment->discount_amount)) echo ' disabled="disabled"'; ?>></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Net Payable"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" id="txtNetAmount" name="txtNetAmount" value="<?php echo $total; ?>"></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Paid Amount"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" id="txtTotalPaidAmount" name="txtTotalPaidAmount" value="<?php echo AppHelper::getFloat($payment->total_paid); ?>"></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Invoice Paid"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" id="txtPaidAmount" name="txtPaidAmount" value="<?php echo $invPaid; ?>"></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo Yii::t("strings", "Due Collection"); ?></strong></td>
                            <td class="text-right"><input type="text" class="text-right" id="txtAdvAmount" name="txtAdvAmount" value="<?php echo AppHelper::getFloat($payment->advance_amount); ?>"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="text-align: center;">
            <button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close" title="Close"><?php echo Yii::t("strings", "Cancel"); ?></button>
            <button type="button" class="btn btn-primary" id="processPayment"><?php echo Yii::t("strings", "Save"); ?></button>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        if ($(".pay_mode:checked").val() == "Cheque Payment") {
            $(".bank_option").show();
        } else {
            $(".bank_option").hide();
        }

        $(document).on("change", ".pay_mode", function() {
            if ($(this).val() == "No Payment") {
                $("#txtTotalPaidAmount").val("");
                $("#txtPaidAmount").val("");
                $("#txtAdvAmount").val("");
            }
            if ($(this).val() == "Cheque Payment") {
                $(".bank_option").slideDown(200);
            } else {
                $(".bank_option").slideUp(200);
                var _bank = document.getElementById('Payment_account_id');
                _bank.selectedIndex = 0;
                $("#Payment_check_no").val("");
            }
        });

        $(document).on("input", "#txtTransportCost, #txtLaborCost", function(e) {
            var _invoice = document.getElementById('txtInvoiceAmount').value;
            var _transport = document.getElementById('txtTransportCost').value;
            var _labor = document.getElementById('txtLaborCost').value;
            if (_transport == "")
                _transport = 0;
            if (_labor == "")
                _labor = 0;

            var result = parseInt(_transport) + parseInt(_labor);
            var total = result + parseInt(_invoice);
            if (!isNaN(result)) {
                document.getElementById('txtTotalAmount').value = parseFloat(total).toFixed(2);
                document.getElementById('txtNetAmount').value = parseFloat(total).toFixed(2);
            }
            e.preventDefault();
        });

        $(document).on("input", "#txtLessAmount", function(e) {
            var _value = $(this).val();
            var _totalAmount = document.getElementById('txtTotalAmount').value;
            var _total = (parseInt(_totalAmount - _value));

            $("#txtNetAmount").val(parseFloat(_total).toFixed(2));
        });

        $(document).on("change", '.chk_no_mvam', function(e) {
            if ($(this).val() !== "") {
                enable("#txtLessAmount");
            } else {
                $("#txtNetAmount").val($("#txtTotalAmount").val());
                disable("#txtLessAmount");
                $("#txtLessAmount").val("");
            }
            e.preventDefault();
        });

        $(document).on("input", "#txtPaidAmount", function(e) {
            var netAmount = parseInt($("#txtNetAmount").val());
            var _total = (netAmount - $(this).val());
            $("#txtDueAmount").val(parseFloat(_total).toFixed(2));
            e.preventDefault();
        });

        $(document).on("input", "#txtTotalPaidAmount", function(e) {
            var netAmount = parseInt($("#txtNetAmount").val());
            if ($(this).val() > netAmount) {
                var _total = ($(this).val() - netAmount);
                $("#txtPaidAmount").val(parseFloat(netAmount).toFixed(2));
                $("#txtAdvAmount").val(parseFloat(_total).toFixed(2));
            } else {
                $("#txtPaidAmount").val(parseFloat($(this).val()).toFixed(2));
                $("#txtAdvAmount").val("");
            }
            e.preventDefault();
        });

        $(document).on("click", "#processPayment", function(e) {
            e.preventDefault();
            showLoader("One Moment Please...", true);
            var _form = $("#frmPayment");
            var _url = ajaxUrl + '/purchase/update_payment';

            $.post(_url, _form.serialize(), function(response) {
                if (response.success === true) {
                    $("#ajaxModalMessage").removeClass('alert-danger').addClass('alert-success').html("");
                    $("#ajaxModalMessage").html(response.message).show();
                    redirectTo(response.goto);
                } else {
                    $("#ajaxModalMessage").removeClass('alert-success').addClass('alert-danger').html("");
                    $("#ajaxModalMessage").html(response.message).show();
                }
                showLoader("", false);
            }, "json");
            e.preventDefault();
        });
    });
</script>