<?php

class SizeController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('product_size_list');
        $this->setHeadTitle("Product Sizes");
        $this->setPageTitle("Product Sizes");
        $this->setCurrentPage(AppUrl::URL_SIZE);

        $_model = new Size();
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
        $this->checkUserAccess('product_size_create');
        $this->setHeadTitle("Product Sizes");
        $this->setPageTitle("Create New Sizes");
        $this->setCurrentPage(AppUrl::URL_SIZE);

        $_model = new Size();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmSize') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['Size'])) {
            $_model->attributes = $_POST['Size'];
            $_model->name = $_POST['Size']['name'];
            $_model->packtype = $_POST['Size']['packtype'];
            $_model->packsize = $_POST['Size']['packsize'];

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
                $this->redirect(array(AppUrl::URL_SIZE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('product_size_edit');
        $this->setHeadTitle("Product Sizes");
        $this->setPageTitle("Edit Product Sizes");
        $this->setCurrentPage(AppUrl::URL_SIZE);

        $_model = new Size();
        $_key = Yii::app()->request->getParam('y');
        $_data = $_model->find('LOWER(id) = ?', array(strtolower($_key)));

        if (isset($_POST['Size'])) {
            $_data->attributes = $_POST['Size'];
            $_data->name = $_POST['Size']['name'];
            $_data->packtype = $_POST['Size']['packtype'];
            $_data->packsize = $_POST['Size']['packsize'];

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
                $this->redirect(array(AppUrl::URL_SIZE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionDelete() {
        $this->checkUserAccess('product_size_delete');
        $_key = Yii::app()->request->getParam('y');

        $_model = new Size();
        $_data = $_model->find('LOWER(id) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

//            if (empty($_data->size_id)) {
//                throw new Exception(Yii::t("App", "No record found to delete!"));
//            }

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
        $this->checkUserAccess('product_size_delete');
        $_model = new Size();
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
