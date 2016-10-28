<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2016/1/27
 * Time: 9:44
 */
namespace Kbylin\System\Utils;
use Kbylin\System\Core\KbylinException;
use Kbylin\System\Core\Router;
use Soya\Core\Exception;
use Soya\Core\URI;
use Soya\Util\Response;
use Soya\Util\SEK;

/**
 * Class Network 网络相关工具类
 * @package Kbylin\System\Utils
 */
class Network {

//----------------------- HTTP方法 ------------------------------------------//

    /**
     * 采集远程文件
     * @access public
     * @param string $remote 远程文件名
     * @param string $local 本地保存文件名
     * @return mixed
     */
    static public function curlDownload($remote,$local) {
        $cp = curl_init($remote);
        $fp = fopen($local,"w");
        curl_setopt($cp, CURLOPT_FILE, $fp);
        curl_setopt($cp, CURLOPT_HEADER, 0);
        curl_exec($cp);
        curl_close($cp);
        fclose($fp);
    }

    /**
     * 使用 fsockopen 通过 HTTP 协议直接访问(采集)远程文件
     * 如果主机或服务器没有开启 CURL 扩展可考虑使用
     * fsockopen 比 CURL 稍慢,但性能稳定
     * @static
     * @access public
     * @param string $url 远程URL
     * @param array $conf 其他配置信息
     *        int   limit 分段读取字符个数
     *        string post  post的内容,字符串或数组,key=value&形式
     *        string cookie 携带cookie访问,该参数是cookie内容
     *        string ip    如果该参数传入,$url将不被使用,ip访问优先
     *        int    timeout 采集超时时间
     *        bool   block 是否阻塞访问,默认为true
     * @return mixed
     */
    static public function fsockopenDownload($url, $conf = array()) {
        $return = '';
        if(!is_array($conf)) return $return;

        $matches = parse_url($url);
        !isset($matches['host']) 	&& $matches['host'] 	= '';
        !isset($matches['path']) 	&& $matches['path'] 	= '';
        !isset($matches['query']) 	&& $matches['query'] 	= '';
        !isset($matches['port']) 	&& $matches['port'] 	= '';
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;

        $conf_arr = array(
            'limit'		=>	0,
            'post'		=>	'',
            'cookie'	=>	'',
            'ip'		=>	'',
            'timeout'	=>	15,
            'block'		=>	TRUE,
        );

//        foreach (array_merge($conf_arr, $conf) as $k=>$v) ${$k} = $v;//动态变量设置(编辑器不认识)
        $conf_arr = array_merge($conf_arr, $conf);
        $post = $conf_arr['post'];
        $limit = $conf_arr['limit'];
        $cookie = $conf_arr['cookie'];
        $ip = $conf_arr['ip'];
        $timeout = $conf_arr['timeout'];
        $block = $conf_arr['block'];

        if($post) {
            if(is_array($post))
            {
                $post = http_build_query($post);
            }
            $out  = "POST $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            //$out .= "Referer: $boardurl\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= 'Content-Length: '.strlen($post)."\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cache-Control: no-cache\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
            $out .= $post;
        } else {
            $out  = "GET $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            //$out .= "Referer: $boardurl\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
        }
        $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
        if(!$fp) {
            return '';
        } else {
            stream_set_blocking($fp, $block);
            stream_set_timeout($fp, $timeout);
            @fwrite($fp, $out);
            $status = stream_get_meta_data($fp);
            if(!$status['timed_out']) {
                while (!feof($fp)) {
                    if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                        break;
                    }
                }

                $stop = false;
                while(!feof($fp) && !$stop) {
                    $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                    $return .= $data;
                    if($limit) {
                        $limit -= strlen($data);
                        $stop = $limit <= 0;
                    }
                }
            }
            @fclose($fp);
            return $return;
        }
    }

    /**
     * 下载文件
     * 可以指定下载显示的文件名，并自动发送相应的Header信息
     * 如果指定了content参数，则下载该参数的内容
     * @static
     * @access public
     * @param string $filename 下载文件名
     * @param string $showname 下载显示的文件名
     * @param string $content  下载的内容
     * @param integer $expire  下载内容浏览器缓存时间
     * @return void
     * @throws Exception
     */
    static public function download ($filename, $showname='',$content='',$expire=180) {
        if(is_file($filename)) {
            $length = filesize($filename);
        }elseif(is_file($filename)) {
            $length = filesize($filename);
        }elseif($content != '') {
            $length = strlen($content);
        }else {
            throw new Exception("file '$filename' not found !");
        }
        if(empty($showname)) {
            $showname = $filename;
        }
        $showname = basename($showname);
        if(!empty($filename)) {
            $finfo 	= 	new \finfo(FILEINFO_MIME);
            $type 	= 	$finfo->file($filename);
        }else{
            $type	=	"application/octet-stream";
        }
        //发送Http Header信息 开始下载
        header("Pragma: public");
        header("Cache-control: max-age=".$expire);
        //header('Cache-Control: no-store, no-cache, must-revalidate');
        header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
        header("Content-Disposition: attachment; filename=".$showname);
        header("Content-Length: ".$length);
        header("Content-type: ".$type);
        header('Content-Encoding: none');
        header("Content-Transfer-Encoding: binary" );
        if($content == '' ) {
            readfile($filename);
        }else {
            echo($content);
        }
        exit();
    }

    /**
     * 显示HTTP Header 信息
     * @param string $header
     * @param bool|true $echo 是否直接输出但不返回
     * @return string
     */
    static function getHeaderInfo($header='',$echo=true) {
        ob_start();
        $headers   	= getallheaders();
        if(!empty($header)) {
            $info 	= $headers[$header];
            echo($header.':'.$info."\n"); ;
        }else {
            foreach($headers as $key=>$val) {
                echo("$key:$val\n");
            }
        }
        $output 	= ob_get_clean();
        if($echo) echo nl2br($output);
        return $output;
    }

    const DATA_TYPE_TEXT = 0;
    const DATA_TYPE_JSON = 1;




