<?php

class UserController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('user_list');
        $this->setHeadTitle("Users");
        $this->setPageTitle("User list");
        $this->setCurrentPage(AppUrl::URL_USERLIST);

        $_model = new User();
        $criteria = new CDbCriteria();
        $criteria->condition = "id<>:id";
        $criteria->addCondition("deletable=1");
        $criteria->addCondition("is_deleted=0");
        $criteria->params = array(":id" => Yii::app()->user->id);
        $criteria->order = "login ASC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);

        $_dataset = $_model->findAll($criteria);
        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        $this->checkUserAccess('user_create');
        $this->setHeadTitle("Users");
        $this->setPageTitle("Create User");
        $this->setCurrentPage(AppUrl::URL_USERLIST);
        $_model = new User();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmUser') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['User'])) {
            //$generated_password = AppHelper::getRandomPassword(6);
            $_password = $_POST['User']['password'];
            $_model->attributes = $_POST['User'];
            $_model->login = $_POST['User']['login'];
            $_model->email = $_POST['User']['email'];
            $_model->password = $_model->hashPassword($_password);
            $_model->ip = AppHelper::getUserIp();
            $_model->role = $_POST['User']['role'];
            $_model->status = AppConstant::USER_STATUS_INACTIVE;
            $_model->created = AppHelper::getDbTimestamp();
            $_model->created_by = Yii::app()->user->id;
            $_model->activation_token = md5(AppHelper::getUnqiueKey());
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("app", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("app", "Error while saving data."));
                }

                $lastUserID = Yii::app()->db->getLastInsertID();
                $_userProfile = new UserProfile();
                $_userProfile->user_id = $lastUserID;
                if (!$_userProfile->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $modelPermission = new Permission();
                $modelPermission->user_id = $lastUserID;
                $modelPermission->items = json_encode(AppHelper::userDefaultAccessItems());
                if (!$modelPermission->save()) {
                    throw new Exception(Yii::t('error', 'Error while saving user access data.'));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Data saved successfully!");
                $this->redirect(array(AppUrl::URL_USERLIST));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->render('create', array('model' => $_model));
    }

    public function actionEdit() {
        $this->checkUserAccess('profile_edit');
        $this->setHeadTitle("Users");
        $this->setCurrentPage(AppUrl::URL_USER_PROFILE);

        $_modelUser = new User();
        $_modelUserProfile = new UserProfile();
        $_objUser = $_modelUser->findByPk(Yii::app()->user->id);

        if (isset($_POST['saveProfile'])) {
            $_objUser->email = AppHelper::getCleanValue($_POST['txtEmail']);
            $_objUser->login = AppHelper::getCleanValue($_POST['txtUsername']);
            $_objUser->modified = AppHelper::getDbTimestamp();
            $_objUser->modified_by = Yii::app()->user->id;

            try {
                if (!$_objUser->validate()) {
                    throw new Exception(Yii::t('Err001', CHtml::errorSummary($_objUser)));
                }

                if (!$_objUser->save()) {
                    throw new Exception(Yii::t('error', 'Error while saving data.'));
                }

                $_objUserProfile = $_modelUserProfile->findByAttributes(array("user_id" => $_objUser->id));
                $_objUserProfile->firstname = AppHelper::getCleanValue($_POST['txtFname']);
                $_objUserProfile->lastname = AppHelper::getCleanValue($_POST['txtLname']);
                $_objUserProfile->gender = !empty($_POST['gender']) ? $_POST['gender'] : '';
                $_objUserProfile->phone = AppHelper::getCleanValue($_POST['txtPhone']);
                $_objUserProfile->address = AppHelper::getCleanValue($_POST['txtAddress']);

                if (!empty($_FILES['file']['name'])) {
                    $_filename = $_FILES["file"]["name"];
                    $_tmpfilename = $_FILES["file"]["tmp_name"];
                    $_filetype = $_FILES["file"]["type"];
                    $_filesize = $_FILES["file"]["size"];
                    $_size = ($_filesize / 1024) . " KB";
                    $_fileerror = $_FILES["file"]["error"];

                    $_savepath = Yii::getPathOfAlias('webroot') . '/uploads/';
                    $_folder = md5($_objUser->id);
                    $createdNewDirectoryPath = $_savepath . $_folder;
                    AppHelper::createNewDirectory($createdNewDirectoryPath);
                    $_newsavepath = $createdNewDirectoryPath . '/';
                    $allowedExts = array("jpg", "JPG", "png", "jpeg", "gif");
                    $temp = explode(".", $_filename);
                    $extension = end($temp);
                    $_newfilename = AppHelper::getUnqiueKey() . '.' . $extension;

                    if (!empty($_objUserProfile->avatar)) {
                        unlink($_savepath . $_folder . '/' . $_objUserProfile->avatar);
                    }
                    $_objUserProfile->avatar = $_newfilename;
                    move_uploaded_file($_tmpfilename, $_newsavepath . $_newfilename);
                }

                if (!$_objUserProfile->save()) {
                    throw new Exception(Yii::t('error', 'Error while updating data.'));
                }

                Yii::app()->user->setFlash('success', 'Profile Update successfull!');
            } catch (Exception $e) {
                Yii::app()->user->setFlash('danger', $e->getMessage());
            }
        }

        $this->setPageTitle("Edit Profile : <em style='text-transform:lowercase;'>" . $_objUser->login . "</em>");
        $this->model['user'] = $_objUser;
        $this->render('edit', $this->model);
    }

    public function actionAdmin_edit() {
        $this->checkUserAccess('admin_user_edit');
        $this->setHeadTitle("Users");
        $this->setPageTitle("Edit Profile");
        $this->setCurrentPage(AppUrl::URL_USERLIST);

        $_key = Yii::app()->request->getParam('id');
        $_model = new User();
        $_modelUserProfile = new UserProfile();

        $_objUser = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['saveProfile'])) {
            $_objUser->login = AppHelper::getCleanValue($_POST['txtUsername']);
            $_objUser->email = AppHelper::getCleanValue($_POST['txtEmail']);
            $_objUser->role = AppHelper::getCleanValue($_POST['role']);
            $_objUser->modified = AppHelper::getDbTimestamp();
            $_objUser->modified_by = Yii::app()->user->id;

            try {
                if (!$_objUser->validate()) {
                    throw new Exception(Yii::t('Err001', CHtml::errorSummary($_objUser)));
                }

                if (!$_objUser->save()) {
                    throw new Exception(Yii::t('error', 'Error while updating data.'));
                }

                $_userProfile = $_modelUserProfile->findByAttributes(array("user_id" => $_objUser->id));
                $_userProfile->firstname = AppHelper::getCleanValue($_POST['txtFname']);
                $_userProfile->lastname = AppHelper::getCleanValue($_POST['txtLname']);
                $_userProfile->gender = !empty($_POST['gender']) ? $_POST['gender'] : '';
                $_userProfile->phone = AppHelper::getCleanValue($_POST['txtPhone']);
                $_userProfile->address = AppHelper::getCleanValue($_POST['txtAddress']);

                if (!empty($_FILES['file']['name'])) {
                    $_filename = $_FILES["file"]["name"];
                    $_tmpfilename = $_FILES["file"]["tmp_name"];
                    $_filetype = $_FILES["file"]["type"];
                    $_filesize = $_FILES["file"]["size"];
                    $_size = ($_filesize / 1024 ) . " KB";
                    $_fileerror = $_FILES["file"]["error"];

                    $_savepath = Yii::getPathOfAlias('webroot') . '/uploads/';
                    $_folder = md5($_objUser->id);
                    $createdNewDirectoryPath = $_savepath . $_folder;
                    AppHelper::createNewDirectory($createdNewDirectoryPath);
                    $_newsavepath = $createdNewDirectoryPath . '/';
                    $allowedExts = array("jpg", "JPG", "png", "jpeg", "gif");
                    $temp = explode(".", $_filename);
                    $extension = end($temp);
                    $_newfilename = AppHelper::getUnqiueKey() . '.' . $extension;

                    if (!empty($_userProfile->avatar)) {
                        unlink($_savepath . $_folder . '/' . $_userProfile->avatar);
                    }
                    $_userProfile->avatar = $_newfilename;
                    move_uploaded_file($_tmpfilename, $_newsavepath . $_newfilename);
                }

                if (!$_userProfile->save()) {
                    throw new Exception(Yii::t('error', 'Error while updating data.'));
                }

                Yii::app()->user->setFlash('success', 'Data update successfull!');
            } catch (Exception $e) {
                Yii::app()->user->setFlash('danger', $e->getMessage());
            }
        }

        $this->model['user'] = $_objUser;
        $this->render('edit', $this->model);
    }

    public function actionProfile() {
        $this->checkUserAccess('profile_view');
        $this->setHeadTitle("Profile");
        $this->setPageTitle("Profile");
        $this->setCurrentPage(AppUrl::URL_USER_PROFILE);

        $_modelUser = new User();
        $_objUser = null;
        $_userKey = Yii::app()->request->getParam('id');

        if (!empty($_userKey)) {
            $_objUser = $_modelUser->find('LOWER(_key) = ?', array(strtolower($_userKey)));
            $adminEdit = true;
        } else {
            $_objUser = $_modelUser->findByPk(Yii::app()->user->id);
            $adminEdit = false;
        }

        $this->model['user'] = $_objUser;
        $this->model['adminEdit'] = $adminEdit;
        $this->render('profile', $this->model);
    }

    public function actionActivate() {
        $this->checkUserAccess('user_activate');
        $_key = Yii::app()->request->getParam('id');

        $_model = new User();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            if (empty($_data->id)) {
                throw new Exception(Yii::t("App", "No record found!"));
            }

            $_data->status = AppConstant::USER_STATUS_ACTIVE;
            $_data->save();

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'User activation successfull!');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionDeactivate() {
        $this->checkUserAccess('user_activate');
        $_key = Yii::app()->request->getParam('id');

        $_model = new User();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            if (empty($_data->id)) {
                throw new Exception(Yii::t("App", "No record found!"));
            }

            $_data->status = AppConstant::USER_STATUS_INACTIVE;
            $_data->save();
            $_transaction->commit();

            if ($_data->id == Yii::app()->user->id) {
                $this->redirect($this->createUrl(AppUrl::URL_USER_LOGOUT));
            }

            Yii::app()->user->setFlash("success", 'User deactivation successfull!');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionPermission() {
        $this->checkUserAccess('access_control');
        $this->setHeadTitle("User Permissions");
        $this->setPageTitle("User Permissions");
        $this->setCurrentPage(AppUrl::URL_USERLIST);

        $_key = Yii::app()->request->getParam('id');
        $_model = new User();
        $_objUser = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['savePermission'])) {
            $permissions = isset($_POST['permission']) ? $_POST['permission'] : '';
            $jsonData = json_encode($permissions);

            try {
                $_objUser->access_item->items = $jsonData;
                if (!$_objUser->access_item->save()) {
                    throw new Exception(Yii::t('error', 'Error while resetting permission data.'));
                }

                Yii::app()->user->setFlash('success', 'Data update successfull!');
                $this->refresh();
            } catch (Exception $e) {
                Yii::app()->user->setFlash('danger', $e->getMessage());
            }
        }

        $this->model['user'] = $_objUser;
        $this->render('permission', $this->model);
    }

    // Ajax functions
    public function actionSearch() {
        $this->is_ajax_request();
        $_q = Yii::app()->request->getPost('q');
        $_limit = Yii::app()->request->getPost('itemCount');

        $_model = new User();
        $criteria = new CDbCriteria();
        $criteria->condition = "id<>:id";
        $criteria->addCondition("deletable=1");
        $criteria->addCondition("is_deleted=0");
        $criteria->params = array(":id" => Yii::app()->user->id);
        $criteria->order = "login ASC";
        if (!empty($_q)) {
            $criteria->addCondition("login LIKE '%{$_q}%' OR email LIKE '%{$_q}%'");
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_list', $this->model);
    }

    public function actionDeleteall() {
        $this->is_ajax_request();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_model = new User();
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if ($_obj->deletable == 0) {
                        throw new CException(Yii::t('App', "You cannot delete a superadmin user"));
                    }

                    $_obj->is_deleted = 1;
                    if (!$_obj->save()) {
                        throw new CException(Yii::t('App', "Error while deleting record"));
                    }
                }

                $_transaction->commit();
                $response['success'] = true;
                $response['message'] = "Records deleted successfully!";
            } catch (CException $e) {
                $_transaction->rollback();
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['success'] = false;
            $response['message'] = "No record found to delete!";
        }

        echo json_encode($response);
        return json_encode($response);
    }

    /* Autometed user activation */

    public function actionActivation() {
        $_key = Yii::app()->request->getParam('id');

        $_model = new User();
        $_data = $_model->find('LOWER(activation_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            if (empty($_data->id)) {
                throw new CException(Yii::t("App", "No record found!"));
            }

            if ($_data->status == AppConstant::USER_STATUS_ACTIVE) {
                throw new CException(Yii::t("App", "Your account is already activated."));
            } else {
                $_data->status = AppConstant::USER_STATUS_ACTIVE;
                $_data->activation_ip = AppHelper::getUserIp();
                $_data->activated_at = AppHelper::getDbTimestamp();
                $_data->save();
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Activation successfull. You can login now.');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(array(AppUrl::URL_LOGIN));
    }

    public function actionLogout() {
        Yii::app()->user->logout(true);
        $this->redirect($this->createUrl(AppUrl::URL_LOGIN));
        Yii::app()->end();
    }

    public function actionRemove_login($id) {
        $_model = new User();
        $_data = $_model->findByPk($id);

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (!$_data->logins->delete()) {
                throw new CException(Yii::t("App", "Error while removing user login data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'User logout successfull!');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }

        if (!empty(Yii::app()->request->urlReferrer)) {
            $this->redirect(Yii::app()->request->urlReferrer);
        } else {
            $this->redirect($this->createUrl(AppUrl::URL_USERLIST));
        }
    }

}
