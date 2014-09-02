<?php

use \Phpmig\Adapter,
		\Pimple,
		\Illuminate\Database\Capsule\Manager as Capsule;

$container = new Pimple();

$capsule = new Capsule;
require_once('appDbConfig.php');
$capsule->addConnection($dbConfig, 'default');
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = $container->share(function() use ($dbConfig) {
    $dbh = new PDO('mysql:dbname=' . $dbConfig['database'] . ';host=' . $dbConfig['host'],$dbConfig['username'],$dbConfig['password']);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
});

$container['phpmig.adapter'] = $container->share(function() use ($container) {
    return new Adapter\PDO\Sql($container['db'], 'migrations');
});

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

$container['phpmig.migrations_template_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . '.template.php';


return $container;


