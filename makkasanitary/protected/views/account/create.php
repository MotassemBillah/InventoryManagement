<?php
$this->breadcrumbs = array(
    'Account' => array(AppUrl::URL_ACCOUNT),
    'Create'
);
?>
<div class="row content-panel">
    <div class="col-md-4 col-sm-6">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmAccount',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>
        <div class="clearfix">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'bank'); ?>
                <?php echo $form->dropDownList($model, 'bank_id', CHtml::listData(Bank::model()->getList(), 'id', 'name'), array('empty' => 'Select', 'class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'account_name'); ?>
                <?php echo $form->textField($model, 'account_name', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'account_number'); ?>
                <?php echo $form->textField($model, 'account_number', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'account_type'); ?>
                <?php echo $form->dropDownList($model, 'account_type', $model->typeList(), array('empty' => 'Select', 'class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'address'); ?>
                <?php echo $form->textArea($model, 'address', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group text-right">
                <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary btn-block')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>