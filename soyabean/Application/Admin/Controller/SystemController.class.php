<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/7/2
 * Time: 15:16
 */

namespace Application\Admin\Controller;
use Application\System\Common\Library\AdminController;
use Application\System\Config\Model\MenuItemModel;
use Application\System\Config\Model\MenuModel;
use Soya\Extend\Response;

/**
 * Class SystemController 关闭系统设置
 * @package Application\Admin\Controller
 */
class SystemController extends AdminController {

    public function menu(){
        $this->show();
    }

    /**
     * get the menu-config list
     */
    public function getMenus(){
        $menuModel = new MenuModel();
        $config  = [
            'header'    => $menuModel->getHeaderMenu(),
            'sidebar'   => $menuModel->getSidebarMenu(),
        ];


//        \Soya\dumpout(json_encode($menuModel->getHeaderMenu()));
        false === $config and Response::failed('Failed to get menu config!'.$menuModel->error());
        Response::ajaxBack($config);//直接返回文本
    }

    /**
     * @param $title
     * @param $icon
     * @param $value
     */
    public function createMenu($title,$icon,$value){
        $model = new MenuModel();
        $result = $model->createSidedMenu([
            'title' => $title,
            'value' => $value,
            'icon'  => $icon,
        ]);
        if(false === $result){
            Response::failed('菜单创建出错：'.$model->error());
        }else{
            if($result){
                $lastInsert = $model->lastInsertId();
                Response::ajaxBack([
                    'id'    => $lastInsert,
                    'msg'   => '菜单创建成功！',
                ]);

            }else{
                Response::failed('菜单创建失败!');
            }
        }
    }

    /**
     * 穿件菜单项
     * @param $title
     * @param $icon
     * @param $value
     * @throws \Soya\Core\Exception
     */
    public function createMenuItem($title,$icon,$value){
        $model = new MenuItemModel();
        $result = $model->createMenuItem([
            'title' => $title,
            'value' => $value,
            'icon'  => $icon,
        ]);

        if(false === $result){
            Response::failed('菜单项创建出错：'.$model->error());
        }else{
            if($result){
                $lastInsert = $model->lastInsertId();
                Response::ajaxBack([
                    'id'    => $lastInsert,
                    'msg'   => '菜单项创建成功！',
                ]);

            }else{
                Response::failed('菜单项创建失败!');
            }
        }
    }

    /**
     * @param $id
     */
    public function deleteMenu($id){
        $model = new MenuModel();
        $result = $model->deleteSideMenu($id);
        if(false === $result){
            Response::failed('删除失败:'.$model->error());
        }else{
            Response::success('删除成功!');
        }
    }

    /**
     * @param $id
     */
    public function deleteMenuItem($id){
        $model = new MenuItemModel();
        $result = $model->deleteMenuItem($id);
        if(false === $result){
            Response::failed('删除失败:'.$model->error());
        }else{
            Response::success('删除成功!');
        }
    }

    /**
     * @param $id
     * @param $title
     * @param $icon
     */
    public function updateMenu($id,$title,$icon){
        $model = new MenuModel();
        $result = $model->updateMenu([
            'id'    => $id,
            'title' => $title,
            'icon'  => $icon,
        ]);
        if(false === $result){
            Response::failed('修改失败:'.$model->error());
        }else{
            Response::success('修改成功!');
        }
    }

    /**
     * @param $id
     * @param $title
     * @param $icon
     * @param $value
     */
    public function updateMenuItem($id,$title,$icon,$value){
        $model = new MenuItemModel();
        $result = $model->updateMenuItem([
            'id'    => $id,
            'title' => $title,
            'icon'  => $icon,
            'value' => $value,
        ]);
        if(false === $result){
            Response::failed('修改失败:'.$model->error());
        }else{
            Response::success('修改成功!');
        }
    }

    /**
     * @param string $header
     */
    public function saveHeaderMenuConfig($header){
        $header = json_decode($header);
        is_array($header) or Response::failed('无法解析前台传递的序列化的信息!');
//        \Soya\dumpout($this->_travelThrough($header));

        $model = new MenuModel();
        $result = $model->updateMenu([
            'id'    => 1,//id of header menu config
            'value' => $this->_travelThrough($header),
        ]);
        if(false === $result){
            Response::failed('保存失败:'.$model->error());
        }elseif(0 === $result){/* 数据库更新同样的值时不会算作更新成功 */
            Response::warning('保存失败，可能的原因是已经保存过了！');
        }else{
            Response::success('保存成功!');
        }
    }

    /**
     * @param $id
     * @param $sidebar
     */
    public function saveSidebarMenuConfig($id,$sidebar){
        $sidebar = json_decode($sidebar);
        if(!is_array($sidebar)) Response::failed('无法解析前台传递的序列化的信息!');

        $sidebar = $this->_travelThrough($sidebar);
        if(empty($sidebar)){
            $sidebar = 'a:0:{}';
        }else{
            $sidebar = serialize($sidebar);
        }

//        \Soya\dumpout($id,$sidebar);
        $model = new MenuModel();
        $result = $model->updateMenu([
            'id'    => $id,
            'value' => $sidebar,
        ]);
        if(false === $result){
            Response::failed('保存失败:'.$model->error());
        }elseif(0 === $result){
            Response::warning('保存失败，可能的原因是已经保存过了！');
        }else{
            Response::success('保存成功!');
        }
    }

    /**
     * @param array $header
     * @return array
     */
    private function _travelThrough(array $header){
        $result = [];
        foreach ($header as $object){
            $item = [];
            $item['id'] = $object->id;
            if(isset($object->children)){
                $item['children'] = $this->_travelThrough($object->children);
            }
            $result[] = $item;
        }
        return $result;
    }
}