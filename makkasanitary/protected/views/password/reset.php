<div class="row" style="margin-top: 50px;">
    <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><?php echo Yii::t('strings', 'Reset Password'); ?></h4>
            </div>
            <div class="panel-body">
                <form id="frmPassReset" name="frmPassReset" action="" method="post" class="user-form">
                    <div class="form-group">
                        <label for="txtNewPassword"><?php echo Yii::t('strings', 'New Password'); ?></label>
                        <input type="password" class="form-control" id="txtNewPassword" name="txtNewPassword" placeholder="New Password" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="txtReNewPassword"><?php echo Yii::t('strings', 'Repeat Password'); ?></label>
                        <input type="password" class="form-control" id="txtReNewPassword" name="txtReNewPassword" placeholder="Repeat Password" autocomplete="off">
                    </div>

                    <div class="form-action">
                        <input class="btn btn-info" type="reset" value="<?php echo Yii::t('strings', 'Reset'); ?>">
                        <input class="btn btn-primary" type="submit" id="btnSave" name="btnSave" value="<?php echo Yii::t('strings', 'Save Change'); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>