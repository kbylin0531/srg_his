<?php



/**
 * Class A
 * @_method mixed index()
 * @package Web\Home\Controller
 */
class A {

    protected static $attr = 'AA';

    function __call($name, $arguments)
    {
        echo $name;
    }

    public static function index(){
        echo 'static';
    }

    /**
     * @return string
     */
    public static function getAttr()
    {
        return static::$attr;
    }

    /**
     * @param string $attr
     */
    public static function setAttr($attr)
    {
        static::$attr = $attr;
    }

}


class B extends A{
}

class C extends A {

}


$a = new A();

//static
$a->index();
A::index();
