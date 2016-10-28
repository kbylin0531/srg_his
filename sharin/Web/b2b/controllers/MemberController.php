<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-21
 * Time: 下午4:14
 */
class MemberController extends SController {

    public function actionIndex(){
        $this->render('index',[
            'data'  => json_encode(MemberModel::getInstance()->select('platform = \'ec21\'')),
        ]);
    }

    public function actionCapture(){
        $image = (new EC21Member())->getRegisterCapture();
        SEK::ajaxBack([
            'src' => PUBLIC_URL.'/'.$image,
        ]);
    }

    public function actionRegister(){
        $code = $_POST['code'];
        $email = $_POST['email'];
        $platform = new EC21Member();
        $info = $platform->setCapture($code)->setEmail($email)->register();
        if($info){
            //登录平台
            if(!$platform->login($info['username'])) throw new \Exception(var_export($info,true));
            //创建默认分组
            $ginfo = $platform->createCategory();
            if(false !== $ginfo){
                $info['gid'] = $ginfo[0];
                $info['gname'] = $ginfo[1];
            }else{
                SEK::ajaxBack([
                    'type'  => 0,
                    'value'=> '添加产品默认分组失败',
                ]);
            }
        }
        $type = 1;
        if($platform->update()){
            $memberModel = MemberModel::getInstance();
            $memberModel->username = $info['username'];
            $memberModel->passwd = $info['passwd'];
            $memberModel->email = $info['email'];
            $memberModel->phone = '';
            $memberModel->cateid = isset($info['gid'])?$info['gid']:'';
            $memberModel->total = 0;
            $memberModel->platform = 'ec21';
            if(!$memberModel->create()){
                $info = '添加到数据库失败！';
                $type = 0;
            }
        }else{
            $info = '完善公司信息失败';
            $type = 0;
        }
        SEK::ajaxBack([
            'type'  => $type,
            'value' => $info,
        ]);
    }


}