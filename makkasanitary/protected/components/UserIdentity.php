<?php

class UserIdentity extends CUserIdentity {

    private $_id;

    const ERROR_UNVERIFIED_ACCOUNT = 3;

    public function authenticate() {
        $user = User::model()->find('LOWER(email)=?', array(strtolower($this->username)));

        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if (!$user->validatePassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else if ($user->status == 0) {
            $this->errorCode = self::ERROR_UNVERIFIED_ACCOUNT;
        } else {
            $this->_id = $user->id;
            $this->setState('role', $user->role);
            $this->setState('userData', $user);
            $this->errorCode = self::ERROR_NONE;

            $user->lastlogin = AppHelper::getDbTimestamp();
            $user->is_loggedin = 1;
            $user->save();
        }

        return $this->errorCode == self::ERROR_NONE;
    }

    public function getId() {
        return $this->_id;
    }

    public static function getRole() {
        return Yii::app()->user->getState('role');
    }

    public static function isDeletable() {
        if (Yii::app()->user->getState('userData')->deletable == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getErrorMessage() {
        switch ($this->errorCode) {
            case self::ERROR_PASSWORD_INVALID:
                return "Your password is not valid";
                break;
            case self::ERROR_UNKNOWN_IDENTITY:
                return "No account found with this email";
                break;
            case self::ERROR_USERNAME_INVALID:
                return "No account found with this email";
                break;
            case self::ERROR_UNVERIFIED_ACCOUNT:
                return "Unverified or unauthorized account!";
                break;
            default:
                return "";
                break;
        }
    }

    public static function displayname() {
        $displayName = "";
        if (Yii::app()->user->isGuest) {
            $displayName = "Guest";
        } else {
            $userdata = Yii::app()->user->getState('userData');
            if (!empty($userdata->login)) {
                $displayName = $userdata->login;
            } else {
                $displayName = Yii::app()->user->name;
            }
        }

        return $displayName;
    }

}
