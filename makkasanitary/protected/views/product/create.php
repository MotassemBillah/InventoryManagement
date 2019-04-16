<?php
$this->breadcrumbs = array(
    'Product' => array(AppUrl::URL_PRODUCT),
    'Create'
);
?>
<div class="row content-panel">
    <div class="col-md-12 col-sm-12">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmProduct',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        ));
        ?>
        <div class="row clearfix">
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <?php
                    echo $form->labelEx($model, 'company');
                    $companyList = Company::model()->getList();
                    $comlist = CHtml::listData($companyList, 'id', 'name');
                    echo $form->dropDownList($model, 'company_id', $comlist, array('empty' => 'Select', 'class' => 'form-control'));
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    echo $form->labelEx($model, 'category');
                    $categoryList = Category::model()->getList();
                    $catlist = CHtml::listData($categoryList, 'id', 'name');
                    echo $form->dropDownList($model, 'category_id', $catlist, array('empty' => 'Select', 'class' => 'form-control'));
                    ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'name'); ?>
                    <?php echo $form->textField($model, 'name', array('class' => 'form-control', 'placeholder' => 'Name')); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'description'); ?>
                    <?php echo $form->textField($model, 'description', array('class' => 'form-control', 'placeholder' => 'description')); ?>
                </div>
            </div>
            <div class="col-md-2 col-sm-2 no_size">
                <div class="form-group">
                    <?php echo $form->labelEx($model, "size"); ?>
                    <?php echo $form->textField($model, "size", array("class" => "form-control", "placeholder" => "size")); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, "company group"); ?>
                    <select id="Product_type" name="Product[type]" class="form-control pro_type_dropdown"></select>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'unit'); ?>
                    <?php echo $form->dropDownList($model, 'unit', AppHelper::getUnits(), array('empty' => 'Select', 'class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, "unitsize"); ?>
                    <?php echo $form->textField($model, "unitsize", array("disabled" => "disabled", "class" => "form-control", "placeholder" => "unit size")); ?>
                </div>
            </div>
            <div class="col-md-2 col-sm-2 no_size">
                <div class="form-group">
                    <?php echo $form->labelEx($model, "color"); ?>
                    <?php echo $form->textField($model, "color", array("class" => "form-control", "placeholder" => "color")); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'grade'); ?>
                    <?php echo $form->textField($model, 'grade', array('class' => 'form-control', 'placeholder' => 'grade')); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->checkbox($model, 'is_damaged', array('class' => 'chk_no_mvam')); ?>&nbsp;
                    <?php echo $form->labelEx($model, "is_damaged"); ?>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <?php echo $form->labelEx($model, "code"); ?>
                    <?php echo $form->textArea($model, "code", array("class" => "form-control", "placeholder" => "code")); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'picture'); ?>
                    <?php echo $form->fileField($model, 'picture', array('class' => 'form-control', 'disabled' => 'disabled', 'placeholder' => 'picture')); ?>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 form-group text-center">
                <?php echo CHtml::resetButton('Reset', array('class' => 'btn btn-info')); ?>
                <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>