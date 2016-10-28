<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-21
 * Time: 下午4:17
 */
abstract class B2BProduct extends B2BPlatform{

    /**
     * @var string 登录cookie文件地址
     */
    protected $cookie_login = '';

    /**
     * @var string 产品提交页面
     */
    protected $submitAddress   = '';
    /**
     * @var string 产品提交方法
     */
    protected $submitMethod    = 'post';
    /**
     * @var array 产品属性列表
     */
    protected $attrs = [];

    /**
     * B2BProduct constructor.
     */
    public function __construct() {
        $this->cookie_login = PATH_COOKIE.'Login/'.get_class($this).'.cookie';
    }

    public function __get($name){
        return isset($this->attrs[$name]) ? $this->attrs[$name] : '';
    }
    public function __set($name,$val){
        $this->attrs[$name] = $val;
    }
    /**
     * 产品上传
     * @return bool 是否提交成功
     * @throws Exception
     */
    public function submit(){
        $result = self::post($this->submitAddress,http_build_query($this->attrs),$this->cookie_login,true);
        return $this->isSucmitSuccess($result);
    }

    /**
     * @param string $response 请求的响应内容
     * @return bool
     */
    abstract public function isSucmitSuccess($response);

    /**
     * 获取上一次提交的产品
     * @return mixed
     */
    abstract public function getLastSubmit();

    /**
     * 添加关键词
     * @param string $keywork
     * @return bool
     */
    abstract public function addKeywork($keywork);

    /**
     * @var array 产品图片
     */
    protected $images = [];

    /**
     * 设置产品图片
     * @param string|array $images 图片路径
     * @return $this
     */
    public function setImage($images){
        if(strpos(trim($images),'http') === 0){
            //如果图片存放在网络上，先删除
            $images = self::download($images);
        }
        $this->images = $images;
    }

}