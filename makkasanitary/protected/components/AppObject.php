<?php

class AppObject {

    public static function getBanks() {
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        return Bank::model()->count($criteria);
    }

    public static function getBankName($id) {
        $data = Bank::model()->findByPk($id);
        return !empty($data->name) ? $data->name : "";
    }

    public static function getAccountName($id) {
        $data = Bank::model()->findByPk($id);
        return !empty($data->account_name) ? $data->account_name : "";
    }

    public static function getCategories() {
        return Category::model()->count();
    }

    public static function categoryParent($id) {
        if ($id > 0) {
            $data = Category::model()->findByPk($id);
            $cname = "Parent - " . $data->name;
        } else {
            $cname = '';
        }
        return $cname;
    }

    public static function categoryName($id) {
        $data = Category::model()->findByPk($id);
        $cname = !empty($data->name) ? $data->name : '';
        return $cname;
    }

    public static function getCompanies() {
        return Company::model()->count();
    }

    public static function companyName($id) {
        $data = Company::model()->findByPk($id);
        $cname = !empty($data->name) ? $data->name : '';
        return $cname;
    }

    public static function companyPhone($id) {
        $data = Company::model()->findByPk($id);
        $cname = !empty($data->mobile) ? $data->mobile : $data->phone;
        return $cname;
    }

    public static function companyHeadName($id) {
        $data = CompanyHead::model()->findByPk($id);
        $name = !empty($data->value) ? ucfirst($data->value) : '';
        return $name;
    }

    public static function getCustomers() {
        return Customer::model()->count();
    }

    public static function customerName($id) {
        $data = Customer::model()->findByPk($id);
        return !empty($data->company) ? $data->company : $data->name;
    }

    public static function customerType($id) {
        $data = Customer::model()->findByPk($id);
        return !empty($data->type) ? $data->type : "";
    }

    public static function customerCode($id) {
        $data = Customer::model()->findByPk($id);
        return !empty($data->code) ? $data->code : "";
    }

    public static function getRoles() {
        return Role::model()->count();
    }

    public static function getUsers() {
        $criteria = new CDbCriteria();
        $criteria->condition = "deletable = 1";
        return User::model()->count($criteria);
    }

    public static function getProducts() {
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        return Product::model()->count($criteria);
    }

    public static function getParticulers($subHeadID) {
        $criteria = new CDbCriteria();
        $criteria->condition = "sub_head_id=:sub_head_id";
        $criteria->params = array(":sub_head_id" => $subHeadID);
        return LedgerHeadParticuler::model()->count($criteria);
    }

    public static function productName($id) {
        $data = Product::model()->findByPk($id);
        return !empty($data->name) ? $data->name : "";
    }

    public static function productPrice($id) {
        $data = Product::model()->findByPk($id);
        return !empty($data->price) ? $data->price : "";
    }

    public static function productModel($id) {
        $data = Product::model()->findByPk($id);
        return !empty($data->model_no) ? $data->model_no : "";
    }

    public static function productSize($sizeID) {
        $data = Size::model()->findByPk($sizeID);
        return !empty($data->name) ? $data->name : "";
    }

    public static function getProductImages($data) {
        $retVal = '';

        if (empty($data->thumb)) {
            $retVal = Yii::app()->request->baseUrl . '/img/no-img.jpg';
        } else {
            $retVal = Yii::app()->request->baseUrl . '/uploads/products/' . $data->thumb;
        }

        return $retVal;
    }

    public static function getPurchases() {
        return Purchase::model()->count();
    }

    public static function getSales() {
        return Sale::model()->count();
    }

    /* Get Names */

    public static function displayName($data) {
        $name = '';
        if (!empty($data->profile->firstname) && !empty($data->profile->lastname)) {
            $name = $data->profile->firstname . ' ' . $data->profile->lastname;
        } else {
            $name = $data->login;
        }

        return $name;
    }

    public static function displayNameByUser($id) {
        $data = User::model()->findByPk($id);

        if (!empty($data->profile->firstname) && !empty($data->profile->lastname)) {
            $name = $data->profile->firstname . ' ' . $data->profile->lastname;
        } else {
            $name = $data->login;
        }

        return $name;
    }

    public static function userNiceName($data) {
        $name = '';
        if (!empty($data->login)) {
            $name = $data->login;
        }

        return $name;
    }

    public static function getAvatar($data) {
        $_dir = md5($data->id);
        $_filename = Yii::getPathOfAlias('webroot') . "/uploads/{$_dir}/{$data->profile->avatar}";
        $src = '';

        if (!empty($data->profile->avatar)) {
            if (file_exists($_filename)) {
                $src = Yii::app()->request->baseUrl . "/uploads/{$_dir}/{$data->profile->avatar}";
            } else {
                $src = Yii::app()->request->baseUrl . '/img/no_photo.gif';
            }
        } else {
            if ($data->profile->gender == AppConstant::GENDER_MALE) {
                $src = Yii::app()->request->baseUrl . '/img/male.jpg';
            } elseif ($data->profile->gender == AppConstant::GENDER_FEMALE) {
                $src = Yii::app()->request->baseUrl . '/img/female.jpg';
            } else {
                $src = Yii::app()->request->baseUrl . '/img/no_photo.gif';
            }
        }

        return $src;
    }

