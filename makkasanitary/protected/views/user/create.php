<?php
$this->breadcrumbs = array(
    'Users' => array(AppUrl::URL_USERLIST),
    'Create'
);
?>
<div class="row content-panel">
    <div class="col-md-4 col-sm-6">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmUser',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
            'htmlOptions' => array('autocomplete' => "off")
        ));
        ?>
        <div class="clearfix">
            <div class="form-group">
                <?php echo $form->labelEx($model, Yii::t('strings', 'Username')); ?>
                <?php echo $form->textField($model, 'login', array('class' => 'form-control', ' autocomplete' => 'off')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, Yii::t('strings', 'Email')); ?>
                <?php echo $form->textField($model, 'email', array('class' => 'form-control', ' autocomplete' => 'off')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, Yii::t('strings', 'Password')); ?>
                <?php echo $form->passwordField($model, 'password', array('class' => 'form-control', ' autocomplete' => 'off')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model, Yii::t('strings', 'Group')); ?>
                <?php
                $roleList = Role::model()->findAll();
                $rlist = CHtml::listData($roleList, 'id', 'name');
                echo $form->dropDownList($model, 'role', $rlist, array('empty' => 'Select', 'class' => 'form-control', 'options' => array('2' => array('selected' => true))));
                ?>
            </div>
            <div class="form-group text-right">
                <?php echo CHtml::submitButton(Yii::t('strings', 'Create'), array('class' => 'btn btn-success btn-block')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>