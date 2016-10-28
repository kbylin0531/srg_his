<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/12/16
 * Time: 1:08 PM
 */

/**
 * Class SQLite
 * 数据库访问抽象对象
 * @package Library\Utils
 */
abstract class SQLite extends \PDO{
    /**
     * @var string 表名词
     */
    protected $tablename = '';
    /**
     * @var string 主健名
     */
    protected $pk = '';
    /**
     * @var array 惯例配置
     */
    protected static $config = [
        'dsn'  => 'sqlite.sample.db',
    ];

    /**
     * @var SQLite[]
     */
    private static $instances = [];

    /**
     * @param string $dsn
     * @return SQLite
     */
    public static function getInstance($dsn=''){
        $clsnm = static::class;
        if(!isset(self::$instances[$clsnm])){
            $dsn or $dsn = 'sqlite:'.PATH_BASE.'data/pub.db';
            self::$instances[$clsnm] = new $clsnm($dsn);
        }
        return self::$instances[$clsnm];
    }

    /**
     * @var SQLite
     */
    protected $_driver = null;


    protected $fields = [];
    public function __get($name)
    {
        return isset($this->fields[$name])?$this->fields[$name] : '';
    }
    public function __set($name, $value)
    {
        if(key_exists($name,$this->fields)) {
            $this->fields[$name] = $value;
        }else{
            throw new \Exception("字段'{$name}'不存在");
        }
    }

    public function createMemberTable(){
        $sql = 'create table member  (username varchar(64),passwd varchar(64),email varchar(64),phone varchar(64),cateid varchar(64),total interger)';
        return $this->exec($sql);
    }

    public function select($where=''){
        $sql = 'select * from '.$this->tablename;
        if($where){
            $sql .= " where $where ";
        }
        return $this->query($sql)->fetchAll();
    }

    /**
     * @param array|null $fields
     * @return int
     */
    public function create(array $fields=null){
        null === $fields and $fields = $this->fields;
        $holder = "(".implode(",",array_keys($fields)).")";
        $fields = "('".implode("','",$fields)."')";
        $sql = "insert into {$this->tablename} $holder values $fields ;";
        return $this->exec($sql);
    }

    public function delete($pkey){
        $sql = "delete from {$this->tablename} where {$this->pk} = '$pkey';";
        return $this->exec($sql);
    }

    public function update($pkey, array $fields){
        $sql = "update {$this->tablename} set";
        foreach ($fields as $key=>$val){
            $sql .= " $key = '$val',";
        }
        $sql = rtrim($sql,',')." where {$this->pk} = '$pkey';";
        return $this->exec($sql);
    }

    public function getError(){
        $info = $this->errorInfo();
        return $info[2];
    }
}