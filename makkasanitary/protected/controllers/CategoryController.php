<?php

class CategoryController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('category_list');
        $this->setHeadTitle("Categories");
        $this->setPageTitle("Category List");
        $this->setCurrentPage(AppUrl::URL_CATEGORIES);
        $this->addJs('views/category.js');

        $_model = new Category();
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
        $this->checkUserAccess('category_create');
        $this->setHeadTitle("Categories");
        $this->setPageTitle("Add New Category");
        $this->setCurrentPage(AppUrl::URL_CATEGORIES);

        $_model = new Category();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmCategory') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['Category'])) {
            $_model->attributes = $_POST['Category'];
            $_model->parent = !empty($_POST['Category']['parent']) ? $_POST['Category']['parent'] : 0;
            $_model->name = $_POST['Category']['name'];
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
                Yii::app()->user->setFlash("success", "Category has been saved successfully!");
                $this->redirect(array(AppUrl::URL_CATEGORIES));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('category_edit');
        $this->setHeadTitle("Categories");
        $this->setPageTitle("Edit Category");
        $this->setCurrentPage(AppUrl::URL_CATEGORIES);

        $_model = new Category();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Category'])) {
            $_data->attributes = $_POST['Category'];
            $_data->parent = !empty($_POST['Category']['parent']) ? $_POST['Category']['parent'] : 0;
            $_data->name = $_POST['Category']['name'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Category updated successfully!");
                $this->redirect(array(AppUrl::URL_CATEGORIES));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionDelete() {
        $this->checkUserAccess('category_delete');
        $_key = Yii::app()->request->getParam('id');

        $_model = new Category();
        $_data = $_model->find('LOWER(key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            if (empty($_data->id)) {
                throw new Exception(Yii::t("App", "No record found to delete!"));
            }

            if ($_data->parent > 0) {
                throw new Exception(Yii::t("App", "You cannot delete record while there are many records under this category"));
            }

            if (!$_data->delete()) {
                throw new CException(Yii::t("App", "Error while deleting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Category deleted successfully!');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionDeleteall() {
        $this->checkUserAccess('category_delete');
        $_model = new Category();
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
                Yii::app()->user->setFlash('success', "Record deleted successfully!");
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
