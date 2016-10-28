<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:32
 */

namespace Application\System\Config\Model;
use Soya\Extend\Model;
use Soya\Util\SEK;

/**
 * Class MenuItemModel 菜单项管理
 * @package Application\Admin\System\Model
 */
class MenuItemModel extends Model {

    protected $tablename = 'sy_menu_item';

    /**
     * update the menu item by id
     * @param array $info
     * @return bool
     */
    public function updateMenuItem(array $info){
        if(!isset($info['id'])) {
            $this->error = '缺少ID';
            return false;
        }
        $id = $info['id'];
        unset($info['id']);
        return $this->fields($info)->where('id = '.intval($id))->update();
    }

    /**
     * @param array $info
     * @return bool|int
     */
    public function createMenuItem(array $info){
        $data = [
            'title' => null,
            'value' => null,
            'icon'  => null,
            'status'=> null,
        ];
        SEK::merge($data,$info);
        SEK::filter($data,null,true);
        if(empty($data)){
            $this->error = '没有要插入的数据！';
            return false;
        }
        return $this->fields($data)->create();
    }

    /**
     * 删除配置项
     * @param $id
     * @return bool
     */
    public function deleteMenuItem($id){
        return $this->fields([
            'status'    => 0,
        ])->where('id = '.intval($id))->update();
    }

    /**
     * 获取菜单项列表
     * @param bool $idaskey 是否将id作为键
     * @return array|bool
     */
    public function selectMenuItem($idaskey=false){
        $items = $this->where('status = 1')->select();
        if(false === $items){return false;}
        if($idaskey and $items){
            $temp = [];
            foreach ($items as &$item){
                $id = $item['id'];
                unset($item['id']);
                $temp[$id] = $item;
            }
            $items =$temp;
        }
        return $items;
    }

    public function hasMenuItem($id){
        return $this->where('id = '.intval($id))->count();
    }


}