//----------------------- CURL方法 ------------------------------------------//
    /**
     * 请求json数据
     * @param string $url
     * @return mixed
     */
    public static function get4Json($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $param 请求参数
     * @param bool $is_file 是否请求上传文件
     * @param bool $return_array 是否返回数组形式的结果
     * @return mixed
     */
    public static function post4Json($url, $param, $is_file = false, $return_array = true) {
        set_time_limit (0);
        if (! $is_file && is_array ( $param )) {
            $param = SEK::toJson( $param );
        }
        if ($is_file) {
            $header [] = "content-type: multipart/form-data; charset=UTF-8";
        } else {
            $header [] = "content-type: application/json; charset=UTF-8";
        }
        $ch = curl_init ();

        //全部数据使用HTTP协议中的 "POST" 操作来发送
        if (class_exists ( '/CURLFile' )) { // php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
            curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, true );
        } else {
            if (defined ( 'CURLOPT_SAFE_UPLOAD' )) {
                curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false );
            }
        }
        curl_setopt ( $ch, CURLOPT_URL, $url ); //需要获取的 URL 地址，也可以在curl_init() 初始化会话的时候
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );//HTTP 请求时，使用自定义的 Method 来代替"GET"或"HEAD"
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE ); //	FALSE 禁止 cURL 验证对等证书
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );//设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header ); //设置 HTTP 头字段的数组。格式： array('Content-type: text/plain', 'Content-length: 100')
        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );//	在HTTP请求中包含一个"User-Agent: "头的字符串。
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $res = curl_exec ( $ch );
        curl_close ( $ch );
//        $flat = curl_errno ( $ch );//get the last error number
//        if ($flat) {
//            curl_error ( $ch );
            //TODO:记录删词发生的错误
//        }
        return $return_array?json_decode ( $res, true ):$res;
    }

    /**
     * 模拟GET请求
     *
     * @param string $url
     * @param string $data_type
     *
     * @return mixed
     *
     * Examples:
     * ```
     * HttpCurl::get('http://api.example.com/?a=123&b=456', 'json');
     * ```
     */
    static public function get($url, $data_type='text') {
        $cl = curl_init();
        if(stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($data_type == 'json') {
                $content = json_decode($content);
            }
            return $content;
        } else {
            return FALSE;
        }
    }

    /**
     * 模拟POST请求
     *
     * @param string $url
     * @param array $fields
     * @param string $data_type
     *
     * @return mixed
     *
     * Examples:
     * ```
     * HttpCurl::post('http://api.example.com/?a=123', array('abc'=>'123', 'efg'=>'567'), 'json');
     * HttpCurl::post('http://api.example.com/', '这是post原始内容', 'json');
     * 文件post上传
     * HttpCurl::post('http://api.example.com/', array('abc'=>'123', 'file1'=>'@/data/1.jpg'), 'json');
     * ```
     */
    static public function post($url, $fields, $data_type='text') {
        $cl = curl_init();
        if(stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($cl, CURLOPT_POST, true);
        curl_setopt($cl, CURLOPT_POSTFIELDS, $fields);
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($data_type == 'json') {
                $content = json_decode($content);
            }
            return $content;
        } else {
            return false;
        }
    }



}