<?php
$this->breadcrumbs = array(
    'User' => array(AppUrl::URL_USERLIST),
    'Profile'
);
?>
<div class="content-panel" id="">
    <div class="row clearfix">
        <div class="col-md-2 col-sm-3">
            <div class="thumbnail">
                <img alt="<?php echo AppObject::displayName($user); ?>" class="img-responsive" src="<?php echo AppObject::getAvatar($user); ?>">
            </div>
            <?php if ($adminEdit === true): ?>
                <a class="btn btn-primary btn_sm_block" href="<?php echo $this->createUrl(AppUrl::URL_USER_ADMIN_EDIT, array('id' => $user->_key)); ?>" id="btnEdit"><?php echo Yii::t('strings', 'Edit'); ?></a>
            <?php else: ?>
                <a class="btn btn-primary btn_sm_block" href="<?php echo $this->createUrl(AppUrl::URL_USER_EDIT); ?>" id="btnEdit"><?php echo Yii::t('strings', 'Edit'); ?></a>
            <?php endif; ?>
        </div>
        <div class="col-md-4 col-sm-4 form-group profile_view">
            <p><strong>Username :</strong> <?php echo!empty($user->login) ? $user->login : ''; ?></p>
            <p><strong>First Name :</strong> <?php echo!empty($user->profile->firstname) ? $user->profile->firstname : ''; ?></p>
            <p><strong>Last Name :</strong> <?php echo!empty($user->profile->lastname) ? $user->profile->lastname : ''; ?></p>
            <p><strong>Email :</strong> <?php echo $user->email; ?></p>
            <p><strong>Phone :</strong> <?php echo!empty($user->profile->phone) ? $user->profile->phone : ''; ?></p>
            <p><strong>Address :</strong> <?php echo $user->profile->address; ?></p>
            <p><strong>Gender :</strong> <?php echo!empty($user->profile->gender) ? $user->profile->gender : ''; ?></p>
            <p><strong>Joined :</strong> <?php echo date("j M Y", strtotime($user->created)); ?></p>
            <p><strong>Created By :</strong> <?php echo!empty($user->created_by) ? AppObject::displayNameByUser($user->created_by) : 'Self'; ?></p>
            <p><strong>Last Update :</strong> <?php echo date("j M Y, h:i", strtotime($user->modified)); ?></p>
            <p><strong>Update By :</strong> <?php echo!empty($user->modified_by) ? AppObject::displayNameByUser($user->modified_by) : 'Self'; ?></p>
            <p><strong>Last Activity :</strong> <?php echo!empty($user->lastlogin) ? date("j M Y, h:i", strtotime($user->lastlogin)) : ''; ?></p>
        </div>
    </div>
</div>