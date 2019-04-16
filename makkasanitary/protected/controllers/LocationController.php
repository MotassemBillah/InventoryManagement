<?php

class LocationController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('location_list');
        $this->setHeadTitle("Product Location");
        $this->setPageTitle("Product Location");
        $this->setCurrentPage(AppUrl::URL_LOCATION);

        $_model = new Location();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "name ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        $this->checkUserAccess('location_create');
        $this->checkUserAccess('location');
        $this->setHeadTitle("Product Location");
        $this->setPageTitle("Create New Location");
        $this->setCurrentPage(AppUrl::URL_LOCATION);

        $_model = new Location();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmLocation') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['Location'])) {
            $_model->attributes = $_POST['Location'];
            $_model->name = $_POST['Location']['name'];
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
                Yii::app()->user->setFlash("success", "New record save successfull!");
                $this->redirect(array(AppUrl::URL_LOCATION));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('location_edit');
        $this->setHeadTitle("Product Location");
        $this->setPageTitle("Edit Product Location");
        $this->setCurrentPage(AppUrl::URL_LOCATION);

        $_model = new Location();
        $_key = Yii::app()->request->getParam('key');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Location'])) {
            $_data->attributes = $_POST['Location'];
            $_data->name = $_POST['Location']['name'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record update successfull!");
                $this->redirect(array(AppUrl::URL_LOCATION));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionDelete() {
        $this->checkUserAccess('location_delete');
        $_key = Yii::app()->request->getParam('key');

        $_model = new Location();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            if (empty($_data->id)) {
                throw new Exception(Yii::t("App", "No record found to delete!"));
            }

            if (!$_data->delete()) {
                throw new CException(Yii::t("App", "Error while deleting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Record delete successfull!');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionDeleteall() {
        $this->checkUserAccess('location_delete');
        $_model = new Location();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if (!$_obj->delete()) {
                        throw new CException(Yii::t('App', "Error while deleting record"));
                    }
                }

                $_transaction->commit();
                Yii::app()->user->setFlash('success', "Record delete successfull!");
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
