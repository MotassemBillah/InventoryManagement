<?php

class AppHelper {

    public static function getParamValue($name, $default = null) {
        if (isset(Yii::app()->params[$name]))
            return Yii::app()->params[$name];
        else
            return $default;
    }

    public static function setParamValue($name, $value = '') {
        Yii::app()->params[$name] = $value;
    }

    public static function getCleanValue($data) {
        if (isset($data) && !empty($data)) {
            return trim($data);
        }
    }

    public static function capFirstWord($data) {
        if (isset($data) && !empty($data)) {
            return ucwords(trim(strtolower($data)));
        }
    }

    public static function getValue(&$obj = '', $type = 'string') {
        $_retVal = '';

        switch ($type) {
            case 'string':
                if (!empty($obj)) {
                    $_retVal = trim($obj);
                } else {
                    $_retVal = '';
                }
                break;
            case 'int':
                if (!empty($obj)) {
                    $_retVal = (int) $obj;
                } else {
                    $_retVal = 0;
                }
                break;
            case 'double':
                if (!empty($obj)) {
                    $_retVal = (double) $obj;
                } else {
                    $_retVal = 0.00;
                }
                break;
            default:
                break;
        }

        return $_retVal;
    }

    public static function getGeoInfoByIp($ip = '') {
        $_retObj = new stdClass();

        $rs = file_get_contents(self::GEO_INFO_URL . $ip);
        return $_retObj = json_decode($rs);
    }

    public static function getUnqiueKey() {
        return strtoupper(uniqid() . date('s'));
    }

    public static function getDbTimestamp() {
        date_default_timezone_set('Asia/Dhaka');
        return date('Y-m-d H:i:s');
    }

    public static function getDbDate() {
        date_default_timezone_set('Asia/Dhaka');
        return date('Y-m-d');
    }

    public static function getRandomPassword($length) {
        //$length = AppConstant::PASSWORD_LENGTH;
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($chars);
        $password = implode(array_slice($chars, 0, $length));
        return $password;
    }

    public static function getFilenameAsUnique($remdir = '', $file = '') {
        if (!empty($remdir) && !empty($file)) {
            $pos = strrpos($file, '.');
            $ext = substr($file, $pos);
            $dir = strrpos($file, '/');
            $dr = substr($file, 0, ($dir + 1));
            $arr = explode('/', $file);
            $fName = self::getUnqiueKey();
            return $fName . $ext;
        }
    }

    public static function fileRename($url, $filename, $savepath = './') {
        if (empty($url) || empty($filename)) {
            return FALSE;
        }
        $file_info = pathinfo($url);
        $downloaded_data = @file_get_contents($url);
        if (!$downloaded_data) {
            return FALSE;
        }
        $file = $savepath . $filename;
        $save = @file_put_contents($file, $downloaded_data);
        if (!$save) {

            return FALSE;
        }
        if (file_exists($url)) {
            unlink($url);
        }
        return TRUE;
    }

    public static function createNewDirectory($directoryName = '') {
        if (empty($directoryName)) {
            return false;
        }

        if (!is_dir($directoryName)) {
            mkdir($directoryName, 0755);
        }
    }

    public static function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public static function limitText($text, $len) {
        if (strlen($text) < $len) {
            return $text;
        }
        $text_words = explode(' ', $text);
        $out = null;

        foreach ($text_words as $word) {
            if ((strlen($word) > $len) && $out == null) {
                return substr($word, 0, $len) . "...";
            }
            if ((strlen($out) + strlen($word)) > $len) {
                return $out . "...";
            }
            $out.=" " . $word;
        }
        return $out;
    }

