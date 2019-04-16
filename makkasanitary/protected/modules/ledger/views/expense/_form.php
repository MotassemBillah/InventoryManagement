<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Expense' => array(AppUrl::URL_LEDGER_EXPENSE),
    'Create'
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
        <div class="form-group clearfix">
            <?php echo $form->labelEx($model, 'Date', ['class' => 'col-md-4 col-xs-6 text-right']); ?>
            <?php
            if ($model->isNewRecord) {
                echo $form->textField($model, 'pay_date', array('class' => 'col-md-8 col-xs-6', 'value' => date('d-m-Y'), 'readonly' => 'readonly'));
            } else {
                echo $form->textField($model, 'pay_date', array('class' => 'col-md-8 col-xs-6', 'value' => date('d-m-Y', strtotime($model->pay_date)), 'readonly' => 'readonly'));
            }
            ?>
        </div>
        <div class="form-group clearfix">
            <?php echo $form->labelEx($model, 'Head Name', ['class' => 'col-md-4 col-xs-6 text-right']); ?>
            <?php
            $headList = LedgerHead::model()->getList();
            $hlist = CHtml::listData($headList, 'id', 'name');
            if (!empty($headList)) {
                echo $form->dropdownList($model, 'ledger_head_id', $hlist, array('empty' => 'Select', 'class' => 'col-md-8 col-xs-6'));
            } else {
                $_create_link = Yii::app()->createUrl(AppUrl::URL_LEDGER_HEAD_CREATE);
                echo "<a href='{$_create_link}'>Create Head First</a>";
            }
            ?>
        </div>
        <div class="form-group clearfix">
            <?php echo $form->labelEx($model, 'by_whom', ['class' => 'col-md-4 col-xs-6 text-right']); ?>
            <?php echo $form->textField($model, 'by_whom', array('class' => 'col-md-8 col-xs-6')); ?>
        </div>
        <div class="form-group clearfix">
            <?php echo $form->labelEx($model, 'purpose', ['class' => 'col-md-4 col-xs-6 text-right']); ?>
            <?php echo $form->textArea($model, 'purpose', array('class' => 'col-md-8 col-xs-6')); ?>
        </div>
        <div class="form-group clearfix">
            <?php echo $form->labelEx($model, 'amount', ['class' => 'col-md-4 col-xs-6 text-right']); ?>
            <?php echo $form->numberField($model, 'amount', array('class' => 'col-md-8 col-xs-6', 'min' => 0, 'step' => 'any')); ?>
        </div>
        <div class="form-group text-center">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Save' : 'Update', array('class' => 'btn btn-primary', 'style' => 'width:30%')); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#Expense_pay_date").datepicker({
            format: 'dd-mm-yyyy'
        });
    });
</script>