<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-23
 * Time: 上午10:58
 */

class AA {

}
class AB extends AA{

}

class AC extends AB {

}

print_r(class_parents(AC::class));