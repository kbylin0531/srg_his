<?php

/**
 * Class Ec21Category
 */
class Ec21Category extends B2BCategory {

    protected $address = 'http://www.ec21.com/global/category/categoryMajorSelectGetData.jsp?actionName=category&step=%d&categoryCd=%d';

    /**
     * 获取末梢列表
     * @return array|bool
     */
    public function getCategoryLeaves(){
        if(false !== $this->_getCateRecu()){
            return $this->cate_temp;
        }
        return false;
    }

    private $cate_temp = [];
    private function _getCateRecu($catID=0,$level=1,array $parent=[]){
        if($level === 1){
            $this->cate_temp = [];
        }
        if($level < 5){
            $list = $this->getCategory($catID,$level);
            if($list) foreach ($list as $item){
                $name = $item['categoryEnm'];
                $id = $item['categoryCd'];
                empty($parent['categoryEnm']) or $item['categoryEnm'] =  $parent['categoryEnm'].' > '.$item['categoryEnm'];
                if(!empty($item['finalYn']) and ('N' == $item['finalYn'])){
                    $this->_getCateRecu($id,$level+1,$item);
                }else{
                    //抵达末梢
                    $this->cate_temp[$id] = [
                        'id'    => $id,
                        'name'  => $item['categoryEnm'],
                        'leaf'  => $name,
                    ];
                }
            }
        }
        return true;
    }

    public function requestCategory($catID, $level){
        $content = self::get(sprintf($this->address,$level,$catID));
        if(($result = json_decode($content,true))){
            return $result;
        }
        return false;
    }

}

