<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-21
 * Time: 下午1:30
 */
class EcplazaCategory extends B2BCategory {

    protected $address = 'http://member.ecplaza.net/asp/category/search_api.asp';
    private $cate_temp = [];

    protected $top_parent_id = '';

    public function getCategoryLeaves(){
        if(false !== $this->_getCateRecu($this->top_parent_id)){
            return $this->cate_temp;
        }
        return false;
    }

    private function _getCateRecu($catID,$level=1,$parent=[]){
        if($level === 1) $this->cate_temp = [];
        if($level < $this->max_level){
            $list = $this->getCategory($catID,$level);
            if($list) foreach ($list as $item){
                $id = $item['id'];
                $leaf = $item['name'];
                empty($parent['name']) or $item['name'] = $parent['name'].' > '.$item['name'];
                if($this->hasChild($item)){
                    $this->_getCateRecu($id,$level+1,$item);
                } else{
                    //抵达末梢
                    $this->cate_temp[$id] = [
                        'name'  => $item['name'],
                        'id'    => $id,
                        'leaf'  => $leaf,
                    ];
                }
            }
        }
        return true;
    }

    protected function hasChild($node){
        return !empty($node['last']) and ('N' === $node['last']);
    }
    protected function requestCategory($catID,$level){
        $content = self::post($this->address,[
            'no'    => $catID,
            'cmd'   => 'get',
        ]);
        return self::json2Array($content);
    }

}