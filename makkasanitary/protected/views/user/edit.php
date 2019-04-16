<?php
$this->breadcrumbs = array(
    'Users' => array(AppUrl::URL_USERLIST),
    'Edit'
);
?>
<div class="content-panel" id="">
    <form action="" enctype="multipart/form-data" method="post">
        <div class="row clearfix">
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="txtUsername"><?php echo Yii::t('strings', 'Username'); ?></label>
                    <input type="text" class="form-control" id="txtUsername" name="txtUsername" value="<?php echo!empty($user->login) ? $user->login : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="txtFname"><?php echo Yii::t('strings', 'First Name'); ?></label>
                    <input type="text" class="form-control" id="txtFname" name="txtFname" value="<?php echo!empty($user->profile->firstname) ? $user->profile->firstname : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="txtLname"><?php echo Yii::t('strings', 'Last Name'); ?></label>
                    <input type="text" class="form-control" id="txtLname" name="txtLname" value="<?php echo!empty($user->profile->lastname) ? $user->profile->lastname : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="txtEmail"><?php echo Yii::t('strings', 'Email'); ?></label>
                    <input type="text" class="form-control" id="txtEmail" name="txtEmail" value="<?php echo $user->email; ?>">
                </div>
                <div class="form-group">
                    <label for="gender"><?php echo Yii::t('strings', 'Gender'); ?></label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="<?php echo AppConstant::GENDER_MALE; ?>"<?php if ($user->profile->gender == AppConstant::GENDER_MALE) echo ' selected="selected"'; ?>><?php echo AppConstant::GENDER_MALE; ?></option>
                        <option value="<?php echo AppConstant::GENDER_FEMALE; ?>"<?php if ($user->profile->gender == AppConstant::GENDER_FEMALE) echo ' selected="selected"'; ?>><?php echo AppConstant::GENDER_FEMALE; ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label for="txtPhone"><?php echo Yii::t('strings', 'Phone'); ?></label>
                    <input type="text" class="form-control" id="txtPhone" name="txtPhone" value="<?php echo!empty($user->profile->phone) ? $user->profile->phone : ''; ?>">
                </div>
                <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                    <div class="form-group">
                        <label for="role"><?php echo Yii::t('strings', 'Group'); ?></label>
                        <?php
                        $roleList = Role::model()->getList();
                        $list = CHtml::listData($roleList, 'id', 'name');
                        echo CHtml::dropDownList('role', $user->role, $list, array('class' => 'form-control'));
                        ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="txtAddress"><?php echo Yii::t('strings', 'Address'); ?></label>
                    <textarea class="form-control" id="txtAddress" name="txtAddress" placeholder="Address"><?php echo!empty($user->profile->address) ? $user->profile->address : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="file">Upload Profile Image</label>
                    <input type="file" class="form-control" name="file" id="file">
                </div>
            </div>
            <div class="col-md-2 col-sm-3">
                <label>Avatar</label>
                <div class="thumbnail">
                    <img alt="<?php echo AppObject::displayName($user); ?>" class="img-responsive" src="<?php echo AppObject::getAvatar($user); ?>">
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-8 form-group text-center">
            <input type="button" class="btn btn-info" value="<?php echo Yii::t('strings', 'View'); ?>" onclick="redirectTo('<?php echo Yii::app()->createUrl(AppUrl::URL_USER_PROFILE); ?>')"/>
            <input type="submit" class="btn btn-primary" id="saveProfile" name="saveProfile" value="<?php echo Yii::t('strings', 'Save'); ?>"/>
        </div>
    </form>
</div>

