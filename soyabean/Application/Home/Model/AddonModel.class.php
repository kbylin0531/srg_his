<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/8/16
 * Time: 7:50 PM
 */

namespace Application\Home\Model;
use Soya\Extend\Model;

/**
 * Class AddonModel 微信插件列表
 * @package Application\Home\Controller
 */
class AddonModel extends Model {

    protected $tablename = 'addons';

    public function getAddonList(){
        return $this->query('select a.*,c.title as ctitle from sy_addon a left outer join sy_addon_category c on c.id = a.cate_id;');
    }

}