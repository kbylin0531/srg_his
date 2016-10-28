<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-19
 * Time: 下午4:07
 */
class DiyTradeCategory extends B2BCategory {

    protected $address = 'http://my.diytrade.com/diyep/xsite/common/rsDirCatSelect?1';
    protected $top_parent_id = 0;

    public function getCategoryLeaves(){
        if(false !== $this->seedCategory($this->top_parent_id)){
            return $this->cate_temp;
        }
        return false;
    }

    private $cate_temp = [];
    private function seedCategory($catID,$level=1){
        if(1 === $level) $this->cate_temp = [];
        if($level < 5){
            $list = $this->getCategory($catID,$level);
            if($list) foreach ($list as $item){
                $id = $item['value'];
                if($this->hasChild($item)){
                    $this->seedCategory($id,$level+1);
                }else{
                    //抵达末梢
                    $this->cate_temp[$id] = [
                        'name'  => $item['pathName'],
                        'id'    => $id,
                        'leaf'  => rtrim($item['name'],' >'),
                    ];
                }
            }
        }
        return true;
    }

    /**
     * @param $node
     * @return bool
     */
    protected function hasChild($node){
        return !empty($node['haveChild']) and ('true' === trim($node['haveChild']));
    }

    protected function requestCategory($catID,$level){
        $content = self::post($this->address,[
            'catID' => $catID,
            'level' => $level,
        ]);
        if(($result = self::xml2Array($content)) and isset($result['result']['data']['catList']['cat'])){
            return $result['result']['data']['catList']['cat'];
        }
        return false;
    }

}