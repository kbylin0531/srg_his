<?php
/**
 * Class EC21Product
 * @property string $gcatalog_id
 * @property string $categorymId
 * @property string $categoryNm
 */
class EC21Product extends B2BProduct{

    protected $submitAddress   = 'http://www.ec21.com/global/basic/MyProductEditSubmit.jsp';
    protected $attrs = [
        //必要参数
        'catalog_nm'=>'',
        'allDesc'=>'',//描述,要求一定字数
        'origin'=>'CN',//商品产地
        'display'=>'Y',//'N'
        'categorymId'=>'212815',//分类ID
        'categoryNm'=>'Pharmaceutical Intermediates',//分类名称
        'gcatalog_id'=>'GC10133729',//产品分组ID
        //可选
        //可以通过方法设置
        'keyword'=>'',
        'keyword1'=>'',
        'keyword2'=>'',
        'keyword3'=>'',
        'keyword4'=>'',
        'keyword_s'=>'',
        //自动设置（无关参数）
        'frequencyCategory'=>'',//常用分类，默认为''
        'Upimgname'=>'',//上传图片访问地址
        'attribute_input_580'=>'',
        'attribute_input_66'=>'',
        'brand'=>'',
        'catalog_id'=>'',
        'categoryCd'=>'',
        'certi_num_CCC'=>'',
        'certi_num_CE'=>'',
        'certi_num_FCC'=>'',
        'certi_num_FDA'=>'',
        'certi_num_TUV'=>'',
        'certi_num_UL'=>'',
        'currency'=>'',
        'imageModifyFlag'=>'0',
        'leadtime'=>'',
        'measure_unit'=>'',
        'minorder'=>'',
        'model'=>'',
        'noGroupFlag'=>'',
        'package'=>'',
        'package_hei'=>'',
        'package_len'=>'',
        'package_unit'=>'',
        'package_wei'=>'',
        'package_wid'=>'',
        'package_wunit'=>'',
        'pageNum'=>'1',
        'pimg1LocalSize'=>'',
        'pimg_0'=>'',
        'port'=>'',
        'price'=>'',
        'price_condition'=>'04',//价格条件
        'role_nms'=>'',
        'supplyability'=>'',
        'usereattri_nm1'=>'',
        'usereattri_nm2'=>'',
        'usereattri_nm3'=>'',
        'usereattri_nm4'=>'',
        'usereattri_nm5'=>'',
        'usereattri_val1'=>'',
        'usereattri_val2'=>'',
        'usereattri_val3'=>'',
        'usereattri_val4'=>'',
        'usereattri_val5'=>'',
        'video_seq'=>'',
        'video_type'=>'1',
    ];

    /**
     * 设置产品名称
     * @param $name
     * @return $this
     * @throws Exception
     */
    public function setName($name){
        if(strlen($name) >= 5){
            $this->attrs['catalog_nm'] = $name;
        }else{
            throw new Exception('名称不合法！');
        }
        return $this;
    }

    /**
     * 设置产品描述
     * 字数限制
     * @param $description
     * @return $this
     */
    public function setDescription($description){
        $this->attrs['allDesc'] = $description;
        return $this;
    }

    /**
     * 添加关键字列表，以逗号i分隔
     * @param $keyworks
     */
    public function addKeywords($keyworks){
        $keyworks = explode(',',$keyworks);
        foreach ($keyworks as $k){
            $this->addKeywork($k);
        }
    }

    /**
     * 添加单个关键词
     * @param string $keywork
     * @return $this
     * @throws Exception
     */
    public function addKeywork($keywork){
        if(empty($this->attrs['keyword'])){
            $this->attrs['keyword'] = $keywork;
        }elseif(empty($this->attrs['keyword1'])){
            $this->attrs['keyword1'] = $keywork;
        }elseif(empty($this->attrs['keyword2'])){
            $this->attrs['keyword2'] = $keywork;
        }elseif(empty($this->attrs['keyword3'])){
            $this->attrs['keyword3'] = $keywork;
        }elseif(empty($this->attrs['keyword4'])){
            $this->attrs['keyword4'] = $keywork;
        }else{
            throw new Exception('最多填写4个关键词');
        }
        if(empty($this->attrs['keyword_s'])){
            $this->attrs['keyword_s'] = $keywork;
        }else{
            $this->attrs['keyword_s'] .= ','.$keywork;
        }
        return $this;
    }

    /**
     * @param string $path 文件路径
     * @return string|false 返回文件上传地址,失败时返回false
     * @throws Exception
     */
    private function uploadImage($path){
        $imagename = 'PI_'.microtime(true);
        //第一次提交上传图片
        self::post(
            'http://upimage.ec21.com/global/upload/product/productOneImageUploadForSwf.jsp',[
            'f_n'  => $imagename,
            'temp_file'  => new \CURLFile($path,'application/octet-stream'),
            'Upload'  => 'Submit Query',
        ],$this->cookie_login,true);//返回\n * 15
        //第二次提交上获取图片地址
        $fields = [
            'fileFlag'  => 1,
            'type'  => 'PI',
            'pnum'  => 0,
            'pimg1Local'  => $imagename.'.jpg',
            'pimg1LocalSize'    => 9472,
            'hideFileID'    => '',
        ];
        $result2 = self::post(
            'http://www.ec21.com/global/fileup/UploadImageResultMulti.jsp',
            $fields,
            $this->cookie_login,
            true
        );

        if(preg_match('/http:\/\/upimage\.ec21\.com\/upload\/temporary\/([\w\d_\.]+)/',$result2,$matches)){
            if(isset($matches[1])){
                return $matches[0];
            }
        }
        return false;
    }
    /**
     * @return bool
     */
    public function submit(){
        $this->attrs['actionName'] = 'insert';//表示添加操作
        if($this->images){
            $image = $this->uploadImage($this->images);
            if($image){
                $this->attrs['Upimgname'] = $image;
            }
        }
        return parent::submit();
    }

    protected $error = '';

    /**
     * 产品成功提交返回的信息：
     * HTTP/1.1 100 Continue HTTP/1.1 302 Found Date: Thu, 01 Sep 2016 08:54:18 GMT Server: Apache Set-Cookie: JSESSIONID=96A9AF56BD1833D44EE7E3D65F1A8B5B.worker1e; Path=/global/; HttpOnly Location: http://www.ec21.com/global/basic/MyEcSuccess.jsp?transposition=product&pageNum=1 Content-Length: 0 Vary: User-Agent Content-Type: text/html;charset=utf-8
     * alert(\'Select a category.\'); 表示分类未能完全获取(实际上时描述未设置)
     * @param string $response
     * @return bool
     */
    public function isSucmitSuccess($response){
        if(strpos($response,'global/basic/MyEcSuccess.jsp')) {
            return true;
        }else{
            if(strpos($response,'Exceed the fixed numer of open products')){
                $this->error = '提交的产品数量达到上限';//
            }else{
                $this->error = '未知的错误';
            }
            return false;
        }
    }

    public function getLastSubmit(){
        $url = 'http://www.ec21.com/global/basic/MyProductList.jsp';
        $content = self::get($url,'',$this->cookie_login);
        $len = preg_match_all('/<a\shref=\"([^\"]+)\"\starget=\"\_NEW1">/',$content,$matches);
        if($len  and isset($matches[1][$len-1])) {
            return $matches[1][$len-1];
        } else {
            return '';
        }
    }

}