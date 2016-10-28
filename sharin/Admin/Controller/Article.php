<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 9/25/16
 * Time: 1:03 PM
 */

namespace Admin\Controller;



use Sharin\Core\Controller;

class Article extends Controller  {

    public function add(){
        $this->display();
    }
    public function lists(){
        $this->display();
    }
    public function feedback(){
        $this->display();
    }
}