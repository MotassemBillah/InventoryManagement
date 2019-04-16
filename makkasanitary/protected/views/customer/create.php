<?php
$this->breadcrumbs = array(
    'Customer' => array(AppUrl::URL_CUSTOMER),
    'Create'
);
?>
<div class="row content-panel">
    <div class="col-md-12">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmCustomer',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>
        <div class="row clearfix">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'name'); ?>
                    <?php echo $form->textField($model, 'name', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'company'); ?>
                    <?php echo $form->textField($model, 'company', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'email'); ?>
                    <?php echo $form->textField($model, 'email', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'phone'); ?>
                    <?php echo $form->textField($model, 'phone', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'address'); ?>
                    <?php echo $form->textArea($model, 'address', array('class' => 'form-control', 'style' => '')); ?>
                </div>
                <div class="form-group text-center">
                    <?php echo CHtml::resetButton('Reset', array('class' => 'btn btn-info')); ?>
                    <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary', 'name' => 'btnCustomer', 'id' => 'btnCustomer')); ?>
                    &nbsp;<img class="ajaxLoader" src="<?php echo Yii::app()->request->baseUrl; ?>/img/loading.gif" alt="Loading..." style="display: none;">
                </div>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>