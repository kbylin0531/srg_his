<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/7/1
 * Time: 12:40
 */

namespace Application\Home\Controller;
use Application\Home\Model\AddonModel;
use Application\System\Common\Library\HomeController;
use Soya\Extend\View;

/**
 * Class IndexController
 * @package Application\Home\Controller
 */
class IndexController extends HomeController{

    public function main(){
        //$data 插件列表
        $data = (new AddonModel())->getAddonList();
        //获取用户人数和组
        foreach ($data as $k=>&$vo){
            $app_icon = __PUBLIC__ . '/addon/' . $vo ['name'] . '/icon.png';
            if (!file_exists ( $app_icon )) {
                $vo ['app_icon'] = __PUBLIC__ . '/assets/app/home/images/app_no_pic.png';
            }
            $vo['addons_url'] = '';
        }

        $this->assign([
           'count'  => [
               'total'  => 1,
               'today'  => 1,
               'yestoday'   => 1,
           ],
            'data'  => $data,
        ]);

        $this->show();
    }

}