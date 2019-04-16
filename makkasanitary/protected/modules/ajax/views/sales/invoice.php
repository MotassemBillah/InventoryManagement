<?php
//$otherCost = AppHelper::getFloat($model->transport + $model->labor);
//$invPaid = AppHelper::getFloat($model->invoice_paid);
$curDue = AppHelper::getFloat($model->due_amount);
$total = AppHelper::getFloat($model->net_amount);
$_sumBalance = AppObject::sumBalanceAmount($model->customer_id);
?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close"><span aria-hidden="true">x</span></button>
            <h4 class="modal-title"><?php echo Yii::t("strings", "Payment Info"); ?></h4>
        </div>
        <div class="modal-body" style="height:440px;overflow-y:auto;">
            <div id="printDivModal">
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
                </div><br>
                <div class="row clearfix txt_left_xs media_print">
                    <div class="col-sm-4 form-group mpw_50">
                        <strong><?php echo Yii::t("strings", "Payment Voucher : " . sprintf("%d", $model->id)); ?></strong><br>
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
                        <?php
                        if (!empty($model->created)) :
                            $_datetime = explode(' ', $model->created);
                            $_ctime = $_datetime[1];
                            ?>
                            <?php echo Yii::t("strings", "Time"); ?>&nbsp;:&nbsp;<?php echo date('h:i:s A', strtotime($_ctime)); ?><br/>
                        <?php endif; ?>
                    </div>
                </div><br>
                <div class="form-group clearfix">
                    <div class="table-responsive">
                        <table class="table tbl_invoice_view no_mrgn">
                            <tr>
                                <td><?php echo Yii::t("strings", "Previous Due"); ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat(abs($_sumBalance) + $model->advance_amount); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo Yii::t("strings", "Paid Amount"); ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($model->advance_amount); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo Yii::t("strings", "Total Due"); ?></td>
                                <td class="text-right<?php echo (!empty($_sumBalance) && $_sumBalance > 0) ? " color_green" : " color_red"; ?>"><?php echo AppHelper::getFloat($_sumBalance); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="clearfix sale_info">
                    <div class="form-group clearfix">
                        <strong><u><?php echo Yii::t("strings", "Inword Paid"); ?></u>&nbsp;:</strong>&nbsp;<span class="text-capitalize" style="width: auto !important"><?php echo AppHelper::int_to_words($model->advance_amount) . " Taka Only"; ?></span><br>
                        <strong><u><?php echo Yii::t("strings", "Inword Due"); ?></u>&nbsp;:</strong>&nbsp;<span class="text-capitalize" style="width: auto !important"><?php echo AppHelper::int_to_words(abs($_sumBalance)) . " Taka Only"; ?></span>
                    </div>
                    <div class="form-group clearfix">
                        <strong><u><?php echo Yii::t("strings", "Prepared By"); ?></u>&nbsp;:</strong>&nbsp;<?php echo AppObject::displayNameByUser($model->created_by); ?>
                    </div>
                    <div class="col-md-4 mp_30 pull-right" style="padding-top:50px">
                        <div style="border-top:1px solid #000000;text-align:center;">Authorized Signature and Seal</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="text-align: center;">
            <button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close" title="Close"><?php echo Yii::t("strings", "Cancel"); ?></button>
            <button type="button" class="btn btn-primary" onclick="printElem('#printDivModal')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
        </div>
    </div>
</div>