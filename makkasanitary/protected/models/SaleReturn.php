<?php

class SaleReturn extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{sale_returns}}';
    }

    public function rules() {
        return array(
            array('return_invoice', 'unique'),
        );
    }

    public function relations() {
        return array(
            'items' => array(self::HAS_MANY, 'SaleReturnItem', 'sale_return_id'),
            'stocks' => array(self::HAS_MANY, 'Stock', 'sale_return_id'),
            'info' => array(self::HAS_ONE, 'SaleReturnInfo', 'sale_return_id'),
            //'invoice' => array(self::HAS_ONE, 'Invoice', 'sale_return_id'),
            'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
            'payment' => array(self::HAS_ONE, 'SaleReturnPayment', 'sale_return_id'),
            'sumQty' => array(self::STAT, 'SaleReturnItem', 'sale_return_id', 'select' => 'SUM(quantity)'),
            'sumPrice' => array(self::STAT, 'SaleReturnItem', 'sale_return_id', 'select' => 'SUM(price)'),
            'sumTotal' => array(self::STAT, 'SaleReturnItem', 'sale_return_id', 'select' => 'SUM(total)'),
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
