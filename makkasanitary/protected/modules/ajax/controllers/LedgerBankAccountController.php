<?php

class LedgerBankAccountController extends AppController {

    public function beginRequest() {
        if (Yii::app()->request->isAjaxRequest) {
            return true;
        }
        return false;
    }

    public function beforeAction($action) {
        $this->actionAuthorized();
        $this->is_ajax_request();
        return true;
    }

    public function actionIndex() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $_q = Yii::app()->request->getPost('q');

        $_model = new LedgerBankAccount();
        $criteria = new CDbCriteria();
        if (!empty($_q)) {
            $criteria->condition = "account_name like '%" . $_q . "%'";
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "account_name ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('index', $this->model);
    }

    public function actionBalance() {
        $accountID = Yii::app()->request->getPost('accountID');
        $_limit = Yii::app()->request->getPost('itemCount');
        $_category = Yii::app()->request->getPost('type');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new LedgerBankAccountBalance();
        $criteria = new CDbCriteria();
        if ($_category !== "All") {
            $criteria->condition = "description='$_category'";
            $criteria->params = array(":description" => $_category);
        } else {
            $criteria->addInCondition("description", array(AppConstant::CASH_IN, AppConstant::CASH_OUT));
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('last_transaction_time', $dateForm, $dateTo);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $accBalance = '';
        if ($_category == AppConstant::CASH_IN) {
            $accBalance = AppObject::sumLedgerCashIn($accountID);
        } else if ($_category == AppConstant::CASH_OUT) {
            $accBalance = AppObject::sumLedgerCashOut($accountID);
        } else {
            $accBalance = AppObject::sumLedgerCashBalance($accountID);
        }

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->model['account'] = LedgerBankAccount::model()->findByPk($accountID);
        $this->model['totalAmount'] = $accBalance;
        $this->renderPartial('balance', $this->model);
    }

    public function actionAdd_balance() {
        $_key = Yii::app()->request->getParam('id');
        $model = new Account();
        $_data = $model->find("LOWER(_key)=?", array(strtolower($_key)));

        $this->model['model'] = $_data;
        $this->renderPartial('add', $this->model);
    }

    public function actionSave_balance() {
        $accountID = Yii::app()->request->getPost('accountID');
        $accountBalance = Yii::app()->request->getPost('account_balance');
        $model = new AccountBalance();

        if (!empty($accountBalance)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                $model->account_id = $accountID;
                $model->balance_amount = $accountBalance;
                if (!$model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
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
            $response['message'] = "Balance amount required.";
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionDeleteall() {
        $_model = new LedgerBankAccount();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if (!empty($_obj->balance)) {
                        foreach ($_obj->balance as $balance) {
                            if (!$balance->delete()) {
                                throw new CException(Yii::t("App", "Error while deleting account balance data."));
                            }
                        }
                    }

                    if (!$_obj->delete()) {
                        throw new CException(Yii::t("App", "Error while deleting data."));
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
