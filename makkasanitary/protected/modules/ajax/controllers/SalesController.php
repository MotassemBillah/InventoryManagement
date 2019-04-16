<?php

class SalesController extends AppController {

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
        $_status = Yii::app()->request->getPost('status');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $_customer = Yii::app()->request->getPost('customer');
        $_invoiceNo = Yii::app()->request->getPost('invoice');
        $_user = Yii::app()->request->getPost('user');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new Sale();
        $criteria = new CDbCriteria();
        if ($_status != "All") {
            $criteria->condition = "status=:status";
            $criteria->params = array(":status" => $_status);
        } else {
            $criteria->addInCondition("status", array(AppConstant::ORDER_COMPLETE, AppConstant::ORDER_PENDING));
        }
        if (Yii::app()->user->role != AppConstant::ROLE_SUPERADMIN) {
            $criteria->addCondition("created_by =" . Yii::app()->user->id);
        }
        if (!empty($_customer)) {
            $_cmodel = new Customer();
            $_ccriteria = new CDbCriteria();
            $_ccriteria->addCondition("name LIKE '%" . trim($_customer) . "%'");
            $_cdata = $_cmodel->findAll($_ccriteria);
            foreach ($_cdata as $_cd) {
                $_cid[] = $_cd->id;
            }
            $criteria->addInCondition("customer_id", $_cid);
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('created', $dateForm, $dateTo);
        }
        if (!empty($_invoiceNo)) {
            $criteria->addCondition("invoice_no = " . $_invoiceNo);
        }
        if (!empty($_user)) {
            $criteria->addCondition("created_by = {$_user}");
        }
        $criteria->order = "created DESC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('index', $this->model);
    }

    public function actionExport() {
        $_customer = Yii::app()->request->getPost('customer');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new CustomerPayment();
        $criteria = new CDbCriteria();
        if (!empty($_customer)) {
            $criteria->addCondition("customer_id =" . $_customer);
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('pay_date', $dateForm, $dateTo);
        }
        $criteria->order = "pay_date DESC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->renderPartial('export_view', $this->model);
    }

    public function actionReturn_items() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $_customer = Yii::app()->request->getPost('customer');
        $_status = Yii::app()->request->getPost('status');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");
        $sum = array();

        $_model = new SaleReturn();
        $criteria = new CDbCriteria();

        if (!empty($_customer)) {
            $criteria->addCondition("customer_id =" . $_customer);
        }
        if ($_status !== "All") {
            $criteria->condition = "status=:status";
            $criteria->params = array(":status" => $_status);
        } else {
            $criteria->addInCondition("status", array(AppConstant::ORDER_COMPLETE, AppConstant::ORDER_PENDING));
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('created', $dateForm, $dateTo);
        }

        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "created DESC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('return_items', $this->model);
    }

    public function actionView() {
        $_invoiceNo = Yii::app()->request->getPost('invoice_no');

        $_model = new Sale();
        $_data = $_model->find("LOWER(invoice_no) = ?", array(strtolower($_invoiceNo)));

        if (!empty($_data)) {
            $_showContent = 1;
            $_payModel = new CustomerPayment();
            $_payment = $_payModel->find("sale_id=:sale_id", array(":sale_id" => $_data->id));
            $this->model['payment'] = $_payment;
        } else {
            $_showContent = 0;
        }

        $this->model['hasContent'] = $_showContent;
        $this->model['model'] = $_data;
        $this->renderPartial('view', $this->model);
    }

    public function actionCreate() {
        $response = array();
        $sum = array();
        $customerType = Yii::app()->request->getPost('customer_type');
        $customer = Yii::app()->request->getPost('customer_id');
        $invoiceNo = Yii::app()->request->getPost('invoice_no');

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($customerType)) {
                throw new CException(Yii::t("App", "Customer type required!"));
            }

            if ($customerType == "exist") {
                if (empty($customer) || !is_numeric($customer)) {
                    throw new CException(Yii::t("App", "Please select a customer."));
                }
                $customerID = $customer;
            } else {
                $_modelCustomer = new Customer();
                $_modelCustomer->attributes = $_POST['Customer'];
                $_modelCustomer->name = isset($_POST['Customer']['name']) ? $_POST['Customer']['name'] : "";
                $_modelCustomer->phone = isset($_POST['Customer']['phone']) ? $_POST['Customer']['phone'] : "";
                $_modelCustomer->address = isset($_POST['Customer']['address']) ? $_POST['Customer']['address'] : "";
                $_modelCustomer->_key = AppHelper::getUnqiueKey();

                if (!$_modelCustomer->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_modelCustomer)));
                }

                if (!$_modelCustomer->save()) {
                    throw new CException(Yii::t("App", "Error while saving customer data."));
                }
                $customerID = Yii::app()->db->getLastInsertId();
            }

            $_order = new Sale();
            $_order->customer_id = $customerID;
            $_order->invoice_no = !empty($invoiceNo) ? $invoiceNo : date("dHis");
            $_order->has_transport = isset($_POST['transport']) ? $_POST['transport'] : 0;
            $_order->status = AppConstant::ORDER_PENDING;
            $_order->created = AppHelper::getDbTimestamp();
            $_order->created_by = Yii::app()->user->id;
            $_order->on_time = date('Y-m-d H:i:s');
            $_order->_key = AppHelper::getUnqiueKey();

            if (!$_order->validate()) {
                throw new CException(Yii::t("App", CHtml::errorSummary($_order)));
            }

            if (!$_order->save()) {
                throw new CException(Yii::t("App", "Error while saving data."));
            }

            $saleID = Yii::app()->db->getLastInsertID();
            $arrayProducts = Yii::app()->request->getPost('products');
            $arrayQuantity = Yii::app()->request->getPost('quantity');
            $arrayPrices = Yii::app()->request->getPost('prices');

            if (empty($arrayProducts)) {
                throw new CException(Yii::t("App", "Please select at least 1 product."));
            }

            foreach ($arrayProducts as $key => $val) {
                $productID = $val;
                $productName = AppObject::productName($productID);

                if (!empty($arrayProducts[$key])) {
                    $_orderItem = new SaleItem();
                    $_orderItem->sale_id = $saleID;
                    $_orderItem->product_id = $productID;

                    if (empty($arrayQuantity[$productID])) {
                        throw new CException(Yii::t("App", "Quantity required for <b>{$productName}</b>"));
                    } elseif (!is_numeric($arrayQuantity[$productID])) {
                        throw new CException(Yii::t("App", "Only number allowed for quantity (0-9)"));
                    } else {
                        $_orderItem->quantity = $arrayQuantity[$productID];
                    }

                    if (!empty($arrayPrices[$productID])) {
                        $_orderItem->price = $arrayPrices[$productID];
                    }
                    $_orderItem->total = ($_orderItem->quantity * $_orderItem->price);

                    if (!$_orderItem->save()) {
                        throw new CException(Yii::t('App', 'Error while saving order items.'));
                    }

                    $sum['s'][] = ($_orderItem->quantity * $_orderItem->price);
                    $productPrice = AppObject::productPrice($productID);
                    $sum['p'][] = ($_orderItem->quantity * $productPrice);
                }
            }

            $totalSumSale = array_sum($sum['s']);
            $modelPayment = new CustomerPayment();
            $modelPayment->customer_id = $customerID;
            $modelPayment->sale_id = $saleID;
            $modelPayment->invoice_no = $_order->invoice_no;
            $modelPayment->type = AppConstant::TYPE_INVOICE;
            $modelPayment->pay_date = date("Y-m-d", strtotime($_order->created));
            $modelPayment->payment_mode = AppConstant::PAYMENT_NO;
            $modelPayment->invoice_amount = AppHelper::getFloat($totalSumSale);
            $modelPayment->invoice_total = AppHelper::getFloat($totalSumSale);
            $modelPayment->net_amount = AppHelper::getFloat($totalSumSale);
            $modelPayment->due_amount = AppHelper::getFloat($totalSumSale);
            $modelPayment->balance_amount = -($totalSumSale);
            $modelPayment->created = AppHelper::getDbTimestamp();
            $modelPayment->created_by = Yii::app()->user->id;
            $modelPayment->_key = AppHelper::getUnqiueKey();
            if (!$modelPayment->save()) {
                throw new CException(Yii::t("App", "Error while saving payment data."));
            }

            $totalSumPurchase = array_sum($sum['p']);
            $invoice = new Invoice();
            $invoice->sale_id = $saleID;
            $invoice->invoice_no = $_order->invoice_no;
            $invoice->invoice_date = date("Y-m-d", strtotime($_order->created));
            $invoice->invoice_amount = AppHelper::getFloat($totalSumSale);
            $invoice->purchase_amount = AppHelper::getFloat($totalSumPurchase);
            $invoice->profit = AppHelper::getFloat($invoice->invoice_amount);
            $invoice->_key = AppHelper::getUnqiueKey();
            if (!$invoice->save()) {
                throw new CException(Yii::t("App", "Error while saving invoice record."));
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['redirect'] = true;
            $response['message'] = "Record saved successfull!";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionCreate_return() {
        $response = array();
        $sum = array();
        $customerID = Yii::app()->request->getPost('customerID');
        $saleID = Yii::app()->request->getPost('saleID');
        $saleInvoiceNO = Yii::app()->request->getPost('saleInvoiceNO');
        $invoiceNO = Yii::app()->request->getPost('invoiceNO');

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            $_order = new SaleReturn();
            $_order->customer_id = $customerID;
            $_order->sale_id = $saleID;
            $_order->sale_invoice = $saleInvoiceNO;
            $_order->return_invoice = !empty($invoiceNO) ? $invoiceNO : date("dHis");
            $_order->status = AppConstant::ORDER_PENDING;
            $_order->created = AppHelper::getDbTimestamp();
            $_order->created_by = Yii::app()->user->id;
            $_order->_key = AppHelper::getUnqiueKey();

            if (!$_order->validate()) {
                throw new CException(Yii::t("App", CHtml::errorSummary($_order)));
            }

            if (!$_order->save()) {
                throw new CException(Yii::t("App", "Error while saving data."));
            }

            $saleReturnID = Yii::app()->db->getLastInsertId();
            $arrayProducts = Yii::app()->request->getPost('products');
            $arrayQuantity = Yii::app()->request->getPost('quantity');
            $arrayPrices = Yii::app()->request->getPost('prices');

            if (empty($arrayProducts)) {
                throw new CException(Yii::t("App", "Please select at least 1 product."));
            }

            foreach ($arrayProducts as $key => $val) {
                $productID = $val;
                $productName = AppObject::productName($productID);

                if (!empty($arrayProducts[$key])) {
                    $_orderItem = new SaleReturnItem();
                    $_orderItem->sale_return_id = $saleReturnID;
                    $_orderItem->product_id = $productID;

                    if (empty($arrayQuantity[$productID])) {
                        throw new CException(Yii::t("App", "Quantity required for <b>{$productName}</b>"));
                    } elseif (!is_numeric($arrayQuantity[$productID])) {
                        throw new CException(Yii::t("App", "Only number allowed for quantity (0-9)"));
                    } else {
                        $_orderItem->quantity = $arrayQuantity[$productID];
                    }

                    if (!empty($arrayPrices[$productID])) {
                        $_orderItem->price = $arrayPrices[$productID];
                    } else {
                        throw new CException(Yii::t("App", "Price required for <b>{$productName}</b>"));
                    }

                    $_orderItem->total = ($_orderItem->quantity * $_orderItem->price);

                    if (!$_orderItem->save()) {
                        throw new CException(Yii::t('App', 'Error while saving return order items.'));
                    }
                }

                $sum[] = $_orderItem->total;
            }

            $totalSumSale = array_sum($sum);
            $modelPayment = new SaleReturnPayment();
            //$modelPayment->customer_id = $customerID;
            $modelPayment->sale_return_id = $saleReturnID;
            $modelPayment->pay_date = date("Y-m-d", strtotime($_order->created));
            $modelPayment->type = "invoice return";
            $modelPayment->invoice_no = $_order->return_invoice;
            $modelPayment->invoice_amount = AppHelper::getFloat($totalSumSale);
            $modelPayment->created = AppHelper::getDbTimestamp();
            $modelPayment->created_by = Yii::app()->user->id;
            $modelPayment->_key = AppHelper::getUnqiueKey();
            if (!$modelPayment->save()) {
                throw new CException(Yii::t("App", "Error while saving return payment."));
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['redirect'] = true;
            $response['message'] = "Record saved successfull!";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionProcess() {
        $_model = new Sale();
        $response = array();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            foreach ($_data->items as $items) {
                $stock = new Stock();
                $stock->sale_id = $_data->id;
                $stock->product_id = $items->product_id;
                $stock->company_id = $items->product->company_id;
                $stock->category_id = $items->product->category_id;
                $stock->quantity = -($items->quantity);
                $stock->type = AppConstant::STOK_TYPE_SALE;
                $stock->created = AppHelper::getDbTimestamp();
                $stock->_key = AppHelper::getUnqiueKey();
                if (!$stock->save()) {
                    throw new CException(Yii::t("App", "Error while update stock record."));
                }
            }

            $_data->status = AppConstant::ORDER_COMPLETE;
            if (!$_data->save()) {
                throw new CException(Yii::t("App", "Error while saving record."));
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = "Order process successfull!";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionProcess_return() {
        $_model = new SaleReturn();
        $response = array();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            foreach ($_data->items as $items) {
                $stock = new Stock();
                $stock->product_id = $items->product_id;
                $stock->sale_return_id = $_data->id;
                $stock->quantity = $items->quantity;
                $stock->type = AppConstant::STOK_TYPE_SALE_RETURN;
                $stock->created = AppHelper::getDbTimestamp();
                $stock->_key = AppHelper::getUnqiueKey();
                if (!$stock->save()) {
                    throw new CException(Yii::t("App", "Error while update stock record."));
                }
            }

            $_data->status = AppConstant::ORDER_COMPLETE;
            if (!$_data->save()) {
                throw new CException(Yii::t("App", "Error while saving record."));
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = "Order process successfull!";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionDeleteall() {
        $_model = new Sale();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    foreach ($_obj->items as $item) {
                        if (!$item->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {items}"));
                        }
                    }

                    if (!empty($_obj->stocks)) {
                        foreach ($_obj->stocks as $stock) {
                            if (!$stock->delete()) {
                                throw new CException(Yii::t('App', "Error while deleting record {stocks}"));
                            }
                        }
                    }

                    if (!empty($_obj->info)) {
                        if (!$_obj->info->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {info}"));
                        }
                    }

                    if (!empty($_obj->payment)) {
                        if (!$_obj->payment->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {payment}"));
                        }
                    }

                    if (!empty($_obj->invoice)) {
                        if (!$_obj->invoice->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {invoice}"));
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

    public function actionDeleteall_return() {
        $_model = new SaleReturn();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    foreach ($_obj->items as $item) {
                        if (!$item->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {items}"));
                        }
                    }

                    if (!empty($_obj->stocks)) {
                        foreach ($_obj->stocks as $stock) {
                            if (!$stock->delete()) {
                                throw new CException(Yii::t('App', "Error while deleting record {stocks}"));
                            }
                        }
                    }

                    if (!empty($_obj->info)) {
                        if (!$_obj->info->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {info}"));
                        }
                    }

                    if (!empty($_obj->payment)) {
                        if (!$_obj->payment->delete()) {
                            throw new CException(Yii::t('App', "Error while deleting record {payment}"));
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

    public function actionResetall() {
        $_model = new Sale();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->with('stocks')->findByPk($_data[$i]);

                    if (!empty($_obj->stocks)) {
                        foreach ($_obj->stocks as $stock) {
                            if (!$stock->delete()) {
                                throw new CException(Yii::t('App', "Error while resetting stocks"));
                            }
                        }
                    }

                    $_obj->status = AppConstant::ORDER_PENDING;
                    if (!$_obj->save()) {
                        throw new CException(Yii::t("App", "Error while resetting data."));
                    }
                }

                $_transaction->commit();
                $response['success'] = true;
                $response['message'] = "Data reset successfull!";
            } catch (CException $e) {
                $_transaction->rollback();
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['success'] = false;
            $response['message'] = "No record found to reset!";
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionPayment() {
        $_key = Yii::app()->request->getParam('paymentID');
        $purchaseTotal = Yii::app()->request->getParam('purchaseTotal');
        $saleTotal = Yii::app()->request->getParam('saleTotal');
        $model = new Sale();
        $_data = $model->find("LOWER(_key)=?", array(strtolower($_key)));

        $customerPayment = new CustomerPayment();
        $_payment = $customerPayment->find("sale_id=:sale_id", array(":sale_id" => $_data->id));

        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted=:is_deleted";
        $criteria->params = array(":is_deleted" => 0);
        $criteria->order = "name ASC";
        $_banklist = Bank::model()->findAll($criteria);

        $_settings = $this->getSettings();
        $_payModes = json_decode($_settings->payment_modes);

        $this->model['model'] = $_data;
        $this->model['payment'] = $_payment;
        $this->model['bankList'] = $_banklist;
        $this->model['payModes'] = $_payModes;
        $this->model['ptotal'] = $purchaseTotal;
        $this->model['stotal'] = $saleTotal;
        $this->renderPartial('payment', $this->model);
    }

    public function actionUpdate_payment() {
        $response = array();
        $paymentID = Yii::app()->request->getPost('paymentID');
        $purchaseTotal = Yii::app()->request->getPost('pTotal');
        $saleTotal = Yii::app()->request->getPost('sTotal');
        $payment_mode = Yii::app()->request->getPost('payment_mode');
        $invoiceAmount = Yii::app()->request->getPost('txtInvoiceAmount');
        $transportCost = Yii::app()->request->getPost('txtTransportCost');
        $laborCost = Yii::app()->request->getPost('txtLaborCost');
        $invoiceTtotal = Yii::app()->request->getPost('txtTotalAmount');
        $lessType = Yii::app()->request->getPost('lessType');
        $lessAmount = Yii::app()->request->getPost('txtLessAmount');
        $netAmount = Yii::app()->request->getPost('txtNetAmount');
        $totalPaid = Yii::app()->request->getPost('txtTotalPaidAmount');
        $invoicePaid = Yii::app()->request->getPost('txtPaidAmount');
        $advPaid = Yii::app()->request->getPost('txtAdvAmount');

        $model = new CustomerPayment();
        $payment = $model->findByPk($paymentID);

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            $payment->payment_mode = $payment_mode;
            $payment->invoice_amount = AppHelper::getFloat($invoiceAmount);
            $payment->transport = !empty($transportCost) ? AppHelper::getFloat($transportCost) : NULL;
            $payment->labor = !empty($laborCost) ? AppHelper::getFloat($laborCost) : NULL;
            $payment->invoice_total = !empty($invoiceTtotal) ? AppHelper::getFloat($invoiceTtotal) : NULL;
            $payment->discount_type = !empty($lessType) ? $lessType : NULL;
            $payment->discount_amount = isset($lessAmount) ? AppHelper::getFloat($lessAmount) : NULL;
            $payment->net_amount = !empty($netAmount) ? AppHelper::getFloat($netAmount) : NULL;
            $payment->total_paid = !empty($totalPaid) ? AppHelper::getFloat($totalPaid) : NULL;
            $payment->invoice_paid = !empty($invoicePaid) ? AppHelper::getFloat($invoicePaid) : NULL;
            $payment->advance_amount = !empty($advPaid) ? AppHelper::getFloat($advPaid) : NULL;
            $payment->modified = AppHelper::getDbTimestamp();
            $payment->modified_by = Yii::app()->user->id;

            if (empty($payment_mode)) {
                throw new CException(Yii::t("App", "You must select Payment mode."));
            }

            if ($payment_mode == AppConstant::PAYMENT_CHECK) {
                $bank_name = isset($_POST['CustomerPayment']['bank_name']) ? $_POST['CustomerPayment']['bank_name'] : "";
                $check_no = isset($_POST['CustomerPayment']['check_no']) ? $_POST['CustomerPayment']['check_no'] : "";

                if (empty($bank_name)) {
                    throw new CException(Yii::t("App", "You must select a bank."));
                }
                if (empty($check_no)) {
                    throw new CException(Yii::t("App", "You must provide a cheque number."));
                }

                $payment->bank_name = $bank_name;
                $payment->check_no = $check_no;

                if (empty($invoicePaid)) {
                    throw new CException(Yii::t("App", "You must enter paid amount."));
                }

                if ($totalPaid < $netAmount) {
                    $payment->due_amount = ($netAmount - $totalPaid);
                    $payment->balance_amount = -($netAmount - $totalPaid);
                } else if ($totalPaid > $netAmount) {
                    $payment->due_amount = NULL;
                    $payment->balance_amount = ($totalPaid - $netAmount);
                } else {
                    $payment->due_amount = NULL;
                    $payment->balance_amount = NULL;
                }
            } else if ($payment_mode == AppConstant::PAYMENT_CASH) {
                if (empty($invoicePaid)) {
                    throw new CException(Yii::t("App", "You must enter paid amount."));
                }

                if ($totalPaid < $netAmount) {
                    $payment->due_amount = ($netAmount - $totalPaid);
                    $payment->balance_amount = -($netAmount - $totalPaid);
                } else if ($totalPaid > $netAmount) {
                    $payment->due_amount = NULL;
                    $payment->balance_amount = ($totalPaid - $netAmount);
                } else {
                    $payment->due_amount = NULL;
                    $payment->balance_amount = NULL;
                }
            } else {
                $payment->invoice_paid = NULL;
                $payment->advance_amount = NULL;
                $payment->due_amount = $netAmount;
                $payment->balance_amount = -($netAmount);
            }

            if (!$payment->save()) {
                throw new CException(Yii::t("App", "Error while updating payment info."));
            }

            $invoice = Invoice::model()->find("invoice_no=:invoice_no", array(":invoice_no" => $payment->invoice_no));
            $invoice->invoice_amount = AppHelper::getFloat($netAmount);
            $invoice->purchase_amount = AppHelper::getFloat($purchaseTotal);
            $invoice->profit = AppHelper::getFloat($saleTotal - $purchaseTotal);
            $invoice->discount_amount = AppHelper::getFloat($lessAmount);
            $invoice->net_profit = AppHelper::getFloat($invoice->profit - $invoice->discount_amount);
            if (!$invoice->save()) {
                throw new CException(Yii::t("App", "Error while saving invoice record."));
            }

            $modelBalanceSheet = new Balancesheet();
            $balanceSheet = $modelBalanceSheet->find("customer_payment_id=:customer_payment_id", array(":customer_payment_id" => $payment->id));
            if (!empty($balanceSheet)) {
                $balanceSheet->debit = AppHelper::getFloat($totalPaid);
                $balanceSheet->balance = $balanceSheet->debit;
                if (!$balanceSheet->save()) {
                    throw new CException(Yii::t("App", "Error while saving leger record."));
                }
            } else {
                $modelBalanceSheet->customer_payment_id = $payment->id;
                $modelBalanceSheet->pay_date = $payment->pay_date;
                $modelBalanceSheet->debit = AppHelper::getFloat($totalPaid);
                $modelBalanceSheet->balance = $modelBalanceSheet->debit;
                if (!$modelBalanceSheet->save()) {
                    throw new CException(Yii::t("App", "Error while saving leger record."));
                }
            }

            Customer::model()->updateBalance($payment->customer_id);

            if ($this->settings->sendsms == 1) {
                $sms = Yii::app()->smsGlobal;
                $_to = $payment->customer->mobile;
                $_msg = "Hello <u>" . $payment->customer->name . "</u> \r\n";
                $_msg .= "Payment summary for invoice number <u>" . $payment->invoice_no . "</u> \r\n";
                $_msg .= "Invoice amount = " . $payment->invoice_amount . " \r\n";
                $_msg .= "Paid amount = " . $payment->invoice_paid . " \r\n";
                if (!empty($payment->due_amount)) {
                    $_msg .= "Due amount = " . $payment->due_amount . " \r\n";
                }
                $_msg .= "Date/Time = " . date('j M Y, h:i A', strtotime($payment->modified)) . " \r\n";
                $_message = array('to' => $_to, 'message' => $_msg);

                if (!$sms->sendSMS($_message)) {
                    throw new CException(Yii::t("App", "Message sending failed."));
                }
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = "Record update successfull. Redirecting.....";
            $response['goto'] = Yii::app()->createUrl(AppUrl::URL_SALE_VIEW, array('id' => $payment->invoice_no));
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionPayment_view() {
        $_key = Yii::app()->request->getParam('id');
        $_model = new CustomerPayment();
        $_data = $_model->find("LOWER(_key)=?", array(strtolower($_key)));
        $this->model['model'] = $_data;
        $this->renderPartial('invoice', $this->model);
    }

    public function actionSave_info() {
        $_model = new SaleInfo();
        $response = array();
        $infoID = Yii::app()->request->getPost('infoID');
        $saleID = Yii::app()->request->getPost('saleID');

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($infoID)) {
                $_model->sale_id = $saleID;
                $_model->transport_name = AppHelper::capFirstWord($_POST['info_transport_name']);
                $_model->transport_driver_name = AppHelper::capFirstWord($_POST['info_transport_driver']);
                $_model->transport_driver_phone = $_POST['info_transport_driver_phone'];
                $_model->viechel_no = strtoupper($_POST['info_viechel_no']);
                $_model->supervisor_name = AppHelper::capFirstWord($_POST['info_supervisor_name']);
                $_model->supervisor_phone = $_POST['info_supervisor_phone'];
                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving sale transport info."));
                }
            } else {
                $_data = $_model->findByPk($infoID);
                $_data->transport_name = AppHelper::capFirstWord($_POST['info_transport_name']);
                $_data->transport_driver_name = AppHelper::capFirstWord($_POST['info_transport_driver']);
                $_data->transport_driver_phone = $_POST['info_transport_driver_phone'];
                $_data->viechel_no = strtoupper($_POST['info_viechel_no']);
                $_data->supervisor_name = AppHelper::capFirstWord($_POST['info_supervisor_name']);
                $_data->supervisor_phone = $_POST['info_supervisor_phone'];
                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while updating sale transport info."));
                }
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = "Record saved successfull.";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionRemove() {
        $response = array();
        $_itemID = Yii::app()->request->getParam('itemNo');
        $model = new SaleItem();
        $obj = $model->findByPk($_itemID);

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (!$obj->delete()) {
                throw new CException(Yii::t("App", "Operation failed! Please try again."));
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = "Item Remove Successfull.";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionSearch() {
        $companyID = Yii::app()->request->getPost('company');
        $typeID = Yii::app()->request->getPost('type');
        $categoryID = Yii::app()->request->getPost('category');
        $q = Yii::app()->request->getPost('q');

        $_model = new Product();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted=0";

        if (!empty($companyID)) {
            $criteria->addCondition("company_id =" . $companyID);
        }
        if (!empty($typeID)) {
            $criteria->addCondition("type =" . $typeID);
        }
        if (!empty($categoryID)) {
            $criteria->addCondition("category_id =" . $categoryID);
        }
        if (!empty($q)) {
            $criteria->addCondition("name like '%" . $q . "%'");
            $this->model['highlight'] = $q;
        }

        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('search', $this->model);
    }

    public function actionAdd_to_cart() {
        $response = array();
        $_model = new Cart();

        $productID = Yii::app()->request->getPost('pid');
        $qty = Yii::app()->request->getPost('qty');
        $price = Yii::app()->request->getPost('price');

        try {
            $_model->product_id = $productID;
            $_model->qty = $qty;
            $_model->price = $price;
            $_model->token = date("Ymd") . Yii::app()->user->id;
            $_model->created_by = Yii::app()->user->id;

            if (!$_model->save()) {
                throw new CException("App", "Cart update failed.");
            }

            $response['success'] = true;
            $response['message'] = "Cart update successfull.";
        } catch (CException $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionAdd() {
        $response = array();
        $saleID = Yii::app()->request->getPost('saleId');
        $productID = Yii::app()->request->getPost('productID');
        $_model = new SaleItem();

        if (!empty($saleID) && !empty($productID)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                $_model->sale_id = $saleID;
                $_model->product_id = $productID;
                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while adding items"));
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
            $response['message'] = "No product selected";
        }

        echo CJSON::encode($response);
        return CJSON::encode($response);
    }

}