    public static function makeAlias($text) {
        if (!$text) {
            return '';
        }

        $text = str_replace(array('`', ' ', '+', '.', '?', '_'), '-', $text);

        /* Strip all HTML tags first */
        $text = strip_tags($text);

        /* Preserve %data */
        $text = preg_replace('#%([a-fA-F0-9][a-fA-F0-9])#', '-xx-$1-xx-', $text);
        $text = str_replace(array('%', '`'), '', $text);
        $text = preg_replace('#-xx-([a-fA-F0-9][a-fA-F0-9])-xx-#', '%$1', $text);

        /* Convert accented chars */
        $text = self::convertAccents($text);

        /* Convert it */
        if (self::isUTF8($text)) {
            if (function_exists('mb_strtolower')) {
                $text = mb_strtolower($text, 'UTF-8');
            }
            $text = self::utf8Encode($text, 500);
        }

        /* Finish off */
        $text = strtolower($text);

        if (strtolower(Yii::app()->charset) == 'utf-8') {
            $text = preg_replace('#&.+?;#', '', $text);
            $text = preg_replace('#[^%a-z0-9 _-]#', '', $text);
        } else {
            /* Remove &#xx; and &#xxx; but keep &#xxxx; */
            $text = preg_replace('/&#(\d){2,3};/', '', $text);
            $text = preg_replace('#[^%&\#;a-z0-9 _-]#', '', $text);
            $text = str_replace(array('&quot;', '&amp;'), '', $text);
        }

        $text = str_replace(array('`', ' ', '+', '.', '?', '_'), '-', $text);
        $text = preg_replace("#-{2,}#", '-', $text);
        $text = trim($text, '-');

        return ( $text ) ? $text : '-';
    }

    public static function convertAccents($string) {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        if (self::isUTF8($string)) {
            $_chr = array(
                /* Latin-1 Supplement */
                chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
                chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
                chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
                chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
                chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
                chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
                chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
                chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
                chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
                chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
                chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
                chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
                chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
                chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
                chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
                chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
                chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
                chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
                chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
                chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
                chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
                chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
                chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
                chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
                chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
                chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
                chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
                chr(195) . chr(191) => 'y',
                /* Latin Extended-A */
                chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
                chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
                chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
                chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
                chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
                chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
                chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
                chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
                chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
                chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
                chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
                chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
                chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
                chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
                chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
                chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
                chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
                chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
                chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
                chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
                chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
                chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
                chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
                chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
                chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
                chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
                chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
                chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
                chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
                chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
                chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
                chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
                chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
                chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
                chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
                chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
                chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
                chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
                chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
                chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
                chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
                chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
                chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
                chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
                chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
                chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
                chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
                chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
                chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
                chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
                chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
                chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
                chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
                chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
                chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
                chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
                chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
                chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
                chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
                chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
                chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
                chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
                chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
                chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
                /* Euro Sign */
                chr(226) . chr(130) . chr(172) => 'E',
                /* GBP (Pound) Sign */
                chr(194) . chr(163) => '');

            $string = strtr($string, $_chr);
        } else {
            $_chr = array();
            $_dblChars = array();

            /* We assume ISO-8859-1 if not UTF-8 */
            $_chr['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
                    . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
                    . chr(195) . chr(199) . chr(200) . chr(201) . chr(202)
                    . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
                    . chr(211) . chr(212) . chr(213) . chr(217) . chr(218)
                    . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
                    . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
                    . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
                    . chr(244) . chr(245) . chr(249) . chr(250) . chr(251)
                    . chr(252) . chr(253) . chr(255) . chr(191) . chr(182) . chr(179) . chr(166)
                    . chr(230) . chr(198) . chr(175) . chr(172) . chr(188)
                    . chr(163) . chr(161) . chr(177);

            $_chr['out'] = "EfSZszYcYuAAAACEEEEIIIINOOOOUUUUYaaaaceeeeiiiinoooouuuuyyzslScCZZzLAa";

            $string = strtr($string, $_chr['in'], $_chr['out']);
            $_dblChars['in'] = array(chr(140), chr(156), chr(196), chr(197), chr(198), chr(208), chr(214), chr(216), chr(222), chr(223), chr(228), chr(229), chr(230), chr(240), chr(246), chr(248), chr(254));
            $_dblChars['out'] = array('Oe', 'oe', 'Ae', 'Aa', 'Ae', 'DH', 'Oe', 'Oe', 'TH', 'ss', 'ae', 'aa', 'ae', 'dh', 'oe', 'oe', 'th');
            $string = str_replace($_dblChars['in'], $_dblChars['out'], $string);
        }

        return $string;
    }

    public static function isUTF8($str) {
        $c = 0;
        $b = 0;
        $bits = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);

            if ($c > 128) {
                if (($c >= 254))
                    return false;
                elseif ($c >= 252)
                    $bits = 6;
                elseif ($c >= 248)
                    $bits = 5;
                elseif ($c >= 240)
                    $bits = 4;
                elseif ($c >= 224)
                    $bits = 3;
                elseif ($c >= 192)
                    $bits = 2;
                else
                    return false;

                if (($i + $bits) > $len)
                    return false;

                while ($bits > 1) {
                    $i++;
                    $b = ord($str[$i]);
                    if ($b < 128 || $b > 191)
                        return false;
                    $bits--;
                }
            }
        }

