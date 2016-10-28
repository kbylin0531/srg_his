<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/30
 * Time: 16:11
 */

namespace Application\Test\Controller;
use Application\System\Member\Model\MemberModel;

class MemberController {
    /**
     * @var MemberModel
     */
    protected $memberModel;

    public function index(){
        echo __METHOD__.'<br />';
        $this->memberModel = new MemberModel();
        //测试部分
//        $this->testList();
//        $this->testCreate();
//        $this->testDelete();
//        $this->testLogin();
    }


//-------------------------------测试member-----------------------------//
    public function testList(){
        $list = $this->memberModel->listMember();
        \Soya\dumpout($list);
    }

    public function testCreate(){
        $result = $this->memberModel->createMember([
            'username'  => 'lin'
        ]);
        if(false === $result) $result = $this->memberModel->error();
        \Soya\dumpout($result);
    }

    public function testDelete(){
        $result = $this->memberModel->deleteMember(8);
        \Soya\dumpout($result);
    }

    public function testLogin(){
        $result =  $this->memberModel->checkLogin('admin',md5(sha1('123456')));
        \Soya\dumpout($result);
    }

//-------------------------------测试role-----------------------------//
//-------------------------------测试membergroup-----------------------------//
//-------------------------------测试permission-----------------------------//




}