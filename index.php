<?php

require_once 'config.php';
require_once 'classes/Router.php';
require_once 'classes/Service.php';
require_once 'classes/Handler.php';

$_router = new Router();
require_once 'routers.php';

header('Content-type: text/html; charset=utf-8');

try {
    $arrest = new Service($db_config, $_router);
    $arrest->rest();
} 
catch (Exception $e) {
    echo $e;
}

?>