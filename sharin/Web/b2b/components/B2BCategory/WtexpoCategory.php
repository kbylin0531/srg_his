<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-23
 * Time: 下午4:24
 */
class WtexpoCategory extends B2BCategory {

    protected $address = 'http://wtexpo.com/mywtexpo/category.php?object=opener.document.joinnow.subcat';
    private $prev_cookie = '';

    public function __construct() {
        $this->prev_cookie = PATH_COOKIE.'/'.static::class;
    }

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
                if(!empty($item['hasChild'])) {
                    $this->_getCateRecu($id,$level+1,$item);
                } else {
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
        $address = $catID ? $this->address.'&maincatid='.$catID : $this->address;
        $cookie = $this->prev_cookie;

        $content = Session::waitOn(3,function() use ($address,$cookie){
           return  self::get($address,$cookie);
        });

        $result = [];
        if(preg_match_all('/<a\s*class=[\'\"]dynamic_black_link.*?maincatid=(\d+).*?>([^<]*)<\/a>/',$content,$matches)){
            $len = count($matches[0]);
            for($i = 0 ; $i < $len ; $i ++){
                $id = $matches[1][$i];
                $name = $matches[2][$i];
                $result[$id] = [
                    'id'    => $id,
                    'name'  => $name,
                    'leaf'  => $name,
                    'hasChild'  => true,
                ];
            }
        }
        if(preg_match_all('/<a\s*class=[\'\"]dynamic_black_link.*?javascript:opener.*?,\'(\d+)\',.*?>([^<]*)<\/a>/',$content,$matches)){
            $len = count($matches[0]);
            for($i = 0 ; $i < $len ; $i ++){
                $id = $matches[1][$i];
                $name = $matches[2][$i];
                $result[$id] = [
                    'id'    => $id,
                    'name'  => $name,
                    'leaf'  => $name,
                    'hasChild'  => false,
                ];
            }
        }
        return $result;
    }


}