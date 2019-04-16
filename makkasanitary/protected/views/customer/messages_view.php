<?php if (!empty($data) && count($data) > 0) : ?>
    <div class="row clearfix">
        <div class="col-md-8 profile_view">
            <form action="" id="msgUpdateForm" method="post">
                <div class="form-group clearfix">
                    <strong><?php echo Yii::t('strings', 'Name'); ?> : </strong>
                    <span><?php echo AppHelper::getCleanValue($data->name); ?></span>
                </div>
                <div class="form-group clearfix">
                    <strong><?php echo Yii::t('strings', 'Email'); ?> : </strong>
                    <span><?php echo AppHelper::getCleanValue($data->email); ?></span>
                </div>
                <div class="form-group clearfix">
                    <strong><?php echo Yii::t('strings', 'Phone'); ?> : </strong>
                    <span><?php echo AppHelper::getCleanValue($data->phone); ?></span>
                </div>
                <div class="form-group clearfix">
                    <strong><?php echo Yii::t('strings', 'Subject'); ?> : </strong>
                    <span><?php echo AppHelper::getCleanValue($data->subject); ?></span>
                </div>
                <div class="form-group clearfix">
                    <strong style="vertical-align: top;"><?php echo Yii::t('strings', 'Message'); ?> : </strong>
                    <div class="msg_view" style=""><?php echo $data->message; ?></div>
                </div>
                <div class="form-group clearfix">
                    <strong><?php echo Yii::t('strings', 'Status'); ?> : </strong>
                    <select id="status" class="form-control" name="status" style="display: inline-block;width: 120px;">
                        <option value="All">All</option>
                        <option value="<?php echo AppConstant::CONTACT_PENDING; ?>"<?php if ($data->status == AppConstant::CONTACT_PENDING) echo ' selected="selected"'; ?>><?php echo AppConstant::CONTACT_PENDING; ?></option>
                        <option value="<?php echo AppConstant::CONTACT_PROGRESS; ?>"<?php if ($data->status == AppConstant::CONTACT_PROGRESS) echo ' selected="selected"'; ?>><?php echo AppConstant::CONTACT_PROGRESS; ?></option>
                        <option value="<?php echo AppConstant::CONTACT_SOLVED; ?>"<?php if ($data->status == AppConstant::CONTACT_SOLVED) echo ' selected="selected"'; ?>><?php echo AppConstant::CONTACT_SOLVED; ?></option>
                    </select>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" name="updateMessage" value="Change">
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info"><?php echo Yii::t("strings", "No records to view"); ?></div>
<?php endif; ?>