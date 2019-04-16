<?php

class AppUser {

    const OPERATION_FAILED = '-1';
    const STATUS_UNVERIFIED = '0';
    const STATUS_VERIFIED = '1';
    const STATUS_BLOCKED = '2';
    const STATUS_TRASHED = '3';

    public $emailVerify = FALSE, $errorCode = '0', $errorMessage = '';

    public function auth($login, $pass, $autoLog = FALSE) {

        try {
            if (empty($login)) {
                throw new CException(Yii::t('strings', 'Login must be supplied'));
            }
            if (empty($pass)) {
                throw new CException(Yii::t('strings', 'Password must be supplied'));
            }

            $_auth = new UserIdentity($login, $pass);

            if (!$_auth->authenticate()) {
                throw new CException(Yii::t('strings', $_auth->getErrorMessage()));
            }

            Yii::app()->user->allowAutoLogin = $autoLog;
            Yii::app()->user->login($_auth);

            // Create log
            //$_userLogin = new UserLogin();
            //$_userLogin->ul_user_id = $_auth->getId();
            //$_userLogin->ul_time = AppHelper::getDbTimestamp();
            //$_userLogin->ul_ip = AppHelper::getUserIp();
            //$_userLogin->ul_client = AppHelper::getUserClient('json');
            //$_userLogin->ul_status = 1;
            //$_userLogin->save();
            // End of log

            return true;
        } catch (CException $e) {
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

    public function register($userModel = User, $repeatedPassword = FALSE) {
        try {
            if ($repeatedPassword != FALSE && $repeatedPassword != $userModel->user_password) {
                throw new CException(Yii::t('AppUser', 'Repeat password needs to be same as password'));
            }

            if (!$this->create($userModel)) {
                throw new CException(Yii::t('AppUser', $this->errorMessage));
            }

            return TRUE;
        } catch (CException $e) {
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    public function activateUser($login, $key) {
        try {
            if (empty($login)) {
                throw new CException(Yii::t('AppUser', 'User login is not supplied'));
            }

            if (empty($key)) {
                throw new CException(Yii::t('AppUser', 'User activation key is not supplied'));
            }

            $_userModel = new User;

            $_user = $_userModel->find('LOWER(user_login) = ?', array(strtolower(trim($login))));

            if (empty($_user->user_id)) {
                throw new CException(Yii::t('AppUser', 'Invalid request.'));
            }

            if (!empty($_user->user_status) && $_user->user_status != AppConstant::USER_STATUS_INACTIVE) {
                throw new CException(Yii::t('AppUser', 'This request is no longer valid for activation.'));
            }

            if (empty($_user->user_activation_key) || trim($_user->user_activation_key) != trim($key)) {
                throw new CException(Yii::t('AppUser', 'Activation key is not valid.'));
            }

            $_user->user_activated_at = AppHelper::getDbTimestamp();
            $_user->user_status = AppConstant::USER_STATUS_ACTIVE;

            $_user->save();

            return true;
        } catch (CException $e) {
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

    public function create($userModel = User) {
        $_retVal = FALSE;

        $userModel->user_created_at = AppHelper::getDbTimestamp();
        $userModel->user_key = AppHelper::getUnqiueKey();
        //$userModel->user_ip = AppHelper::getUserIp();
        $userModel->user_ip = AppHelper::getCleanValue($_POST['txtIP']);

        if (empty($userModel->user_role))
            $userModel->user_role = AppConstant::USER_NORMAL;

        $_transaction = Yii::app()->db->beginTransaction();

        try {
            if (empty($userModel->user_login)) {
                throw new CException(Yii::t('AppUser', 'User ID is required'));
            }

            if (empty($userModel->user_email)) {
                throw new CException(Yii::t('AppUser', 'Email is required'));
            }
            if (empty($userModel->user_password)) {
                throw new CException(Yii::t('AppUser', 'Password is required'));
            }
            if (empty($userModel->user_first_name)) {
                throw new CException(Yii::t('AppUser', 'First name is required'));
            }

            // user ID and email should be always in lower case
            $userModel->user_login = strtolower($userModel->user_login);
            $userModel->user_email = strtolower($userModel->user_email);

            // check if the user is existed with this email
            if (!$this->checkUserId($userModel->user_login)) {
                throw new CException(Yii::t('AppUser', 'An user is already existed with this ID'));
            }

            if (!$this->checkEmail($userModel->user_email)) {
                throw new CException(Yii::t('AppUser', 'An user is already existed with this email'));
            }

            // before create encrypt the password
            $userModel->user_password = $userModel->hashPassword($userModel->user_password);

            if ($this->emailVerify) {
                // let's set the status to hold the user until verification
                $userModel->user_status = self::STATUS_UNVERIFIED;
                $userModel->user_activation_key = AppHelper::getUnqiueKey();
            }

            if ($userModel->save()) {
                $_userId = Yii::app()->db->getLastInsertID();
                $_userRoleModel = new UserRole;

                $_userRoleModel->user_id = $_userId;
                $_userRoleModel->id = $userModel->user_role;
                $_userRoleModel->created_at = AppHelper::getDbTimestamp();

                $_userRoleModel->save();
            }

            if ($this->emailVerify) {
                // send verification email
                $_dataModel = array();

                $_activateUrl = Yii::app()->createAbsoluteUrl(AppConstant::URL_USER_ACTIVATE);

                $_dataModel['recipient'] = $userModel->user_login;
                $_dataModel['activationLink'] = $_activateUrl . '/?login=' . $userModel->user_login . '&key=' . $userModel->user_activation_key;

                $_mail = new AppMail; // default is 'YiiMail'

                $_msgBody = Yii::app()->controller->renderInternal(Yii::app()->getViewPath() . '/mail_templates/signup_activation.php', $_dataModel, true);
                $_mail->html = $_msgBody;
                $_mail->subject = $userModel->user_login . ", Welcome to Alzheimers Diet";
                $_mail->to = $userModel->user_email;
                $_mail->from = AppConstant::MAIL_SENDER_EMAIL;

                if (!$_mail->sendMessage()) {
                    //throw new CException(Yii::t('AppUser', 'Error while registration. Please try later.'));
                    throw new CException(Yii::t('AppUser', $_mail->getErrorMessage()));
                }

                /*
                  $message = new YiiMailMessage;
                  $message->view = 'signup_activation';

                  //userModel is passed to the view
                  $message->setBody($_dataModel, 'text/html');
                  $message->setSubject($userModel->user_login . ", Welcome to Alzheimers Diet");
                  $message->setTo(array($userModel->user_email => $userModel->user_login));
                  $message->setFrom(array(AppConstant::MAIL_SENDER_EMAIL => AppConstant::MAIL_SENDER_NAME));

                  Yii::app()->mail->send($message);
                 * 
                 */
            }

            $_transaction->commit();
            return true;
        } catch (CException $e) {
            $_transaction->rollback();
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }
    
    public function recoverPassword($login, &$activationUrl) {

        try {
            if (empty($login)) {
                throw new CException(Yii::t('AppUser', 'Login/Email must be supplied'));
            }

            $_user = null;

            // initiate model
            $_userModel = new User;

            if (strpos($login, '@') > 0) {
                $_user = $_userModel->find('LOWER(user_email)=?', array(strtolower($login)));
            } else {
                $_user = $_userModel->find('LOWER(user_login)=?', array(strtolower($login)));
            }

            if (empty($_user->user_id) || (int) $_user->user_id <= 0) {
                throw new CException(Yii::t('AppUser', 'No account found with this login/email'));
            }

            // create the password request
            $_passwordModel = new Password;

            $_passwordModel->pw_user_id = $_user->user_id;
            $_passwordModel->pw_activation_key = AppHelper::getUnqiueKey();
            $_passwordModel->pw_requested_at = AppHelper::getDbTimestamp();
            $_passwordModel->pw_request_ip = AppHelper::getUserIp();

            $_passwordModel->save();

            // send verification email
            $activationUrl = AppConstant::URL_PASSWORD_ACTIVATE . '/?key=' . $_passwordModel->pw_activation_key;

            $_dataModel = array();
            $_dataModel['recipient'] = $_user->user_login;
            $_dataModel['recipientEmail'] = $_user->user_email;

            $_dataModel['activationLink'] = Yii::app()->createAbsoluteUrl($activationUrl);

            $_mail = new AppMail('mandrill');

            $_msgBody = Yii::app()->controller->renderInternal(Yii::app()->getViewPath() . '/mail_templates/password_activation.php', $_dataModel, true);
            $_mail->html = $_msgBody;
            $_mail->subject = "Password Recovery - BMA System";
            $_mail->to = $_user->user_email;
            $_mail->from = AppConstant::MAIL_SENDER_EMAIL;

            if (!$_mail->sendMessage()) {
                //throw new CException(Yii::t('AppUser', 'Error while reseting your password. Please try later.'));
                throw new CException(Yii::t('AppUser', $_mail->getErrorMessage()));
            }

            return true;
        } catch (CException $e) {
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

    public function activatePasswordRequest($key) {
        try {
            if (empty($key)) {
                throw new CException(Yii::t('AppUser', 'Password activation key is not supplied'));
            }

            $_passwordModel = new Password;

            $_pass = $_passwordModel->find('LOWER(pw_activation_key)=?', array(strtolower(trim($key))));

            if (empty($_pass->pw_id)) {
                throw new CException(Yii::t('AppUser', 'This is an invalid request. Please request again and follow the supplied link.'));
            }

            if ($_pass->pw_status != '0') {
                throw new CException(Yii::t('AppUser', 'This request is no longer available for activation. Please request again.'));
            }

            $_pass->pw_activated_at = AppHelper::getDbTimestamp();
            $_pass->pw_activation_ip = AppHelper::getUserIp();
            $_pass->pw_status = '1';

            $_pass->save();

            return true;
        } catch (CException $e) {
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

    public function savePassword($activationKey, $newPassword, $repeatPassword) {
        // begin transaction
        $_transaction = Yii::app()->db->beginTransaction();

        try {
            if (empty($activationKey)) {
                throw new CException(Yii::t('AppUser', 'Somehow, your request is invalid. Please contact with administrator.'));
            }

            if (empty($newPassword)) {
                throw new CException(Yii::t('AppUser', 'New password must be supplied'));
            }

            if (empty($repeatPassword)) {
                throw new CException(Yii::t('AppUser', 'Repeat password must be supplied'));
            }

            if ($repeatPassword !== $newPassword) {
                throw new CException(Yii::t('AppUser', 'Repeat password is not same as new password'));
            }

            $_passwordModel = new Password;

            $_pass = $_passwordModel->find('LOWER(pw_activation_key)=?', array(strtolower(trim($activationKey))));

            if (empty($_pass->pw_id)) {
                throw new CException(Yii::t('AppUser', 'This is an invalid request. Please request again and follow the supplied link.'));
            }

            if ($_pass->pw_status != '1') {
                throw new CException(Yii::t('AppUser', 'This is not activated request. Please request again and follow the supplied link.'));
            }

            if (empty($_pass->pw_user_id)) {
                throw new CException(Yii::t('AppUser', 'This request is invalid.'));
            }

            $_objUserModel = new User;

            $_hashPass = $_objUserModel->hashPassword($newPassword);
            $_pass->pw_password = $_hashPass;
            $_pass->pw_created_at = AppHelper::getDbTimestamp();
            $_pass->save();

            if ($_pass->save()) {
                $_objUser = $_objUserModel->findByPk($_pass->pw_user_id);

                if (empty($_objUser->user_id)) {
                    throw new CException(Yii::t('AppUser', 'Error while saving your password. Please try later.'));
                }

                $_objUser->user_password = $_hashPass;
                $_objUser->save();
            }

            $_transaction->commit();
            return true;
        } catch (CException $e) {
            $_transaction->rollback();
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

    public function changePassword($userId, $oldPassword, $newPassword, $repeatPassword) {
        // begin transaction
        $_transaction = Yii::app()->db->beginTransaction();

        try {
            if (empty($userId)) {
                throw new CException(Yii::t('AppUser', 'Change request does not contain any valid user id.'));
            }

            if (empty($oldPassword)) {
                throw new CException(Yii::t('AppUser', 'Old password is required for request validation.'));
            }

            if (empty($newPassword)) {
                throw new CException(Yii::t('AppUser', 'New password must be supplied.'));
            }

            if (empty($repeatPassword)) {
                throw new CException(Yii::t('AppUser', 'Repeat password must be supplied.'));
            }

            if ($repeatPassword !== $newPassword) {
                throw new CException(Yii::t('AppUser', 'Repeat password is not same as new password.'));
            }

            $_userModel = new User;
            $_hashPass = $_userModel->hashPassword($newPassword);

            // see if the user is really owns the old password
            $_user = $_userModel->findByPk($userId);

            if (!$_user->validatePassword($oldPassword)) {
                throw new CException(Yii::t('AppUser', 'Please make sure your old password is correct.'));
            }

            if (empty($_user->user_status) OR $_user->user_status != AppConstant::USER_STATUS_ACTIVE) {
                throw new CException(Yii::t('AppUser', 'You are not active user to change your password.'));
            }

            // create the password in the password table first
            $_passwordModel = new Password;

            $_passwordModel->pw_user_id = $_user->user_id;
            $_passwordModel->pw_password = $_hashPass;
            $_passwordModel->pw_requested_at = AppHelper::getDbTimestamp();
            $_passwordModel->pw_request_ip = AppHelper::getUserIp();
            $_passwordModel->pw_created_at = AppHelper::getDbTimestamp();
            $_passwordModel->pw_status = AppConstant::PASSWORD_STATUS_ACTIVE;

            if ($_passwordModel->save()) {
                $_user->user_password = $_hashPass;
                $_user->save();
            }

            $_transaction->commit();
            return true;
        } catch (CException $e) {
            $_transaction->rollback();
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

    public function deleteUser($id = 0) {
        try {
            if ((int) $id <= 0) {
                throw new CException(Yii::t('AppUser', 'ID is required to delete'));
            }

            $_objUserModel = new User;
            $_isDeleted = $_objUserModel->deleteByPk($id);

            if (!$_isDeleted) {
                throw new CException(Yii::t('AppUser', 'Unable to delete record'));
            }

            return true;
        } catch (CException $e) {
            $this->errorCode = self::OPERATION_FAILED;
            $this->errorMessage = $e->getMessage();
            return false;
        }
        return false;
    }

    public static function getUser($condition = array()) {
        $_userModel = new User;
        return $_userModel->findAllByAttributes($condition);
    }

    public function getList() {
        //primary_role
        $_users = Yii::app()->db->createCommand()
                ->select('*')
                ->from('view_users_with_role')
                ->queryAll();

        return $_users;
    }

    public static function getDisplayName(&$login = '', &$fname = '', &$lname = '') {
        $_retName = '';

        if (empty($fname) && empty($lname)) {
            $_retName = $login;
        } else {
            $_retName = $fname . ' ' . $lname;
        }

        return trim($_retName);
    }

    /* private function */

    private function _afterAuth($auth = '') {
        $mail = new AppMailgun();

        $mail->to = 'snasim@gmail.com';
        $mail->html = 'You just have logged in';

        echo $mail->sendMessage();
    }

    /* Callback for check if available */

    public function checkUserId($login = '-1') {
        $_user = User::model()->find('LOWER(user_login)=?', array(strtolower($login)));
        return empty($_user->user_id);
    }

    public function checkEmail($email = '-1') {
        $_user = User::model()->find('LOWER(user_email)=?', array(strtolower($email)));
        return empty($_user->user_id);
    }

}