<?php

/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:31
 */
namespace Application\System\Config\Model;
use Soya\Core\Exception;
use Soya\Extend\Model;
use Soya\Util\SEK;

/**
 * Class AdminMenuModel 系统菜单管理
 * @package Application\Admin\Model
 */
class MenuModel extends Model{

    protected $tablename = 'sy_menu';

    /**
     * 添加侧边栏菜单
     * @param array $info
     * @return bool
     */
    public function createSidedMenu(array $info){
        $data = [
            'title' => null,
            'icon'  => null,
            'status'    => null,
            'create_time'   => time(),
        ];
        is_array($info['value']) and $info['value'] = @serialize($info['value']);
        SEK::merge($data,$info);
        SEK::filter($data,[null,false,'']);
        return $this->fields($data)->create();
    }

    /**
     * 修改侧边栏菜单项目
     * @param array $info
     * @return bool
     */
    public function updateMenu(array $info){
        if(!isset($info)) {
            $this->error = '未设置ID项';
        }
        $data = [
            'title' => null,
            'value' => null,
            'icon'  => null,
            'order' => null,
            'status'=> null,
        ];
        //补丁
        if(empty($info['value'])){
            $info['value'] = null;
        }else{
            is_array($info['value']) and $info['value'] = @serialize($info['value']);
        }

        SEK::merge($data,$info);
        SEK::filter($data,[null,false,''],true);
        $id = $info['id'];

        $result = $this->fields($data)->where('id = '.intval($id))->update();;
//        \Soya\dumpout($data,$info,$result);
        return $result;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteSideMenu($id){
        $result = $this->fields('value')->where('id = '.intval($id))->find();
        if(false === $result){
            return false;
        }elseif(empty($result)){
            $this->error = "ID为'{$id}'的配置项不存在";
            return false;
        }else{
            $result = @unserialize($result['value']);
            if(false !== $result){
                if(empty($result)){
                    return $this->fields([
                        'status'    => 0,
                    ])->where('id = '.intval($id))->update();
                }else{
                    $this->error = "无法删除ID为'{$id}'的非空配置项，";
                    return false;
                }
            }
            return false;
        }
    }

    /**
     * 获取全部的菜单项目
     * @param bool $idaskey
     * @param bool $onlyside
     * @return array|bool
     */
    public function selectMenus($idaskey=false,$onlyside=false){
        $where = $onlyside?'status = 1 and id > 1':'status = 1';
        $list = $this->where($where)->select();
        if($list){
            $temp = [];
            foreach ($list as &$item){
//                \Soya\dumpout($item['value']);
                $value = @unserialize($item['value']);
                if(false === $value){
                    //无法反序列化，保持不变
                    \Soya::trace(['无法反序列化菜单项的值',$item['value']]);
                    continue;
                }
                $item['value'] = $value;
                if($idaskey){
                    $id = $item['id'];
                    unset($item['id']);
                    $temp[$id] = $item;
                }
            }
            if($idaskey) return $temp;
        }
        return $list;
    }

    /**
     * 获取顶部菜单设置
     * @return array|false 错误发生时返回false
     */
    public function getHeaderMenu(){
        $menus = $this->selectMenus(true,false);
//        \Soya\dumpout($menus);
        if($menus){
            $header = $menus[1]['value'];
            $this->_sortHeaderMenu($header,$menus);
            return $header;
        }
        return false;
    }

    /**
     * 整理菜单配置
     * @param $header
     * @param $others
     */
    private function _sortHeaderMenu(&$header,&$others){
        if(!isset($header['id'])){//是列表
            foreach ($header as &$item){
                if(isset($item['id'])){
                    $this->_sortHeaderMenu($item,$others);
                }
                if(isset($item['children'])){
                    $this->_sortHeaderMenu($item['children'],$others);
                }
            }
        }else{//是单个菜单项目
            if(isset($header['id'])){
                $id = $header['id'];
                isset($others[$id]) and $header = array_merge($header,$others[$id]);
            }
        }
    }

    /**
     * @return array|bool
     */
    public function getSidebarMenu(){
        $sides = $this->selectMenus(true,true);
        if($sides){
            return $this->_applyMenuItem($sides);
        }
        return false;
    }

    /**
     * 将菜单项配置应用到菜单配置中
     * @param array $menus 菜单项配置
     * @return array
     */
    private function _applyMenuItem(array $menus){
//        $sorted = [];
        if($menus){
            $menuItemModel = new MenuItemModel();
            $items = $menuItemModel->selectMenuItem(true);

            if($items){
                foreach ($menus as &$menu){
//                    \Soya\dump($menu,$items);
                    $value = $menu['value'];
                    if($value){
                        $menu['value'] = is_string($menu['value'])?@unserialize($menu['value']):$menu['value'];
//                        \Soya\dumpout($menu,$items);
                        if(is_array($menu['value']) and $menu['value']){
                            $this->_arrangeMenu($menu['value'], $items);
                        }else{
                            $menu['value'] = [];
                        }
                    }
                }
            }
        }
//        \Soya\dumpout($menus,$sorted);
        return $menus;
    }

    /**
     * apply menuitem to menu config
     * @param array $menuitems
     * @param array $items
     */
    private function _arrangeMenu(array &$menuitems, array &$items){
        foreach ($menuitems as &$item){
//            \Soya\dumpout($item,$items);
            $id = $item['id'];
            if(isset($items[$id])){
                $item = array_merge($item,$items[$id]);
                if(isset($item['children'])){
                    $this->_arrangeMenu($item['children'],$items);
                }
            }
        }
    }

    /**
     * @param array $sideset
     * @param int $id
     * @return bool
     */
    public function setSideMenu($sideset,$id){
        if(is_string($sideset)) $sideset = json_decode($sideset);
        is_array($sideset) or Exception::throwing('Menu setting should be array/string(json)');

        $config = $this->_travelThrough($sideset);
        if(empty($config)){
            $config = '[]';
        }else{
            $config = serialize($config);
        }

        $where = 'parent = '.intval($id);
        //check if exist
        $result = $this->where($where)->count();
        if(false === $result){
            return false;
        }
        if($result){
            return $this->fields([
                'value' => $config,
            ])->where($where)->update();
        }else{
            return $this->fields([
                'value'     =>  $config,
                'parent'    =>  $id,
            ])->create();
        }
    }

    /**
     * @param array $topset
     * @return bool
     */
    public function setTopMenu($topset){
        if(is_string($topset)) $topset = json_decode($topset);
        is_array($topset) or Exception::throwing('Menu setting should be array/string(json)');

        $config = $this->_travelThrough($topset);
        if(empty($config)){
            $config = '[]';
        }else{
            $config = serialize($config);
        }
//        dumpout($config);;
        return $this->fields([
            'value' => $config,
        ])->where('id = 1')->update();
    }

    /**
     * @param array $topset
     * @return array
     */
    private function _travelThrough(array $topset){
        $result = [];
        foreach ($topset as $object){
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