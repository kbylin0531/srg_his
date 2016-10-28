<?php

class SEK {

    /**
     * return the request in ajax way
     * and call this method will exit the script
     * @access protected
     * @param mixed $data general type of data
     * @param bool $json 是否以json格式返回，false时使用纯文本格式
     * @return void
     */
    public static function ajaxBack($data, $json = true){
        ob_get_level() > 0 and ob_end_clean();
        if($json){
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode($data));
        }else{
            header('Content-Type:text/plain; charset=utf-8');
            exit($data);
        }
    }

    /**
     * 检查文件如果文件不存在则创建一个空的文件，并且解决上层目录的问题
     * @param string $file 文件路径
     * @return bool
     * @throws \Exception 无权限时抛出异常
     */
    public static function touch($file){
        $dir = dirname($file);
        if(!is_dir($dir)){
            if(!mkdir($dir,0777,true)){
                throw new \Exception("创建目录失败'$dir'!");
            }
        }
        if(!is_writable($dir)){
            if(!chmod($dir,0777)){
                throw new \Exception("修改目录权限失败'$dir'!");
            }
        }
        return touch($file);
    }
}