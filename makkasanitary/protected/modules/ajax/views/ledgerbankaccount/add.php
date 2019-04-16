<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close"><span aria-hidden="true">x</span></button>
            <h4 class="modal-title"><u><?php echo Yii::t("strings", "Amount Available"); ?></u>&nbsp;:&nbsp;<?php echo AppHelper::getFloat($model->sumBalance); ?>&nbsp;Tk</h4>
            <div id="ajaxModalMessage" class="alert" style="display: none"></div>
        </div>
        <div class="modal-body" style="overflow-y: auto;min-height: 200px;max-height: 440px;">
            <form id="frmAccountBalance" action="" method="post">
                <input type="hidden" name="accountID" value="<?php echo $model->id; ?>">
                <div class="clearfix">
                    <div class="form-group">
                        <label style="width: 150px;"><?php echo Yii::t("strings", "Bank Name"); ?></label>&nbsp;:&nbsp;<?php echo $model->bank_name; ?>
                    </div>
                    <div class="form-group">
                        <label style="width: 150px;"><?php echo Yii::t("strings", "Account Name"); ?></label>&nbsp;:&nbsp;<?php echo $model->account_name; ?>
                    </div>
                    <div class="form-group">
                        <label style="width: 150px;"><?php echo Yii::t("strings", "Account Number"); ?></label>&nbsp;:&nbsp;<?php echo $model->account_number; ?>
                    </div>
                    <div class="form-group">
                        <label for="account_balance" class="required">Add Amount <span class="required">*</span></label>
                        <div class="input-group">
                            <input class="form-control" name="account_balance" id="account_balance" type="text" placeholder="Amount">
                            <span class="input-group-addon">Tk</span>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <input class="btn btn-primary btn-block" type="submit" name="yt0" value="Save">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close" title="Close"><?php echo Yii::t("strings", "Close"); ?></button>
        </div>
    </div>
</div>