    public static function getImage($data) {
        $_filename = Yii::getPathOfAlias('webroot') . "/uploads/{$data}";
        $src = '';

        if (!empty($data)) {
            if (file_exists($_filename)) {
                $src = Yii::app()->request->baseUrl . "/uploads/{$data}";
            } else {
                $src = Yii::app()->request->baseUrl . '/img/no_photo.gif';
            }
        } else {
            $src = Yii::app()->request->baseUrl . '/img/no_photo.gif';
        }

        return $src;
    }

    public static function roleName($roleid) {
        $data = Role::model()->findByPk($roleid);
        return !empty($data->name) ? $data->name : "";
    }

    public static function userStatus($status) {
        $user_status = "";
        switch ($status) {
            case 0:
                $user_status = "Inactive";
                break;
            case 1:
                $user_status = "Active";
                break;
            case 2:
                $user_status = "Blocked";
                break;
            default :
                break;
        }

        return $user_status;
    }

    /* Sum From Stocks */

    public static function stokByProduct($pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(quantity) FROM `stocks` WHERE product_id='$pid'");
        $amount = $command->queryScalar();
        return !empty($amount) ? intVal($amount) : 0;
    }

    public static function stokByProductSize($size) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(quantity) FROM `stocks` WHERE size_id='$size'");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    /* Sum From Purchases Items */

