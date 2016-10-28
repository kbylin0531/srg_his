<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-26
 * Time: 下午3:32
 */
class WeikuCategory extends B2BCategory {

    protected $addr = 'http://www.weiku.com/buyoffers/post_buying_lead.aspx';
    protected $qaddr = 'http://www.weiku.com/ajax/productAttribute_ajax.ashx?action=%s&classid=%d';

    protected $top_parent_id = 0;

    public function getCategoryLeaves()    {
        if(false !== $this->seedCategory($this->top_parent_id)){
            return $this->cate_temp;
        }
        return false;
    }

    private $cate_temp = [];

    private function getFirstLevel(){
        $this->cate_temp = [];
        $content = self::get($this->addr);
        if(preg_match('/<select\ssize=\"4\"\sname=\"FirstSelect\"(.|\s|\r|\t)*?<\/select>/',$content,$matches)){
            $content = $matches[0];
            if(preg_match_all('/<option\svalue=\"(\d+)\">(.*?)<\/option>/',$content,$matches)){
                $len = count($matches[0]);
                for ($i = 0 ; $i < $len; $i ++) {
                    $id = $matches[1][$i];
                    $this->cate_temp[$id] = [
                        'id'    => $id,
                        'name'  => $matches[2][$i],
                        'leaf'  => $matches[2][$i],
                    ];
                }
            }
            return true;
        }else{
            return false;
        }
    }

    private function seedCategory($catID=0,$level=0,array $parent=[]){
        if(0 === $level) {
            if($this->getFirstLevel()){
                foreach ($this->cate_temp as $item){
                    $this->seedCategory($item['id'],1);
                }
                return true;
            }else{
                return false;
            }
        }
        if($level < 5){
            $list = $this->getCategory($catID,$level);
            if($list) foreach ($list as $id=>$item){
                $name = $item['name'];
                empty($parent['name']) or $item['name'] = "{$parent['name']} > {$item['name']}";
                if($this->hasChild($item,$level)){
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

    private $childMap = null;

    /**
     * @param $node
     * @param $level
     * @return mixed
     */
    protected function hasChild($node,$level){
        $key = $this->key($node['id'],$level);
        $tempfile = static::class.'.child.map';
        if(!isset($this->childMap)){
            $this->childMap = Tempper::get($tempfile,[]);
        }
        if(!isset($this->childMap[$key])){
            $content = $this->requestCategory($node['id'],$level,true);
            $this->childMap[$key] = strlen(trim($content)) > 0;
            Tempper::set($tempfile,$this->childMap);
        }
        return $this->childMap[$key];
    }

    private function key($id,$level){
        return "[$id]-[$level]";
    }
    private function level($level){
        switch (intval($level)) {
            case 1:$level = 'first';break;
            case 2:$level = 'second';break;
            case 3:$level = 'three';break;
            default:
                throw new Exception("Level $level ?");
        }
        return $level;
    }

    private $cache = [];

    protected function requestCategory($catID,$level,$justcontent=false) {
        $key = $this->key($catID,$level);
        if(!isset($this->cache[$key])){
            $this->cache[$key] = self::get(sprintf($this->qaddr,$this->level($level),$catID));
        }
        $content = $this->cache[$key];
        if(!$justcontent){
            $content = htmlspecialchars_decode(rtrim($content,'#'));
            $content = explode('##',$content);
            $result = [];
            foreach ($content as $item) {
                list($id,$name) = explode(':',$item);
                $result[$id] = [
                    'id'    => $id,
                    'name'  => $name,
                ];
            }
            return $result;
        } else {
            return $content;
        }
    }

}