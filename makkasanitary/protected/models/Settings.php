<?php

class Settings extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{settings}}';
    }

    public function rules() {
        return array(
            array('author_email', 'email'),
        );
    }

    public function currencyOptions() {
        return array(
            'Tk' => 'BDT',
            '$' => 'USD',
            '$' => 'AUD',
            '€' => 'EUR',
            '£' => 'GBP',
        );
    }

}
