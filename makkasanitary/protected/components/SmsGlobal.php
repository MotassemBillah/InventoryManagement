<?php

class SmsGlobal extends CApplicationComponent {

    public $username;
    public $password;
    public $from;
    protected $url = 'http://www.smsglobal.com/http-api.php';

    public function __construct($username = null, $password = null, $from = null) {
        $this->username = $username = 'qh5s6wjk';
        $this->password = $password = 'rN5jR35p';
        $this->from = $from = '01814491116';
    }

    public function sendSMS($data) {
        if (!empty($data) && $data['to'] != '' && $data['message'] != '') {
            $queryString = $this->geturl($data);
            return $this->curl($queryString);
        }
        return "ERROR";
    }

    private function geturl($data) {
        $query_string = "?action=sendsms&user=" . $this->username . "&password=" . $this->password;
        $query_string .= "&from=" . rawurlencode($this->from) . "&to=" . rawurlencode($data['to']);
        $query_string .= "&clientcharset=utf-8";

//        if ($data['scheduledDate']) {
//            $query_string .= "&scheduledatetime=" . rawurlencode($data['scheduledDate']);
//        }

        $query_string .= "&text=" . rawurlencode(stripslashes($data['message']));
        return $this->url . $query_string;
    }

    private function curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}
