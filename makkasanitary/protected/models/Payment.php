<?php

class Payment extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{payments}}';
    }

    public function rules() {
        return array(
            array('check_no', 'unique')
        );
    }

    public function relations() {
        return array(
            'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
            'purchase' => array(self::BELONGS_TO, 'Purchase', 'purchase_id'),
            'account' => array(self::BELONGS_TO, 'Account', 'account_id'),
            'account_balance' => array(self::HAS_ONE, 'AccountBalance', 'payment_id'),
        );
    }

}
