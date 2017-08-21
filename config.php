<?php
// Подключение обработчиков
require_once 'handlers/MobileHandler.php';
require_once 'handlers/ErpMapXHandler.php';

define('BASE_DIR', 'base_dir');

$db_config = array(
    'server'   => 'localhost',
    'database' => 'delivery_service',
    'username' => 'root',
    'password' => 'password',
    'verbose'  => false // If true errors will be shown
);

$log_config = array(
    'enabled' => true,
    'level'  => 0,
    'service_path_log'   => 'logs/service.log'
);

?>