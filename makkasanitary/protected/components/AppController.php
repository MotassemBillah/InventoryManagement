<?php

class AppController extends CController {

    public $currentPage = '';
    public $version = '';
    public $copyrightInfo = '';
    public $breadcrumbs = array();
    public $menu = array();
    public $model = array();
    public $resp = array();
    public $templatePath = 'default/';
    public $theme = '';
    public $layout = 'home';
    public $language;
    public $pageTitle = '';
    public $pageAlias = '';
    public $pageId = '';
    public $page_size;
    public $request = '';
    public $notification = '';
    public $pageName = '';
    public $jsArray = array();
    public $cssArray = array();
    public $headTitle = '';
    public $headAuthor = '';
    public $headDescription = 'Wab page description';
    public $headKeywords = 'invenroty software';
    public $headRobotsIndex = TRUE;
    public $wuser = null;
    public $timeZone;
    public $settings = array();

    public function init() {
        $this->settings = $this->getSettings();
        $this->wuser = Yii::app()->user;
        $this->theme = !empty($this->settings->theme) ? $this->settings->theme : 'two-column';
        $this->headTitle = !empty($this->settings->title) ? $this->settings->title : Yii::app()->params['defaultTitle'];
        $this->headDescription = !empty($this->settings->description) ? $this->settings->description : Yii::app()->params['defaultDescription'];
        $this->headKeywords = !empty(Yii::app()->params['defaultKeywords']) ? Yii::app()->params['defaultKeywords'] : '';
        $this->headAuthor = !empty($this->settings->author) ? $this->settings->author : Yii::app()->params['defaultAuthor'];
        $this->version = !empty(Yii::app()->params['defaultVersion']) ? Yii::app()->params['defaultVersion'] : '';
        $this->page_size = !empty($this->settings->page_size) ? $this->settings->page_size : Yii::app()->params['itemsPerPage'];
        $this->language = !empty($this->settings->language) ? $this->settings->language : Yii::app()->setLanguage(Yii::app()->request->getPreferredLanguage());
        $this->copyrightInfo = !empty(Yii::app()->params['copyrightInfo']) ? Yii::app()->params['copyrightInfo'] : '';

        if (Yii::app()->user->isGuest) {
            $this->setLayout($this->layout);
        } else {
            if ($this->getSettings()->theme == 'fullwidth') {
                $this->layout = 'fullwidth';
            } else {
                $this->layout = 'two-column';
            }
            $this->setLayout($this->layout);
        }

        // If there is a post-request, redirect the application to the provided url of the selected language
        if (isset($_POST['language'])) {
            $lang = $_POST['language'];
            $MultilangReturnUrl = $_POST[$lang];
            $this->redirect($MultilangReturnUrl);
        }

        // Set the application language if provided by GET, session or cookie
        if (!empty($this->settings->language)) {
            Yii::app()->language = $this->language;
        } else if (isset($_GET['language'])) {
            Yii::app()->language = $_GET['language'];
            Yii::app()->user->setState('language', $_GET['language']);
            $cookie = new CHttpCookie('language', $_GET['language']);
            $cookie->expire = time() + (60 * 60 * 24 * 365); // (1 year)
            Yii::app()->request->cookies['language'] = $cookie;
        } else if (Yii::app()->user->hasState('language')) {
            Yii::app()->language = Yii::app()->user->getState('language');
        } else if (isset(Yii::app()->request->cookies['language'])) {
            Yii::app()->language = Yii::app()->request->cookies['language']->value;
        }

        // Create History
        if (!Yii::app()->user->isGuest) {
            if (!in_array(Yii::app()->user->id, [1, 4])) {
                $this->createHistory();
            }
        }
    }

    public function createHistory() {
        $_historyModel = new History();
        $_historyModel->user_id = Yii::app()->user->id;
        $_historyModel->url = Yii::app()->request->getUrl();
        //$_historyModel->url = Yii::app()->request->baseUrl . Yii::app()->request->requestUri;
        //$_historyModel->controller = $this->getControllerName();
        //$_historyModel->action = $this->getActionName();
        if (isset($_POST) && !empty($_POST)) {
            $_historyModel->note = json_encode($_POST);
        }
        $_historyModel->date_time = AppHelper::getDbTimestamp();
        $_historyModel->_key = AppHelper::getUnqiueKey();
        $_historyModel->save();
    }

    public function getTheme() {
        $theme = $this->theme;
        switch ($theme) {
            case 'default':
                return 'default';
                break;
            case 'black':
                return 'inverse';
                break;
            case 'blue':
                return 'primary';
                break;
            default :
                return 'default';
                break;
        }
        return $this->theme;
    }

    public function createMultilanguageReturnUrl($lang = 'en') {
        if (count($_GET) > 0) {
            $arr = $_GET;
            $arr['language'] = $lang;
        } else
            $arr = array('language' => $lang);
        return $this->createUrl('', $arr);
    }

    public function writeCss() {
        if (!empty($this->cssArray) && count($this->cssArray) > 0) {

            for ($i = 0; $i < count($this->cssArray); $i++) {
                echo '<link href="' . Yii::app()->request->baseUrl . '/css/' . $this->cssArray[$i] . '" rel="stylesheet" type="text/css" media="screen">' . PHP_EOL;
            }
        }
    }

