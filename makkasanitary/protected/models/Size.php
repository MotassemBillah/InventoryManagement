<?php

class Size extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{product_sizes}}';
    }

    public function rules() {
        return array(
                //array('product_id', 'required', 'message' => 'Please select a product'),
                //array('size_id', 'required', 'on' => 'create', 'message' => 'Please select a product size'),
                //array('size_value', 'required', 'on' => 'create'),
                //array('quantity', 'required', 'message' => 'Quantity cannot be blank'),
                //array('retail_price', 'required', 'on' => 'create', 'message' => 'Price cannot be blank'),
                //array('quantity', 'numerical', 'integerOnly' => true, 'message' => 'Please provide quantity in number'),
                //array('retail_price', 'match', 'on' => 'create', 'pattern' => '/([1-9][0-9]*?)(\.[0-9]{2})?/', 'message' => 'Price is invalid. Please choose from 0.01 to 9999.99'),
        );
    }

    public function relations() {
        return array(
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
        );
    }

}
