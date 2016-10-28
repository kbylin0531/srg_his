<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-29
 * Time: 下午12:03
 */
class Tradeeasy extends B2BCategory {
    protected $reg1 = '/<option value=\"(\d+)\"\sclass=\"(cat|sub-cat)\".*?>(.*?)<\/option>/';
    public function getCategoryLeaves() {

    }


}