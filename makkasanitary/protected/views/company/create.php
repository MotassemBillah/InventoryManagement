<?php
$this->breadcrumbs = array(
    'Company' => array(AppUrl::URL_COMPANY),
    'Create'
);
?>
<div class="row content-panel">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'frmCompany',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>
    <div class="clearfix">
        <div class="col-md-4 col-sm-6">
            <div class="form-group">
                <?php echo $form->labelEx($model, 'name'); ?>
                <?php echo $form->textField($model, 'name', array('class' => 'form-control')); ?>
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
                <?php echo $form->labelEx($model, 'mobile'); ?>
                <?php echo $form->textField($model, 'mobile', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'fax'); ?>
                <?php echo $form->textField($model, 'fax', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'other_contacts'); ?>&nbsp;<?php echo Yii::t("strings", "separated by coma (,)"); ?>
                <?php echo $form->textArea($model, 'other_contacts', array('class' => 'form-control')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, 'address'); ?>
                <?php echo $form->textArea($model, 'address', array('class' => 'form-control')); ?>
            </div>
        </div>
        <div class="col-md-2 col-sm-3">
            <div class="clearfix">
                <label><?php echo Yii::t("strings", "Company Head"); ?></label>
                <?php for ($i = 0; $i < 6; $i++) : ?>
                    <div class="form-group">
                        <input type="text" name="company_meta_option[]" class="form-control">
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <div class="col-md-9 form-group text-center">
            <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>