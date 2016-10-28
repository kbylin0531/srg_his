<?php
/**
 * Created by PhpStorm.
 * User: zheng
 * Date: 16-9-29
 * Time: 上午10:22
 */
class AsianPdCategory extends B2BCategory{

    protected $address ='https://member.asianproducts.com/modules.php?name=rfq&action=selectCategory&category_id=%s&rfq_id=&mode=new';
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

    private $cate_temp=[];
    private function _getCateRecu($catID='',$parent=[]){
        static $a = 20;
        if($catID === '' ||$catID == 'root'){
            $this->cate_temp = [];
        }
        if(isset($catID)){
            $list = $this->getCategory($catID);
            if($list) foreach ($list as $item){
                $leaf = $item['category_name'];
                empty($parent['category_name']) or $item['category_name'] =  $parent['category_name'].' > '.$item['category_name'];
                if($this->hasChild($item['category_id'])){
                    $this->_getCateRecu($item['category_id'],$item);
                }else{
                    //抵达末梢
                    $this->cate_temp[$item['category_id']] = [
                        'name'  => $item['category_name'],
                        'id'    => $item['category_id'],
                        'leaf'  => $leaf,
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
            return $data;
        }else{
            if($catID===''||$catID=='root'){
                $content = self::get('https://member.asianproducts.com/modules.php?name=rfq&action=selectCategory&category_id=root&mode=new');
            }else{
                $content = self::get(sprintf($this->address,$catID));
            }

            if(($result = self::FinalCate($content)) && isset($result)){
                return self::cacheCategory($catID,$level,$result)?$result:false;//无法建立缓存数据
            }
        }
        return false;
    }

    function getNomalCate($response){
        $pa = '%<a href=\"(.*?&category_id=(.*)&rfq_id=(.*)&mode=(.*))\">(.*?)</a>%U';
        preg_match_all($pa,$response,$arr);
        $result=array();
        $number=count($arr[1]);
        for($i=0;$i<$number;$i++){
            $result[$i]['category_id']=$arr[2][$i];
            $result[$i]['category_name']=$arr[5][$i];
        }
        return $result;
    }
    /**
     * 获取第二种分类的信息
     * @param string $response
     * @return array 解析失败返回空数组
     */
    function getExtraCate($response){
        $pa = '%<td width=\"(.*)\">(.*?)</td>%U';
        $varr = $karr = [];
        preg_match_all($pa,$response,$arr);
        for($i=0;$i<count($arr[1]);$i++){
            if($i%2==0){
                preg_match_all('/<input (.*) value="(.*)"(.*)>/U',$arr[2][$i],$k);
                if(empty($k[2])){
                    return [];
                }else{
                    $karr[]=$k[2][0];
                }
            }else{
                preg_match_all('/<.*?>(.*?)<\/.*?>/is',$arr[2][$i],$v);
                //print_r($v);
                $varr[]=$v[1][0];
            }
        }
        $res=array();
        for($i=0;$i<count($karr);$i++){
            if($karr[$i]){
                $res[$i]['category_id']= preg_replace("/<a [^>]*>|<\/a>/","",$karr[$i]);
                $res[$i]['category_name']=preg_replace("/<a [^>]*>|<\/a>/","",$varr[$i]);;
            }
        }
        return $res;
    }

    /**
     * 处理获取到的分类合成最终的结果
     * @param string $response
     *  @return array 解析失败返回空数组
     */
    function FinalCate($response){
        //print_r($response);
        $result1=$this->getNomalCate($response);
        $result2=$this->getExtraCate($response);
        if(empty($result2)){
            $final =$result1;
        }else{
            $final =array_merge_recursive($result1,$result2);
        }
        return $final?$final : false;
    }




}