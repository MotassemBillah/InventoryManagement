<?php

return array(
    'class' => 'UrlManager',
    'urlFormat' => 'path',
    'cacheID' => 'cache',
    'showScriptName' => false,
    'appendParams' => true,
    'caseSensitive' => false,
    'urlSuffix' => '',
    'rules' => array(
        '<language:[a-z]{2}>/<_m>/<_c>' => '<_m>/<_c>',
        '<language:[a-z]{2}>/<_m>/<_c>/<_a>*' => '<_m>/<_c>/<_a>',
        '<language:[a-z]{2}>/<_m>/<_a>' => '<_m>/<_a>',
        '<language:[a-z]{2}>/<_c>' => '<_c>',
        '<language:[a-z]{2}>/<_c>/<_a>' => '<_c>/<_a>',
        // Nothing should go below these default rules
        '<controller:\w+>/<id:\d+>' => '<controller>/view',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
    ),
);

