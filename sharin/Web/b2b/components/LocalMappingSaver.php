<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-28
 * Time: 下午7:05
 */
class LocalMappingSaver implements B2BCategorySaveInterface{

    public function get($code) {
        $file = PATH_DATA.'/maps/'.$code.'.map.php';
        $map = is_file($file)?(include $file):[];
        return $map;
    }

    public function set($code, array $map) {
        $file = PATH_DATA.'/maps/'.$code.'.map.php';
        SEK::touch($file);
        return file_put_contents($file,'<?php return '.var_export($map,true).';')?true:false;
    }


}