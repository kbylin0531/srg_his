<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/7/6
 * Time: 18:22
 */

namespace Application\Home\Controller;
use Application\System\Common\Library\HomeController;
use Soya\Extend\Verify;

/**
 * Class UserController 用户控制器
 * @package Application\Home\Controller
 */
class UserController extends HomeController {

    public function login(){
        $this->show();
    }

    public function register(){
        $this->show();
    }

    /**
     * 修改密码
     */
    public function profile(){
        $this->show();
    }

    /**
     * 微信绑定登录
     */
    public function bindLogin(){
        $this->show();
    }
    /**
     * XXX
     */
    public function simpleLogin(){
        $this->show();
    }

    public function verify(){
        Verify::getInstance()->entry();
    }

}