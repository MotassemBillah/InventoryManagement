<?php

class PurchaseController extends AppController {

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
        $_company = Yii::app()->request->getPost('company');
        $_status = Yii::app()->request->getPost('status');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $invoiceNO = Yii::app()->request->getPost('q');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");
        $sum = array();

        $_model = new Purchase();
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
        if (!empty($_company)) {
            $criteria->addCondition("company_id =" . $_company);
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('invoice_date', $dateForm, $dateTo);
        }
        if (!empty($invoiceNO)) {
            $criteria->addCondition("invoice_no =" . $invoiceNO);
        }

        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "invoice_date DESC";
        $_dataset = $_model->findAll($criteria);

        foreach ($_dataset as $_data) {
            foreach ($_data->items as $items) {
                $sum[] = $items->total;
            }
        }
        $totalSum = array_sum($sum);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->model['total'] = AppHelper::getFloat($totalSum);
        $this->renderPartial('index', $this->model);
    }

    public function actionCreate() {
        $response = array();
        $sum = array();
        $_order = new Purchase();

        $_company = Yii::app()->request->getPost('company');
        $_category = Yii::app()->request->getPost('category');
        $invoice_no = Yii::app()->request->getPost('invoice_no');
        $invoice_date = Yii::app()->request->getPost('invoice_date');
        $local_company = Yii::app()->request->getPost('local_company_name');

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_company)) {
                throw new CException(Yii::t("App", "Please select a company"));
            }

            if (empty($invoice_no)) {
                throw new CException(Yii::t("App", "Please provide a invoice number"));
            } else if (!$this->invoiceAvailable($invoice_no)) {
                throw new CException(Yii::t("App", "Invoice number <b>{$invoice_no}</b> is already taken"));
            }

            if (empty($invoice_date)) {
                throw new CException(Yii::t("App", "Please provide a invoice date"));
            }

            $_order->company_id = !empty($_company) ? $_company : NULL;
            $_order->category_id = !empty($_category) ? $_category : NULL;
            $_order->invoice_no = $invoice_no;
            $_order->invoice_date = date("Y-m-d", strtotime($invoice_date));
            $_order->local_company_name = !empty($local_company) ? $local_company : NULL;
            $_order->has_transport = isset($_POST['Purchase']['has_transport']) ? $_POST['Purchase']['has_transport'] : 0;
            $_order->status = AppConstant::ORDER_PENDING;
            $_order->created = AppHelper::getDbTimestamp();
            $_order->created_by = Yii::app()->user->id;
            $_order->_key = AppHelper::getUnqiueKey();

            if (!$_order->save()) {
                throw new CException(Yii::t("App", "Error while saving data."));
            }

            $purchaseID = Yii::app()->db->getLastInsertID();
            $arrayProducts = Yii::app()->request->getPost('products');
            $arrayQuantity = Yii::app()->request->getPost('quantity');
            $arrayPrices = Yii::app()->request->getPost('prices');

            if (empty($arrayProducts)) {
                throw new CException(Yii::t("App", "Please select at least 1 product!"));
            }

            foreach ($arrayProducts as $key => $val) {
                if (!empty($arrayProducts[$key])) {
                    $productID = $val;
                    $_orderItem = new PurchaseItem();
                    $_orderItem->purchase_id = $purchaseID;
                    $_orderItem->product_id = $val;

                    if (empty($arrayQuantity[$productID])) {
                        throw new CException(Yii::t("App", "Quantity required for <b>" . AppObject::productName($productID) . "</b>"));
                    } else {
                        $_orderItem->quantity = $arrayQuantity[$productID];
                    }

                    if (empty($arrayPrices[$productID])) {
                        throw new CException(Yii::t("App", "Price required for <b>" . AppObject::productName($productID) . "</b>"));
                    } else {
                        $_orderItem->price = $arrayPrices[$productID];
                    }

                    $_orderItem->total = ($_orderItem->quantity * $_orderItem->price);

                    if (!$_orderItem->save()) {
                        throw new CException(Yii::t('App', 'Error while saving order items'));
                    }

                    $sum[] = $_orderItem->total;
                }
            }

            $totalSum = array_sum($sum);
            $modelPayment = new Payment();
            $modelPayment->purchase_id = $purchaseID;
            $modelPayment->company_id = $_order->company_id;
            $modelPayment->invoice_no = $_order->invoice_no;
            $modelPayment->type = "invoice";
            $modelPayment->pay_date = $_order->invoice_date;
            $modelPayment->payment_mode = AppConstant::PAYMENT_NO;
            $modelPayment->invoice_amount = $totalSum;
            $modelPayment->invoice_total = AppHelper::getFloat($totalSum);
            $modelPayment->net_amount = AppHelper::getFloat($totalSum);
            $modelPayment->due_amount = AppHelper::getFloat($totalSum);
            $modelPayment->balance_amount = -($totalSum);
            $modelPayment->created = AppHelper::getDbTimestamp();
            $modelPayment->created_by = Yii::app()->user->id;
            $modelPayment->_key = AppHelper::getUnqiueKey();
            if (!$modelPayment->save()) {
                throw new CException(Yii::t("App", "Error while saving payment."));
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['redirect'] = true;
            $response['message'] = "Record saved successfull!";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['redirect'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionProcess() {
        $_model = new Purchase();
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
                $stock->purchase_id = $_data->id;
                $stock->company_id = $_data->company_id;
                $stock->category_id = $_data->category_id;
                $stock->product_id = $items->product_id;
                $stock->quantity = $items->quantity;
                $stock->type = AppConstant::STOK_TYPE_PURCHASE;
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
        $_model = new Purchase();
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
        $_model = new Purchase();
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
                $response['message'] = "Data reset successfull.";
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
        $_key = Yii::app()->request->getParam('id');
        $model = new Purchase();
        $data = $model->find("LOWER(_key)=?", array(strtolower($_key)));

        $modelPayment = new Payment();
        $_payment = $modelPayment->find("purchase_id=:purchase_id", array(":purchase_id" => $data->id));

        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted=:is_deleted";
        $criteria->params = array(":is_deleted" => 0);
        $criteria->order = "name ASC";
        $_banklist = Bank::model()->findAll($criteria);

        $_settings = $this->getSettings();
        $_payModes = json_decode($_settings->payment_modes);

        $this->model['model'] = $data;
        $this->model['payment'] = $_payment;
        $this->model['bankList'] = $_banklist;
        $this->model['payModes'] = $_payModes;
        $this->renderPartial('payment', $this->model);
    }

    public function actionUpdate_payment() {
        $response = array();
        $model = new Payment();

        $paymentID = Yii::app()->request->getPost('paymentID');
        $payment = $model->findByPk($paymentID);

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
                throw new CException(Yii::t("App", "You must select a payment mode."));
            }

            if ($payment_mode == AppConstant::PAYMENT_CHECK) {
                $accountID = isset($_POST['Payment']['account_id']) ? $_POST['Payment']['account_id'] : "";
                $check_no = isset($_POST['Payment']['check_no']) ? $_POST['Payment']['check_no'] : "";

                if (empty($accountID)) {
                    throw new CException(Yii::t("App", "You must select an account."));
                }
                if (empty($check_no)) {
                    throw new CException(Yii::t("App", "You must provide a cheque number."));
                }

                $payment->account_id = $accountID;
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

                $totalPaidAmount = $invoicePaid + $advPaid;
                $accountBalance = AppObject::sumCashBalance($accountID);
                if ($accountBalance < $totalPaidAmount) {
                    throw new CException(Yii::t("App", "Not enough balance in your account to pay."));
                }

                $_modelAccountBalance = new AccountBalance();
                $payment_balance_info = AccountBalance::model()->find("payment_id=:payment_id", array(":payment_id" => $payment->id));
                if (!empty($payment_balance_info)) {
                    $payment_balance_info->account_id = $accountID;
                    $payment_balance_info->category = AppConstant::CASH_OUT;
                    $payment_balance_info->purpose = "Invoice payment";
                    $payment_balance_info->by_whom = AppObject::displayNameByUser(Yii::app()->user->id);
                    $payment_balance_info->amount = AppHelper::getFloat($totalPaidAmount);
                    if (!$payment_balance_info->save()) {
                        throw new CException(Yii::t("App", "Error while updating account balance info."));
                    }
                } else {
                    $_modelAccountBalance->account_id = $accountID;
                    $_modelAccountBalance->payment_id = $payment->id;
                    $_modelAccountBalance->category = AppConstant::CASH_OUT;
                    $_modelAccountBalance->purpose = "Invoice payment";
                    $_modelAccountBalance->by_whom = AppObject::displayNameByUser(Yii::app()->user->id);
                    $_modelAccountBalance->amount = AppHelper::getFloat($totalPaidAmount);
                    if (!$_modelAccountBalance->save()) {
                        throw new CException(Yii::t("App", "Error while updating account balance info."));
                    }
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

                $payment->account_id = NULL;
                $payment->check_no = NULL;
            } else {
                $payment->account_id = NULL;
                $payment->check_no = NULL;
                $payment->invoice_paid = NULL;
                $payment->advance_amount = NULL;
                $payment->due_amount = $netAmount;
                $payment->balance_amount = -($netAmount);
            }

            if (!$payment->save()) {
                throw new CException(Yii::t("App", "Error while updating payment info."));
            }

            $modelBalanceSheet = new Balancesheet();
            $balanceSheet = $modelBalanceSheet->find("payment_id=:payment_id", array(":payment_id" => $payment->id));
            if (!empty($balanceSheet)) {
                $balanceSheet->credit = AppHelper::getFloat($totalPaid);
                $balanceSheet->balance = $balanceSheet->credit;
                if (!$balanceSheet->save()) {
                    throw new CException(Yii::t("App", "Error while saving leger record."));
                }
            } else {
                $modelBalanceSheet->payment_id = $payment->id;
                $modelBalanceSheet->pay_date = $payment->pay_date;
                $modelBalanceSheet->credit = AppHelper::getFloat($totalPaid);
                $modelBalanceSheet->balance = $modelBalanceSheet->credit;
                if (!$modelBalanceSheet->save()) {
                    throw new CException(Yii::t("App", "Error while saving leger record."));
                }
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = "Record saved successfully. Redirecting.....";
            $response['goto'] = Yii::app()->createUrl(AppUrl::URL_PURCHASE_VIEW, array('id' => $payment->invoice_no));
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionSave_info() {
        $_model = new PurchaseInfo();
        $response = array();
        $infoID = Yii::app()->request->getPost('infoID');
        $purchaseID = Yii::app()->request->getPost('purchaseID');

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($infoID)) {
                $_model->purchase_id = $purchaseID;
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
        $model = new PurchaseItem();
        $response = array();
        $_key = Yii::app()->request->getParam('key');
        $purchaseID = Yii::app()->request->getParam('pid');
        $obj = $model->findByPk($_key);

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (!$obj->delete()) {
                throw new CException(Yii::t("App", "Operation failed! Please try again."));
            }

            $criteria = new CDbCriteria();
            $criteria->condition = "purchase_id=:purchase_id";
            $criteria->params = array("purchase_id" => $purchaseID);
            $dataset = $model->findAll($criteria);

            if (count($dataset) == 0) {
                $purchase = Purchase::model()->findByPk($purchaseID);
                if (!$purchase->delete()) {
                    throw new CException(Yii::t("App", "Operation failed! Please try again {Purchase}."));
                } else {
                    $response['redirect'] = true;
                }
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = "Remove Successfull!";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function invoiceAvailable($invoice) {
        $model = new Purchase();
        $thisInvoice = AppHelper::getCleanValue($invoice);
        $checkInvoice = $model->find('invoice_no=:invoice_no', array(':invoice_no' => $thisInvoice));

        if (empty($checkInvoice)) {
            return true;
        } else {
            return false;
        }
    }

    public function actionInvoice_bill() {
        $model = new Purchase();
        $response = array();
        $invoice = Yii::app()->request->getPost('invoice_no');
        $data = $model->find("invoice_no=:invoice_no", array(":invoice_no" => $invoice));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($data->purchase_id)) {
                throw new CException(Yii::t("App", "No record found."));
            }

            $amount = AppObject::sumPurchaseTotalById('retail_total', $data->id);

            $_transaction->commit();
            $response['success'] = true;
            $response['amount'] = $amount;
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionProductlist() {
        $model = new Product();
        $response = array();
        $type = Yii::app()->request->getPost('ptype');

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            $criteria = new CDbCriteria();
            //$criteria->condition = 'type=:type';
            //if (isset($_POST['products'])) {
            //$criteria->addNotInCondition('product_id', $_POST['products']);
            //}
            //$criteria->params = array(':type' => $type);
            $dataset = $model->findAll($criteria);
            $_html = '';

            foreach ($dataset as $data) {
                $_html .= "<tr>";
                $_html .= "<td>";
                $_html .= "<label class='txt_np' for='product-{$data->id}'>";
                $_html .= "<input type='checkbox' id='product-{$data->id}' name='aproducts[]' value='{$data->id}'>&nbsp;";
                $_html .= AppObject::productName($data->id) . " ( " . AppObject::categoryName($data->category_id) . " )";
                $_html .= "</label>";
                $_html .= "</td>";
                $_html .= "<td>";
                foreach ($data->sizes as $sizes) {
                    $_html .= "<div class='psizes clearfix'>";
                    $_html .= "<span class='pull-left wxs_100'>";
                    $_html .= "<label class='txt_np' for='size-{$sizes->id}'>";
                    $_html .= "<input type='checkbox' id='size-{$sizes->id}' name='asizes[]' value='{$sizes->product_id}_{$sizes->id}'>&nbsp;";
                    $_html .= AppObject::productSize($sizes->id);
                    $_html .= "</label>";
                    $_html .= "</span>";
                    $_html .= "<span class='pull-right qty_price'><input type='number' class='form-control rp' name='aprices[{$sizes->id}]' placeholder='price' min='0' step='any'></span>";
                    $_html .= "<span class='pull-right qty_price'><input type='number' class='form-control rp' name='afrees[{$sizes->id}]' placeholder='free' min='0' step='any'></span>";
                    $_html .= "<span class='pull-right qty_price'><input type='number' class='form-control rp' name='aquantity[{$sizes->id}]' placeholder='quantity' min='0' step='any'></span>";
                    $_html .= "</div>";
                }
                $_html .= "</td>";
                $_html .= "</tr>";
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['data'] = $_html;
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

}
