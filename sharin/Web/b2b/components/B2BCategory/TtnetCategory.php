<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-27
 * Time: 上午10:44
 */
class TtnetCategory extends B2BCategory {

    private $addr = 'http://www.ttnet.net/classify/level.json?value=%s';
    protected $top_parent_id = 'main';

    public function getCategoryLeaves(){
        if(false !== $this->seedCategory($this->top_parent_id)){
            return $this->cate_temp;
        }
        return false;
    }

    private $cate_temp = [];
    private function seedCategory($catID,$level=1,array $parent = []){
        if(1 === $level){
            $this->cate_temp = [];
        }
        if($level < 5){
            $list = $this->getCategory($catID);
            if($list) foreach ($list as $item){
                $id = $item['id'];
                $name = $item['name'];
                if($level !== 2 and !empty($parent['name'])){
                     $item['name'] = "{$parent['name']} > {$item['name']}";
                }
                if($this->hasChild($item)){
                    $this->seedCategory($id,$level+1,$item);
                }else{
                    //抵达末梢
                    $this->cate_temp[$id] = [
                        'name'  => $item['name'],
                        'id'    => $id,
                        'leaf'  => $name,
                    ];
                }
            }
        }
        return true;
    }

    private function key($id){
        return "[$id]";
    }
    protected function hasChild($node){
        static $_childMap = [];
        $key = $this->key($node['id']);
        $tempfile = static::class.'.child.map';
        if(!$_childMap){
            $_childMap = Tempper::get($tempfile,[]);
        }
        if(!isset($_childMap[$key])){
            $content = $this->getCategory($node['id']);
            $first = reset($content);
            if(empty($content)){
                $hasChild = false;
            }else{
                $hasChild = (is_array($content) and (count($content) === 1) and $first and ($first['id'] == $node['id']));
            }
            $_childMap[$key] = $hasChild?0:1;
            Tempper::set($tempfile,$_childMap);
        }
        return intval($_childMap[$key]);
    }

    protected function requestCategory($catID,$level){
        $content = self::get(sprintf($this->addr,$catID));
        $result = json_decode($content,true);
        if($result){
            $temp = [];
            foreach ($result[0] as $item){
                $id = $item['value'];
                $temp[$id] = [
                    'id'    => $id,
                    'name'  => $item['text'],
                ];
            }
            return $temp;
        }
        return false;
    }

}