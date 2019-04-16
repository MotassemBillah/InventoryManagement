<?php

class WebUser extends CWebUser {

    public function beforeLogout() {
        if (parent::beforeLogout()) {
            $user = User::model()->findByPk(Yii::app()->user->id);
            $user->is_loggedin = 0;
            $user->saveAttributes(array('is_loggedin'));
            return true;
        } else {
            return false;
        }
    }

}
