<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Expense' => array(AppUrl::URL_LEDGER_EXPENSE),
    'Edit'
);
?>
<div class="row content-panel">
    <div class="col-md-4 col-sm-6">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmLedgerExpense',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
        ));
        ?>
        <div class="clearfix">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'purpose'); ?>
                <?php echo $form->textField($model, 'purpose', array('class' => 'form-control', 'required' => 'required')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'amount'); ?>
                <?php echo $form->numberField($model, 'amount', array('class' => 'form-control', 'required' => 'required', 'min' => 0, 'step' => 'any')); ?>
            </div>
            <div class="form-group text-center">
                <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary btn-block')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>