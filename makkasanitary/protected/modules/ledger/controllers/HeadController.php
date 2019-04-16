<?php

class HeadController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('head_list');
        $this->setHeadTitle("Ledger Heads");
        $this->setPageTitle("Ledger Heads");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD);

        $_model = new LedgerHead();
        $criteria = new CDbCriteria();
        $criteria->order = "code ASC";
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
        $this->checkUserAccess('head_create');
        $this->setHeadTitle("Ledger Head");
        $this->setPageTitle("New Ledger Head");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD);

        $_model = new LedgerHead();

        if (isset($_POST['LedgerHead'])) {
            $_model->attributes = $_POST['LedgerHead'];
            $_model->type = $_POST['LedgerHead']['type'];
            $_model->name = $_POST['LedgerHead']['name'];
            if (in_array(Yii::app()->user->id, [1, 4])) {
                $_model->is_fixed = $_POST['LedgerHead']['is_fixed'];
            }
            $_model->code = time();
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
                Yii::app()->user->setFlash("success", "New Record save successfull.");
                $this->redirect(array(AppUrl::URL_LEDGER_HEAD));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('_form', $this->model);
    }

    public function actionEdit($id) {
        $this->checkUserAccess('head_edit');
        $this->setHeadTitle("Ledger Head");
        $this->setPageTitle("Update Ledger Head");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD);

        $_model = new LedgerHead();
        $_data = $_model->find("LOWER(_key)=?", [strtolower($id)]);

        if (isset($_POST['LedgerHead'])) {
            $_data->attributes = $_POST['LedgerHead'];
            $_data->type = $_POST['LedgerHead']['type'];
            $_data->name = $_POST['LedgerHead']['name'];
            if (in_array(Yii::app()->user->id, [1, 4])) {
                $_data->is_fixed = $_POST['LedgerHead']['is_fixed'];
            }

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }
                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record update successfull.");
                $this->redirect(array(AppUrl::URL_LEDGER_HEAD));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('_form', $this->model);
    }

    public function actionView($id) {
        $this->checkUserAccess('head_transaction_view');
        $this->setHeadTitle("Ledger Head");
        $this->setPageTitle("Transactions Details");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new CashAccount();
        $criteria = new CDbCriteria();
        $criteria->condition = "ledger_head_id=:lhid";
        $criteria->params = [":lhid" => $id];
        $criteria->order = "created DESC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['headName'] = LedgerHead::model()->findByPk($id)->name;
        $this->model['headID'] = $id;
        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('view', $this->model);
    }

    /*
     * Ajax search and other responses
     */

    public function actionSearch() {
        $this->is_ajax_request();
        $_limit = Yii::app()->request->getPost('itemCount');
        $_type = Yii::app()->request->getPost('itemType');

        $_model = new LedgerHead();
        $criteria = new CDbCriteria();
        if ($_type == 1) {
            $criteria->condition = "is_fixed = {$_type}";
        }
        $criteria->order = "code ASC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_list', $this->model);
    }

    public function actionSearch_detail() {
        $this->is_ajax_request();
        $_headID = Yii::app()->request->getPost('ledger_head_id');
        $_limit = Yii::app()->request->getPost('itemCount');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new CashAccount();
        $criteria = new CDbCriteria();
        $criteria->condition = "ledger_head_id={$_headID}";
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('created', $dateForm, $dateTo);
        }
        $criteria->order = "created DESC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_view', $this->model);
    }

    public function actionDeleteall() {
        $this->is_ajax_request();
        $response = array();
        $_data = $_POST['data'];
        $_model = new LedgerHead();

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    $_cash_account = CashAccount::model()->findAll('ledger_head_id=:lhid', [':lhid' => $_obj->id]);
                    if (!empty($_cash_account)) {
                        throw new CException(Yii::t('App', "Cannot delete this head. One or mode transaction exist."));
                    }

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
