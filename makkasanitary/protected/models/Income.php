<?php

class Income extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{incomes}}';
    }

    public function rules() {
        return array(
            array('name', 'required'),
        );
    }

    public function relations() {
        return array(
            'sub_head' => array(self::HAS_MANY, 'LedgerSubHead', 'ledger_head_id'),
            'particulers' => array(self::HAS_MANY, 'LedgerParticuler', 'head_id'),
            'payments' => array(self::HAS_MANY, 'LedgerPayment', 'head_id'),
        );
    }

    public function getNameById($id) {
        $data = LedgerHead::model()->findByPk($id);
        return !empty($data->name) ? $data->name : "";
    }

    public function getCode($id) {
        $data = LedgerHead::model()->findByPk($id);
        return !empty($data->code) ? $data->code : "";
    }

    public function getList() {
        $criteria = new CDbCriteria();
        $criteria->order = "name ASC";
        $_dataset = LedgerHead::model()->findAll($criteria);
        return $_dataset;
    }

    public function getCodeList() {
        return array(
            '1' => 'Asset',
            '2' => 'Liability',
            '3' => 'Oweners Equity',
            '4' => 'Income',
            '5' => 'Expense',
        );
    }

}
