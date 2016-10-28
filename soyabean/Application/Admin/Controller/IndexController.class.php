<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/30
 * Time: 20:59
 */

namespace Application\Admin\Controller;
use Application\System\Common\Library\AdminController;

/**
 * Class IndexController
 * @package Application\Admin\Controller
 */
class IndexController extends AdminController{

    public function index(){
        $this->show();
    }


}