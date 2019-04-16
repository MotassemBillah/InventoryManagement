<div class="row" style="">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmContact',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>
        <div class="form-group">
            <?php echo $form->labelEx($model, 'username'); ?>
            <?php echo $form->textField($model, 'username', array('class' => 'form-control')); ?>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model, 'email'); ?>
            <?php echo $form->textField($model, 'email', array('class' => 'form-control')); ?>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model, 'subject'); ?>
            <?php echo $form->textField($model, 'subject', array('class' => 'form-control')); ?>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model, 'message'); ?>
            <?php echo $form->textArea($model, 'message', array('class' => 'form-control')); ?>
        </div>
        <div class="form-group form-action">
            <?php echo CHtml::submitButton(Yii::t('app', 'Send'), array('class' => 'btn btn-success btn-block', 'name' => 'submitContact')); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>