<?php

class Service {
    const LOG_LEVEL_INFO    = 1;
    const LOG_LEVEL_WARNING = 2;
    const LOG_LEVEL_DEBUG   = 9;

    private static $db_instance;

    private $db;
    private $uri;
    private $router;
    private $system_key_id;
    
    /**
     * Конструктор сервиса
     * @param type $db_config конфигурация базы данных. см. config.php
     * @param type $router объект маршрутизатора
     * @param type $base_uri если сервис установлен не в корне 
     * @throws Exception
     */
    public function __construct($db_config, &$router, $base_uri = '') {
        $this->system_key_id = 0;

        $this->db = self::getDb($db_config);
        $this->router = $router;
        $this->router->addParam($this->get_uri_path());
        
    }

    public static function getDb($db_config){
        if(self::$db_instance == null){
             self::$db_instance = new mysqli($db_config["server"], $db_config["username"], $db_config["password"], $db_config["database"]);
        }
        return self::$db_instance;
    }

     /**
     * Проверка ключа клиента сервиса
     * @return boolean
     */
    private function is_valid_key() {
        $sql = "SELECT * FROM `service_clients` WHERE `key` = " . $this->router->getParam(0) . "";
        $res = $this->db->query($sql);
        if ($row = $res->fetch_array(MYSQL_ASSOC)) {
            $this->system_key_id = $row['_id'];
        }
        return $this->system_key_id > 0;
    }

    private function get_uri_path() {
        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);
            if (isset($_SERVER['QUERY_STRING']))
                $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }

        $url = '';
        $request_url    = $_SERVER['REQUEST_URI'];
        $script_url     = $_SERVER['PHP_SELF'];
        $request_url    = str_replace(BASE_DIR, '', $request_url);
        
        if ($request_url != $script_url)
            $url = trim(preg_replace('/' . str_replace('/', '\/', str_replace('index.php', '', $script_url)) . '/', '', $request_url, 1), '/');

        $url = rtrim(preg_replace('/\?.*/', '', $url), '/');

        $this->uri = $url;

        return explode('/', $url);
    }

    /**
     * 
     * @global type $log_config
     * @param type $level
     * @param type $message
     */
    public static function log($level, $message) {
        global $log_config;

        if ($log_config['enabled'] && $level == 0) {
            file_put_contents($log_config['service_path_log'], date("Y-m-d H:i:s") . ' - ' . $message . "\n", FILE_APPEND);
        }
    }

    /**
     * Точка входа сервиса
     * @return type
     */
    public function rest() {
        Service::log(0, 'Request data: ' . $this->uri);

        if (!$this->is_valid_key()) {
            Service::log(0, "Key not valid :" . $this->router->getParam(0));
            // TODO: вернуть 403 ошибку
            return;
        }

        // Заменим ключ на идентификатор
        $this->router->setParam(0, $this->system_key_id);

        header('Content-type: application/json');

        $this->db->query("SET NAMES utf8");

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->router->Run(Router::$METHOD_POST, $this->uri);
                break;
            case 'GET':
                $this->router->Run(Router::$METHOD_GET, $this->uri);
                break;
        }
    }


}

?>