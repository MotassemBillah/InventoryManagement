<?php

class Purchase extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{purchase}}';
    }

    public function rules() {
        return array(
            //array('invoice_no,invoice_date', 'required', 'on' => 'create'),
            array('invoice_no', 'unique'),
        );
    }

    public function relations() {
        return array(
            'items' => array(self::HAS_MANY, 'PurchaseItem', 'purchase_id'),
            'stocks' => array(self::HAS_MANY, 'Stock', 'purchase_id'),
            'info' => array(self::HAS_ONE, 'PurchaseInfo', 'purchase_id'),
            'payment' => array(self::HAS_ONE, 'Payment', 'purchase_id'),
            'sumQty' => array(self::STAT, 'PurchaseItem', 'purchase_id', 'select' => 'SUM(quantity)'),
            'sumBonus' => array(self::STAT, 'PurchaseItem', 'purchase_id', 'select' => 'SUM(bonus)'),
            'sumPrice' => array(self::STAT, 'PurchaseItem', 'purchase_id', 'select' => 'SUM(price)'),
            'sumTotal' => array(self::STAT, 'PurchaseItem', 'purchase_id', 'select' => 'SUM(total)'),
        );
    }

}
