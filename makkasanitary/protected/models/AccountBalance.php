<?php

class AccountBalance extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{account_balance}}';
    }

    public function rules() {
        return array(
            array('amount', 'required'),
        );
    }

    public function relations() {
        return array(
            'account' => array(self::BELONGS_TO, 'Account', 'account_id'),
        );
    }

    public function categoryList() {
        return array(
            'Cash In' => 'Cash In',
            'Cash Out' => 'Cash Out',
        );
    }

    public function sumDebit($accId = null) {
        if (!is_null($accId)) {
            $_where = "account_id={$accId}";
        } else {
            $_where = "";
        }
        $data = Yii::app()->db->createCommand()->select('SUM(debit) as total')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

    public function sumCredit($accId = null) {
        if (!is_null($accId)) {
            $_where = "account_id={$accId}";
        } else {
            $_where = "";
        }
        $data = Yii::app()->db->createCommand()->select('SUM(credit) as total')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

    public function sumBalance($accId = null) {
        $_debit = $this->sumDebit($accId);
        $_credit = $this->sumCredit($accId);
        $_balance = ($_debit - $_credit);
        return !empty($_balance) ? AppHelper::getFloat($_balance) : 0;
    }

}
