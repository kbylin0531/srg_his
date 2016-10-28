<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/7/5
 * Time: 14:52
 */
namespace Application\Admin\Controller;
use Application\System\Common\Library\AdminController;

/**
 * Class ContentController 内容管理
 * @package Application\Admin\Controller
 */
class ContentController extends AdminController {

    public function index(){
        $this->show();
    }

}