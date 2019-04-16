<?php

class ProfitController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('profit');
        $this->setHeadTitle("Profit");
        $this->setPageTitle("Profit And Loss Statement");
        $this->setCurrentPage(AppUrl::URL_PROFIT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $curDate = date('Y-m-d');
        $_model = new Invoice();
        $criteria = new CDbCriteria();
        $criteria->addBetweenCondition('invoice_date', $curDate, $curDate);
        $criteria->order = "invoice_date DESC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    // Ajax functions
    public function actionSearch() {
        $this->is_ajax_request();
        $_limit = Yii::app()->request->getPost('itemCount');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");
        $sum[] = array();

        $_model = new Invoice();
        $criteria = new CDbCriteria();
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('invoice_date', $dateForm, $dateTo);
        }
        $criteria->order = "invoice_date DESC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_list', $this->model);
    }

}
