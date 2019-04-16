<?php

class Balancesheet extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{balancesheet}}';
    }

    public function rules() {
        return array();
    }

    public function relations() {
        return array(
            'payments' => array(self::BELONGS_TO, 'CustomerPayment', 'customer_payment_id'),
        );
    }

    public function balanceAmount() {
        $criteria = new CDbCriteria();
        $criteria->select = "DISTINCT pay_date";
        $_dataset = Balancesheet::model()->findAll($criteria);
        return $_dataset;
    }

}
