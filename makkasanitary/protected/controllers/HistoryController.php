<?php

class HistoryController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        if (!in_array(Yii::app()->user->id, [1, 4])) {
            $this->redirect($this->createUrl(AppUrl::URL_DASHBOARD));
        }
        return true;
    }

    public function actionIndex() {
        $this->setHeadTitle("History");
        $this->setPageTitle("History List");
        $this->setCurrentPage(AppUrl::URL_HISTORY);

        $_model = new History();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "date_time DESC";

        $_dataset = $_model->findAll($criteria);
        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionView($id) {
        $this->setHeadTitle("History");
        $this->setPageTitle("History View");
        $this->setCurrentPage(AppUrl::URL_HISTORY);

        $_model = new History();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($id)));

        $this->model['model'] = $_data;
        $this->render('view', $this->model);
    }

    public function actionDelete($id) {
        $_model = new History();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($id)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($id)) {
                throw new CException(Yii::t("App", "You are trying to access invalid Url."));
            }

            if (empty($_data->id)) {
                throw new Exception(Yii::t("App", "No record found to delete!"));
            }

            if (!$_data->delete()) {
                throw new CException(Yii::t("App", "Error while deleting record."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'History Record Deleted Successfully.');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionDeleteall() {
        $response = array();
        $_model = new History();

        if (isset($_POST['data'])) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_POST['data']); $i++) {
                    $_obj = $_model->findByPk($_POST['data'][$i]);

                    if (!$_obj->delete()) {
                        throw new CException(Yii::t('App', "Error while deleting history record"));
                    }
                }

                $_transaction->commit();
                $response['success'] = true;
                $response['message'] = "Records deleted successfully.";
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

    public function actionClear() {
        $_model = new History();
        $_dataset = $_model->findAll();

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            foreach ($_dataset as $_data) {
                if (!$_data->delete()) {
                    throw new CException(Yii::t("App", "Error while clearing record."));
                }
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'History Clear Successfull.');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

}
