<?php
/**
 * Этот файл предназначен для добавления обработчиков сервиса
 * Объект класса-маршрутизатора: $__router
 */

$_router->addRoute(Router::$METHOD_GET, '/users\/login\/\w{1,20}\/pass\/\w{1,20}\/settings\/\w{1,255}/i', array('MobileHandler', 'getLogin'));
$_router->addRoute(Router::$METHOD_GET, '/users/i', array('MobileHandler', 'getUsers'));
$_router->addRoute(Router::$METHOD_GET, '/user\/[0-9]{1,10}/i', array('MobileHandler', 'getUser'));

$_router->addRoute(Router::$METHOD_POST, '/routes_erp\/upload/i', array('ErpMapXHandler', 'setRoutes'));

?>