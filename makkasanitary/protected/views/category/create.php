<?php
$this->breadcrumbs = array(
    'Category' => array(AppUrl::URL_CATEGORIES),
    'Edit'
);
?>
<div class="row content-panel">
    <div class="col-md-4 col-sm-6">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmCategory',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>
        <div class="clearfix">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'Parent'); ?>
                <?php
                $listData = CHtml::listData($model->getCategoryOptions(), 'id', 'text', 'group');
                echo $form->dropDownList($model, 'parent', $listData, array('empty' => 'Parent', 'class' => 'form-control'));
                ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'name'); ?>
                <?php echo $form->textField($model, 'name', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group text-right">
                <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary btn-block')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>