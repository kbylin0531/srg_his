<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-20
 * Time: 上午9:23
 */
class BossgooCategory extends B2BCategory{
    protected function hasChild($node){}

    protected function requestCategory($catID, $level)
    {
    }

    public function getCategoryLeaves(){
        if(!($data = self::cacheCategory(99,99))){
            $list = Yii::app()->db->createCommand('select t.cat_id id,t.cat_name name,all_child_cat cat from {{categories}} t  order by t.cat_name asc')->queryAll();
            $data = [];
            foreach ($list as $k => $cate) {
                $cate['leaf'] = $cate['name'];
                $data[$cate['id']] = $cate;
            }

            $this->_stripleaf($data);
            if(!self::cacheCategory(99,99,$data)) return false;
        }
        return $data;
    }

    /**
     * 上级分类并入下级分类中
     * @param array $leafes
     * @param int $count
     */
    private function _stripleaf(array &$leafes,$count = 0){
        if($count < 2){//整理3次，相对于行业目录最大分类
            foreach ($leafes as $mid=>$cate) {
                if(!empty($cate['cat'])){
                    $subcateids = explode(',',$cate['cat']);
                    foreach ($subcateids as $id){
                        if(isset($leafes[$id])){
                            $leafes[$id]['name'] = "{$cate['name']} > {$leafes[$id]['name']}";
                        }
                    }
                    unset($leafes[$mid]);
                }
                unset($leafes[$mid]['cat']);
            }
            $this->_stripleaf($leafes,++$count);
        }
    }

}