<?php

class PurchaseController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('purchase_list');
        $this->setHeadTitle("Purchase");
        $this->setPageTitle("Purchases");
        $this->setCurrentPage(AppUrl::URL_PURCHASE);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/purchase.js');

        $_model = new Purchase();
        $criteria = new CDbCriteria();
        if (Yii::app()->user->role != AppConstant::ROLE_SUPERADMIN) {
            $criteria->condition = "created_by = " . Yii::app()->user->id;
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "invoice_date DESC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        $this->checkUserAccess('purchase_create');
        $this->setHeadTitle("Purchase");
        $this->setPageTitle("Purchases Order Form");
        $this->setCurrentPage(AppUrl::URL_PURCHASE);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/purchase.js');

        $_model = new Product();
        $_purchase = new Purchase();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        $criteria->order = "name ASC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $dataset;
        $this->model['model'] = $_purchase;
        $this->model['pages'] = $pages;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('purchase_edit');
        $this->setHeadTitle("Purchase");
        $this->setPageTitle("Purchases");
        $this->setCurrentPage(AppUrl::URL_PURCHASE);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_key = strtolower(Yii::app()->request->getParam('id'));
        $_model = new Purchase();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));
        $_isPosted = Yii::app()->request->getPost('btnPurchase');

        if (empty($_data->id)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(array(AppUrl::URL_PURCHASE));
        }

        if ($_data->status == AppConstant::ORDER_COMPLETE) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(array(AppUrl::URL_PURCHASE));
        }

        if (!empty($_isPosted)) {
            $_data->company_id = !empty($_POST['company']) ? $_POST['company'] : NULL;
            $_data->category_id = !empty($_POST['category']) ? $_POST['category'] : NULL;
            $_data->invoice_no = $_POST['invoice_no'];
            $_data->invoice_date = date("Y-m-d", strtotime($_POST['invoice_date']));
            $_data->local_company_name = !empty($_POST['local_company_name']) ? $_POST['local_company_name'] : NULL;
            $_data->has_transport = isset($_POST['Purchase']['has_transport']) ? $_POST['Purchase']['has_transport'] : 0;
            $_data->created_by = $_POST['created_by'];
            $_data->modified = AppHelper::getDbTimestamp();
            $_data->modified_by = Yii::app()->user->id;

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while update record."));
                }

                $arrayProducts = Yii::app()->request->getPost('products');
                $arrayPID = Yii::app()->request->getPost('pid');
                $arrayQuantity = Yii::app()->request->getPost('quantity');
                $arrayPrices = Yii::app()->request->getPost('prices');

                if (empty($arrayProducts)) {
                    throw new CException(Yii::t('App', 'No product is selected!'));
                }

                $_orderItem = new PurchaseItem();
                $_items = $_orderItem->findAll('purchase_id=:purchase_id', array(':purchase_id' => $_data->id));

                foreach ($_items as $i => $_item) {
                    $productID = $arrayPID[$i];
                    $_item->product_id = $productID;

                    if (!empty($arrayQuantity[$productID])) {
                        $_item->quantity = $arrayQuantity[$productID];
                    }

                    if (!empty($arrayPrices[$productID])) {
                        $_item->price = $arrayPrices[$productID];
                    }

                    $_item->total = ($_item->quantity * $_item->price);

                    if (!$_item->save()) {
                        throw new CException(Yii::t('App', 'Error while saving order Items'));
                    }
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record updated successfull!");
                $this->redirect(array(AppUrl::URL_PURCHASE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionView() {
        $this->checkUserAccess('purchase_view');
        $this->setHeadTitle("Purchase Detail");
        $this->setPageTitle("Purchase Detail");
        $this->setCurrentPage(AppUrl::URL_PURCHASE);

        $_key = Yii::app()->request->getParam('id');
        $_model = new Purchase();
        $_data = $_model->with('items')->find('LOWER(invoice_no) = ?', array(strtolower($_key)));

        if (empty($_data->id)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        $_payModel = new Payment();
        $_payment = $_payModel->find("purchase_id=:purchase_id", array(":purchase_id" => $_data->id));

        $this->model['model'] = $_data;
        $this->model['payment'] = $_payment;
        $this->render('detail', $this->model);
    }

    public function actionReset($id) {
        $_model = new Purchase();
        $_data = $_model->with('stocks')->findByPk($id);

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            foreach ($_data->stocks as $stocks) {
                if (!$stocks->delete()) {
                    throw new CException(Yii::t('App', 'Error while resetting stocks'));
                }
            }

            $_data->status = AppConstant::ORDER_PENDING;
            if (!$_data->save()) {
                throw new CException(Yii::t("App", "Error while resetting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", "Data reset successfull!");
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }

        if (!empty(Yii::app()->request->urlReferrer)) {
            $this->redirect(Yii::app()->request->urlReferrer);
        } else {
            $this->redirect($this->createUrl(AppUrl::URL_PURCHASE));
        }
    }

    // update functions
    public function actionUpdate_company() {
        $_dataset = PurchaseItem::model()->findAll();
        echo count($_dataset) . "<br>";

        $_counter = 0;
        foreach ($_dataset as $_data) {
            $_counter++;
            $_data->company_id = Product::model()->find('id=:id', [':id' => $_data->product_id])->company_id;
            if ($_data->save()) {
                echo "{$_counter} = Saved<br>";
            } else {
                echo "{$_counter} = Failed<br>";
            }
        }
        exit;
    }

}
