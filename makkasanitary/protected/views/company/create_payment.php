<?php
$this->breadcrumbs = array(
    'Company' => array(AppUrl::URL_CUSTOMER),
    'Payment' => array(AppUrl::URL_COMPANY_PAYMENT, 'id' => $company->_key),
    'Create'
);
?>
<div class="row content-panel">
    <div class="col-md-12">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmCustomerPayment',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>
        <div class="row clearfix">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <input type="hidden" name="company_id" value="<?php echo $company->id; ?>">
                    <?php echo $form->labelEx($model, 'company'); ?>
                    <input type="text" value="<?php echo AppObject::companyName($company->id); ?>" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'Payment Type : '); ?>
                    <label class="txt_np" for="advance"><input value="<?php echo AppConstant::TYPE_ADVANCE; ?>" id="advance" class="btn_pay_type" type="radio" name="pay_type">&nbsp;<?php echo Yii::t('strings', 'Advance'); ?></label>
                    <label class="txt_np" for="due"><input value="<?php echo AppConstant::TYPE_DUE; ?>" id="due" class="btn_pay_type" type="radio" name="pay_type" checked>&nbsp;<?php echo Yii::t('strings', 'Due Pay'); ?></label>
                    <label class="txt_np" for="previous_due"><input value="<?php echo AppConstant::TYPE_PREVIOUS_DUE; ?>" id="previous_due" class="btn_pay_type" type="radio" name="pay_type">&nbsp;<?php echo Yii::t('strings', 'Add Previous Due'); ?></label>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'payment_mode', array('style' => 'display:block')); ?>
                    <ul class="clearfix">
                        <?php
                        foreach ($payModes as $k => $v):
                            if ($v !== AppConstant::PAYMENT_NO) :
                                ?>
                                <li>
                                    <label for="Payment_payment_mode_<?php echo $k; ?>">
                                        <input type="radio" class="pay_mode" name="Payment[payment_mode]"<?php if ($model->payment_mode == $payModes[$k]) echo ' checked="checked"'; ?> value="<?php echo $v; ?>" id="Payment_payment_mode_<?php echo $k; ?>" disabled="disabled">&nbsp;<?php echo $v; ?>
                                    </label>
                                </li>
                                <?php
                            endif;
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div id="bankOption" style="display: none;">
                    <div class="form-group">
                        <?php
                        echo $form->labelEx($model, 'account');
                        $accountList = Account::model()->getList();
                        $accList = CHtml::listData($accountList, 'id', function($client) {
                                    return $client->account_name . ' ( ' . AppObject::getBankName($client->bank_id) . ' )';
                                });
                        echo $form->dropDownList($model, 'account_id', $accList, array('empty' => 'Select', 'class' => 'form-control'));
                        ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'check_no'); ?>
                        <?php echo $form->textField($model, 'check_no', array('class' => 'form-control')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->label($model, 'amount'); ?>
                    <div class="input-group">
                        <?php echo $form->textField($model, 'advance_amount', array('class' => 'form-control')); ?>
                        <span class="input-group-addon">Tk</span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'pay_date'); ?>
                    <div class="input-group">
                        <input type="text" id="datepickerExample" class="form-control" name="pay_date" value="<?= date('d-m-Y'); ?>" placeholder="(dd-mm-yyyy)" readonly>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>
                <div class="form-group text-center">
                    <?php echo CHtml::resetButton('Reset', array('class' => 'btn btn-info')); ?>
                    <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary', 'name' => 'btnPayment', 'id' => 'btnPayment')); ?>
                </div>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var _bank = document.getElementById('Payment_account_id');

        $("#datepickerExample").datepicker({
            format: 'dd-mm-yyyy'
        });

        $(document).on("change", ".btn_pay_type", function() {
            if ($(this).val() == "advance") {
                enable(".pay_mode");
            } else {
                _bank.selectedIndex = 0;
                $("#Payment_check_no").val('');
                $("#bankOption").slideUp(150);
                $(".pay_mode").prop("checked", false);
                //disable(".pay_mode");
            }
        });

        $(document).on("change", ".pay_mode", function() {
            if ($(this).val() == "Cheque Payment") {
                $("#bankOption").slideDown(150);
            } else {
                _bank.selectedIndex = 0;
                $("#Payment_check_no").val('');
                $("#bankOption").slideUp(150);
            }
        });
    });
</script>