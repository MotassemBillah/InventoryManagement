<div class="row" style="margin-top: 50px;">
    <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmRegister',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>
        <div class="form-group">
            <?php echo $form->labelEx($model, 'username'); ?>
            <?php echo $form->textField($model, 'user_username', array('class' => 'form-control', 'placeholder' => 'Username')); ?>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model, 'email'); ?>
            <?php echo $form->textField($model, 'user_email', array('class' => 'form-control', 'placeholder' => 'Email')); ?>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model, 'password'); ?>
            <?php echo $form->passwordField($model, 'user_password', array('class' => 'form-control', 'placeholder' => 'Password', 'autocomplete' => 'off')); ?>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model, 'repeat_password'); ?>
            <?php echo $form->passwordField($model, 'repeat_password', array('class' => 'form-control', 'placeholder' => 'Password')); ?>
        </div>
        <div class="form-group form-action">
            <?php echo CHtml::submitButton(Yii::t('app', 'Create Account'), array('class' => 'btn btn-success btn-block')); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>