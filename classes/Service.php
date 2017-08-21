<?php

class Service {

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

        $this->router = $router;
        $this->router->addParam($this->get_uri_path());


        $this->db = new Database($db_config);

        if (!$this->db->init()) {
            throw new Exception($this->db->get_error());
        }
    }

    /**
     * Проверка ключа клиента сервиса
     * @return boolean
     */
    private function is_valid_key() {
        $sql = "SELECT * FROM `service_clients` WHERE `key` = " . $this->router->getParam(0) . "";
        $res = mysql_query($sql);
        if ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
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

        mysql_query("SET NAMES utf8");

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->router->Run(Router::$METHOD_POST, $this->uri);
                break;
            case 'GET':
                $this->router->Run(Router::$METHOD_GET, $this->uri);
                break;
        }
    }

    private function _post() {
        $rawData = file_get_contents("php://input");

        $uri_tracking = "/tracking\/upload/i";
        $uri_routes = "/routes\/upload/i";
        $uri_message = "/messages\/upload/i";
        $uri_img = "/img\/upload/i";
        $uri_pointlocation = "/pointlocation\/upload/i";
        $uri_travel_totals = "/travel_totals\/upload/i";
        $uri_remains = "/remains\/upload/i";
        $uri_delivery = "/delivery\/upload/i";
        $uri_nexttravel = "/nexttravel\/upload/i";


        Service::log(0, $this->uri);

        if (preg_match($uri_tracking, $this->uri) == 1) {
            postTracking($rawData);
        } elseif (preg_match($uri_routes, $this->uri) == 1) {
            postRoutes($rawData);
        } elseif (preg_match($uri_travel_totals, $this->uri) == 1) {
            @postTravelTotals($rawData);
        } elseif (preg_match($uri_message, $this->uri) == 1) {
            postMessage($rawData);
        } elseif (preg_match($uri_img, $this->uri) == 1) {
            postImg($rawData);
        } elseif (preg_match($uri_pointlocation, $this->uri) == 1) {
            postPointlocation($rawData);
        } elseif (preg_match($uri_remains, $this->uri) == 1) {
            postRemains($rawData);
        } elseif (preg_match($uri_delivery, $this->uri) == 1) {
            postDelivery($rawData);
        } elseif (preg_match($uri_nexttravel, $this->uri) == 1) {
            postNexttravel($rawData);
        } elseif (_postRouterERP($this->uri, $rawData)) { // добавлено: post обработчики ERP
            // ничего здесь не делаем
        } else {
            $resp = array(
                'response' => false,
                'message' => 'Endpoint not found',
                'data' => array()
            );

            die(json_encode($resp));
        }
    }

}

?>