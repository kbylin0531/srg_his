<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:20
 */

namespace Application\System\Common\Library;
use Soya\Extend\Controller;
use Soya\Extend\Response;

/**
 * Class CommonController
 * @package Application\System\Common\Library
 */
abstract class CommonController extends Controller {

    public function PageIconSelection(){
        $this->display();
    }

    /**
     * @param $path
     */
    protected function go($path){
        $this->redirect($path);
    }

}