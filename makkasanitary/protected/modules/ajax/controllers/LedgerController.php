<?php

class LedgerController extends AppController {

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
        $_headID = Yii::app()->request->getPost('head_id');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        //$invoiceNO = Yii::app()->request->getPost('q');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new LedgerPayment();
        $criteria = new CDbCriteria();
        if (!empty($_headID)) {
            $criteria->condition = "head_id = " . $_headID;
            //$criteria->addCondition("name LIKE :match OR mobile LIKE :match OR phone LIKE :match");
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('pay_date', $dateForm, $dateTo);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        //$criteria->order = "name ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('index', $this->model);
    }

    public function actionCreate() {
        $response = array();
        $_particulers = Yii::app()->request->getPost('description');
        $_purpose = Yii::app()->request->getPost('purpose');
        $_debit = Yii::app()->request->getPost('debit');
        $_credit = Yii::app()->request->getPost('credit');
        $_accountID = Yii::app()->request->getPost('account_id');
        $check_no = Yii::app()->request->getPost('check_no');
        $ledgerPayment = new LedgerPayment();

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_POST['head_id'])) {
                throw new CException(Yii::t("App", "Please select a head."));
            }

            if (empty($_POST['pay_date'])) {
                throw new CException(Yii::t("App", "Please select a date."));
            }

            $ledgerPayment->head_id = $_POST['head_id'];
            $ledgerPayment->sub_head_id = $_POST['sub_head_id'];
            $ledgerPayment->pay_date = !empty($_POST['pay_date']) ? date('Y-m-d', strtotime($_POST['pay_date'])) : date('Y-m-d');
            if (!$ledgerPayment->save()) {
                throw new CException(Yii::t("App", "Error while saving data."));
            }
            $lastID = Yii::app()->db->getLastInsertId();

            if (empty($_particulers)) {
                throw new CException(Yii::t("App", "Please select at least 1 particuler."));
            }

            if (!empty($_particulers)) {
                foreach ($_particulers as $_key => $value) {
                    if (!empty($_particulers[$_key])) {
                        $arrID = explode("_", $value);
                        $particulerID = $arrID[0];
                        $descriptionID = $arrID[1];
                        $descrpName = LedgerParticulerDescription::model()->getNameById($descriptionID);

                        $paymentItem = new LedgerPaymentItem();
                        $paymentItem->ledger_payment_id = $lastID;
                        $paymentItem->sub_head_id = $_POST['sub_head_id'];
                        $paymentItem->particuler_id = $particulerID;
                        $paymentItem->description_id = $descriptionID;
                        $paymentItem->pay_date = $ledgerPayment->pay_date;

                        if (empty($_debit[$descriptionID]) && empty($_credit[$descriptionID])) {
                            throw new CException(Yii::t("App", "you must enter debit or credit amount for <b>" . $descrpName . "</b>"));
                        }
                        if (!empty($_purpose[$descriptionID])) {
                            $paymentItem->purpose = $_purpose[$descriptionID];
                        }
                        if (!empty($_debit[$descriptionID])) {
                            $paymentItem->debit = $_debit[$descriptionID];
                            $paymentItem->amount = $_debit[$descriptionID];
                        }
                        if (!empty($_credit[$descriptionID])) {
                            $paymentItem->credit = $_credit[$descriptionID];
                            $paymentItem->amount = $_credit[$descriptionID];
                        }
                        if (!empty($_accountID[$descriptionID])) {
                            $paymentItem->account_id = $_accountID[$descriptionID];
                        }
                        if (!empty($check_no[$descriptionID])) {
                            $paymentItem->check_no = $check_no[$descriptionID];
                        }
                        if (!$paymentItem->save()) {
                            throw new CException(Yii::t("App", "Error while saving item data."));
                        }

                        if (!empty($_accountID[$descriptionID])) {
                            $ledgerBankAccBalance = new LedgerBankAccountBalance();
                            $ledgerBankAccBalance->ledger_bank_account_id = $_accountID[$descriptionID];
                            $ledgerBankAccBalance->description = $descrpName;
                            if (!empty($_debit[$descriptionID])) {
                                $ledgerBankAccBalance->debit = $_debit[$descriptionID];
                                $ledgerBankAccBalance->balance = $_debit[$descriptionID];
                            } elseif (!empty($_credit[$descriptionID])) {
                                $ledgerBankAccBalance->credit = $_credit[$descriptionID];
                                $ledgerBankAccBalance->balance = $_credit[$descriptionID];
                            } else {
                                if ($_debit[$descriptionID] > $_credit[$descriptionID]) {
                                    $ledgerBankAccBalance->balance = ($_debit[$descriptionID] - $_credit[$descriptionID]);
                                }
                            }
                            if (!$ledgerBankAccBalance->save()) {
                                throw new CException(Yii::t("App", "Error while saving account balance data."));
                            }
                        }
                    }
                }
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = "New Record has been saved successfully.";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionParticuler() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $_headID = Yii::app()->request->getPost('head_id');
        $_subHeadID = Yii::app()->request->getPost('sub_head_id');

        $_model = new LedgerParticuler();
        $criteria = new CDbCriteria();
        if (!empty($_headID)) {
            $criteria->condition = "head_id = " . $_headID;
        }
        if (!empty($_subHeadID)) {
            $criteria->addCondition("sub_head_id = " . $_subHeadID);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "id ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('particulers', $this->model);
    }

    public function actionFindsubhead() {
        $resp = array();
        $_model = new LedgerSubHead();
        $headID = Yii::app()->request->getPost('head_id');
        $subHead = $_model->findAll('ledger_head_id=:ledger_head_id', array('ledger_head_id' => $headID));

        $html = "<option value=''>Select</option>";

        if (!empty($subHead) && count($subHead) > 0) {
            foreach ($subHead as $meta) {
                $html .= "<option value='{$meta->id}'>{$meta->name}</option>";
            }

            $resp['success'] = true;
            $resp['html'] = $html;
        } else {
            $resp['success'] = false;
            $resp['html'] = "<option value=''>Not Found!</option>";
        }

        echo json_encode($resp);
        return json_encode($resp);
    }

    public function actionForm() {
        $headID = Yii::app()->request->getPost('head_id');
        $subHeadID = Yii::app()->request->getPost('sub_head_id');

        $_model = new LedgerParticuler();
        $criteria = new CDbCriteria();
        $criteria->condition = "head_id=" . $headID;
        if (!empty($subHeadID)) {
            $criteria->addCondition("sub_head_id=" . $subHeadID);
        }
        $_dataset = $_model->findAll($criteria);
        //AppHelper::pr($_dataset);

        $this->model['dataset'] = $_dataset;
        $this->renderPartial('_form', $this->model);
    }

    public function actionFind_particuler() {
        $resp = array();
        $_model = new LedgerParticuler();
        $headID = Yii::app()->request->getPost('head_id');
        $subHeadID = Yii::app()->request->getPost('sub_head_id');

        $criteria = new CDbCriteria();
        $criteria->condition = "head_id = " . $headID;
        if (!empty($subHeadID)) {
            $criteria->addCondition("sub_head_id = " . $subHeadID);
        }
        $dataset = $_model->findAll($criteria);

        $html = "<option value=''>Select</option>";

        if (!empty($dataset) && count($dataset) > 0) {
            foreach ($dataset as $data) {
                $html .= "<option value='{$data->id}'>{$data->particuler}</option>";
            }

            $resp['success'] = true;
            $resp['html'] = $html;
        } else {
            $resp['success'] = false;
            $resp['html'] = "<option value=''>Not Found!</option>";
        }

        echo json_encode($resp);
        return json_encode($resp);
    }

    public function actionDeleteall() {
        $_model = new LedgerPayment();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if (!empty($_obj->items)) {
                        foreach ($_obj->items as $item) {
                            if (!$item->delete()) {
                                throw new CException(Yii::t('App', "Error while deleting record {items}"));
                            }
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

}
