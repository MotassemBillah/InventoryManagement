<?php

class Expense extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{expenses}}';
    }

    public function rules() {
        return array(
            array('by_whom,purpose,amount', 'required'),
            array('ledger_head_id', 'required', 'message' => 'You must select a ledger head'),
        );
    }

    public function relations() {
        return array(
                //'head' => array(self::BELONGS_TO, 'LedgerHead', 'ledger_head_id'),
        );
    }

    public function sumSalary() {
        $data = Yii::app()->db->createCommand()->select('SUM(amount) as total')->where("ledger_head_id=" . AppConstant::HEAD_SALARY)->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

    public function sumTotal() {
        $data = Yii::app()->db->createCommand()->select('SUM(amount) as total')->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

}
