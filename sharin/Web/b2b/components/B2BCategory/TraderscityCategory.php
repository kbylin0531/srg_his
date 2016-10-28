<?php

/**
 * Class Traderscity
 *
 * 分类ID即名称
 *
 */
class TraderscityCategory extends B2BCategory {


    public function getCategoryLeaves(){
        //login



        if(false !== $this->seedCategory()){
            return $this->cate_temp;
        }
        return false;
    }



    private $cate_temp = [];
    private function seedCategory($catID=0,$level=1,array $parent = []){
        if(1 === $level) $this->cate_temp = [];
        if($level < 5){
            $list = $this->getCategory($catID,$level);
            if($list) foreach ($list as $item){
                $id = $item['value'];
                $name = $item['name'];
                empty($parent['name']) or $item['name'] = "{$parent['name']} > {$item['name']}";
                if($this->hasChild($item,$level)){
                    $this->seedCategory($id,$level+1);
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

    protected function hasChild($node,$level){
        static $_childMap = [];
        $key = $node['id'];
        $tempfile = static::class.'.child.map';
        if(!$_childMap){
            $_childMap = Tempper::get($tempfile,[]);
        }
        if(!isset($_childMap[$key])){
            $content = $this->getCategory($node['id'],$level);
            $_childMap[$key] = empty($content)?0:1;
            Tempper::set($tempfile,$_childMap);
        }
        return intval($_childMap[$key]);
    }

    protected function requestCategory($catID,$level){
        $result = [];
        if(0 === $catID){
            $content = self::get('http://www.tradeprince.com/tradelead_navigation.action');
            if(false === preg_match_all('/<option\svalue=\"(\d+)\">.*?<\/option>/',$content,$matches)){
                return false;
            }
            dumpout($content,$matches);
            $idi = 1;
            $nmi = 2;
        }else{
            $content = self::post('http://www.tradeprince.com/tradelead_getSubCatalogs.action',[
                'currentCatalogDataType' => $level>1?2:1,
                'currentCatalogLevel' => $level,
                'currentSearchId' => $catID,
                't' => '0.9848467706824562',
            ]);
            if(false === preg_match_all('/new\sOption\(\"(.*?)\",\"(\d+)\"/',$content,$matches)){
                return false;
            }
            $idi = 2;
            $nmi = 1;
        }
        $len = count($matches[0]);
        for($i = 0 ; $i < $len; $i++){
            $id = $matches[$idi][$i];
            $nm = $matches[$nmi][$i];
            $result[$id] = [
                'id'    => $id,
                'name'  => $nm,
            ];
        }
        return false;
    }

}