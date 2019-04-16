<?php

class CustomerPayment extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{customer_payments}}';
    }

    public function rules() {
        return array(
                //array('advance_amount', 'match', 'pattern' => '/([1-9][0-9]*?)(\.[0-9]{2})?/', 'message' => 'Amount is invalid. Please choose from 0.01 to 99999999.99'),
        );
    }

    public function relations() {
        return array(
            'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
            'sale' => array(self::BELONGS_TO, 'Sale', 'sale_id'),
            'balancesheet' => array(self::HAS_MANY, 'Balancesheet', 'customer_payment_id'),
        );
    }

}
