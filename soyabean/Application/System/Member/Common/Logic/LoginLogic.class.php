<?php

/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/30
 * Time: 21:02
 */
namespace Application\System\Member\Common\Logic;
use Application\System\Member\Model\MemberModel;
use Soya\Core\Exception;
use Soya\Extend\Cookie;
use Soya\Extend\Logic;
use Soya\Extend\Session;
use Soya\Util\Encrypt\Base64;

class LoginLogic extends Logic {

    private static $key = '_userinfo_';

    /**
     * @return LoginLogic
     * @param null $identify
     * @return Logic
     */
    public static function getInstance($identify = NULL){
        return parent::getInstance();
    }

    /**
     * check the current user login status
     * @return bool
     */
    public function isLogin(){
        $session = Session::getInstance();
        $status = $session->get(self::$key);//return null if not set
        if(!$status){
            $cookie = Cookie::getInstance()->get(self::$key);
            if($cookie){
                $usrinfo = unserialize(Base64::decrypt($cookie, self::$key));
                $session->set(self::$key, $usrinfo);
                return true;
            }
        }
        return $status?true:false;
    }


    /**
     * @param string $username
     * @param null $password
     * @param bool $remember
     * @return bool|string 返回的string代表着错误的信息，返回true表示登陆成功
     */
    public function login($username,$password,$remember){
        $model = new MemberModel();
        $usrinfo = $model->checkLogin($username,$password);
//        \Soya\dumpout($usrinfo);
        if(false === $usrinfo){
//            \Soya\dumpout($model->error());
            return $model->error();
        }

        //set session,browser must enable cookie
        if($remember){
            $sinfo = serialize($usrinfo);
            $cookie = Base64::encrypt($sinfo, self::$key);
            Cookie::getInstance()->set(self::$key, $cookie,7*24*3600);//一周的时间
        }
        Session::getInstance()->set(self::$key, $usrinfo);
        return true;
    }

    /**
     * 获取登录信息
     * @param string $name 信息名称
     * @return array|false|null|mixed 发生了错误时返回FALSE
     */
    public function getLoginInfo($name=null){
        static $info = null;
        if(null === $info){
            $session = Session::getInstance();
            $info = $session->get(self::$key);
            if(null === $session){
                //用户未登录,按照情况执行抛出异常操作或者返回null
                return false;//'用户未登录，无法执行该操作！'
            }
        }

        if($name){
            return isset($info[$name])?$info[$name]:null;
        }
        return $info;
    }

    /**
     * 注销登陆
     * @return void
     */
    public function logout(){
        Session::getInstance()->clear(self::$key);
        Cookie::getInstance()->clear(self::$key);
    }

}