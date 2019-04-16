<?php

class ExpenseController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('expense_list');
        $this->setHeadTitle("Ledger Expense");
        $this->setPageTitle("Ledger Expense");
        $this->setCurrentPage(AppUrl::URL_LEDGER_EXPENSE);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new Expense();
        $criteria = new CDbCriteria();
        $criteria->order = "pay_date DESC";
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
        $this->checkUserAccess('expense_create');
        $this->setHeadTitle("Ledger Expense");
        $this->setPageTitle("Create Ledger Expense");
        $this->setCurrentPage(AppUrl::URL_LEDGER_EXPENSE);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new Expense();
        if (isset($_POST['Expense'])) {
            $_date = $_POST['Expense']['pay_date'];
            $_model->attributes = $_POST['Expense'];
            $_model->ledger_head_id = $_POST['Expense']['ledger_head_id'];
            $_model->purpose = $_POST['Expense']['purpose'];
            $_model->amount = $_POST['Expense']['amount'];
            $_model->by_whom = $_POST['Expense']['by_whom'];
            $_model->pay_date = !empty($_date) ? date('Y-m-d', strtotime($_date)) : AppHelper::getDbDate();
            $_model->created = AppHelper::getDbTimestamp();
            $_model->created_by = Yii::app()->user->id;
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $lastID = Yii::app()->db->getLastInsertId();
                $modelCashAccount = new CashAccount();
                $modelCashAccount->expense_id = $lastID;
                $modelCashAccount->ledger_head_id = $_model->ledger_head_id;
                $modelCashAccount->type = "W";
                $modelCashAccount->transaction_type = "Cash";
                $modelCashAccount->pay_date = $_model->pay_date;
                $modelCashAccount->purpose = $_model->purpose;
                $modelCashAccount->by_whom = $_model->by_whom;
                $modelCashAccount->credit = AppHelper::getFloat($_model->amount);
                $modelCashAccount->balance = -($modelCashAccount->credit);
                $modelCashAccount->note = $_model->purpose;
                $modelCashAccount->created = AppHelper::getDbTimestamp();
                $modelCashAccount->created_by = $_model->created_by;
                $modelCashAccount->_key = AppHelper::getUnqiueKey();
                if (!$modelCashAccount->save()) {
                    throw new CException(Yii::t("App", "Error while saving transaction."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New Record Create Successfull.");
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_EXPENSE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('_form', $this->model);
    }

    public function actionEdit($id) {
        $this->checkUserAccess('expense_edit');
        $this->setHeadTitle("Ledger Expense");
        $this->setPageTitle("Edit Ledger Expense");
        $this->setCurrentPage(AppUrl::URL_LEDGER_EXPENSE);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new Expense();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($id)));

        if (isset($_POST['Expense'])) {
            $_date = $_POST['Expense']['pay_date'];
            $_data->attributes = $_POST['Expense'];
            $_data->ledger_head_id = $_POST['Expense']['ledger_head_id'];
            $_data->purpose = $_POST['Expense']['purpose'];
            $_data->amount = $_POST['Expense']['amount'];
            $_data->by_whom = $_POST['Expense']['by_whom'];
            $_data->pay_date = !empty($_date) ? date('Y-m-d', strtotime($_date)) : AppHelper::getDbDate();
            $_data->modified = AppHelper::getDbTimestamp();
            $_data->modified_by = Yii::app()->user->id;

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }
                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $modelCashAccount = CashAccount::model()->find("expense_id=:expId", array(":expId" => $_data->id));
                $modelCashAccount->ledger_head_id = $_data->ledger_head_id;
                $modelCashAccount->pay_date = $_data->pay_date;
                $modelCashAccount->purpose = $_data->purpose;
                $modelCashAccount->by_whom = $_data->by_whom;
                $modelCashAccount->credit = AppHelper::getFloat($_data->amount);
                $modelCashAccount->balance = -($modelCashAccount->credit);
                $modelCashAccount->note = $_data->purpose;
                $modelCashAccount->modified = $_data->modified;
                $modelCashAccount->modified_by = $_data->modified_by;
                if (!$modelCashAccount->save()) {
                    throw new CException(Yii::t("App", "Error while saving transaction."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record Update Successfull.");
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_EXPENSE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }


        $this->model['model'] = $_data;
        $this->render('_form', $this->model);
    }

    // Ajax calls

    public function actionSearch() {
        $this->is_ajax_request();
        $_limit = Yii::app()->request->getPost('itemCount');
        $_head = Yii::app()->request->getPost('ledger_head');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");
        $_q = Yii::app()->request->getPost('search_purpose');

        $_model = new Expense();
        $criteria = new CDbCriteria();
        if (!empty($_head)) {
            $criteria->addCondition("ledger_head_id={$_head}");
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('pay_date', $dateForm, $dateTo);
        }
        if (!empty($_q)) {
            $criteria->addCondition("purpose like '%" . $_q . "%'");
            $this->model['highlight'] = $_q;
        }
        $criteria->order = "pay_date DESC";
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
        $_model = new Expense();

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    $_cash = CashAccount::model()->find("expense_id=:expId", array(":expId" => $_obj->id));
                    if (!empty($_cash)) {
                        if (!$_cash->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting cash record."));
                        }
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

    // update functions
    public function actionUpdate_all() {
        $_dataset = Expense::model()->findAll();
        echo count($_dataset) . "<br>";

        $_counter = 0;
        foreach ($_dataset as $_data) {
            if (empty($_data->by_whom)) {
                $_counter++;
                //$_cash = CashAccount::model()->find("expense_id=:expId", array(":expId" => $_data->id));
                $_data->by_whom = User::model()->displayname($_data->created_by);
                if ($_data->save()) {
                    echo "{$_counter} = Saved<br>";
                } else {
                    echo "{$_counter} = Failed<br>";
                }
            }
        }
        exit;
    }

    public function actionUpdate_cash() {
        $_model = new Expense();
        $criteria = new CDbCriteria();
        //$criteria->condition = "purpose LIKE '%Bank%'";
        $_dataset = $_model->findAll($criteria);
        echo count($_dataset) . "<br>";

        $_counter = 0;
        foreach ($_dataset as $_data) {
            $_counter++;
            $modelCashAccount = new CashAccount();
            $modelCashAccount->expense_id = $_data->id;
            $modelCashAccount->ledger_head_id = $_data->ledger_head_id;
            $modelCashAccount->pay_date = $_data->pay_date;
            $modelCashAccount->purpose = $_data->purpose;
            $modelCashAccount->note = $_data->purpose;
            $modelCashAccount->by_whom = User::model()->displayname($_data->created_by);
            $modelCashAccount->credit = AppHelper::getFloat($_data->amount);
            $modelCashAccount->balance = -($modelCashAccount->credit);
            $modelCashAccount->type = "W";
            $modelCashAccount->created = $_data->created;
            $modelCashAccount->created_by = $_data->created_by;
            $modelCashAccount->_key = AppHelper::getUnqiueKey() . $_counter;
            if ($modelCashAccount->save()) {
                echo "{$_counter} = Saved<br>";
            } else {
                echo "{$_counter} = Failed<br>";
            }
        }
        exit;
    }

}
