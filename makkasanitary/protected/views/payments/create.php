<?php
$this->breadcrumbs = array(
    'Payments' => array(AppUrl::URL_PAYMENT),
    'Create'
);
?>
<div class="row content-panel">
    <div class="col-md-12">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmCustomerPayment',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
        ));
        ?>
        <div class="row clearfix">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <?php
                    echo $form->labelEx($model, 'company');
                    $companyList = Company::model()->getList();
                    $list = CHtml::listData($companyList, 'id', 'name');
                    echo $form->dropDownList($model, 'company_id', $list, array('empty' => 'Select', 'class' => 'form-control'));
                    ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'payment_mode'); ?>
                    <ul class="clearfix">
                        <?php
                        foreach ($payModes as $k => $v):
                            if ($v !== "No Payment") :
                                ?>
                                <li>
                                    <label for="Payment_payment_mode_<?php echo $k; ?>">
                                        <input type="radio" class="pay_mode" name="Payment[payment_mode]" value="<?php echo $v; ?>" id="Payment_payment_mode_<?php echo $k; ?>">&nbsp;<?php echo $v; ?>
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
                    <?php echo $form->labelEx($model, 'Payment Type : '); ?>
                    <label class="txt_np" for="advance"><input value="advance" id="advance" class="btn_pay_type" type="radio" name="pay_type" checked="checked">&nbsp;<?php echo Yii::t('strings', 'Advance'); ?></label>
                </div>
                <div class="form-group pay_type" id="adv_div">
                    <?php echo $form->labelEx($model, 'advance_amount'); ?>
                    <div class="input-group">
                        <?php echo $form->textField($model, 'advance_amount', array('class' => 'form-control')); ?>
                        <span class="input-group-addon">Tk</span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'pay_date'); ?>
                    <div class="input-group">
                        <input type="text" id="datepickerExample" class="form-control" name="pay_date" placeholder="(dd-mm-yyyy)" readonly>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>
                <div class="form-group text-center">
                    <?php echo CHtml::resetButton('Reset', array('class' => 'btn btn-info')); ?>
                    <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary', 'name' => 'btnCustomerPayment', 'id' => 'btnCustomerPayment')); ?>
                </div>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#datepickerExample").datepicker({
            format: 'dd-mm-yyyy'
        });

        var _bank = document.getElementById('Payment_account_id');
        _bank.selectedIndex = 0;

        $(document).on("change", ".pay_mode", function() {
            if ($(this).val() == "Cheque Payment") {
                $("#bankOption").slideDown(200);
            } else {
                $("#bankOption").slideUp(200);
                var _bank = document.getElementById('Payment_account_id');
                _bank.selectedIndex = 0;
                $("#Payment_check_no").val("");
            }
        });
    });
</script>