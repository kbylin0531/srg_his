<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-21
 * Time: 下午5:59
 */
use Sharin\Developer as Developer;

class EC21Member extends B2BMember {

    public function login($username,$password='',$capture=''){
        $this->username = $username;
        //检查cookie之前先检查是否有登录记录
        $id = $this->getIdentify();
        $records = is_file($this->loginrecord)?include $this->loginrecord:[];
        Developer::trace($records);
        if($records and isset($records[$id]) and ($records[$id] + 60/* 距离过期时间60秒便认为是过期 */) > NOW and is_file($this->cookie_login)){
            Developer::trace('cookie存在且未过期');
            return true;/*cookie被删除如何...*/
        }else{
            Developer::trace('cookie不存在或者已过期');
        }
        return parent::login($username);
    }

    protected function isLoginSuccess($response) {
        return strpos($response,'Set-Cookie')?true:false;
    }
    /**
     * @var string 打開註冊頁面 時候的cookie
     */
    private $register_page_cookie = '';
    /**
     * @var string 图片获取cookie
     */
    private $register_image_cookie = '';


    private function getRegisterPage(){
        $url = 'http://www.ec21.com/global/member/MyRegist.jsp';
        $content = self::get($url,$this->register_page_cookie,true);
        return $content;
    }

    private function getMagic(){
        $url = 'http://api.solvemedia.com/papi/challenge.script?k=9PRRNhB78ykeJvMH-fDB-ypgIsmsdvyB';
        $content = self::get($url,'',false);
        if(preg_match('/magic[\s]*:[\s]*\'([\w\d\.-_]+)\'/',$content,$matches) and isset($matches[1])){
            return $matches[1];
        }
        return false;
    }

    private function getChidId($magic=''){
        $time = time(); $time2 = $time + 3;
        $magic or $magic = $this->getMagic();
        $url = 'http://api.solvemedia.com/papi/_challenge.js?k=9PRRNhB78ykeJvMH-fDB-ypgIsmsdvyB;f=_ACPuzzleUtil.callbacks%5B0%5D;l=en;t=img;s=300x150;c=js,h5c,h5ct,svg,h5v,v/h264,v/ogg,v/webm,h5a,a/mp3,a/ogg,ua/chrome,ua/chrome52,os/linux,swf22,swf22.0,swf,fwv/M7oZvQ.fbsw41,jslib/jquery,htmlplus;am='.$magic.';ca=script;ts='.$time.';ct='.$time2.';th=custom;r=0.0021079165513289144';
        $content = self::get($url,$this->register_image_cookie);
        if(preg_match('/\"chid\"[\s]*:[\s]*\"(.*)\"/',$content,$matches) and isset($matches[1])) {
            return $matches[1];
        }
        return false;
    }

    /**
     * 设置注册验证码
     * @param string $capture
     * @return $this
     */
    public function setCapture($capture) {
        $chid = Session::get('childId');
        $url = 'http://www.ec21.com/global/captcha/captchaSubmit.jsp';
        $content = self::post($url,http_build_query(array( //application/x-www-form-urlencoded
            'adcopy_response'   => $capture,
            'adcopy_challenge'  => $chid,
        )),$this->register_page_cookie);
        if(preg_match('/dataForm\.captchaState\.value[\s]*=[\s]*\'(.*)\'/',$content,$matches) and isset($matches[1])){
            $this->capture = $matches[1];
        }else{
            $this->error = '无法获取深度验证码';
        }
        return $this;
    }
    /**
     * @param string $img_path oppsite to script path
     * @param string $chid
     * @param string $magic
     * @return bool|string
     */
    public function saveImage($img_path='',$chid='',$magic=''){
        $chid or $chid = $this->getChidId($magic);
        $img_path or $img_path = '/dynamic/capture/'.md5($chid).'ec21.gif';
        $url = 'http://api.solvemedia.com/papi/media?c='.$chid.';w=300;h=100;fg=000000';
        $content = self::get($url,$this->register_image_cookie);
        $path = PATH_PUBLIC.'/'.ltrim($img_path,'/');
        SEK::touch($path);
        $size = file_put_contents($path,$content);
        if($size < 300){
            /* 准确来说是227 media-error.gif的大小 */
            return false;
        }else {
            return $img_path;
        }
    }

    /**
     * 获取登录验证码
     * @return string
     */
    public function getRegisterCapture(){
        $this->getRegisterPage();
        $magic = $this->getMagic();
        $childId = $this->getChidId($magic);
        Session::set('childId',$childId);
        return $this->saveImage('',$childId);
    }

