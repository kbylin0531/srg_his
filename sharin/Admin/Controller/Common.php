<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 9/25/16
 * Time: 11:23 AM
 */

namespace Admin\Controller;
use Sharin\Core\Controller;

/**
 * Class Common 通用控制器
 * @package Admin\Controller
 */
class Common extends Controller {

    public function login(){
        $this->display();
    }
    public function index(){
        $this->display();
    }

    public function welcome(){
        $this->display();
    }


    public function notFound(){
        $this->display('404');
    }

    public function blank(){
        $this->display('_blank');
    }

}