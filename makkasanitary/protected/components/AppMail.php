<?php

class AppMail {

    var $from = '';
    var $to = '';
    var $cc = '';
    var $bcc = '';
    var $subject = '';
    var $text = '';
    var $html = '';
    var $headers = array();
    var $errorMessage = '';
    var $transporter = '';

    function __construct($transporter = 'YiiMail') {
        $this->clear();
        $this->transporter = $transporter;
    }

    public function clear() {
        $this->from = '';
        $this->to = '';
        $this->cc = '';
        $this->bcc = '';
        $this->subject = 'Test Mailgun';
        $this->text = 'Please use HTML supported browser to view this email';
        $this->html = '';
        $this->headers = array();
        $this->errorMessage = '';
        $this->transporter = '';
    }

    function sendMessage() {
        switch (strtolower($this->transporter)) {
            case 'yiimail':
                return $this->_sendWithYiiMail();
                break;
            case 'mailgun':
                return $this->_sendWithMailgun();
                break;
            case 'mandrill':
                return $this->_sendWithMandrill();
                break;
            default:
                return false;
                break;
        }

        return false;
    }

    function getErrorMessage() {
        return $this->errorMessage;
    }

    // private methods
    private function _sendWithYiiMail() {
        try {
            $message = new YiiMailMessage;
            $message->setBody($this->html, 'text/html');
            $message->subject = $this->subject;
            $message->addTo($this->to);
            $message->from = $this->from;
            Yii::app()->mail->send($message);
            return true;
        } catch (CException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

    private function _sendWithMandrill() {

        try {
            $_mandrill = new Mandrill(AppConstant::MAIL_MANDRILL_API);

            $_message = array(
                'subject' => $this->subject,
                'from_email' => $this->from,
                'html' => $this->html,
                'to' => array(array('email' => $this->to, 'name' => '')),
                'text' => $this->text
            );

            $_mandrill->messages->send($_message);

            return true;
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    private function _sendWithMailgun() {
        try {
            $_arrMail = array('from' => self::MAIL_SENDER,
                'to' => $this->to,
                'cc' => $this->cc,
                'bcc' => $this->bcc,
                'subject' => $this->subject,
                'text' => $this->text,
                'html' => $this->html
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, 'api:' . self::API_KEY);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_URL, self::API_URL . self::MAIL_DOMAIN . '/messages');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_arrMail);

            $_result = curl_exec($ch);
            curl_close($ch);
            return true;
        } catch (CException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

}