<div class="row" style="margin-top: 50px;">
    <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><?php echo Yii::t('strings', 'Password Recover'); ?></h4>
            </div>
            <div class="panel-body">
                <form id="frmPassRecover" action="" method="post" class="user-form">
                    <div class="form-group">
                        <label class="control-label" for="txtEmail"><?php echo Yii::t('strings', 'Your Email'); ?></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope fa-lg" aria-hidden="true"></i></span>
                            <input type="email" class="form-control" id="txtEmail" name="txtEmail" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-action">
                            <input type="button" id="btnCancel" class="btn btn-info" onclick="history.back();" value="<?php echo Yii::t('strings', 'Cancel'); ?>" name="btnCancel"/>
                            <input type="submit" class="btn btn-primary" id="btnSend" name="btnSend" value="<?php echo Yii::t('strings', 'Submit'); ?>"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#frmPassRecover').validate({
        rules: {
            txtEmail: {
                required: true,
                email: true
            }
        },
        messages: {
            txtEmail: {
                required: "Email must be supplied!"
            }
        }
    });
</script>