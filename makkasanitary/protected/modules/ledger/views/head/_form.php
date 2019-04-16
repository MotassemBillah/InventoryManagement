<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Head' => array(AppUrl::URL_LEDGER_HEAD),
    $model->isNewRecord ? 'Create' : 'Update',
);
?>
<div class="content-panel">
    <div class="col-md-4 col-sm-6">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmLedgerHead',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
        ));
        ?>
        <div class="clearfix">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'type'); ?>
                <?php echo $form->dropdownList($model, 'type', ['Debit' => 'Debit', 'Credit' => 'Credit'], array('empty' => 'Select', 'class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'name'); ?>
                <?php echo $form->textField($model, 'name', array('class' => 'form-control', 'required' => 'required')); ?>
            </div>
            <?php if (in_array(Yii::app()->user->id, [1, 4])): ?>
                <div class="form-group">
                    <?php echo $form->checkbox($model, 'is_fixed', array('class' => 'chk_no_mvam')); ?>&nbsp;
                    <?php echo $form->labelEx($model, 'is_fixed'); ?>
                </div>
            <?php endif; ?>
            <div class="form-group text-right">
                <?php echo CHtml::submitButton($model->isNewRecord ? 'Save' : 'Update', array('class' => 'btn btn-primary btn-block')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>