    /**
     * 注册账号
     * @return array|false
     */
    public function register(){
        $username = strtolower('zbg'.time());
        $phone = '1701180323';
        $email = $this->email;
        $tel = [
            'tel1_no'=>'86',
            'tel2_no'=>substr($phone,0,3),
            'tel3_no'=>substr($phone,3),
        ];
        $comp_nm = ucfirst($username).' Corporation';
        $data = [
            'languageSelect'=>'chinese',
            'languageSelect1'=>'chinese',
            'another'=>'',
            'another2'=>'',
            'chk_ids'=>'Y',
            'reg_class'=>'F',
            'mType'=>'T',
            'gubun'=>'S',
            'actionName'=>'insert',
            'inKn'=>'',
            'FBIn'=>'',
            'fEmail'=>'',
            'captchaState'=>$this->capture,
            'country'=>'CN',
            'gubuns'=>'S',
            'contact_sex'=>'M',
            'comp_nm'=> $comp_nm,
            'email'=>$email,
            'checkedEmail'=>$email,
            'isValidEmail'=>'true',
            'member_id'=>$username,
            'passwd'=>$username,
            're_passwd'=>$username,
            'mPlan'=>'N',
            'siteName'=>'',
            'noSite'=>'Y',
            'contact_nm'=>$username,
        ];
        $data = array_merge($data,$tel);
        $content = self::post('http://www.ec21.com/global/member/myRegistSubmit.jsp',http_build_query($data),$this->register_page_cookie,true);

//        echo var_export(['http://www.ec21.com/global/member/myRegistSubmit.jsp',http_build_query($data),$this->register_page_cookie],true);
//        echo htmlspecialchars($content);die();
        if(strpos($content,'/myRegistOk.jsp')){
            //返回自动登录表单（自动执行submit）
            return [
                'email'     =>  $email,
                'username'  =>  $username,
                'passwd'    =>  $username,
            ];
        }elseif(strpos($content,'MyRegistError.jsp')){
            //信息填写错误
            return false;
        }else{
            //未知原因
            return false;
        }

    }

    /**
     * 船舰产品分类组
     * @return array|bool
     */
    public function createCategory(){
        $url = 'http://www.ec21.com/global/basic/MyPGroupEditSubmit.jsp?actionName=insertPop';
        $content = self::post($url,http_build_query([
            'fileFlag'=>'0/',
            'pimg1Local'=>'',
            'pimg1LocalSize'=>'',
            'gcatalog_id'=>'',
            'pageNum'=>'',
            'tag'=>'Y',
            'gcatalog_nm'=>'Default',
            'allDesc'=>'',
            'display'=>'Y',
        ]),$this->cookie_login,true);
        echo htmlspecialchars($content);
        if(preg_match("/option\\svalue='(.*)'\\>(.*)\\<\\//",$content,$matches) and isset($matches[1],$matches[2])){
            return [
                $matches[1],//code
                'Default',//name
            ];
        }
        return false;
    }

    /**
     * 完善公司信息
     * @param array $info
     * @return bool
     */
    public function update(array $info=[]){
        $data = [
            'actionName' => 'update',
            'sellCategoryCode' => '4340',
            'buyCategoryCode' => '4314',
            'c_email1' => $this->email,
            'c_email2' => '',
            'mtype' => 'T',
            'flag' => '1',
            'gubuns' => 'S',
            'fileFlag2' => '0',
            'pimg2Local' => '',
            'Upimgname' => '',
            'pimg2LocalSize' => '',
            'logoimg_chk' => '2',
            'cimg1LocalSize' => '',
            'imageModifyFlag' => '0',
            'editBrochure' => '',
            'delBrochure' => '',
            'fn1' => '',
            'fOriNm1' => '',
            'fId1' => '',
            'fSize1' => '',
            'broTitle1' => '',
            'broDesc1' => '',
            'fn2' => '',
            'fOriNm2' => '',
            'fId2' => '',
            'fSize2' => '',
            'broTitle2' => '',
            'broDesc2' => '',
            'fn3' => '',
            'fOriNm3' => '',
            'fId3' => '',
            'fSize3' => '',
            'broTitle3' => '',
            'broDesc3' => '',
            'fn4' => '',
            'fOriNm4' => '',
            'fId4' => '',
            'fSize4' => '',
            'broTitle4' => '',
            'broDesc4' => '',
            'fn5' => '',
            'fOriNm5' => '',
            'fId5' => '',
            'fSize5' => '',
            'broTitle5' => '',
            'broDesc5' => '',
            'gubun' => 'S',
            'comp_nm' => 'Zbg'.time().' Corporation',
            'addr1' => 'Products or Selling Leads 3',
            'addr3' => 'Zhejiang',
            'stateSelect' => '',
            'addr2' => 'Hangzhou',
            'citySelect' => '0571',
            'zip_no' => '0571',
            'country_cd' => 'CN',
            'tel1_no' => '86',
            'tel2_no' => '0571',
            'tel3_no' => '84515458',
            'fax1_no' => '',
            'fax2_no' => '0571',
            'fax3_no' => '',
            'keyword' => 'Please',
            'keyword_s' => 'Please',
            'busi_no' => '',
            'trade_no' => '',
            'type' => '01',
            'found_dt' => '2010',
            'employee' => '04',
            'revenue_qt' => '03',
            'homelink' => '',
            'comp_info' => 'This is a description about nothing !Do not fire us,thank you sir,thank you sir,thank you sir,thank you sir!',
            'video_seq' => '',
            'ytVer' => '',
            'video_type' => '1',
        ];
        $info = array_merge($data,$info);
        $content = self::post('http://www.ec21.com/global/basic/MyCompanyProfileSubmit.jsp',http_build_query($info),$this->register_page_cookie);
        $content = htmlspecialchars($content);
//        echo "完善公司信息返回:{$content}<br>";
        return stripos($content,'OK') !== false;
    }

}