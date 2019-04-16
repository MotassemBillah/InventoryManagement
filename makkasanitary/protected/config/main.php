<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Inventory',
    'preload' => array('log', 'cache'),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.yii-mail.*',
        'application.extensions.mandrill.*',
    ),
    'defaultController' => 'login',
    'charset' => 'UTF-8',
    'sourceLanguage' => 'en',
    'language' => 'en',
    'components' => array(
        'db' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=inv_makkasanitary',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'emulatePrepare' => true,
            'enableProfiling' => true,
            'enableParamLogging' => true,
        ),
        'user' => array(
            'class' => 'WebUser',
            'allowAutoLogin' => true,
//            'identityCookie' => array(
//                'path' => '/',
//                'domain' => '.makkasanitaryandtiles.com',
//            ),
        ),
        'session' => array(
            'cookieMode' => 'allow',
//            'cookieParams' => array(
//                'path' => '/',
//                'domain' => '.makkasanitaryandtiles.com',
//            ),
        ),
        'messages' => array(
            'class' => 'CPhpMessageSource',
            'basePath' => 'protected/messages',
            'cacheID' => 'cache',
        ),
        'smsGlobal' => array(
            'class' => 'SmsGlobal',
            'username' => 'qh5s6wjk',
            'password' => 'rN5jR35p',
            'from' => '01814491116',
        ),
        'cache' => array(
            'class' => 'system.caching.CFileCache',
        ),
        'errorHandler' => array(
            'errorAction' => 'error/index',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'cacheID' => 'cache',
            'showScriptName' => false,
            'appendParams' => true,
            'urlSuffix' => '',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CWebLogRoute',
                    'levels' => 'error, info, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CProfileLogRoute',
                    'enabled' => false,
                ),
            ),
        ),
    ),
    'params' => include(dirname(__FILE__) . '/params.php'),
    'modules' => array(
        'ajax' => array(
            'defaultController' => 'user',
            'class' => 'application.modules.ajax.AjaxModule',
        ),
        'ledger' => array(
            'defaultController' => 'balancesheet',
            'class' => 'application.modules.ledger.LedgerModule',
        )
    ),
);
