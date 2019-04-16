<?php

class Account extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{accounts}}';
    }

    public function rules() {
        return array(
            array('bank_id', 'required', 'message' => 'Please select a bank'),
            array('account_name, account_number, account_type', 'required'),
        );
    }

    public function relations() {
        return array(
            'balance' => array(self::HAS_MANY, 'AccountBalance', 'account_id'),
            'sumBalance' => array(self::STAT, 'AccountBalance', 'account_id', 'select' => 'SUM(balance_amount)'),
        );
    }

    public function name() {
        return $this->account_name;
    }

    public function getname($id = null) {
        $accName = "";
        if (!is_null($id)) {
            $_dataset = Account::model()->findByPk($id);
            $accName = $_dataset->account_name;
        } else {
            $accName = $this->account_name;
        }
        return $accName;
    }

    public function getList($id = null) {
        $criteria = new CDbCriteria();
        if (!is_null($id)) {
            $criteria->condition = "bank_id=$id";
        }
        $criteria->order = "account_name ASC";
        $_dataset = Account::model()->findAll($criteria);
        return $_dataset;
    }

    public function typeList() {
        return array(
            'Current' => 'Current Account',
            'LC' => 'LC Account',
            'Savings' => 'Savings Account'
        );
    }

}
