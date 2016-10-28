<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/7/16
 * Time: 5:15 PM
 */

namespace Application\Home\Controller;


use Application\System\Common\Library\HomeController;

class PublicsController extends HomeController {

    function help() {
        $this->display ( 'Index/help' );
    }
    /**
     * 显示指定模型列表数据
     */
    public function lists() {
        $this->show();
    }
    public function add() {
        $this->show();
    }
    function stepFirst() {
        $this->show();
    }
    function stepSecond() {
        $this->show();
    }
    function stepThird() {
        $this->show();
    }
    // 自动检测
    function checkRes() {
        $this->show();
    }


    protected function checkAttr($Model, $model_id) {
    }
    function changPublic() {
    }

    // 等待审核页面
    function waitAudit() {
    }
    public function del($model = null, $ids = null) {
    }
    public function edit($model = null, $id = 0) {
    }
}