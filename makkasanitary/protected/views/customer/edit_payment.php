<?php
$this->breadcrumbs = array(
    'Customer' => array(AppUrl::URL_CUSTOMER),
    'Payment' => array(AppUrl::URL_CUSTOMER_PAYMENT, 'id' => $model->customer->_key),
    'Edit'
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
                    <?php echo $form->labelEx($model, 'customer'); ?>
                    <input type="text" value="<?php echo AppObject::customerName($model->customer->id); ?>" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'pay_date'); ?>
                    <div class="input-group">
                        <input type="text" id="datepickerExample" class="form-control" name="pay_date" placeholder="(dd-mm-yyyy)" readonly value="<?php echo date("d-m-Y", strtotime($model->pay_date)); ?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'Payment Type'); ?><br>
                    <label class="txt_np" for="due_paid" style="margin-right:10px;"><input value="<?php echo AppConstant::TYPE_DUE_PAID; ?>" id="due_paid" class="btn_pay_type" type="radio" name="pay_type"<?php if ($model->type == AppConstant::TYPE_DUE_PAID) echo ' checked="checked"'; ?>>&nbsp;<?php echo Yii::t('strings', 'Due Paid'); ?></label>
                    <label class="txt_np" for="due" style="margin-right:10px;"><input value="<?php echo AppConstant::TYPE_DUE; ?>" id="due" class="btn_pay_type" type="radio" name="pay_type"<?php if ($model->type == AppConstant::TYPE_DUE) echo ' checked="checked"'; ?>>&nbsp;<?php echo Yii::t('strings', 'Previous Due'); ?></label>
                    <label class="txt_np" for="advance"><input value="<?php echo AppConstant::TYPE_ADVANCE; ?>" id="advance" class="btn_pay_type" type="radio" name="pay_type"<?php if ($model->type == AppConstant::TYPE_ADVANCE) echo ' checked="checked"'; ?>>&nbsp;<?php echo Yii::t('strings', 'Advance'); ?></label>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'payment_mode', array('style' => 'display:block')); ?>
                    <ul class="clearfix">
                        <?php
                        foreach ($payModes as $k => $v):
                            if ($v !== AppConstant::PAYMENT_NO) :
                                ?>
                                <li>
                                    <label for="CustomerPayment_payment_mode_<?php echo $k; ?>">
                                        <input type="radio" class="pay_mode" name="CustomerPayment[payment_mode]"<?php if ($model->payment_mode == $payModes[$k]) echo ' checked="checked"'; ?> value="<?php echo $v; ?>" id="CustomerPayment_payment_mode_<?php echo $k; ?>">&nbsp;<?php echo $v; ?>
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
                        <?php echo $form->labelEx($model, 'bank_name'); ?>
                        <?php echo $form->dropDownList($model, 'bank_name', CHtml::listData(Bank::model()->getList(), 'name', 'name'), array('empty' => 'Select', 'class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'check_no'); ?>
                        <?php echo $form->textField($model, 'check_no', array('class' => 'form-control')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->label($model, 'amount'); ?>
                    <div class="input-group">
                        <input class="form-control" name="CustomerPayment[advance_amount]" id="CustomerPayment_advance_amount" type="text" value="<?php echo!empty($model->advance_amount) ? $model->advance_amount : $model->due_amount; ?>">
                        <span class="input-group-addon">Tk</span>
                    </div>
                </div>
                <div class="form-group text-center">
                    <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary', 'name' => 'btnCustomerPayment', 'id' => 'btnCustomerPayment')); ?>
                </div>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var _bank = document.getElementById('CustomerPayment_bank_name');

        $("#datepickerExample").datepicker({
            format: 'dd-mm-yyyy'
        });

        if ($(".btn_pay_type:checked").val() == "advance" || $(".btn_pay_type:checked").val() == "due paid") {
            enable(".pay_mode");
        } else {
            disable(".pay_mode");
        }

        $(document).on("change", ".btn_pay_type", function() {
            if ($(this).val() == "advance" || $(this).val() == "due paid") {
                enable(".pay_mode");
            } else {
                _bank.selectedIndex = 0;
                $("#CustomerPayment_check_no").val('');
                $("#bankOption").slideUp(150);
                $(".pay_mode").prop("checked", false);
                disable(".pay_mode");
            }
        });

        if ($(".pay_mode:checked").val() == "Cheque Payment") {
            $("#bankOption").show();
        } else {
            $("#bankOption").hide();
        }

        $(document).on("change", ".pay_mode", function() {
            if ($(this).val() == "Cheque Payment") {
                $("#bankOption").slideDown(150);
            } else {
                _bank.selectedIndex = 0;
                $("#CustomerPayment_check_no").val('');
                $("#bankOption").slideUp(150);
            }
        });
    });
</script>