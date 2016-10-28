<?php


class Tempper {


    private static $_cache = [];

    /**
     * @param $identify
     * @param array $replacement
     * @return mixed
     */
    public static function get($identify,$replacement=[]){
        if(!isset(self::$_cache[$identify])){
            $file = PATH_RUNTIME."/temp/{$identify}.tmp";
            if(is_file($file) and is_readable($file)){
                self::$_cache[$identify] = include $file;
            }else{
                self::$_cache[$identify] = $replacement;
            }
        }
        return self::$_cache[$identify];
    }

    /**
     * @param $identify
     * @param array $data
     * @return bool
     */
    public static function set($identify,array $data){
        self::$_cache[$identify] = $data;
    }

    /**
     * 保存运行时配置
     */
    public static function save(){
        foreach (self::$_cache as $identify => $data){
            $file = PATH_RUNTIME."/temp/{$identify}.tmp";
            SEK::touch($file);
            if(!file_put_contents($file,'<?php return '.var_export($data,true).';')){
                return false;
            }
        }
        return true;
    }

}
register_shutdown_function(function (){
    Tempper::save();
});