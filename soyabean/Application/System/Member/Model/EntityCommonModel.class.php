<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/30
 * Time: 20:38
 */

namespace Application\System\Member\Model;
use Soya\Extend\Model;

abstract class EntityCommonModel extends Model {

    /**
     * 获取列表
     * @return array|bool
     */
    public function getList(){
        return $this->select();
    }


}