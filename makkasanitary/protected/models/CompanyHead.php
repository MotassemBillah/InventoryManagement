<?php

class CompanyHead extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{company_head}}';
    }

    public function rules() {
        return array();
    }

    public function relations() {
        return array(
            'cat' => array(self::BELONGS_TO, 'Company', 'company_id'),
        );
    }

}
