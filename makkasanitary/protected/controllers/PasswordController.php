<?php

class PasswordController extends AppController {

    //public $layout = 'home';

    public function beforeAction($action) {
        return true;
    }

    public function actionIndex() {
        $this->redirect(array(AppUrl::URL_PASSWORD_CHANGE));
    }

    public function actionChange() {
        $this->actionAuthorized();
        $this->setHeadTitle("Change Password");
        $this->setPageTitle("Change Password");
        $this->setCurrentPage(AppUrl::URL_PASSWORD_CHANGE);

        $_model = new User();
        $_isPosted = Yii::app()->request->getPost('btnSave');

        if (!empty($_isPosted)) {
            if (isset($_POST['txtPassword'])) {
                $_pass = Yii::app()->request->getPost('txtPassword');
                $_newPass = Yii::app()->request->getPost('txtNewPassword');
                $_repeatPassword = Yii::app()->request->getPost('txtReNewPassword');

                $_transaction = Yii::app()->db->beginTransaction();
                try {
//                    if (empty($_id)) {
//                        throw new CException(Yii::t('AppUser', "Change request does not contain any valid user !"));
//                    }

                    if (empty($_pass)) {
                        throw new CException(Yii::t('AppUser', 'Old password is required for request validation.'));
                    }

                    if (empty($_newPass)) {
                        throw new CException(Yii::t('AppUser', 'New password must be supplied.'));
                    }

                    if (empty($_repeatPassword)) {
                        throw new CException(Yii::t('AppUser', 'Repeat password must be supplied.'));
                    }

                    if ($_repeatPassword !== $_newPass) {
                        throw new CException(Yii::t('AppUser', 'Repeat password is not same as new password.'));
                    }

                    $_user = $_model->findByPk(Yii::app()->user->id);

                    if (!$_user->validatePassword($_pass)) {
                        throw new CException(Yii::t('AppUser', 'Your old password is not correct.'));
                    }

                    if (empty($_user->status) OR $_user->status != AppConstant::STATUS_ACTIVE) {
                        throw new CException(Yii::t('AppUser', 'You are not active user to change your password.'));
                    }

                    $_user->password = $_user->hashPassword($_newPass);
                    $_user->save();

                    Yii::app()->user->setFlash('success', 'Password has been changed successfully.');
                    $_transaction->commit();
                } catch (CException $e) {
                    $_transaction->rollback();
                    Yii::app()->user->setFlash('danger', $e->getMessage());
                }
            }
        }

        $this->render('change');
    }

