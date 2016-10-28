<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/7/16
 * Time: 5:35 PM
 */

namespace Application\Home\Controller;


use Application\System\Common\Library\HomeController;

class WeixinMessageController extends HomeController{
    public function collect() {
        $this->show ();
    }
    public function lists() {
        $this->show ();
    }
    function person() {
        $this->show ();
    }

    public function deal() {
        $this->show ( 'collect' );
    }
    // 使用客户接口回复用户信息  目前只支持发文本
    function reply() {
    }
    //新增临时图片素材
    function get_image_media_id($cover_id) {
    }

    //设置为文本素材
    function set_meterial(){
    }
}