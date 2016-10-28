<?php
/**
 * Created by PhpStorm.
 * User: zheng
 * Date: 16-9-27
 * Time: 上午11:23
 * 无需密码　post buy lead 页面即可获取
 */
class EnChinaCategory extends B2BCategory {


    protected $address = 'http://my.en.china.cn/ajax.php?op=getindustries&parentid=%d';


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
    private function _getCateRecu($catID=0,$parent=[]){
        if($catID === 0){
            $this->cate_temp = [];
        }
        if($catID<3432){
            $list = $this->getCategory($catID);
            if(($list)) foreach ($list as $item){
                $leaf = $item['name'];
                empty($parent['name']) or $item['name'] =  $parent['name'].' > '.$item['name'];
                if($this->hasChild($item['id'])){
                   $this->_getCateRecu($item['id'],$item);
                }else{
                    //抵达末梢
                    $this->cate_temp[$item['id']] = [
                        'name'  => $item['name'],
                        'id'    => $item['id'],
                        'leaf'  =>  $leaf,
                    ];
                }
            }
        }
        return true;
    }

    private function hasChild($caid){
        $ressult = $this->getCategory($caid);
        if(empty($ressult)){
            return false;
        }
        if(is_array($ressult) and isset($ressult[0])){
            if($ressult[0]['id'] == $caid){
                return false;
            }
        }
        return true;
    }


    /**
     * 获取分类
     * @param int $catID 上级分类ID ，没有上级分类默认为0
     * @param int|string $level 分类层级，默认获取第一层级的分类列表
     * @return array|false
     */
    public function getCategory($catID=0,$level=''){

        if($data = self::cacheCategory($catID,$level)){
            //Log::write('从缓存中读取数据');
            return $data;//优先使用缓存数据
        }else{
            $content = self::get(sprintf($this->address,$catID));
            if(is_array($result = json_decode($content,true)) and isset($result)){
                return self::cacheCategory($catID,$level,$result)?$result:false;//无法建立缓存数据
            }
        }

        return false;
    }


}
