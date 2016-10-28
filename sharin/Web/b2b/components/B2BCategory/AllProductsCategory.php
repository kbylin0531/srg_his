<?php
/**
 * Created by PhpStorm.
 * User: zheng
 * Date: 16-9-26
 * Time: 下午2:43
 */
class AllProductsCategory extends B2BCategory {

    protected $address = 'http://submit.allproducts.com/include/SelectPC.php?language=en&cno=1&select_code=%s';
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
    private function _getCateRecu($catID='',$parent=[]){
        if($catID === ''){
            $this->cate_temp = [];
        }
        if(isset($catID)){
            $list = $this->getCategory($catID);
            if($list) foreach ($list as $item){
                $leaf = $item['select_name'];
                empty($parent['select_name']) or $item['select_name'] =  $parent['select_name'].' > '.$item['select_name'];
                if($this->hasChild($item['select_code'])){
                    $this->_getCateRecu($item['select_code'],$item);
                }else{
                    //抵达末梢
                   $this->cate_temp[$item['select_code']] = [
                        'name'  => $item['select_name'],
                        'id'    => $item['select_code'],
                       'leaf'   => $leaf,
                    ];
                }
            }
        }
        return true;
    }
    /**
     * 判断是否有子类
     * @param int $caid 分类ID
     * @return true|false
     */
    private function hasChild($caid){
       $ressult = $this->getCategory($caid);
        if(empty($ressult)){
            return false;
        }
        return true;
    }

    public function getCategory($catID='',$level=''){
        if($data = self::cacheCategory($catID,$level)){
            return $data;//优先使用缓存数据
        }else{
            if($catID ===''){
                $content = self::get('http://submit.allproducts.com/include/SelectPC.php?language=en&cno=1');
            }else{
                $content = self::get(sprintf($this->address,$catID));
            }
            $pa = '%<a href=(.*?)>(.*?)</a>%sim';
            if(($result = self::html2Array($content,$pa)) && isset($result)){
                return self::cacheCategory($catID,$level,$result)?$result:false;//无法建立缓存数据
            }
        }
        return false;
    }

     protected static function html2Array($html,$pa){
        $arr = array();
        preg_match_all($pa,$html,$arr);
        $result=array();
        $number=count($arr[1]);
        for($i=0;$i<$number;$i++){
            $temp=explode('=',$arr[1][$i]);
            $result[$i]['select_code']=$temp[3];
            $result[$i]['select_name']=$arr[2][$i];
        }
        return $result?$result : false;
    }


}