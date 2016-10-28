<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-21
 * Time: 下午4:38
 */
abstract class B2BPlatform {

    public static function get($url,$cookie='', $header=false, array $opts=[]){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, $header); //将头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        if($cookie){
            if(strpos($cookie,'/') === 0){
                SEK::touch($cookie);
            }
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        }
        if($opts) foreach ($opts as $k=> $v){ curl_setopt($ch,$k,$v); }

        $content = curl_exec($ch);
        curl_close($ch);
        return false === $content ? '': (string)$content;
    }


    /**
     * Request
     * @param $method
     * @param $url
     * @param $fields
     * @param string $cookie cookie文件存放路径，false值时表示不启用（默认）
     * @param bool $withHead 返回值是否带header
     * @return string 返回请求结果
     */
    public static function request($method,$url,$fields,$cookie=null,$withHead=true){
        $ch = curl_init($url);
        if(strpos($url,'https://') === 0){
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        switch ($method){
            case 'post':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'put':
                curl_setopt($ch, CURLOPT_PUT, true);
                break;
        }
        //curl_setopt($ch,CURLOPT_COOKIE,$inputcookie);
        curl_setopt($ch, CURLOPT_HEADER, $withHead); //将头文件的信息作为数据流输出
        if($fields) curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        if($cookie){
            //makes curl to use the given file as source for the cookies to send to the server.
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            //连接结束后，比如，调用 curl_close 后，保存 cookie 信息的文件。
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        }

        $content = curl_exec($ch);
        curl_close($ch);
        return false === $content ? '': (string)$content;
    }


    /**
     * @param string $url
     * @param int $timeout
     * @return false|string
     */
    public static function download($url,$timeout=60) {
        $basename = pathinfo($url,PATHINFO_BASENAME);
        $dir = PATH_DATA.'download/';
        $path = $dir.$basename;
        SEK::touch($path);
        $context = stream_context_create([
            'http'=>[
                'method'    =>  'GET',
                'header'    =>  "",
                'timeout'   =>  $timeout
            ],
        ]);
        if(@copy($url, $path, $context)) {
            return $path;
        } else {
            return false;
        }
    }

    /**
     * 模拟post请求
     * @param $url
     * @param $fields
     * @param string $cookie
     * @param bool $header
     * @param array $opts
     * @param array $rq_header
     * @return string
     */
    protected static function post($url, $fields, $cookie='', $header=false, array $opts=[],array $rq_header=[]){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, $header); //将头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $rq_header and curl_setopt($ch, CURLOPT_HTTPHEADER, $rq_header);
        if($cookie){
            if(strpos($cookie,'/') === 0){
                SEK::touch($cookie);
            }
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        }
        if($opts) foreach ($opts as $k=> $v){ curl_setopt($ch,$k,$v); }

        $content = curl_exec($ch);
        curl_close($ch);
        return false === $content ? '': (string)$content;
    }



}