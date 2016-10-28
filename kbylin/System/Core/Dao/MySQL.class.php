<<<<<<< HEAD
<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/17
 * Time: 10:49
 */
namespace System\Core\Dao;

use System\Core\KbylinException;
use System\Core\Dao;

class MySQL extends DaoAbstract{

    /**
     * 转义保留字字段名称
     * @param string $fieldname 字段名称
     * @return string
     */
    public function escape($fieldname){
        return "`{$fieldname}`";
    }

    /**
     * 根据配置创建DSN
     * @param array $config 数据库连接配置
     * @return string
     */
    public function buildDSN(array $config){
        $dsn  =  "mysql:host={$config['host']}";
        if(isset($config['dbname'])){
            $dsn .= ";dbname={$config['dbname']}";
        }
        if(!empty($config['port'])) {
            $dsn .= ';port=' . $config['port'];
        }
        if(!empty($config['socket'])){
            $dsn  .= ';unix_socket='.$config['socket'];
        }
        if(!empty($config['charset'])){
            $dsn  .= ';charset='.$config['charset'];
        }
        return $dsn;
    }

    /**
     * 编译组件成适应当前数据库的SQL字符串
     * @param array $components  复杂SQL的组成部分
     * @param int $actiontype 操作类型
     * @return string
     * @throws KbylinException
     */
    public function compile(array $components,$actiontype){
        $_components = array(
            'distinct'  => '',
            'fields'    => ' * ', //查询的表域情况
            'table'     => null,
            'join'      => null,     //join部分，需要带上join关键字
            'where'     => null, //where部分
            'group'     => null, //分组 需要带上group by
            'having'    => null,//having子句，依赖$group存在，需要带上having部分
            'order'     => null,//排序，不需要带上order by
            'limit'     => 2,
            'offset'    => 5,
        );
        $components = array_merge($_components,$components);

        switch($actiontype){
            case Dao::ACTION_SELECT:
                return $this->compileSelect($components);
                break;

            default:
                throw new KbylinException('Unexpect action type');
        }
    }

    /**
     * 使用语句编译SELECT语句
     * @param array $components SQL组件
     * @return string
     */
    protected function compileSelect($components){
        $components['distinct'] and $components['distinct'] = 'distinct';//为true或者1时转化为distinct关键字

        $sql = "SELECT {$components['distinct']} \r\n{$components['fields']} \r\nFROM {$components['table']} \r\n";

        //group by，having 加上关键字(对于如group by的组合关键字，只要判断第一个是否存在)如果不是以该关键字开头  则自动添加
        if($components['join']){
            $sql .= "{$components['join']} \r\n";
        }
        if($components['where']){
//            $components['where'] = ((0 !== stripos(trim($components['where']),'where'))?'WHERE ':'').$components['where'];
            $sql .= "WHERE {$components['where']} \r\n";
        }
        if($components['group'] ){
//            $components['group'] = ((0 !== stripos(trim($components['group']),'group'))?'GROUP BY ':'').$components['group'];
            $sql .= "GROUP BY {$components['group']} \r\n";
        }
        if( $components['having']){
//            $components['having'] = ((0 !== stripos(trim($components['having']),'having'))?'HAVING ':'').$components['having'];
            $sql .= "HAVING {$components['having']} \r\n";
        }
        //去除order by
//        $components['order'] = preg_replace_callback('|order\s*by|i',function(){return '';},$components['order']);

        if($components['order']) $sql .= "ORDER BY {$components['order']} \r\n";

        //是否偏移
        if($components['limit']){
            if($components['offset']) $components['offset'] .= ',';
            $sql .= "LIMIT {$components['offset']}{$components['limit']} \r\n";
        }
        return $sql;
    }

=======
<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/17
 * Time: 10:49
 */
namespace System\Core\Dao;

use System\Core\KbylinException;
use System\Core\Dao;

class MySQL extends DaoAbstract{

    /**
     * 转义保留字字段名称
     * @param string $fieldname 字段名称
     * @return string
     */
    public function escape($fieldname){
        return "`{$fieldname}`";
    }

    /**
     * 根据配置创建DSN
     * @param array $config 数据库连接配置
     * @return string
     */
    public function buildDSN(array $config){
        $dsn  =  "mysql:host={$config['host']}";
        if(isset($config['dbname'])){
            $dsn .= ";dbname={$config['dbname']}";
        }
        if(!empty($config['port'])) {
            $dsn .= ';port=' . $config['port'];
        }
        if(!empty($config['socket'])){
            $dsn  .= ';unix_socket='.$config['socket'];
        }
        if(!empty($config['charset'])){
            $dsn  .= ';charset='.$config['charset'];
        }
        return $dsn;
    }

    /**
     * 编译组件成适应当前数据库的SQL字符串
     * @param array $components  复杂SQL的组成部分
     * @param int $actiontype 操作类型
     * @return string
     * @throws KbylinException
     */
    public function compile(array $components,$actiontype){
        $_components = array(
            'distinct'  => '',
            'fields'    => ' * ', //查询的表域情况
            'table'     => null,
            'join'      => null,     //join部分，需要带上join关键字
            'where'     => null, //where部分
            'group'     => null, //分组 需要带上group by
            'having'    => null,//having子句，依赖$group存在，需要带上having部分
            'order'     => null,//排序，不需要带上order by
            'limit'     => 2,
            'offset'    => 5,
        );
        $components = array_merge($_components,$components);

        switch($actiontype){
            case Dao::ACTION_SELECT:
                return $this->compileSelect($components);
                break;

            default:
                throw new KbylinException('Unexpect action type');
        }
    }

    /**
     * 使用语句编译SELECT语句
     * @param array $components SQL组件
     * @return string
     */
    protected function compileSelect($components){
        $components['distinct'] and $components['distinct'] = 'distinct';//为true或者1时转化为distinct关键字

        $sql = "SELECT {$components['distinct']} \r\n{$components['fields']} \r\nFROM {$components['table']} \r\n";

        //group by，having 加上关键字(对于如group by的组合关键字，只要判断第一个是否存在)如果不是以该关键字开头  则自动添加
        if($components['join']){
            $sql .= "{$components['join']} \r\n";
        }
        if($components['where']){
//            $components['where'] = ((0 !== stripos(trim($components['where']),'where'))?'WHERE ':'').$components['where'];
            $sql .= "WHERE {$components['where']} \r\n";
        }
        if($components['group'] ){
//            $components['group'] = ((0 !== stripos(trim($components['group']),'group'))?'GROUP BY ':'').$components['group'];
            $sql .= "GROUP BY {$components['group']} \r\n";
        }
        if( $components['having']){
//            $components['having'] = ((0 !== stripos(trim($components['having']),'having'))?'HAVING ':'').$components['having'];
            $sql .= "HAVING {$components['having']} \r\n";
        }
        //去除order by
//        $components['order'] = preg_replace_callback('|order\s*by|i',function(){return '';},$components['order']);

        if($components['order']) $sql .= "ORDER BY {$components['order']} \r\n";

        //是否偏移
        if($components['limit']){
            if($components['offset']) $components['offset'] .= ',';
            $sql .= "LIMIT {$components['offset']}{$components['limit']} \r\n";
        }
        return $sql;
    }

>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
}