        return true;
    }

    public static function utf8Encode($string, $len = 0) {
        $_unicode = '';
        $_values = array();
        $_nOctets = 1;
        $_unicodeLength = 0;
        $stringLength = strlen($string);

        for ($i = 0; $i < $stringLength; $i++) {
            $value = ord($string[$i]);

            if ($value < 128) {
                if ($len && ( $_unicodeLength >= $len )) {
                    break;
                }

                $_unicode .= chr($value);
                $_unicodeLength++;
            } else {
                if (count($_values) == 0) {
                    $_nOctets = ( $value < 224 ) ? 2 : 3;
                }

                $_values[] = $value;

                if ($len && ( $_unicodeLength + ($_nOctets * 3) ) > $len) {
                    break;
                }

                if (count($_values) == $_nOctets) {
                    if ($_nOctets == 3) {
                        $_unicode .= '%' . dechex($_values[0]) . '%' . dechex($_values[1]) . '%' . dechex($_values[2]);
                        $_unicodeLength += 9;
                    } else {
                        $_unicode .= '%' . dechex($_values[0]) . '%' . dechex($_values[1]);
                        $_unicodeLength += 6;
                    }

                    $_values = array();
                    $_nOctets = 1;
                }
            }
        }

        return $_unicode;
    }

    /* Flash Message */

    public static function hasFlashMessage() {
        return Yii::app()->user->hasFlash('warning') OR Yii::app()->user->hasFlash('success') OR Yii::app()->user->hasFlash('danger') OR Yii::app()->user->hasFlash('info') OR Yii::app()->user->hasFlash('error');
    }

    public static function renderFlashMessage($return = false) {
        $_ret = '';
        if (Yii::app()->user->hasFlash('warning')):
            $_ret = '<div class="alert alert-warning alert-dismissible no_mrgn" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . Yii::app()->user->getFlash('warning') . '</div>';
        elseif (Yii::app()->user->hasFlash('success')):
            $_ret = '<div class="alert alert-success alert-dismissible no_mrgn" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . Yii::app()->user->getFlash('success') . '</div>';
        elseif (Yii::app()->user->hasFlash('danger')):
            $_ret = '<div class="alert alert-danger alert-dismissible no_mrgn" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . Yii::app()->user->getFlash('danger') . '</div>';
        elseif (Yii::app()->user->hasFlash('info')):
            $_ret = '<div class="alert alert-info alert-dismissible no_mrgn" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . Yii::app()->user->getFlash('info') . '</div>';
        elseif (Yii::app()->user->hasFlash('error')):
            $_ret = '<div class="alert alert-danger alert-dismissible no_mrgn" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . Yii::app()->user->getFlash('error') . '</div>';
        endif;

        if (!$return) {
            echo $_ret;
            return true;
        }

        return $_ret;
    }

    public static function getUserClient($retType = 'array') {
        $_browser = array();

        if ($retType == 'json') {
            return CJSON::encode($_browser);
        }
        return $_browser;
    }

    public static function getUserIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getLocation($lat = '', $long = '') {
        try {
            if (!empty($long) && !empty($lat)) {
                $geocode = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $long . '&sensor=false');
                return $output = json_decode($geocode);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getIpinfo($ip, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;

        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }

        $purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );

        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city" => @$ipdat->geoplugin_city,
                            "state" => @$ipdat->geoplugin_regionName,
                            "country" => @$ipdat->geoplugin_countryName,
                            "country_code" => @$ipdat->geoplugin_countryCode,
                            "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode,
                            "region" => @$ipdat->geoplugin_region,
                            "region_code" => @$ipdat->geoplugin_regionCode,
                            "latitude" => @$ipdat->geoplugin_latitude,
                            "longitude" => @$ipdat->geoplugin_longitude
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            } else {
                $output = array(
                    "city" => @$ipdat->geoplugin_city,
                    "state" => @$ipdat->geoplugin_regionName,
                    "country" => @$ipdat->geoplugin_countryName,
                    "country_code" => @$ipdat->geoplugin_countryCode,
                    "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                    "continent_code" => @$ipdat->geoplugin_continentCode,
                    "region" => @$ipdat->geoplugin_region,
                    "region_code" => @$ipdat->geoplugin_regionCode,
                    "latitude" => @$ipdat->geoplugin_latitude,
                    "longitude" => @$ipdat->geoplugin_longitude
                );
            }
        }
        return json_encode($output);
    }

    public static function getBrowser($retType = '') {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Win';
            if (preg_match('/NT 6.2/i', $u_agent)) {
                $platform .= ' 8';
            } elseif (preg_match('/NT 6.3/i', $u_agent)) {
                $platform .= ' 8.1';
            } elseif (preg_match('/NT 6.1/i', $u_agent)) {
                $platform .= ' 7';
            } elseif (preg_match('/NT 6.0/i', $u_agent)) {
                $platform .= ' Vista';
            } elseif (preg_match('/NT 5.1/i', $u_agent)) {
                $platform .= ' XP';
            } elseif (preg_match('/NT 5.0/i', $u_agent)) {
                $platform .= ' 2000';
            }
            if (preg_match('/WOW64/i', $u_agent) || preg_match('/x64/i', $u_agent)) {
                $platform .= ' (x64)';
            }
        }

        $ub = '';
        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/msie/i', $u_agent)) {
            $bname = 'MSIE';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        if ($retType == 'json') {
            $_browser = array();
            $_browser['userAgent'] = $u_agent;
            $_browser['name'] = $bname;
            $_browser['version'] = $version;
            $_browser['platform'] = $platform;
            $_browser['pattern'] = $pattern;
            return CJSON::encode($_browser);
        } else {
            $_browser = new stdClass();
            $_browser->userAgent = $u_agent;
            $_browser->name = $bname;
            $_browser->version = $version;
            $_browser->platform = $platform;
            $_browser->pattern = $pattern;
        }

        return $_browser;
    }

    public static function getTimeZones() {
        return array(
            'Pacific/Kwajalein' => '(GMT-12:00) Kwajalein',
            'Pacific/Midway' => '(GMT-11:00) Midway Island',
            'Pacific/Samoa' => '(GMT-11:00) Samoa',
            'Pacific/Honolulu' => '(GMT-10:00) Hawaii',
            'America/Anchorage' => '(GMT-09:00) Alaska',
            'America/Los_Angeles' => '(GMT-08:00) Pacific Time',
            'America/Tijuana' => '(GMT-08:00) Tijuana, Baja California',
            'America/Denver' => '(GMT-07:00) Mountain Time',
            'America/Chihuahua' => '(GMT-07:00) Chihuahua',
            'America/Mazatlan' => '(GMT-07:00) Mazatlan',
            'America/Phoenix' => '(GMT-07:00) Arizona',
            'America/Regina' => '(GMT-06:00) Saskatchewan',
            'America/Tegucigalpa' => '(GMT-06:00) Central America',
            'America/Chicago' => '(GMT-06:00) Central Time',
            'America/Mexico_City' => '(GMT-06:00) Mexico City',
            'America/Monterrey' => '(GMT-06:00) Monterrey',
            'America/New_York' => '(GMT-05:00) Eastern Time',
            'America/Bogota' => '(GMT-05:00) Bogota',
            'America/Lima' => '(GMT-05:00) Lima',
            'America/Rio_Branco' => '(GMT-05:00) Rio Branco',
            'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
            'America/Caracas' => '(GMT-04:30) Caracas',
            'America/Halifax' => '(GMT-04:00) Atlantic Time',
            'America/Manaus' => '(GMT-04:00) Manaus',
            'America/Santiago' => '(GMT-04:00) Santiago',
            'America/La_Paz' => '(GMT-04:00) La Paz',
            'America/St_Johns' => '(GMT-03:30) Newfoundland',
            'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
            'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
            'America/Godthab' => '(GMT-03:00) Greenland',
            'America/Montevideo' => '(GMT-03:00) Montevideo',
            'Atlantic/South_Georgia' => '(GMT-02:00) Mid-Atlantic',
            'Atlantic/Azores' => '(GMT-01:00) Azores',
            'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
            'Europe/Dublin' => '(GMT) Dublin',
            'Europe/Lisbon' => '(GMT) Lisbon',
            'Europe/London' => '(GMT) London',
            'Africa/Monrovia' => '(GMT) Monrovia',
            'Atlantic/Reykjavik' => '(GMT) Reykjavik',
            'Africa/Casablanca' => '(GMT) Casablanca',
            'Europe/Belgrade' => '(GMT+01:00) Belgrade',
            'Europe/Bratislava' => '(GMT+01:00) Bratislava',
            'Europe/Budapest' => '(GMT+01:00) Budapest',
            'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
            'Europe/Prague' => '(GMT+01:00) Prague',
            'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
            'Europe/Skopje' => '(GMT+01:00) Skopje',
            'Europe/Warsaw' => '(GMT+01:00) Warsaw',
            'Europe/Zagreb' => '(GMT+01:00) Zagreb',
            'Europe/Brussels' => '(GMT+01:00) Brussels',
            'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
            'Europe/Madrid' => '(GMT+01:00) Madrid',
            'Europe/Paris' => '(GMT+01:00) Paris',
            'Africa/Algiers' => '(GMT+01:00) West Central Africa',
            'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
            'Europe/Berlin' => '(GMT+01:00) Berlin',
            'Europe/Rome' => '(GMT+01:00) Rome',
            'Europe/Stockholm' => '(GMT+01:00) Stockholm',
            'Europe/Vienna' => '(GMT+01:00) Vienna',
            'Europe/Minsk' => '(GMT+02:00) Minsk',
            'Africa/Cairo' => '(GMT+02:00) Cairo',
            'Europe/Helsinki' => '(GMT+02:00) Helsinki',
            'Europe/Riga' => '(GMT+02:00) Riga',
            'Europe/Sofia' => '(GMT+02:00) Sofia',
            'Europe/Tallinn' => '(GMT+02:00) Tallinn',
            'Europe/Vilnius' => '(GMT+02:00) Vilnius',
            'Europe/Athens' => '(GMT+02:00) Athens',
            'Europe/Bucharest' => '(GMT+02:00) Bucharest',
            'Europe/Istanbul' => '(GMT+02:00) Istanbul',
            'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
            'Asia/Amman' => '(GMT+02:00) Amman',
            'Asia/Beirut' => '(GMT+02:00) Beirut',
            'Africa/Windhoek' => '(GMT+02:00) Windhoek',
            'Africa/Harare' => '(GMT+02:00) Harare',
            'Asia/Kuwait' => '(GMT+03:00) Kuwait',
            'Asia/Riyadh' => '(GMT+03:00) Riyadh',
            'Asia/Baghdad' => '(GMT+03:00) Baghdad',
            'Africa/Nairobi' => '(GMT+03:00) Nairobi',
            'Asia/Tbilisi' => '(GMT+03:00) Tbilisi',
            'Europe/Moscow' => '(GMT+03:00) Moscow',
            'Europe/Volgograd' => '(GMT+03:00) Volgograd',
            'Asia/Tehran' => '(GMT+03:30) Tehran',
            'Asia/Muscat' => '(GMT+04:00) Muscat',
            'Asia/Baku' => '(GMT+04:00) Baku',
            'Asia/Yerevan' => '(GMT+04:00) Yerevan',
            'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
            'Asia/Karachi' => '(GMT+05:00) Karachi',
            'Asia/Tashkent' => '(GMT+05:00) Tashkent',
            'Asia/Kolkata' => '(GMT+05:30) Calcutta',
            'Asia/Colombo' => '(GMT+05:30) Sri Jayawardenepura',
            'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
            'Asia/Dhaka' => '(GMT+06:00) Dhaka',
            'Asia/Almaty' => '(GMT+06:00) Almaty',
            'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
            'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
            'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
            'Asia/Bangkok' => '(GMT+07:00) Bangkok',
            'Asia/Jakarta' => '(GMT+07:00) Jakarta',
            'Asia/Brunei' => '(GMT+08:00) Beijing',
            'Asia/Chongqing' => '(GMT+08:00) Chongqing',
            'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
            'Asia/Urumqi' => '(GMT+08:00) Urumqi',
            'Asia/Irkutsk' => '(GMT+08:00) Irkutsk',
            'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
            'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
            'Asia/Singapore' => '(GMT+08:00) Singapore',
            'Asia/Taipei' => '(GMT+08:00) Taipei',
            'Australia/Perth' => '(GMT+08:00) Perth',
            'Asia/Seoul' => '(GMT+09:00) Seoul',
            'Asia/Tokyo' => '(GMT+09:00) Tokyo',
            'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
            'Australia/Darwin' => '(GMT+09:30) Darwin',
            'Australia/Adelaide' => '(GMT+09:30) Adelaide',
            'Australia/Canberra' => '(GMT+10:00) Canberra',
            'Australia/Melbourne' => '(GMT+10:00) Melbourne',
            'Australia/Sydney' => '(GMT+10:00) Sydney',
            'Australia/Brisbane' => '(GMT+10:00) Brisbane',
            'Australia/Hobart' => '(GMT+10:00) Hobart',
            'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
            'Pacific/Guam' => '(GMT+10:00) Guam',
            'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
            'Asia/Magadan' => '(GMT+11:00) Magadan',
            'Pacific/Fiji' => '(GMT+12:00) Fiji',
            'Asia/Kamchatka' => '(GMT+12:00) Kamchatka',
            'Pacific/Auckland' => '(GMT+12:00) Auckland',
            'Pacific/Tongatapu' => '(GMT+13:00) Nukualofa'
        );
    }

    public static function getUnits() {
        return array(
            'Inch' => 'Inch',
            'Foot' => 'Foot',
            'Centimetre' => 'Centimetre',
            'Meter' => 'Meter',
            'Gram' => 'Gram',
            'Kilogram' => 'Kilogram',
            'Liter' => 'Liter',
            'Gallon' => 'Gallon',
            'Piece' => 'Piece',
            'Packet' => 'Packet',
            'Kartun' => 'Kartun',
            'Set' => 'Set',
        );
    }

    public static function userAccessItems() {
        return array(
            'access_control',
            'admin_user_edit',
            'admin_profile_view',
            'account_list',
            'account_create',
            'account_edit',
            'account_delete',
            'account_balance',
            'account_balance_add',
            'account_balance_edit',
            'account_balance_delete',
            'balance_sheet',
            'balance_sheet_update',
            'cash_account_list',
            'cash_account_edit',
            'cash_account_delete',
            'cash_deposit',
            'cash_deposit_edit',
            'cash_withdraw',
            'cash_withdraw_edit',
            'cash_voucher',
            'category_list',
            'category_create',
            'category_edit',
            'category_delete',
            'company_list',
            'company_create',
            'company_edit',
            'company_delete',
            'company_payment',
            'company_payment_create',
            'company_payment_edit',
            'company_payment_delete',
            'customer_list',
            'customer_create',
            'customer_edit',
            'customer_delete',
            'customer_payment',
            'customer_payment_create',
            'customer_payment_edit',
            'customer_payment_delete',
            'dashboard',
            'dues_list',
            'expense_list',
            'expense_create',
            'expense_edit',
            'expense_delete',
            'head_list',
            'head_create',
            'head_edit',
            'head_transaction_view',
            'head_delete',
            'income_list',
            'payment_list',
            'payment_create',
            'payment_edit',
            'payment_delete',
            'payment_option',
            'product_list',
            'product_stock',
            'product_create',
            'product_edit',
            'product_delete',
            'profile_view',
            'profile_edit',
            'profit',
            'purchase_list',
            'purchase_create',
            'purchase_view',
            'purchase_edit',
            'purchase_process',
            'purchase_delete',
            'purchase_item_delete',
            'purchase_return_list',
            'purchase_return_cretae',
            'purchase_return_view',
            'purchase_return_edit',
            'purchase_return_process',
            'purchase_return_delete',
            'reset_invoice',
            'sale_list',
            'sale_create',
            'sale_view',
            'sale_edit',
            'sale_process',
            'sale_delete',
            'sale_item_delete',
            'sale_return_list',
            'sale_return_create',
            'sale_return_view',
            'sale_return_edit',
            'sale_return_process',
            'sale_return_delete',
            'settings',
            'user_list',
            'user_create',
            'user_edit',
            'user_delete',
            'user_activate',
            'view_cart',
        );
    }

    public static function userDefaultAccessItems() {
        return array(
            'category_list',
            'category_create',
            'category_edit',
            'company_list',
            'company_create',
            'company_edit',
            'dashboard',
            'payment_list',
            'payment_create',
            'payment_edit',
            'payment_option',
            'product_list',
            'product_stock',
            'product_create',
            'product_edit',
            'profile_view',
            'profile_edit',
            'purchase_list',
            'purchase_create',
            'purchase_view',
            'purchase_edit',
            'purchase_process',
            'purchase_return_list',
            'purchase_return_create',
            'purchase_return_view',
            'purchase_return_edit',
            'purchase_return_process',
            'sale_list',
            'sale_create',
            'sale_view',
            'sale_edit',
            'sale_process',
            'sale_return_list',
            'sale_return_create',
            'sale_return_view',
            'sale_return_edit',
            'sale_return_process',
        );
    }

    public static function get_time_difference($starttime, $endtime) {
        $starttime = strtotime(date('h:i A'));
        $endtime = strtotime(date('h:i A'));
        $difference = $endtime - $starttime;
        $hoursDiff = $difference / 3600;
        $hoursDiff . " hour ";
        return $hoursDiff;
    }

    public static function getQuerySQL($_model, $_dbCriteria) {
        $command = $_model->getCommandBuilder()->createFindCommand($_model->getTableSchema(), $_dbCriteria);
        return $command->getText();
    }

    public static function pr($obj, $exit = true) {
        echo '<pre>';
        print_r($obj);
        echo '</pre>';

        if ($exit == true) {
            exit;
        }
    }

    public static function en2bn($str) {
        $en = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $bn = array('o', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
        if (Yii::app()->language == 'bn') {
            $tstr = str_replace($en, $bn, $str);
        } else {
            $tstr = str_replace($bn, $en, $str);
        }
        return $tstr;
    }

    public static function getFloat($value) {
        $format = number_format((float) $value, 2, '.', '');
        return !empty($value) ? $format : "";
    }

    public static function int_to_words($x) {
        $nwords = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen", "twenty", 30 => "thirty", 40 => "forty", 50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty", 90 => "ninety");
        if (!is_numeric($x)) {
            $w = '#';
        } else if (fmod($x, 1) != 0) {
            $w = '#';
        } else {
            if ($x < 0) {
                $w = 'minus ';
                $x = -$x;
            } else {
                $w = '';
            }
            if ($x < 21) {
                $w .= $nwords[floor($x)];
            } else if ($x < 100) {
                $w .= $nwords[10 * floor($x / 10)];
                $r = fmod($x, 10);
                if ($r > 0) {
                    $w .= '-' . $nwords[$r];
                }
            } else if ($x < 1000) {
                $w .= $nwords[floor($x / 100)] . ' hundred';
                $r = fmod($x, 100);
                if ($r > 0) {
                    $w .= ' and ' . self::int_to_words($r);
                }
            } else if ($x < 100000) {
                $w .= self::int_to_words(floor($x / 1000)) . ' thousand';
                $r = fmod($x, 1000);
                if ($r > 0) {
                    $w .= ' ';
                    if ($r < 100) {
                        $w .= 'and ';
                    }
                    $w .= self::int_to_words($r);
                }
            } else {
                $w .= self::int_to_words(floor($x / 100000)) . ' lakh';
                $r = fmod($x, 100000);
                if ($r > 0) {
                    $w .= ' ';
                    if ($r < 100) {
                        $word .= 'and ';
                    }
                    $w .= self::int_to_words($r);
                }
            }
        }
        return $w;
    }

    public static function phonePrefix() {
        return array(
            '02', '031', '041', '051', '071', '081', '091',
            '0321', '0331', '0351', '0361', '0371', '0381',
            '0431', '0441', '0448', '0451', '0461', '0468', '0471', '0481', '0488', '0491', '0498',
            '0521', '0531', '0541', '0551', '0561', '0568', '0571', '0581', '0591',
            '0601', '0628', '0631', '0641', '0651', '0661', '0668', '0691',
            '0721', '0731', '0741', '0751', '0761', '0771', '0781', '0791',
            '0821', '0831', '0841', '0851', '0861', '0871',
            '0921', '0931', '0941', '0951', '0981',
            '03020', '03022', '03023', '03024', '03025', '03026', '03027', '03028', '03029', '03032', '03033', '03034', '03035', '03036', '03221', '03222', '03225', '03322', '03323', '03324', '03325', '03326', '03422', '03424', '03425', '03427', '03529', '03822', '03824',
            '04020', '04029', '04033', '04223', '04224', '04225', '04226', '04227', '04228', '04320', '04322', '04323', '04324', '04325', '04326', '04327', '04328', '04329', '04332', '04422', '04423', '04424', '04425', '04426', '04455', '04523', '04525', '04623', '04624', '04625', '04626', '04627', '04652', '04653', '04654', '04655', '04656', '04657', '04658', '04823', '04854', '04922', '04924', '04925', '04953',
            '05023', '05024', '05028', '05029', '05222', '05224', '05225', '05227', '05323', '05325', '05326', '05327', '05424', '05526', '05653', '05724', '05826',
            '06023', '06024', '06222', '06223', '06224', '06225', '06253', '06254', '06255', '06257', '06323', '06324', '06327', '06423', '06424', '06524', '06527', '06622', '06623', '06624', '06652', '06653', '06654', '06655', '06722', '06723', '06724', '06725', '06822', '06823', '06824', '06825', '06922', '06923', '06924', '06925', '06926',
            '07022', '07225', '07227', '07228', '07229', '07323', '07324', '07325', '07326', '07327', '07328', '07329', '07425', '07426', '07522', '07523', '07524', '07525', '07526', '07527', '07528', '07529', '07622', '07724', '07823', '07825', '07923',
            '08020', '08022', '08023', '08024', '08025', '08026', '08027', '08028', '08029', '08032', '08033', '08220', '08222', '08223', '08224', '08225', '08226', '08227', '08229', '08232', '08325', '08327', '08328', '08332', '08424', '08425', '08426', '08427', '08522', '08523', '08524', '08525', '08526', '08527', '08528', '08622', '08623', '08624', '08625', '08626', '08723', '08725', '08727',
            '09020', '09022', '09024', '09025', '09027', '09028', '09032', '09033', '09222', '09223', '09225', '09226', '09227', '09228', '09229', '09232', '09233', '09423', '09424', '09426', '09428', '09524', '09525', '09528', '09529', '09824', '09827'
        );
    }

    public static function mobilePrefix() {
        return array('015', '016', '017', '018', '019');
    }

    public static function a2zlist() {
        return array('ALL', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'MS', 'MSS', 'M/S', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    }

}
