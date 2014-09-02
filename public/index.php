<?php

require '../vendor/autoload.php';

$app = new \Slim\Slim();

require '../appConfig.php';

// set base url
$app->hook('slim.before', function () use ($app) {
    $baseUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
    if ($baseUrl != '') {
        $baseUrl = DS . $baseUrl;
    }
    $app->view()->appendData(array('baseUrl' => $baseUrl . DS));
});

$app->run();

