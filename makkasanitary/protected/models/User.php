<?php

class User extends CActiveRecord {

    public $repeat_password;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{users}}';
    }

    public function rules() {
        return array(
            //array('login', 'required', 'message' => 'Username cannot be blank'),
            array('email,password', 'required'),
            array('email', 'email'),
            array('email, login', 'unique'),
                //array('repeat_password', 'safe'),
        );
    }

    public function relations() {
        return array(
            'profile' => array(self::HAS_ONE, 'UserProfile', 'user_id'),
            'access_item' => array(self::HAS_ONE, 'Permission', 'user_id'),
            'logins' => array(self::HAS_ONE, 'UserLogin', 'user_id'),
            //
            'accounts' => array(self::HAS_MANY, 'Account', 'created_by'),
        );
    }

    public function getList() {
        $criteria = new CDbCriteria();
        $criteria->condition = "status=1";
        $criteria->addCondition("deletable = 1");
        $_dataset = User::model()->findAll($criteria);
        return $_dataset;
    }

    public function displayname($id = null) {
        $displayName = "";
        if (!is_null($id)) {
            $data = User::model()->findByPk($id);
            if (!empty($data->login)) {
                $displayName = $data->login;
            } else {
                $displayName = $data->email;
            }
        } else {
            if (!empty($this->login)) {
                $displayName = $this->login;
            } else {
                $displayName = $this->email;
            }
        }

        return ucwords($displayName);
    }

    public function validatePassword($password) {
        return crypt($password, $this->password) === $this->password || $password === $this->password;
    }

    public function verified() {
        return $this->status == AppConstant::USER_STATUS_ACTIVE;
    }

    public function hashPassword($password) {
        return crypt($password, $this->generateSalt());
    }

    protected function generateSalt($cost = 10) {
        if (!is_numeric($cost) || $cost < 4 || $cost > 31) {
            throw new CException(Yii::t('Cost parameter must be between 4 and 31.'));
        }
        // Get some pseudo-random data from mt_rand().
        $rand = '';
        for ($i = 0; $i < 8; ++$i) {
            $rand.=pack('S', mt_rand(0, 0xffff));
        }
        // Add the microtime for a little more entropy.
        $rand.=microtime();
        // Mix the bits cryptographically.
        $rand = sha1($rand, true);
        // Form the prefix that specifies hash algorithm type and cost parameter.
        $salt = '$2a$' . str_pad((int) $cost, 2, '0', STR_PAD_RIGHT) . '$';
        // Append the random salt string in the required base64 format.
        $salt.=strtr(substr(base64_encode($rand), 0, 22), array('+' => '.'));
        return $salt;
    }

}
