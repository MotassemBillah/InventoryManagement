<?php

class BalancesheetController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('balance_sheet');
        $this->setHeadTitle("Ledger Balancesheet");
        $this->setPageTitle("Ledger Balancesheet");
        $this->setCurrentPage(AppUrl::URL_LEDGER_BALANCE_SHEET);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new Balancesheet();
        $criteria = new CDbCriteria();
        $criteria->order = "pay_date DESC";
        $criteria->group = "pay_date";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = 20;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionSearch() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new Balancesheet();
        $criteria = new CDbCriteria();
        $criteria->order = "pay_date DESC";
        $criteria->group = "pay_date";
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('pay_date', $dateForm, $dateTo);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('search', $this->model);
    }

    public function actionUpdate() {
        ini_set('max_execution_time', 120);
        $_transaction = Yii::app()->db->beginTransaction();
        try {
            $_model = new Balancesheet();
            //AppObject::emptyTable($_model->tableName());

            $_dataset = $_model->findAll();
            foreach ($_dataset as $_data) {
                if (!$_data->delete()) {
                    throw new CException(Yii::t("App", "Error while updating balancesheet information {{DEL}}."));
                }
            }

            $_modelCP = new CustomerPayment();
            $criteria = new CDbCriteria();
            $criteria->condition = "invoice_paid IS NOT NULL OR advance_amount IS NOT NULL";
            $criteria->addInCondition("type", array(AppConstant::TYPE_ADVANCE, AppConstant::TYPE_INVOICE));
            $criteria->order = "pay_date DESC";
            $_datasetCP = $_modelCP->findAll($criteria);
            foreach ($_datasetCP as $_datacp) {
                $_modelBS = new Balancesheet();
                $_modelBS->customer_payment_id = $_datacp->id;
                $_modelBS->pay_date = $_datacp->pay_date;
                $_modelBS->debit = ($_datacp->invoice_paid + $_datacp->advance_amount);
                $_modelBS->balance = $_modelBS->debit;
                if (!$_modelBS->save()) {
                    throw new CException(Yii::t("App", "Error while updating balancesheet information {{DBT}}."));
                }
            }

            $_modelP = new Payment();
            $criteriaP = new CDbCriteria();
            $criteriaP->condition = "invoice_paid IS NOT NULL OR advance_amount IS NOT NULL";
            $criteriaP->addInCondition("type", array(AppConstant::TYPE_ADVANCE, AppConstant::TYPE_INVOICE));
            $criteriaP->order = "pay_date DESC";
            $_datasetP = $_modelP->findAll($criteriaP);
            foreach ($_datasetP as $_datap) {
                $_modelPB = new Balancesheet();
                $_modelPB->payment_id = $_datap->id;
                $_modelPB->pay_date = $_datap->pay_date;
                $_modelPB->credit = ($_datap->invoice_paid + $_datap->advance_amount);
                $_modelPB->balance = -($_modelPB->credit);
                if (!$_modelPB->save()) {
                    throw new CException(Yii::t("App", "Error while updating balancesheet information {{CRDT}}."));
                }
            }

            $_modelEx = new Expense();
            $_datasetEx = $_modelEx->findAll();
            foreach ($_datasetEx as $_dataex) {
                $_modelEX = new Balancesheet();
                $_modelEX->expense_id = $_dataex->id;
                $_modelEX->pay_date = $_dataex->pay_date;
                $_modelEX->credit = $_dataex->amount;
                $_modelEX->balance = -($_modelEX->credit);
                if (!$_modelEX->save()) {
                    throw new CException(Yii::t("App", "Error while updating balancesheet information {{CRDT}}."));
                }
            }

            /* update previous balance */
            $dates = $_model->balanceAmount();
            foreach ($dates as $dt) {
                $date = $dt->pay_date;
                $prev_date = date('Y-m-d', strtotime($date . ' -1 day'));
                $sumBalance = AppObject::balancesheetSumBalance($prev_date);
                $upBlncModel = new Balancesheet();
                $upBlncModel->pay_date = $dt->pay_date;
                if ($sumBalance > 0) {
                    $upBlncModel->debit = $sumBalance;
                    $upBlncModel->balance = $upBlncModel->debit;
                } else {
                    $upBlncModel->credit = $sumBalance;
                    $upBlncModel->balance = -($upBlncModel->credit);
                }
                if (!$upBlncModel->save()) {
                    throw new CException(Yii::t("App", "Error while updating balancesheet information {{DBT}}."));
                }
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", "Record Update Successfull.");
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }

        $this->redirect(array(AppUrl::URL_LEDGER_BALANCE_SHEET));
    }

}
