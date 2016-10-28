<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-27
 * Time: 上午9:34
 */
class TradettCategory extends B2BCategory {


    protected $address = 'http://www.ecvv.com/Myecvv/Login.html';
    protected $post_addr = 'http://www.ecvv.com/ajaxpro/ECVV.Ecvv_MyEcvv.Product.Edit_product,ECVV.ashx';
    protected $cookie = '';

    public function __construct() {
        $this->cookie = PATH_COOKIE.'/'.static::class;
        unlink($this->cookie);
        touch($this->cookie);
    }

    public function getCategoryLeaves(){

        $this->login();

        $content = self::get('http://us.en.tradett.com/productmanage/productadd.aspx');
        dumpout($content);

        if(false !== $this->seedCategory($this->top_parent_id)){
            return $this->cate_temp;
        }
        return false;
    }

    private $cate_temp = [];
    private function seedCategory($catID,$level=1,array $parent=[]){
        if(1 === $level) $this->cate_temp = [];
        if($level < 5){
            $list = $this->getCategory($catID,$level);
            if($list) foreach ($list as $item){
                $name = $item['value'];
                empty($parent['value']) or $item['value'] = "{$parent['value']} > {$item['value']}";
                $id = $item['attrs']['categoryId'];
                if($this->hasChild($item)){
                    $this->seedCategory($id,$level+1,$item);
                }else{
                    //抵达末梢
                    $this->cate_temp[$id] = [
                        'name'  => $item['value'],
                        'id'    => $id,
                        'leaf'  => $name,
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
        return !empty($node['attrs']['IsLeaf']) and ('false' === trim($node['attrs']['IsLeaf']));
    }

    protected function requestCategory($catID,$level){
        $content = self::post($this->post_addr,json_encode([
            'parentCategoryId'   => $catID,
            'categoryType'       => 'allCategories',
        ]),$this->cookie,false,[],[
            'X-AjaxPro-Method: GetCategoryListStr',
            'Content-Type: text/plain; charset=UTF-8',
            'Referer: http://www.ecvv.com/myecvv/product/Post_Product.html',
            'Accept-Language: en-US,en;q=0.8,zh-CN;q=0.6,zh;q=0.4',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36',
            'Origin: http://www.ecvv.com',
        ]);
        $content = str_replace([
            '\'','&',
        ],[
            '"','&amp;'
        ],trim($content,' ";/*'));
//        echo $content;
//        dumpout(self::xml2ArrayInAdv($content));
        if(($result = self::xml2ArrayInAdv($content)) and isset($result['items']['li'])){
            return $result['items']['li'];
        }
        return false;
    }

    protected function login(){
        $content = self::post('http://us.en.tradett.com/',[],$this->cookie,true,[
            CURLOPT_COOKIE => 'TT_Language=en-us; uName=kbylin@163.com; TT_Log_system_code=8352',
        ]);
//        $content .= self::get('http://us.en.tradett.com/home.aspx',$this->cookie,true);
        dumpout(htmlspecialchars($content));
        self::post($this->address,http_build_query([
            'txtPassword'  => 'kbylin@163.com',
            'chkRemember'  => '1',
            'txtID'  => 'kbylin',
            '__VIEWSTATE'  => '/wEPDwUJNzAwMjQwMjc4ZGQ69owBykADHcsuHRYfB8NrSREA8w==',
            '__VIEWSTATEGENERATOR'  => '3C9D8666',
        ]),$this->cookie,true);
//        dump($content);
        $content = self::get('http://www.ecvv.com/myecvv/Index.html?LoginTip=1',$this->cookie);
    }

}