<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Класс-маршрутизатор запросов
 */
class Router{
    static $METHOD_GET  = 0;
    static $METHOD_POST = 1;
    
    /**
     * Массив обработчиков GET запросов
     * @var type 
     */
    private $routes_get;
    /**
     * Массив обработчиков POST запросов
     * @var type 
     */
    private $routes_post;
    /**
     * Обработчик ошибки маршрутизации GET (отсутствует маршрут)
     * @var type 
     */    
    private $no_routes_get;
    /**
     * Обработчик ошибки маршрутизации POST (отсутствует маршрут)
     * @var type 
     */    
    private $no_routes_post;
    
    private $path;
    
    public function __construct() {
        $this->no_routes_get    = '';
        $this->no_routes_post   = '';
        
        $this->routes_get   = array();
        $this->routes_post  = array();
    }
    
    /**
     * Добавление маршрутизации
     * @param int $method метод запроса (GET/POST)
     * @param string $exp выражение поиска
     * @param string $handler имя метода обработчика
     */
    public function addRoute($method, $exp, $handler){
    
        if ($method == Router::$METHOD_GET) {
            $this->routes_get[$exp] = $handler;
        }
        if ($method == Router::$METHOD_POST) {
            $this->routes_post[$exp] = $handler;
        }
    }
    
    /**
     * Удаление маршрутизации
     * @param type $method метод запроса (GET/POST)
     * @param type $exp выражение поиска
     * @return boolean
     */
    public function delRoute($method, $exp){
        if ($method == Router::$METHOD_GET) {
            if(isset($this->routes_get[$exp])){
                unset($this->routes_get[$exp]);
                return true;
            }
        }
        if ($method == Router::$METHOD_POST) {
            if(isset($this->routes_post[$exp])){
                unset($this->routes_post[$exp]);
                return true;
            }
        }
        return false;
    }
    
    /**
     * Добавление обработчика для отсутствующего маршрута
     * @param type $method
     * @param type $handler
     */
    public function noRoute($method, $handler){
        if ($method == Router::$METHOD_GET) {
            $this->no_routes_get = $handler;
        }
        if ($method == Router::$METHOD_POST) {
            $this->no_routes_post = $handler;
        }
    }
    
    /**
     * Запуск обработчика
     * @param type $method
     * @param type $exp
     */
    public function Run($method, $exp){
        if ($method == Router::$METHOD_GET) {
            Service::log(0, 'GET');
            $this->callHandler($this->routes_get, $exp);
            Service::log(0, 'No route for ' . $exp);
            $this->callDefault($this->no_routes_get);
        }
        
        if ($method == Router::$METHOD_POST) {
            Service::log(0, 'POST');
            $this->callHandler($this->routes_post, $exp);
            Service::log(0, 'No route for ' . $exp);
            $this->callDefault($this->no_routes_post);

        }        
    }
    
    public function addParam($array_path){
        $this->path = $array_path;
    }
    
    public function getParam($index){
        if (isset($this->path[$index])) {
            return $this->path[$index];
        }
        return false;
    }
    
    public function setParam($index, $value){
        $this->path[$index] = $value;
    }

    private function callHandler($routers, $exp){
        foreach ($routers as $key => $row){
            if (preg_match($key, $exp) == 1) {
                if(is_array($row)){
                    $this->callMethod($row);
                }else{
                    $this->callFunction($row);
                }
            }
        }
    }
    
    private function callFunction($function){
        Service::log(0, $function);
        
        if(function_exists($function)){
            call_user_func($function);
            return true;
        }         
    }

    private function callMethod($class_method){
        Service::log(0, $class_method[0] . "->" . $class_method[1]);
        
        $obj = new $class_method[0]();
        $obj->setPath($this->path);
        $obj->setRawData(file_get_contents('php://input'));
        
        if(method_exists($obj, $class_method[1])){
            call_user_func(array($obj, $class_method[1]));
        }     
    }

    
    /**
     * Выполняет вызов обработчика при отсутствии маршрута
     * Сначала производится попытка вызова назначенного обработчика, потом обработчика по-умолчанию
     * @param type $handler_no_route
     */
    private function callDefault($handler_no_route){
        if(function_exists($handler_no_route)){
            call_user_func($handler_no_route);
        }else{
            $this->defaultNoRoute();
        }
    }
    
    
    /**
     * Обработчик ошибочного запроса по-умолчанию
     */
    private function defaultNoRoute(){
        $resp = array(
            'response' => false,
            'message' => 'Router not found',
            'data' => array()
        );
        die(json_encode($resp));
    }
}

