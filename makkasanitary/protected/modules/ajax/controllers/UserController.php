<?php

class UserController extends AppController {

    public function beginRequest() {
        if (Yii::app()->request->isAjaxRequest) {
            return true;
        }
        return false;
    }

    public function beforeAction($action) {
        //$this->actionAuthorized();
        //$this->is_ajax_request();
        return true;
    }

    public function actionIndex() {
        $this->is_ajax_request();
        $_model = new User();
        $_q = Yii::app()->request->getPost('q');
        $_limit = Yii::app()->request->getPost('itemCount');

        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->condition = "id<>:id";
        $criteria->addCondition("deletable=1");
        $criteria->params = array(":id" => Yii::app()->user->id);
        $criteria->order = "login ASC";

        if (!empty($_q)) {
            $criteria->condition = "login LIKE :match OR email LIKE :match";
            $criteria->params = array(':match' => "%$_q%");
        }

        $_dataset = $_model->findAll($criteria);
        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('index', $this->model);
    }

    public function actionLogin() {
        $_resp = new AjaxResponse($this);

        $_login = AppHelper::getCleanValue($_POST['LoginForm']['username']);
        $_password = AppHelper::getCleanValue($_POST['LoginForm']['password']);

        $_objUser = new AppUser;

        if ($_objUser->auth($_login, $_password)) {
            $_resp->message = 'Logged in successfull. Redirecting...';
            $_resp->success = true;
        } else {
            $_resp->error = true;
            $_resp->exception = $_objUser->errorMessage;
        }

        $this->model = $_resp;

        $this->renderJson();
    }

    public function actionDeleteall() {
        $_model = new User();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if ($_obj->deletable == 0) {
                        throw new CException(Yii::t('App', "You cannot delete a superadmin user"));
                    }

                    if (!empty($_obj->profile)) {
                        if (!$_obj->profile->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {profile}"));
                        }
                    }

                    if (!empty($_obj->access_item)) {
                        if (!$_obj->access_item->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {access items}"));
                        }
                    }

//                    if (!empty($_obj->logins)) {
//                        if (!$_obj->logins->delete()) {
//                            throw new CException(Yii::t('App', "Error while deleting record {logins}"));
//                        }
//                    }

                    if (!$_obj->delete()) {
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

}
