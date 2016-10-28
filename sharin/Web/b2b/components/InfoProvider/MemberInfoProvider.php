<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-21
 * Time: 下午4:18
 */
class MemberInfoProvider
{

    protected $uid = null;

    public function __construct($uid) {
        $this->uid = $uid;
    }

}