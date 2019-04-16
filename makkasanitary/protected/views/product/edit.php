<?php
$this->breadcrumbs = array(
    'Product' => array(AppUrl::URL_PRODUCT),
    'Edit'
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
                    $catList = CHtml::listData($categoryList, 'id', 'name');
                    echo $form->dropDownList($model, 'category_id', $catList, array('empty' => 'Select', 'class' => 'form-control'));
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
            <div class="col-md-2 col-sm-2">
                <div class="form-group">
                    <?php echo $form->labelEx($model, "size"); ?>
                    <?php echo $form->textField($model, "size", array("class" => "form-control", "placeholder" => "size")); ?>
                </div>
                <div class="form-group">
                    <?php
                    echo $form->labelEx($model, 'company group');
                    $companyHaedList = CompanyHead::model()->findAll("company_id=:company_id", array(":company_id" => $model->company_id));
                    $_attArr1 = array('empty' => 'Select Company !', 'class' => 'form-control');
                    if (!empty($companyHaedList)) {
                        $_attArr2 = array();
                    } else {
                        $_attArr2 = array('disabled' => 'disabled', 'class' => 'form-control error');
                    }
                    $newAttrArr = array_merge($_attArr1, $_attArr2);
                    $haedList = CHtml::listData($companyHaedList, 'id', 'value');
                    echo $form->dropDownList($model, 'type', $haedList, $newAttrArr);
                    ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'unit'); ?>
                    <?php echo $form->dropDownList($model, 'unit', AppHelper::getUnits(), array('empty' => 'Select', 'class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, "unitsize"); ?>
                    <?php echo $form->textField($model, "unitsize", array("class" => "form-control", "placeholder" => "unit size")); ?>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
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
                <?php echo CHtml::submitButton(Yii::t("strings", "Save"), array('class' => 'btn btn-primary')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        if ($("#Product_unitsize").val() == "") {
            //$("#Product_unitsize").val("");
            //disable("#Product_unit");
            disable("#Product_unitsize");
        } else {
            enable("#Product_unit");
            enable("#Product_unitsize");
        }

        $(document).on("change", "#Product_unit", function() {
            if ($(this).val() == "") {
                $("#Product_unitsize").val("");
                disable("#Product_unitsize");
            } else {
                enable("#Product_unitsize");
            }
        });

        $(document).on("change", "#Product_company_id", function() {
            showLoader("Fetching company heads...", true);
            var _url = ajaxUrl + "/company/findmeta";

            if ($(this).val() !== "") {
                $("#Product_type").removeClass('error');
                $.post(_url, {com_id: $(this).val()}, function(response) {
                    if (response.success === true) {
                        $("#Product_type").html(response.html);
                        enable("#Product_type");
                    } else {
                        $("#Product_type").html(response.html);
                        disable("#Product_type");
                    }
                    showLoader("", false);
                }, "json");
            } else {
                $("#Product_type").html("<option data-class='error'>Select Company !</option>").addClass('error');
                disable("#Product_type");
                showLoader("", false);
            }
        });
    });
</script>