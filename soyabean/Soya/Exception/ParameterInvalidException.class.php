<?php
/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 10:53
 */
namespace Soya\Exception;
use Soya\Core\Exception;

class ParameterInvalidException extends Exception {
    /**
     * ParameterInvalidException constructor.
     * @param mixed $param
     * @param string $expect
     */
    public function __construct($param,$expect){
        parent::__construct();
        $value = var_export($param,true);
        $this->message = "Expect param to be {$expect},but passed as {$value};\n\n";
    }

}