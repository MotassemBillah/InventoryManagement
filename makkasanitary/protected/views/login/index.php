<div class="row" style="margin-top: 50px;">
    <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
        <div id="ajaxHandler">
            <div id="ajaxMessage" class="alert"></div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><?php echo Yii::t("strings", "User Login"); ?></h4>
            </div>
            <div class="panel-body">
                <?php
                $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'frmLogin',
                    'enableClientValidation' => true,
                    'clientOptions' => array(
                        'validateOnSubmit' => true,
                    ),
                ));
                ?>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user fa-lg" aria-hidden="true"></i></span>
                        <?php echo $form->textField($model, 'username', array('class' => 'form-control', 'placeholder' => 'Username')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                        <?php echo $form->passwordField($model, 'password', array('class' => 'form-control', 'placeholder' => 'Password')); ?>
                        <span class="input-group-addon" id="show_charecter"><i class="fa fa-eye-slash fa-lg"></i></span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->checkBox($model, 'rememberMe', array('class' => 'chk_no_mvam')); ?>
                    <?php echo $form->label($model, Yii::t('strings', 'Remember Me'), array('class' => 'no_mrgn')); ?>
                </div>
                <div class="form-group form-action">
                    <?php echo CHtml::submitButton(Yii::t('strings', 'Login'), array('class' => 'btn btn-primary btn-block')); ?>
                </div>
                <div class="text-center">
                    <?php echo CHtml::Link(Yii::t('strings', 'I cannot access my account'), array(AppUrl::URL_PASSWORD_RECOVER), array('class' => 'btn btn-link no_pad')); ?>
                </div>
                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</div>