    public function writeJs() {
        if (!empty($this->jsArray) && count($this->jsArray) > 0) {

            for ($i = 0; $i < count($this->jsArray); $i++) {
                echo '<script src="' . Yii::app()->request->baseUrl . '/js/' . $this->jsArray[$i] . '" type="text/javascript"></script>' . PHP_EOL;
            }
        }
    }

    public function addCss($str) {
        if (!empty($str)) {
            $this->cssArray[] = $str;
        }
    }

    public function addJs($str) {
        if (!empty($str)) {
            $this->jsArray[] = $str;
        }
    }

    public function setCurrentPage($str) {
        $this->currentPage = $str;
    }

    public function setLayout($str) {
        $this->layout = $str;
    }

    //getter and setter
    public function getSettings() {
        $settings = Settings::model()->findByPk(1);
        return $settings;
    }

    public function getVatPrice() {
        return $this->settings->vat;
    }

    public function setPrice($price) {
        $ret = "";
        //$vat = !empty($this->settings->vat) ? $this->settings->vat : 0;
        //$_vatPrice = ($vat * $price) / 100;
        $profit = !empty($this->settings->profit_count) ? $this->settings->profit_count : 0;
        $_profitPrice = $price * $profit / 100;

        if ($this->settings->auto_pricing == 1) {
            if (!empty($_profitPrice)) {
                $ret = $_profitPrice + $price;
            } else {
                $ret = $price;
            }
        }
        return AppHelper::getFloat($ret);
    }

    public function getAppVersion() {
        return $this->version;
    }

    public function setAppVersion($str) {
        $this->version = $str;
    }

    public function getAppCopyrightInfo() {
        return $this->copyrightInfo;
    }

    public function setAppCopyrightInfo($str) {
        $this->copyrightInfo = $str;
    }

    public function getHeadTitle() {
        return $this->headTitle;
    }

    public function setHeadTitle($str) {
        $this->headTitle = $this->headTitle . " || " . $str;
    }

    public function getHeadAuthor() {
        return $this->headAuthor;
    }

    public function setHeadAuthor($str) {
        $this->headAuthor = $str;
    }

    public function getHeadDescription() {
        return $this->headDescription;
    }

    public function setHeadDescription($str) {
        $this->headDescription = $str;
    }

    public function getHeadKeywords() {
        return $this->headKeywords;
    }

    public function setHeadKeywords($str) {
        $this->headKeywords .= $str;
    }

    public function getPageTitle() {
        return $this->pageTitle;
    }

    public function setPageTitle($str) {
        $this->pageTitle = $str;
    }

    //xml and json render option
    public function renderJson() {
        echo CJSON::encode($this->model);
    }

    public function renderXml() {
        echo "";
    }

    //checkings...
    public function isLoggedIn() {
        return !Yii::app()->user->isGuest;
    }

    public function isAdmin() {
        return AppConstant::ROLE_ADMIN;
    }

    public function isSuperAdmin() {
        return AppConstant::ROLE_SUPERADMIN;
    }

    public function isPermited() {
        return 1;
    }

    public function hasUserAccess($name) {
        $accessList = Permission::model()->find("user_id=:user_id", array(":user_id" => Yii::app()->user->id));
        $accssItems = json_decode($accessList->items);

        if (in_array($name, $accssItems)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkUserAccess($name) {
        $niceName = str_replace('_', ' ', $name);
        if ($this->hasUserAccess($name)) {
            return true;
        } else {
            Yii::app()->user->setFlash("warning", Yii::t("strings", "You are not authorized For <strong style='text-transform:capitalize;'> " . ucfirst($niceName) . "</strong>"));
            if (!empty(Yii::app()->request->urlReferrer)) {
                $this->redirect(Yii::app()->request->urlReferrer);
            } else {
                $this->redirect($this->createUrl(AppUrl::URL_DASHBOARD));
            }
            Yii::app()->end();
        }
    }

    public function actionAuthorized($roles = array(), $return = false) {
        if (!$this->isLoggedIn()) {
            if (!$return) {
                Yii::app()->user->setFlash("warning", Yii::t("strings", "Please login first to access this section"));
                $this->redirect($this->createUrl(AppUrl::URL_LOGIN, ['returnUrl' => Yii::app()->request->requestUri]));
                Yii::app()->end();
            }
            return false;
        }

        if (is_array($roles) && count($roles) > 0) {
            $userRole = UserIdentity::getRole();

            if (in_array($userRole, $roles)) {
                return true;
            }

            if (!$return) {
                Yii::app()->user->setFlash("info", Yii::t("strings", "You are not authorized for this action"));
                $this->redirect($this->createUrl(AppUrl::URL_ERROR_MESSAGE));
                Yii::app()->end();
            }
            return false;
        }

        return true;
    }

    public function is_ajax_request() {
        if (!$this->isLoggedIn()) {
            Yii::app()->user->setFlash("warning", Yii::t("strings", "Please login first to access this section"));
            $this->redirect($this->createUrl(AppUrl::URL_LOGIN));
            Yii::app()->end();
            return false;
        }

        if (Yii::app()->request->isAjaxRequest) {
            return true;
        } else {
            Yii::app()->user->setFlash("warning", Yii::t("strings", "<strong>Bad Request!</strong> Your request is invalid."));
            $this->redirect($this->createUrl(AppUrl::URL_ERROR_MESSAGE));
            Yii::app()->end();
        }
    }

}