    public static function sumPurchasePrice() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(purchase_price) FROM `purchase`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumPurchaseTotal($field) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM($field) FROM `purchase_items`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumPurchaseTotalById($field, $pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM($field) FROM `purchase_items` WHERE purchase_id='$pid'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumSaleTotalById($field, $id) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM($field) FROM `sale_items` WHERE sale_id='$id'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumPurchaseQty() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(quantity) FROM `purchase`");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    public static function sumPurchaseQtyById($pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(quantity) FROM `purchase_items` WHERE purchase_id='$pid'");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    public static function sumPurchaseFreeById($pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(free) FROM `purchase_items` WHERE purchase_id='$pid'");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    /* Sum From Sales Items */

    public static function sumSalePrice() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(price) FROM `sales`");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    public static function sumSaleTotal($field) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM($field) FROM `sale_items`");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    public static function sumSaleQty() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(quantity) FROM `sales`");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    public static function sumSaleQtyById($pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(quantity) FROM `sale_items` WHERE sale_id='$pid'");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    public static function sumSaleFreeQtyById($pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(free) FROM `sale_items` WHERE sale_id='$pid'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    /* Sales Payments */

    public static function sumInvoiceAmount($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_amount) FROM `customer_payments` WHERE customer_id='$customerID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumInvoicePaid($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_paid) FROM `customer_payments` WHERE customer_id='$customerID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumDueAmount($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(due_amount) FROM `customer_payments` WHERE customer_id='$customerID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function sumDueAmountNotCurrent($customerID, $invoiceNO) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(due_amount) FROM `customer_payments` WHERE customer_id='$customerID' AND invoice_no !='$invoiceNO'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function sumBalanceAmount($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance_amount) FROM `customer_payments` WHERE customer_id='$customerID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function sumBalanceAmountNoInvoice($customerID, $invoice) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance_amount) FROM `customer_payments` WHERE customer_id='$customerID' AND invoice_no <> '$invoice'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getSaleInvoiceAmount($invoiceNO) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_amount) FROM `customer_payments` WHERE invoice_no='$invoiceNO'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getSaleInvoicePaid($invoiceNO) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_paid) FROM `customer_payments` WHERE invoice_no='$invoiceNO'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getSaleBalance($invoiceNO) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance_amount) FROM `customer_payments` WHERE invoice_no='$invoiceNO'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    /* Purchase Payment */

    public static function getPurchaseInvoiceAmount($invoiceNO) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_amount) FROM `payments` WHERE invoice_no='$invoiceNO'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getPurchaseInvoicePaid($invoiceNO) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_paid) FROM `payments` WHERE invoice_no='$invoiceNO'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getPurchaseBalance($invoiceNO) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance_amount) FROM `payments` WHERE invoice_no='$invoiceNO'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function sumPurchaseInvoiceAmount() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_amount) FROM `payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function sumPurchaseInvoicePaid() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(advance_amount) + SUM(invoice_paid) FROM `payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function sumPurchaseBalance() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance_amount) FROM `payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    /* Indevidual */

    public static function getAllDues() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(due_amount) FROM `customer_payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getDueAmount($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(due_amount) FROM `customer_payments` WHERE customer_id='$customerID' AND `type`='due'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getDiscountAmount($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(discount_amount) FROM `customer_payments` WHERE customer_id='$customerID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getAdvancePayments($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(advance_amount) FROM `customer_payments` WHERE customer_id='$customerID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getInvoicePayments($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_amount) FROM `customer_payments` WHERE customer_id='$customerID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getInvoicePaidPayments($customerID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_paid) FROM `customer_payments` WHERE customer_id='$customerID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function getInvoiceNumberAmount($cid, $invoice) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_amount) FROM `customer_payments` WHERE customer_id='$cid' AND invoice_no='$invoice'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function getInvoiceNumberPaidAmount($cid, $invoice) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_paid) FROM `customer_payments` WHERE customer_id='$cid' AND invoice_no='$invoice'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function customerBalanceAmount($customerID) {
        //$invAmount = self::sumInvoiceAmount($customerID);
        //$invPaid = self::getInvoicePaidPayments($customerID);
        $advPaid = self::getAdvancePayments($customerID);
        //$dueAmount = (($invPaid + $advPaid) - $invAmount);
        $discountAmount = self::getDiscountAmount($customerID);
        //$invDue = $invAmount - $invPaid;
        $totalDue = self::sumDueAmount($customerID);
        $totalPaid = $totalDue - $advPaid;
        $balance = $totalPaid - $discountAmount;
        return (int) $balance;
    }

    public static function sumCashIn($accountID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(amount) FROM `account_balance` WHERE account_id='$accountID' AND category='Cash In'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumCashOut($accountID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(amount) FROM `account_balance` WHERE account_id='$accountID' AND category='Cash Out'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumCashBalance($accountID) {
        $cashIn = self::sumCashIn($accountID);
        $cashOut = self::sumCashOut($accountID);
        $balance = ($cashIn - $cashOut);
        return !empty($balance) ? AppHelper::getFloat($balance) : 0;
    }

    public static function sumLedgerCashIn($accountID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance) FROM `ledger_bank_account_balance` WHERE ledger_bank_account_id='$accountID' AND description='Debit'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumLedgerCashOut($accountID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance) FROM `ledger_bank_account_balance` WHERE ledger_bank_account_id='$accountID' AND description='Credit'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0;
    }

    public static function sumLedgerCashBalance($accountID) {
        $cashIn = self::sumLedgerCashIn($accountID);
        $cashOut = self::sumLedgerCashOut($accountID);
        $balance = ($cashIn - $cashOut);
        return !empty($balance) ? AppHelper::getFloat($balance) : 0;
    }

    /* Self Payments Options */

    public static function selfDiscount() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(discount_amount) FROM `payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function selfInvoiceAmount() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_amount) FROM `payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function selfInvoicePaid() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_paid) FROM `payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function selfAdvanceAmount() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(advance_amount) FROM `payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function selfDueAmount() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(due_amount) FROM `payments`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function selfBalance() {
        $invAmount = self::selfInvoiceAmount();
        $invPaid = self::selfInvoicePaid();
        $invDue = $invAmount - $invPaid;
        $dueAmount = self::selfDueAmount();
        $totalDue = $invDue + $dueAmount;
        $advAmount = self::selfAdvanceAmount();
        //$totalPaid = $advAmount + $paidAmount;
        $balance = $advAmount - $totalDue;
        return AppHelper::getFloat($balance);
    }

    public static function selfBalanceNoInvoice($invoice) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance_amount) FROM `payments` WHERE invoice_no <> '$invoice'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function selfBalanceByCategory($type) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance_amount) FROM `payments` WHERE category = '$type'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    public static function sumSelfBalanceAmount($companyID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance_amount) FROM `payments` WHERE company_id='$companyID'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
    }

    /* AVG */

    public static function getAvgPurchase($pid) {
        $tot = Yii::app()->db->createCommand()
                ->select('CAST(AVG(retail_price) AS DECIMAL (10,2)) as avgPrice')
                ->from('purchases')
                ->where('unit_price > 0 AND product_id = ' . $pid)
                ->queryRow();
        return $tot['avgPrice'];
    }

    public static function getAvgSale($pid) {
        $data = Yii::app()->db->createCommand()
                ->select('CAST(AVG(retail_price) AS DECIMAL (10,2)) as avgPrice')
                ->from('sales')
                ->where('retail_price > 0 AND product_id = ' . $pid)
                ->queryRow();
        return $data['avgPrice'];
    }

    public static function retailPrice($pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT retail_price FROM `product_sizes` WHERE size_id = '$pid'");
        return $amount = $command->queryScalar();
    }

    public static function inStock($pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(quantity) FROM `stocks` WHERE size_id = '$pid'");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    public static function inStockProduct($pid) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(quantity) FROM `stocks` WHERE product_id = '$pid'");
        $amount = $command->queryScalar();
        return !empty($amount) ? $amount : 0;
    }

    public static function stockPrice($sizeID) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT purchase_price FROM `stocks` WHERE size_id = '$sizeID'");
        $data = $command->queryScalar();
        //print_r($data);
        return !empty($amount) ? $amount : 0;
    }

    /*
      public static function sumExpense($type = "") {
      if ($type == "P") {
      $field = "purchase_amount";
      } else {
      $field = "sale_amount";
      }
      $connection = Yii::app()->db;
      $command = $connection->createCommand("SELECT SUM($field) FROM `expenditures`");
      $amount = $command->queryScalar();
      return !empty($amount) ? AppHelper::getFloat($amount) : 0.00;
      }
     */

    /* Discount */

    public static function purchasePrice($productID) {
        $_model = new PurchaseItem();
        $criteria = new CDbCriteria();
        $criteria->condition = "product_id = " . $productID;
        $criteria->order = "id DESC";
        $criteria->limit = 1;
        $item = $_model->find($criteria);

        return !empty($item->price) ? AppHelper::getFloat($item->price) : 0;
    }

    public static function getPrice($_profit, $_price) {
        if (empty($_profit)) {
            $_profit = 0;
        }

        $_profitPrice = $_price * $_profit / 100;
        $_totalPrice = $_price + $_profitPrice;
        return AppHelper::getFloat($_totalPrice);
    }

    public static function getVat($_vat, $_price) {
        if (empty($_vat)) {
            $_vat = 0;
        }

        $_vatPrice = ($_vat * $_price) / 100;
        //$_totalPrice = $_price + $_vatPrice;
        return AppHelper::getFloat($_vatPrice);
    }

    public static function getDiscount($price, $discount) {
        $_val = $price * 2 / 100;
        $_discount = $price * $discount / 100;
        $_totalVal = $price + $_val;
        $_discountPrice = $_totalVal - $_discount;
        return $_discountPrice;
    }

    public static function formatedDate($value) {
        $settings = Settings::model()->findByPk(1);
        return date($settings->datetime_format, strtotime($value));
    }

    public static function sumInvoice() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(invoice_amount) FROM `invoices`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "0.00";
    }

    public static function sumProfit() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(profit) FROM `invoices`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "0.00";
    }

    public static function sumDiscount() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(discount_amount) FROM `invoices`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "0.00";
    }

    public static function sumInvoiceProfit() {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(net_profit) FROM `invoices`");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "0.00";
    }

    /* Balance sheet functions */

    public static function sumPaidPayment($_date = NULL) {
        $connection = Yii::app()->db;
        if ($_date != NULL) {
            $command = $connection->createCommand("SELECT SUM(invoice_paid) + SUM(advance_amount) FROM `payments` WHERE `pay_date` = '$_date'");
        } else {
            $command = $connection->createCommand("SELECT SUM(invoice_paid) + SUM(advance_amount) FROM `payments`");
        }
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "";
    }

    public static function sumPaidCustomerPayment($_date = NULL) {
        $connection = Yii::app()->db;
        if ($_date != NULL) {
            $command = $connection->createCommand("SELECT SUM(invoice_paid) + SUM(advance_amount) FROM `customer_payments` WHERE `pay_date` = '$_date'");
        } else {
            $command = $connection->createCommand("SELECT SUM(invoice_paid) + SUM(advance_amount) FROM `customer_payments`");
        }
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "";
    }

    public static function sumDailyExpense($_date = NULL) {
        $connection = Yii::app()->db;
        if ($_date != NULL) {
            $command = $connection->createCommand("SELECT SUM(amount) FROM `expenses` WHERE `pay_date` = '$_date'");
        } else {
            $command = $connection->createCommand("SELECT SUM(amount) FROM `expenses`");
        }
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "";
    }

    public static function balancesheetSumDebit($_date) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(debit) FROM `balancesheet` WHERE `pay_date` = '$_date'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "";
    }

    public static function balancesheetSumCredit($_date) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(credit) FROM `balancesheet` WHERE `pay_date` = '$_date'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "";
    }

    public static function balancesheetSumBalance($_date) {
        $connection = Yii::app()->db;
        $command = $connection->createCommand("SELECT SUM(balance) FROM `balancesheet` WHERE `pay_date` = '$_date'");
        $amount = $command->queryScalar();
        return !empty($amount) ? AppHelper::getFloat($amount) : "";
    }

    public static function emptyTable($_table) {
        if (Yii::app()->db->createCommand("TRUNCATE TABLE `{$_table}`")->execute()) {
            return true;
        } else {
            return false;
        }
    }

}
