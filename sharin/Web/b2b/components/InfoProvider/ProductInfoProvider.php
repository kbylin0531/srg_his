<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-21
 * Time: 下午4:18
 */
class ProductInfoProvider
{

    protected $pid = null;

    public function __construct($pid=0) {
        $this->pid = $pid;
    }

    /**
     * @param int $limit 一次获取的产品数量
     * @return array
     */
    public function getlist($limit=10){
        $list = [];

        while($limit --> 0){
            $list[] = [
                'pid'   => NOW.$limit,
                'name'  => 'Instrument V'.NOW,
                'describe'   => 'There are moments in life when you miss someone so much that you just want to pick them from your dreams and hug them for real! Dream what you want to dream;go where you want to go;be what you want to be,because you have only one life and one chance to do all the things you want to do.',
                'keywork'   => 'Moments,Dreams,Justin',
                'image'     => 'http://www.en8848.com.cn/d/file/201303/40d6fdf2adfda4469f1f97488eff3eb0.jpg',
            ];
        }
        return $list;
    }
}