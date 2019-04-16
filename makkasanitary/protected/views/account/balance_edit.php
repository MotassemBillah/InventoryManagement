<?php
$this->breadcrumbs = array(
    'Account' => array(AppUrl::URL_ACCOUNT),
    'Balance Edit'
);
?>
<div class="row clearfix" style="border-bottom:1px solid #cccccc;margin:0 -15px 10px -15px;padding-bottom:7px;">
    <div class="col-md-4 xs_txt_left">
        <strong><?php echo Yii::t("strings", "Bank"); ?></strong>:&nbsp;<?php echo AppObject::getBankName($account->bank_id); ?>
    </div>
    <div class="col-md-4 text-center xs_txt_left">
        <strong><?php echo Yii::t("strings", "Account Name"); ?></strong>:&nbsp;<?php echo $account->account_name; ?>
    </div>
    <div class="col-md-4 text-right xs_txt_left">
        <strong><?php echo Yii::t("strings", "Account Number"); ?></strong>:&nbsp;<?php echo $account->account_number; ?>
    </div>
</div>
<div class="row content-panel">
    <div class="col-md-4 col-sm-6">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmAccountBalance',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
        ));
        ?>
        <div class="clearfix">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'category'); ?>
                <?php echo $form->textField($model, 'category', array('class' => 'form-control', 'readonly' => 'readonly')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'purpose'); ?>
                <?php echo $form->textField($model, 'purpose', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'by_whom'); ?>
                <?php echo $form->textField($model, 'by_whom', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'amount'); ?>
                <div class="input-group">
                    <?php echo $form->textField($model, 'amount', array('class' => 'form-control')); ?>
                    <span class="input-group-addon">Tk</span>
                </div>
            </div>
            <div class="form-group text-right">
                <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary btn-block')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>