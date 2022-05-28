<?php namespace SiTEL;

/**
 * Takes an array of arrays and recursivly translates to stdClass
 * 
 * @param array<mixed> $array
 * @return \stdClass
 */
function array_to_object(array $array):\stdClass {
    $object = new \stdClass();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $value = array_to_object($value);
        }
        $object->$key = $value;
    }
    return $object;
}

/**
 * 
 * @throws \SiTEL\Exception\ClassNotFound
 * @return callable
 */
function getAutoloader(){
    /**
     * @param string $class
     * @throws \SiTEL\Exception\ClassNotFound
     */
    return function (string $class):void {
        $file_path = str_replace(['_','\\'],'/',$class) . '.php';
        if(!include_once $file_path){
            throw new \SiTEL\Exception\ClassNotFound("{$file_path} {$class}");
        }
    };
}

/**
 * 
 * @param array<string, mixed> $array
 * @param string $key
 * @throws \Exception
 * @return array<string, mixed>|array[]
 */
function ExtractField(array $array, $key) {
    $return = array();
    foreach ($array as $a) {
        if (!isset($a[$key])) {
            throw new \Exception("Key $key not found in array");
        }
        $return[] = $a[$key];
    }
    return $return;
}

/**
 * @param string $url
 * @return string
 */
function return_url_encode(string $url):string{
    $encoded_url = str_replace(['\\','?','=','&',':','/'],['iZZ11ZZi','iZZ22ZZi','iZZ33ZZi','iZZ44ZZi','iZZ55ZZi','iZZ66ZZi'], $url);
   return $encoded_url;
}

/**
 * 
 * @param string $encoded_url
 * @return string
 */
function return_url_decode(string $encoded_url):string{
    $url = str_replace(['iZZ11ZZi','iZZ22ZZi','iZZ33ZZi','iZZ44ZZi','iZZ55ZZi','iZZ66ZZi'],['\\','?','=','&',':','/'],$encoded_url);
    return $url;
}
