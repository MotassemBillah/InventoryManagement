<?php

class IncomeController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('income_list');
        $this->setHeadTitle("Ledger Income");
        $this->setPageTitle("Ledger Income");
        $this->setCurrentPage(AppUrl::URL_LEDGER_INCOME);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $curDate = date("Y-m-d");
        $_model = new CustomerPayment();
        $criteria = new CDbCriteria();
        $criteria->condition = "invoice_paid IS NOT NULL OR advance_amount IS NOT NULL";
        $criteria->addInCondition("type", array(AppConstant::TYPE_ADVANCE, AppConstant::TYPE_INVOICE));
        $criteria->addBetweenCondition('pay_date', $curDate, $curDate);
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

    // Ajax calls

    public function actionSearch() {
        $this->is_ajax_request();
        $_limit = Yii::app()->request->getPost('itemCount');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new CustomerPayment();
        $criteria = new CDbCriteria();
        $criteria->condition = "invoice_paid IS NOT NULL OR advance_amount IS NOT NULL";
        $criteria->addInCondition("type", array(AppConstant::TYPE_ADVANCE, AppConstant::TYPE_INVOICE));
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('pay_date', $dateForm, $dateTo);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "pay_date DESC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_list', $this->model);
    }

}
