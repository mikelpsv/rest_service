<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Handler
 *
 * @author lapshov
 */
class Handler {
    private $path;
    private $rawData;
    
    public function __construct() {
       
    }

        /**
     * Устанавливает массив сегментов пути для дальнейшего использования в обработчиках
     * @param type $path
     */
    final public function setPath($path){
        $this->path = $path;
    }
    
    /**
     * Возвращает элемент запроса по индексу
     * @param type $index
     * @return type
     */
    final public function getParam($index){
        return $this->path[$index];
    }

    /**
     * Устанавливает данные POST для дальнейшего использования в обработчиках
     * @param type $rawData
     */
    final public function setRawData($rawData) {
        $this->rawData = $rawData;
    }
    
    /**
     * Возвращает объект данных POST
     * @return type
     */
    final public function getRawData(){
        return $this->rawData;
    }

    /**
     * По запросу из базы данных формирует ответ JSON
     * @param type $sql
     * @return type
     */
    static function getDataJSON($sql) {
        Service::log(0, $sql);
        $query = mysql_query($sql);
        $result = array();

        while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
            $result[] = $row;
        }

        mysql_free_result($query);

        $response = (count($result) > 0);
        $message = ((count($result) > 0) ? "" : "No data");

        $resp = array(
            'response'  => $response,
            'message'   => $message,
            'data'      => $result
        );

        return json_encode($resp);
    }
    
    static function jdecoder($json_str){
        $cyr_chars = array (
            '\u0430' => 'а', '\u0410' => 'А',
            '\u0431' => 'б', '\u0411' => 'Б',
            '\u0432' => 'в', '\u0412' => 'В',
            '\u0433' => 'г', '\u0413' => 'Г',
            '\u0434' => 'д', '\u0414' => 'Д',
            '\u0435' => 'е', '\u0415' => 'Е',
            '\u0451' => 'ё', '\u0401' => 'Ё',
            '\u0436' => 'ж', '\u0416' => 'Ж',
            '\u0437' => 'з', '\u0417' => 'З',
            '\u0438' => 'и', '\u0418' => 'И',
            '\u0439' => 'й', '\u0419' => 'Й',
            '\u043a' => 'к', '\u041a' => 'К',
            '\u043b' => 'л', '\u041b' => 'Л',
            '\u043c' => 'м', '\u041c' => 'М',
            '\u043d' => 'н', '\u041d' => 'Н',
            '\u043e' => 'о', '\u041e' => 'О',
            '\u043f' => 'п', '\u041f' => 'П',
            '\u0440' => 'р', '\u0420' => 'Р',
            '\u0441' => 'с', '\u0421' => 'С',
            '\u0442' => 'т', '\u0422' => 'Т',
            '\u0443' => 'у', '\u0423' => 'У',
            '\u0444' => 'ф', '\u0424' => 'Ф',
            '\u0445' => 'х', '\u0425' => 'Х',
            '\u0446' => 'ц', '\u0426' => 'Ц',
            '\u0447' => 'ч', '\u0427' => 'Ч',
            '\u0448' => 'ш', '\u0428' => 'Ш',
            '\u0449' => 'щ', '\u0429' => 'Щ',
            '\u044a' => 'ъ', '\u042a' => 'Ъ',
            '\u044b' => 'ы', '\u042b' => 'Ы',
            '\u044c' => 'ь', '\u042c' => 'Ь',
            '\u044d' => 'э', '\u042d' => 'Э',
            '\u044e' => 'ю', '\u042e' => 'Ю',
            '\u044f' => 'я', '\u042f' => 'Я',
            '\u2019' => '`', '\u00ab' => '"',
            '\u00bb' => '"', '\u201c' => '"',
            '\u201d' => '"', '\u2116' => ' ',
            '\r' => '',
            '\n' => '<br />',
            '\t' => ''
        );

	foreach ($cyr_chars as $key => $value) {
            $json_str = str_replace($key, $value, $json_str);
	}
	
        return $json_str;

    }    

}
