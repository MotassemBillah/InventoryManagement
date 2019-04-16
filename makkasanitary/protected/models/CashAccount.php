<?php

class CashAccount extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{cash_account}}';
    }

    public function rules() {
        return array(
            array('ledger_head_id', 'required', 'message' => 'You must select a ledger head', 'on' => 'cdw'),
            array('transaction_type', 'required', 'message' => 'You must select a transaction name', 'on' => 'cdw'),
            array('by_whom', 'required', 'message' => 'You must enter a person name', 'on' => 'cdw'),
            array('check_no', 'unique', 'on' => 'cdw'),
        );
    }

    public function relations() {
        return array(
            'head' => array(self::BELONGS_TO, 'LedgerHead', 'ledger_head_id'),
        );
    }

    public function typeList() {
        return array(
            'By Cash' => 'By Cash',
            'From Bank' => 'From Bank',
        );
    }

    public function sumDebit($ledger_head_id = null) {
        if (!is_null($ledger_head_id)) {
            $_where = "ledger_head_id = $ledger_head_id";
        } else {
            $_where = "";
        }
        $data = Yii::app()->db->createCommand()->select('SUM(debit) as sb')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['sb']) ? AppHelper::getFloat($data['sb']) : 0;
    }

    public function sumCredit($ledger_head_id = null) {
        if (!is_null($ledger_head_id)) {
            $_where = "ledger_head_id = $ledger_head_id";
        } else {
            $_where = "";
        }
        $data = Yii::app()->db->createCommand()->select('SUM(credit) as sc')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['sc']) ? AppHelper::getFloat($data['sc']) : 0;
    }

    public function sumBalance($ledger_head_id = null) {
        if (!is_null($ledger_head_id)) {
            $_where = "ledger_head_id = $ledger_head_id";
        } else {
            $_where = "";
        }
        $data = Yii::app()->db->createCommand()->select('SUM(balance) as sb')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['sb']) ? AppHelper::getFloat($data['sb']) : 0;
    }

    /* Search By Date */

    public function sumDebitByDate($date, $head_id = null) {
        $_date = date("Y-m-d", strtotime($date));
        $_where = "created=CAST('{$_date}' AS DATE) AND ledger_head_id={$head_id}";
        $data = Yii::app()->db->createCommand()->select('SUM(debit) as total')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

    public function sumCreditByDate($date, $head_id = null) {
        $_date = date("Y-m-d", strtotime($date));
        $_where = "created=CAST('{$_date}' AS DATE) AND ledger_head_id={$head_id}";
        $data = Yii::app()->db->createCommand()->select('SUM(credit) as total')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

    public function sumBalanceByDate($date) {
        $_debit = $this->sumDebitByDate($date);
        $_credit = $this->sumCreditByDate($date);
        $_balance = ($_debit - $_credit);
        return !empty($_balance) ? $_balance : 0;
    }

    public function sumDebitBetweenDate($date1, $date2, $head_id = null) {
        $_date1 = date("Y-m-d", strtotime($date1));
        $_date2 = date("Y-m-d", strtotime($date2));
        $_where = "created BETWEEN CAST('{$_date1}' AS DATE) AND CAST('{$_date2}' AS DATE)";
        if (!is_null($head_id)) {
            $_where.= " AND ledger_head_id={$head_id}";
        }
        $data = Yii::app()->db->createCommand()->select('SUM(debit) as total')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

    public function sumCreditBetweenDate($date1, $date2, $head_id = null) {
        $_date1 = date("Y-m-d", strtotime($date1));
        $_date2 = date("Y-m-d", strtotime($date2));
        $_where = "created BETWEEN CAST('{$_date1}' AS DATE) AND CAST('{$_date2}' AS DATE)";
        if (!is_null($head_id)) {
            $_where.= " AND ledger_head_id={$head_id}";
        }
        $data = Yii::app()->db->createCommand()->select('SUM(credit) as total')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

}
