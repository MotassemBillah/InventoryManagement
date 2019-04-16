<?php

class LedgerHead extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{ledger_heads}}';
    }

    public function rules() {
        return array(
            array('name', 'required'),
            array('code', 'unique'),
        );
    }

    public function relations() {
        return array(
            'accounts' => array(self::HAS_MANY, 'CashAccount', 'ledger_head_id')
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
        $criteria->order = "id ASC";
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

    public function sumDebit() {
        $data = Yii::app()->db->createCommand()->select('SUM(debit) as sb')->from($this->tableName())->queryRow();
        return !empty($data['sb']) ? AppHelper::getFloat($data['sb']) : 0;
    }

    public function sumCredit() {
        $data = Yii::app()->db->createCommand()->select('SUM(credit) as sc')->from($this->tableName())->queryRow();
        return !empty($data['sc']) ? AppHelper::getFloat($data['sc']) : 0;
    }

    public function sumBalance() {
        $data = Yii::app()->db->createCommand()->select('SUM(balance) as sb')->from($this->tableName())->queryRow();
        return !empty($data['sb']) ? AppHelper::getFloat($data['sb']) : 0;
    }

}
