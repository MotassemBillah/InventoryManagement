<?php

function pr($obj, $exit = true) {
    echo '<pre>';
    print_r($obj);
    echo '</pre>';

    if ($exit == true) {
        exit;
    }
}

function debug($obj, $exit = true) {
    echo '<pre>';
    var_dump($obj);
    echo '</pre>';

    if ($exit == true) {
        exit;
    }
}
