<?php

class Sale extends CActiveRecord {

    public $itemSize;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{sales}}';
    }

    public function rules() {
        return array(
            array('invoice_no', 'unique'),
            array('itemSize', 'safe'),
        );
    }

    public function relations() {
        return array(
            'items' => array(self::HAS_MANY, 'SaleItem', 'sale_id'),
            'stocks' => array(self::HAS_MANY, 'Stock', 'sale_id'),
            'info' => array(self::HAS_ONE, 'SaleInfo', 'sale_id'),
            'invoice' => array(self::HAS_ONE, 'Invoice', 'sale_id'),
            'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
            'payment' => array(self::HAS_ONE, 'CustomerPayment', 'sale_id'),
            'sumQty' => array(self::STAT, 'SaleItem', 'sale_id', 'select' => 'SUM(quantity)'),
            'sumFree' => array(self::STAT, 'SaleItem', 'sale_id', 'select' => 'SUM(free)'),
            'sumPrice' => array(self::STAT, 'SaleItem', 'sale_id', 'select' => 'SUM(total)'),
        );
    }

    public function attributeLabels() {
        return array(
            'sale_id' => 'ID',
            'customer_id' => 'Customer',
            'created_by' => 'Person',
            'status' => 'Status',
            'created' => 'Date',
            'items' => 'Items',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('created_by', $this->created_by, true);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider('Sale', array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'sale_id DESC',
            ),
        ));
    }

}
