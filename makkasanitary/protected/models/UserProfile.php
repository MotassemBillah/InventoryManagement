<?php

class UserProfile extends CActiveRecord {

    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function tableName() {
        return '{{user_profiles}}';
    }
    
    public function rules() {
        return array(
//            array('firstname, lastname', 'required'),
        );
    }
    
    public function relations() {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id')
        );
    }

}
