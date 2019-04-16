<?php

class DashboardController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('dashboard');
        $this->setHeadTitle("Dashboard");
        $this->setPageTitle("Dashboard");
        $this->setCurrentPage(AppUrl::URL_DASHBOARD);

        $saleModel = new Sale;
        $criteria = new CDbCriteria;
        $criteria->condition = "status=:status";
        $criteria->params = array(":status" => "Pending");
        $_sales = $saleModel->findAll($criteria);

        $_count = count($_sales);
        $_url = $this->createUrl(AppUrl::URL_SALE);

        if ($_count > 0) {
            $_link = "<a href='{$_url}'>You Have {$_count} Pending Orders</a>";
        } else {
            $_link = 'You Have  No Pending Orders';
        }

        $this->model['link'] = $_link;
        $this->render('index', $this->model);
    }

}
