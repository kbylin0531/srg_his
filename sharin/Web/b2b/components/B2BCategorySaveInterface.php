<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-28
 * Time: 下午7:04
 */
interface B2BCategorySaveInterface {

    public function get($code);

    public function set($code,array $map);

}