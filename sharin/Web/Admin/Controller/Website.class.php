<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/25/16
 * Time: 9:52 PM
 */

namespace Web\Admin\Controller;

/**
 * Class Website manage the website
 * @package Web\Admin\Controller
 */
class Website extends Admin {

    public function info(){
        $this->show();
    }

    public function menu(){
        $this->display();
    }

}