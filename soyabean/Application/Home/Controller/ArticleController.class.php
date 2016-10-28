<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/7/16
 * Time: 4:36 PM
 */

namespace Application\Home\Controller;
use Application\System\Common\Library\HomeController;

class ArticleController extends HomeController {


    /**
     * 文档模型频道页
     */
    public function index(){
        $this->show();
    }

    /**
     * 文档模型列表页
     */
    public function lists(){
        $this->show();
    }

    /**
     * 文档模型详情页
     */
    public function detail(){
        $this->show();
    }

    /**
     * 文档分类检测
     */
    private function category(){
    }


}