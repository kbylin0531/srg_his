<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-21
 * Time: 下午5:51
 */
class ProductController extends SController {
    /**
     * 显示页面
     */
    public function actionIndex(){
        $this->render('index',[
            'data'=>json_encode((new ProductInfoProvider())->getlist(10)),
        ]);
    }

    /**
     * 添加发布
     * @param string $pid
     * @param string $name
     * @param string $describe
     * @param string $keywork
     * @param string $image
     */
    public function actionPublish($pid='',$name='',$describe='',$keywork='',$image=''){
        $meminfo = MemberModel::getInstance()->getAvailableAccount();
        $type = 0;
        if(!$meminfo){
            $message = '无可用的账号';
        } else {
            $member = new EC21Member();
            $product = new EC21Product();
            if($member->login($meminfo['username'])){
                //设置产品属性
                $product->setName($name);
                $product->addKeywords($keywork);
                $product->setImage($image);
                $product->setDescription($describe);
                $product->gcatalog_id = $meminfo['cateid'];//自定义分类ID，各个账号均不同
                $product->categorymId = '212815';
                $product->categoryNm = 'Pharmaceutical Intermediates';
                //提交产品
                $result = $product->submit()?1:0;

                //整理提交结果，无论成功与否
                $productModel = ProductModel::getInstance();
                $productModel->type = $result;
                $productModel->pid = $pid;
                $productModel->name = $name;
                $productModel->image = $image;
                $productModel->atime = NOW;
                $productModel->platform = 'ec21';
                $productModel->uname = $meminfo['username'];

                if($result){
                    //提交成功额外增加产品URL
                    $productModel->url = $product->getLastSubmit();
                    if(!MemberModel::getInstance()->inc($meminfo['username'])) {
                        $message =  "无法增加账户下产品累计!";
                    }else{
                        $type = 1;
                        $message = '提交成功';
                    }
                } else {
                    $message = '提交失败';
                }
                $insert = $productModel->create();
                if(!$insert){
                    $message .=  " ,无法将产品信息插入到数据库中!";
                }
            }else{
                $message = '账号无法登录';
            }
        }
        SEK::ajaxBack([
            'type'  => $type,
            'message'   => $message,
        ]);
    }

    /**
     * 显示发布完成列表，包括发送成功和失败的
     */
    public function actionFinished(){
        $this->render('finished',[
            'data'  => json_encode(ProductModel::getInstance()->select()),
        ]);
    }

}