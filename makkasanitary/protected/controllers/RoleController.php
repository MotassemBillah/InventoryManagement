<?php

class RoleController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('role_list');
        $this->setHeadTitle("Roles");
        $this->setPageTitle("Roles");
        $this->setCurrentPage(AppUrl::URL_ROLE);

        $_model = new Role();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        if (Yii::app()->user->role != AppConstant::ROLE_SUPERADMIN) {
            $criteria->condition = "is_deleted = 0";
        }
        $criteria->order = "name ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        $this->checkUserAccess('role_create');
        $this->setHeadTitle("Roles");
        $this->setPageTitle("Create New Role");
        $this->setCurrentPage(AppUrl::URL_ROLE);

        $_model = new Role();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmRole') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['Role'])) {
            $_model->attributes = $_POST['Role'];
            $_model->name = $_POST['Role']['name'];
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Data saved successfully!");
                $this->redirect(array(AppUrl::URL_ROLE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('role_edit');
        $this->setHeadTitle("Roles");
        $this->setPageTitle("Edit Role");
        $this->setCurrentPage(AppUrl::URL_ROLE);

        $_model = new Role();
        $_key = Yii::app()->request->getParam('key');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Role'])) {
            $_data->attributes = $_POST['Role'];
            $_data->name = $_POST['Role']['name'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Data updated successfully!");
                $this->redirect(array(AppUrl::URL_ROLE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionDelete() {
        $this->checkUserAccess('role_delete');
        $_key = Yii::app()->request->getParam('key');

        $_model = new Role();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            if (empty($_data->id)) {
                throw new Exception(Yii::t("App", "No record found to delete!"));
            }

            if (Yii::app()->user->role == AppConstant::ROLE_SUPERADMIN) {
                if (!$_data->delete()) {
                    throw new CException(Yii::t("App", "Error while deleting data."));
                }
            } else {
                $_data->is_deleted = 1;
                $_data->save();
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Data deleted successfully!');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionDeleteall() {
        $this->checkUserAccess('role_delete');
        $_model = new Role();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if (Yii::app()->user->role == AppConstant::ROLE_SUPERADMIN) {
                        if (!$_obj->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record"));
                        }
                    } else {
                        $_obj->is_deleted = 1;
                        $_obj->save();
                    }
                }

                $_transaction->commit();
                Yii::app()->user->setFlash('success', "Records deleted successfully!");
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash('error', $e->getMessage());
            }
        } else {
            Yii::app()->user->setFlash('warning', "No record found to delete!");
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

}
