<?php $this->breadcrumbs = array('Change Password'); ?>
<div class="row clearfix">
    <div class="col-md-4 col-sm-6" id="">
        <form id="frmPassChange" name="frmPassChange" action="" method="post">
            <div class="form-group">
                <label class="control-label" for="txtPassword"><?php echo Yii::t('strings', 'Old Password'); ?></label>
                <div class="form-field">
                    <input type="password" id="txtPassword" class="form-control" name="txtPassword" placeholder="Old Password" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="txtNewPassword"><?php echo Yii::t('strings', 'New Password'); ?></label>
                <div class="form-field">
                    <input type="password" id="txtNewPassword" class="form-control" name="txtNewPassword" placeholder="New Password" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="txtReNewPassword"><?php echo Yii::t('strings', 'Repeat Password'); ?></label>
                <div class="form-field">
                    <input type="password" id="txtReNewPassword" class="form-control" name="txtReNewPassword" placeholder="Repeat Password" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <div class="text-center">
                    <input class="btn btn-primary" type="submit" id="btnSave" name="btnSave" value="<?php echo Yii::t('strings', 'Save Change'); ?>">
                </div>
            </div>
        </form>
    </div>
</div>