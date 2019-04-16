<?php
$this->breadcrumbs = array(
    'Company' => array(AppUrl::URL_COMPANY),
    'Edit'
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
                <?php
                if (!empty($model->heads) && count($model->heads) > 0):
                    $count = 6 - count($model->heads);
                    foreach ($model->heads as $companyhead):
                        ?>
                        <div class="form-group" style="position: relative;">
                            <input type="text" name="company_meta_option[]" class="form-control" value="<?php echo $companyhead->value; ?>">
                            <input type="hidden" name="acKey[]" value="<?php echo AppHelper::getCleanValue($companyhead->id); ?>">
                            <a class="icon_del delete_meta" href="javascript://" data-info="<?php echo $companyhead->id; ?>"><i class="fa fa-trash-o"></i></a>
                        </div>
                    <?php endforeach; ?>
                    <?php for ($i = 0; $i < $count; $i++): ?>
                        <div class="form-group company_meta_option_add_<?php echo $i; ?>">
                            <input type="text" name="company_meta_option_new[]" class="form-control">
                        </div>
                    <?php endfor; ?>
                <?php else: ?>
                    <?php for ($i = 0; $i < 6; $i++): ?>
                        <div class="form-group company_meta_option_new_<?php echo $i; ?>">
                            <input type="text" name="company_meta_option_new[]" class="form-control">
                        </div>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-9 form-group text-center">
            <?php echo CHtml::submitButton(Yii::t('strings', 'Save'), array('class' => 'btn btn-primary')); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", ".delete_meta", function(e) {
            var $this = $(this);
            var _rc = confirm('Are you sure about this action? This cannot be undone!');

            if (_rc === true) {
                showLoader("Processing...", true);
                var _url = ajaxUrl + '/company/deletemeta';
                $.post(_url, {meta_id: $this.attr('data-info')}, function(res) {
                    if (res.success === true) {
                        $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                        $("#ajaxMessage").html(res.message).show();
                        $this.parent().remove();
                        setTimeout(hide_ajax_message, 3000);
                    } else {
                        $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
                        $("#ajaxMessage").html(res.message).show();
                    }
                    showLoader("", false);
                }, "json");
            } else {
                return false;
            }
            e.preventDefault();
        });
    });
</script>