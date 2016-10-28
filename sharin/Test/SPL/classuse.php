<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-23
 * Time: 上午10:47
 */
trait foo { }
class bar {
    use foo;
}
class noe extends bar{}

var_dump([
    class_uses(new bar()),
    class_uses(noe::class),
]);