    public function actionRecover() {
        if ($this->isLoggedIn()) {
            $this->redirect($this->createUrl(AppUrl::URL_USER_PROFILE));
            Yii::app()->end();
        }

        $this->setHeadTitle("Password Recover");
        $this->setCurrentPage(AppUrl::URL_PASSWORD_RECOVER);

        if (isset($_POST['btnSend'])) {
            if (isset($_POST['txtEmail'])) {
                $_email = Yii::app()->request->getPost('txtEmail');
                $_user = User::model()->findByAttributes(array('email' => $_email));

                $_transaction = Yii::app()->db->beginTransaction();
                try {
                    if (!empty($_user)) {
                        $_user->status = AppConstant::STATUS_INACTIVE;
                        $_user->password_token = md5(AppHelper::getUnqiueKey());
                        $_user->password_request_ip = AppHelper::getUserIp();
                        $_user->save();

                        $_dataModel = array();
                        $activationUrl = AppUrl::URL_PASSWORD_RESET . '/?key=' . $_user->password_token;
                        $_dataModel['site'] = AppHelper::getParamValue("appName");
                        $_dataModel['recipient'] = !empty($_user->login) ? $_user->login : $_user->email;
                        $_dataModel['recipientEmail'] = $_email;
                        $_dataModel['activationLink'] = Yii::app()->createAbsoluteUrl($activationUrl);
                        $_msgBody = Yii::app()->controller->renderInternal(Yii::app()->getViewPath() . '/mail_templates/reset_pass.php', $_dataModel, true);

                        /*
                          $_mail = new AppMail('mandrill');
                          $_mail->html = $_msgBody;
                          $_mail->subject = AppHelper::getParamValue("appName") . " - Forget Password Request";
                          $_mail->to = $_email;
                          $_mail->from = AppHelper::getParamValue("appName");

                          if (!$_mail->sendMessage()) {
                          throw new CException(Yii::t('App', $_mail->getErrorMessage()));
                          }
                         */

                        $_mail_to = AppConstant::MAIL_SENDER_EMAIL;
                        $_mail_subject = 'Reset your password';
                        $_mail_headers = "From: " . AppConstant::MAIL_SENDER_EMAIL . "\r\n";
                        $_mail_headers = "To: " . $_email . "\r\n";
                        $_mail_headers .= "Reply-To: " . AppConstant::MAIL_SENDER_EMAIL . "\r\n";
                        $_mail_headers .= "MIME-Version: 1.0\r\n";
                        $_mail_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $_mail_message = $_msgBody;

                        if (!mail($_mail_to, $_mail_subject, $_mail_message, $_mail_headers)) {
                            throw new CException(Yii::t("App", "Error while sending mail."));
                        }

                        Yii::app()->user->setFlash("success", "A mail has been sent to " . "<b>$_email</b>");
                    } else {
                        Yii::app()->user->setFlash("warning", "No user found with given email: <b>$_email</b>");
                    }

                    $_transaction->commit();
                } catch (CException $e) {
                    $_transaction->rollback();
                    Yii::app()->user->setFlash('error', $e->getMessage());
                }
            }
        }

        $this->render('recover');
    }

    public function actionReset() {
        $this->setHeadTitle("Reset Password");
        //$this->setPageTitle("Reset Password");
        //$this->setCurrentPage(AppUrl::URL_PASSWORD_RESET);

        if ($this->isLoggedIn()) {
            $this->redirect($this->createUrl(AppUrl::URL_USER_PROFILE));
            Yii::app()->end();
        }

        $_key = Yii::app()->request->getParam('key');

        if (empty($_key)) {
            Yii::app()->user->setFlash("info", "Illegal operation.");
            $this->redirect($this->createUrl(AppUrl::URL_SITE_MESSAGE));
            Yii::app()->end();
        }

        $_model = new User();
        $_user = $_model->find('LOWER(password_token) = ?', array(strtolower($_key)));

        if (empty($_user)) {
            Yii::app()->user->setFlash("info", "Invalid url.");
            $this->redirect($this->createUrl(AppUrl::URL_SITE_MESSAGE));
            Yii::app()->end();
        } else {
            if (isset($_POST['btnSave'])) {
                $_new_pass = Yii::app()->request->getPost('txtNewPassword');
                $_new_pass_repeat = Yii::app()->request->getPost('txtReNewPassword');
                $_user->password_token = NULL;
                $_user->password = $_user->hashPassword($_new_pass);
                $_user->password_reset_ip = AppHelper::getUserIp();
                $_user->password_reset_time = AppHelper::getDbTimestamp();
                $_user->status = AppConstant::STATUS_ACTIVE;

                $_transaction = Yii::app()->db->beginTransaction();
                try {
                    if ($_new_pass_repeat != $_new_pass) {
                        throw new CException(Yii::t('App', 'Please repeat passwort exactly.'));
                    }

                    if (!$_user->save()) {
                        throw new CException(Yii::t('App', 'Error while saving data'));
                    }

                    $_transaction->commit();
                    Yii::app()->user->setFlash('success', 'Password reset successfull. You can login now with new password.');
                    $this->redirect(array(AppUrl::URL_LOGIN));
                } catch (CException $e) {
                    $_transaction->rollback();
                    Yii::app()->user->setFlash('error', $e->getMessage());
                }
            }

            $this->render('reset');
        }
    }

}
