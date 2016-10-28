<<<<<<< HEAD
<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2016/2/1
 * Time: 16:23
 */
namespace System\Core;
use PDO;
use System\Core\Dao\DaoAbstract;
use System\Core\Dao\MySQL;
use System\Core\Dao\OCI;
use System\Core\Dao\SQLServer;
use System\Traits\Crux;
use PDOStatement;

/**
 * Class Dao 数据入口对象(Data Access Object)
 * 一个Dao对应一个数据路的入口
 * 具体方法的实现以来于各个驱动
 *
 *
 * 可以通过Dao::getInstance()获取默认的Dao实例
 *
 * @package System\Core
 */
class Dao {
    use Crux;

    const CONF_NAME = 'dao';

    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,
        'DRIVER_CLASS_LIST' => [
            MySQL::class,
            OCI::class,
            SQLServer::class,
        ],
        'DRIVER_CONFIG_LIST' => [
            [
                'type'      => 'Mysql',//数据库类型
                'dbname'    => 'xor',//选择的数据库
                'username'  => 'lin',
                'password'  => '123456',
                'host'      => 'localhost',
                'port'      => '3306',
                'charset'   => 'UTF8',
                'dsn'       => null,//默认先检查差DSN是否正确,直接写dsn而不设置其他的参数可以提高效率，也可以避免潜在的bug
                'options'   => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//默认异常模式
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//结果集返回形式
                ],
            ],
            [
                'type'      => 'Oci',//数据库类型
                'dbname'    => 'xor',//选择的数据库
                'username'  => 'lin',
                'password'  => '123456',
                'host'      => 'localhost',
                'port'      => '3306',
                'charset'   => 'UTF8',
                'dsn'       => null,//默认先检查差DSN是否正确,直接写dsn而不设置其他的参数可以提高效率，也可以避免潜在的bug
                'options'   => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//默认异常模式
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//结果集返回形式
                ],
            ],
            [
                'type'      => 'Sqlsrv',//数据库类型
                'dbname'    => 'xor',//选择的数据库
                'username'  => 'lin',
                'password'  => '123456',
                'host'      => 'localhost',
                'port'      => '3306',
                'charset'   => 'UTF8',
                'dsn'       => null,//默认先检查差DSN是否正确,直接写dsn而不设置其他的参数可以提高效率，也可以避免潜在的bug
                'options'   => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//默认异常模式
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//结果集返回形式
                ],
            ],
        ],
    ];

    /**
     * 自身实例
     * @var Dao[]
     */
    private static $_instances = null;
    

    /**
     * @var DaoAbstract
     */
    private $driver = null;

    /**
     * 指向当前的PDOStatement对象
     * @var PDOStatement
     */
    private $curStatement = null;
    /**
     * SQL执行发生的错误信息
     * @var string|null
     */
    private $error = null;

    /**
     * 获取Dao实例
     * @param int|string|array $index int或者
     * @param array|null $config 自定义配置
     * @return Dao
     */
    public static function getInstance($index=null,array $config = null){
        if(null !== $config){
            //自己设定的数据库访问
            $key = md5(serialize($config));
            if(!isset(self::$_instances[$key])){
                self::$_instances[$key] = new Dao($index,$config);
            }
            return self::$_instances[$key];
        }elseif(!isset(self::$_instances[$index])){
            self::$_instances[$index] = new Dao($index);
        }
        return self::$_instances[$index];
    }

    /**
     * Dao constructor.
     * @param int|string $index 驱动器角标
     * @param array|null $config 自定义配置，可以作为自定义配置或者是对固定值的修正
     */
    private function __construct($index=null,array $config = null){
        if(is_array($config)){
            $info = self::getDriverInfo($index);
            $drivername = $info[0];
            $this->driver = new $drivername($config);
        }else{
            $this->driver = self::getDriverInstance($index);
        }
    }

    /**
     * 获取原生的PDO继承对象
     * @return DaoAbstract
     */
    public function getDriver(){
        return $this->driver;
    }

/********************************* 基本的查询功能 ***************************************************************************************/
    /**
     * 简单地查询一段SQL，并且将解析出所有的结果集合
     * @param string $sql 查询的SQL
     * @param array|null $inputs 输入参数
     *                          如果输入参数未设置或者为null（显示声明），则直接查询
     *                          如果输入参数为非空数组，则使用PDOStatement对象查询
     * @return array|false 返回array类型表述查询结果，返回false表示查询出错，可能是数据表不存在等数据库返回的错误信息
     */
    public function query($sql,array $inputs=null){
        if(null === $inputs){
            //直接使用PDO的查询功能
            try{
                $statement = $this->driver->query($sql);//返回PDOstatement,失败时返回false(或者抛出异常)，视错误的处理方式而定
                if(false !== $statement){
                    //query成功时返回PDOStatement对象
                    return $statement->fetchAll();//成功返回
                }else{
                    $this->setPdoError();
                }
            }catch(\PDOException $e){
                $this->setPdoError($e->getMessage());
            }
        }else{
            try {
                //简介调用PDOStatement的查询功能
                $statement = $this->driver->prepare($sql);
                if(false !== $statement and false !== $statement->execute($inputs)){
                    return $statement->fetchAll();
                }
            }catch(\PDOException $e){
                /* prepare可能失败,返回错误或者抛出异常视PDO::ERRMODE_EXCEPTION设置情况而定 */
                $this->setPdoStatementError($e->getMessage());
            }
        }
        return false;
    }
    /**
     * 简单地执行Insert、Delete、Update操作
     * @param string $sql 待查询的SQL语句，如果未设置输入参数则需要保证SQL已经被转义
     * @param array|null $inputs 输入参数,具体参考query方法的参数二
     * @return int|false 返回受到影响的行数，但是可能不会太可靠，需要用===判断返回值是0还是false
     *                   返回false表示了错误，可以用getError获取错误信息
     */
    public function exec($sql,array $inputs=null){
        if(null === $inputs){
            //调用PDO的查询功能
            try{
                $rst = $this->driver->exec($sql);
                if(false === $rst){
                    $this->setPdoError();
                }else{
                    return $rst;
                }
            }catch (\PDOException $e){
                $this->error = $e->getMessage();
            }
        }else{
            try {
                //简介调用PDOStatement的查询功能
                $statement = $this->driver->prepare($sql);
                if(false !== $statement and false !== $statement->execute($inputs)){
                    return $statement->rowCount();
                }
            }catch(\PDOException $e){
                /* prepare可能失败,返回错误或者抛出异常视PDO::ERRMODE_EXCEPTION设置情况而定 */
                $this->setPdoStatementError($e->getMessage());
            }
        }
        return false;
    }


/********************************* 高级查询功能 ***************************************************************************************/

    /**
     * 准备一段SQL
     *  <note>
     *      prepare('insert *****',$id='helo');  准备一段SQL并命名ID为helo
     *      prepare( null|false|''|0 ,$id='helo');  切换到该ID下，并将PDOStatement返回
     *      prepare('insert *****');  将SQL语句设置ID为0并默认指向0
     *  </note>
     * @param string $sql 查询的SQL，当参数二指定的ID存在，只有在参数一布尔值不为false时，会进行真正地prepare
     * @param array $option prepare方法参数二
     * @return $this 返回prepare返回的对象，如果失败则返回null
     */
    public function prepare($sql,array $option=[]){
        try{
            $this->curStatement = null;//如果之前有prepare的SQLStatement对象，隐式清空
            $this->curStatement = $this->driver->prepare($sql,$option);//prepare失败抛出异常后赋值过程结束,$this->curStatement可能依旧指向之前的SQLStatement对象（可能不为null）
        }catch(\PDOException $e){
            /* 当表不存在或者字段不存在时候 */
            $this->setPdoError($e->getMessage());
        }
        return $this;
    }

    /**
     * 执行查询功能，返回的结果是bool表示是否执行成功
     * @param array|null $input_parameters
     *                  一个元素个数和将被执行的 SQL 语句中绑定的参数一样多的数组。所有的值作为 PDO::PARAM_STR 对待。
     *                  不能绑定多个值到一个单独的参数,如果在 input_parameters 中存在比 PDO::prepare() 预处理的SQL 指定的多的键名，
     *                  则此语句将会失败并发出一个错误。(这个错误在PHP 5.2.0版本之前是默认忽略的)
     * @param PDOStatement|null $statement 该参数未设定或者为null时使用的PDOStatement为上次prepare的对象
     * @return bool|false bool值表示执行结果，当不存在执行对象时返回null，可以通过rowCount方法获取受到影响行数，或者getError获取错误信息
     */
    public function execute(array $input_parameters = null, PDOStatement $statement=null){
        null !== $statement and $this->curStatement = $statement;
        if(!$this->curStatement or !($this->curStatement instanceof PDOStatement)){
            $this->setError('No available PDOStatement in execute!');
            return false;
        }

        try{
            //出错时设置错误信息，注：PDOStatement::execute返回bool类型的结果
            if(false === $this->curStatement->execute($input_parameters)){//参数数目不正确时候会抛出异常"Invalid parameter number"
                $this->setPdoStatementError($this->curStatement);
                return false;
            }
        }catch(\PDOException $e){
            $this->setPdoStatementError($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 返回一个包含结果集中所有剩余行的数组
     * 此数组的每一行要么是一个列值的数组，要么是属性对应每个列名的一个对象
     * @param int|null $fetch_style
     *          想要返回一个包含结果集中单独一列所有值的数组，需要指定 PDO::FETCH_COLUMN ，
     *          通过指定 column-index 参数获取想要的列。
     *          想要获取结果集中单独一列的唯一值，需要将 PDO::FETCH_COLUMN 和 PDO::FETCH_UNIQUE 按位或。
     *          想要返回一个根据指定列把值分组后的关联数组，需要将 PDO::FETCH_COLUMN 和 PDO::FETCH_GROUP 按位或
     * @param int $fetch_argument
     *                  参数一为PDO::FETCH_COLUMN时，返回指定以0开始索引的列（组合形式如上）
     *                  参数一为PDO::FETCH_CLASS时，返回指定类的实例，映射每行的列到类中对应的属性名
     *                  参数一为PDO::FETCH_FUNC时，将每行的列作为参数传递给指定的函数，并返回调用函数后的结果
     * @param array $constructor_args 参数二为PDO::FETCH_CLASS时，类的构造参数
     * @return array
     */
    public function fetchAll($fetch_style = null, $fetch_argument = null, $constructor_args = null){
        $param = array();
        isset($fetch_style)         and $param[0] = $fetch_style;
        isset($fetch_argument)      and $param[1] = $fetch_argument;
        isset($constructor_args)    and $param[2] = $constructor_args;
        return call_user_func_array(array($this->curStatement,'fetchAll'),$param);
    }

    /**
     * 返回上一个由对应的 PDOStatement 对象执行DELETE、 INSERT、或 UPDATE 语句受影响的行数
     * 如果上一条由相关 PDOStatement 执行的 SQL 语句是一条 SELECT 语句，有些数据可能返回由此语句返回的行数
     * 但这种方式不能保证对所有数据有效，且对于可移植的应用不应依赖于此方式
     * @return int
     * @throws KbylinException
     */
    public function rowCount(){
        if(!$this->curStatement){
            throw new KbylinException('Invalid PDOStatement');
        }
        return $this->curStatement->rowCount();
    }


    /**
     * 返回PDO驱动或者上一个PDO语句对象上发生的错误的信息（具体驱动的错误号和错误信息）
     * 注意：调用此函数后会将错误信息清空
     * @return string 返回错误信息字符串，没有错误发生时返回空字符串
     */
    public function getError(){
        $temp =  $this->error;
        $this->error = null;
        return $temp;
    }

    /**
     * 清除错误标记以进行下一次查询
     * @param string $error 错误信息
     * @return void
     */
    public function setError($error){
        $this->error = $error;
    }

    /**
     * 设置PDO对象上发生的错误
     * [
     *      0   => SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).
     *      1   => Driver-specific error code.
     *      2   => Driver-specific error message.
     * ]
     * If the SQLSTATE error code is not set or there is no driver-specific error,
     * the elements following element 0 will be set to NULL .
     * @param null|string $errorInfo 设置错误信息，未设置时自动获取
     * @return bool 返回true表示发生了错误并成功设置错误信息，返回false表示模块未捕捉到错误
     */
    private function setPdoError($errorInfo=null){
        null === $errorInfo and $errorInfo = $this->getPdoError();
        return ($this->error = $errorInfo)===null?false:true;
    }

    /**
     * 获取PDO对象查询时发生的错误
     * @return string
     */
    public function getPdoError(){
        $pdoError = $this->driver->errorInfo();
        return isset($pdoError[0])?"Code:{$pdoError[0]} >>> [{$pdoError[1]}]:[{$pdoError[2]}]":null;
    }
    /**
     * 获取PDOStatemnent对象上查询时发生的错误
     * @param PDOStatement|null $statement
     * @return string
     */
    public function getStatementError(PDOStatement $statement=null){
        null === $statement and $statement = $this->curStatement;
        $stmtError = $statement->errorInfo();
        return isset($stmtError[1])?"[{$stmtError[1]}]:[{$stmtError[2]}]":'';
    }

    public function setPdoStatementError($errorString=null){
        null === $errorString and $errorString = $this->getStatementError();
        return ($this->error = $errorString)===null?false:true;
    }



/****************************** 事务功能 ***************************************************************************************

    /**
     * 开启事务
     * @return bool
     */
    public function beginTransaction(){
        return $this->driver->beginTransaction();
    }

    /**
     * 提交事务
     * @return bool
     */
    public function commit(){
        return $this->driver->commit();
    }
    /**
     * 回滚事务
     * @return bool
     */
    public function rollBack(){
        return $this->driver->rollBack();
    }
    /**
     * 确认是否在事务中
     * @return bool
     */
    public function inTransaction(){
        return $this->driver->inTransaction();
    }

    /**
     * 释放到数据库服务的连接，以便发出其他 SQL 语句(新的参数绑定)，使得该SQL语句处于一个可以被再次执行的状态
     * 当上一个执行的 PDOStatement 对象仍有未取行时，此方法对那些不支持再执行一个 PDOStatement 对象的数据库驱动非常有用。
     * 如果数据库驱动受此限制，则可能出现失序错误的问题
     * PDOStatement::Cursor() 要么是一个可选驱动的特有方法（效率最高）来实现，要么是在没有驱动特定的功能时作为一般的PDO 备用来实现
     * <note>
     *      ① 语意上相当于下面的语句的执行结果
     *          do {
     *              while ($stmt->fetch());
     *              if (!$stmt->nextRowset()) break;
     *          } while (true);
     * </note>
     * @param PDOStatement|null $statement
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE
     */
    public function closeCursor($statement=null){
        isset($statement) and $this->curStatement = $statement;
        return $this->curStatement->closeCursor();
    }

    /**
     * 获取预处理语句包含的信息
     * <note>
     *      ①实际不能获取参数的值，不像文档中写的那样
     *      ②无论是否发生了错误，信息都会存在
     * </note>
     * @return string
     */
    public function getStatementParams(){
        ob_start();//开始本层次的ob缓冲区
        $this->curStatement->debugDumpParams();
        return ob_get_clean();// 相当于ob_get_contents() 和 ob_end_clean()
    }


/************************************** 驱动实现扩展方法 ******************************************************************************************/

    /**
     * 转义保留字字段名称
     * @param string $fieldname 字段名称
     * @return string
     */
    public function escape($fieldname){
        return $this->driver->escape($fieldname);
    }

/************************************** 链式扩展方法 ******************************************************************************************/



    /**
     * 添加数据
     * <code>
     *      $fldsMap ==> array(
     *          'fieldName' => 'fieldValue',
     *          'fieldName' => array('fieldValue',boolean),//第二个元素表示是否对字段名称进行转义
     *      );
     *
     *     $data = ['a'=>'foo','b'=>'bar'];
     *     $keys = array_keys($data);
     *     $fields = '`'.implode('`, `',$keys).'`';
     *     #here is my way
     *     $placeholder = substr(str_repeat('?,',count($keys),0,-1));
     *     $pdo->prepare("INSERT INTO `baz`($fields) VALUES($placeholder)")->execute(array_values($data));
     * </code>
     *
     * 插入数据的sql可以是：
     * ①INSERT INTO 表名称 VALUES (值1, 值2,....)
     * ②INSERT INTO table_name (列1, 列2,...) VALUES (值1, 值2,....)
     *
     * @param string $tablename
     * @param array $fieldsMap
     * @return bool 返回true或者false
     * @throws KbylinException
     */
    public function create($tablename=null,array $fieldsMap=null){

        $fields = $placeholder = '';
        $sql = null;
        $bind  = [];
        $flag = true;//标记是否进行插入形式判断

        $dao  = $this;

        foreach($fieldsMap as $fieldName=>$fieldValue){
            $colnm = $fieldName;
            if($flag){
                if(is_numeric($fieldName)){
                    $placeholder  = rtrim(str_repeat(' ?,',count($fieldsMap)),',');
                    $sql = "INSERT INTO {$tablename} VALUES ( {$placeholder} );";
                    $bind = $fieldsMap;
                    break;
                }
                $flag = false;
            }
            if(is_array($fieldValue)){ //不设置字段名称进行插入时$fieldName无意义
                $colnm = $fieldValue[1]?$dao->escape($fieldName):$fieldName;
                $fieldValue = $fieldValue[0];
            }
            $fields .= " {$colnm} ,";
            $placeholder  .= " :{$fieldName} ,";
            $bind[":{$fieldName}"] = $fieldValue;
        }

        if(isset($sql)){
            $fields = rtrim($fields,',');
            $sql = "INSERT INTO {$tablename} ( {$fields} ) VALUES ( {$placeholder} );";
        }
        return $dao->prepare($sql)->execute($bind);
    }

    /**
     * 更新数据表
     * @param string $tablename
     * @param string|array $flds
     * @param string|array $whr
     * @return bool
     * @throws KbylinException
     */
    public function update($tablename,$flds,$whr){;
        $input_params = [];
        $fields = is_string($flds)?[$flds,[]]:$this->makeSegments($flds,false);
        $where  = is_string($whr) ?[$whr,[]] :$this->makeSegments($whr, false);
        empty($fields[1]) or $input_params = $fields[1];
        empty($where[1]) or array_merge($input_params,$where[1]);
        return $this->prepare("UPDATE {$tablename} SET {$fields[0]} WHERE {$where[0]};")->execute($input_params);
    }

    /**
     * 执行删除数据的操作
     * 如果不设置参数，则进行清空表的操作（谨慎使用）
     * @param string $tablename 数据表的名称
     * @param array $whr 字段映射数组
     * @return bool
     */
    public function delete($tablename,$whr=null){
        $bind = null;
        if(isset($whr)){
            $where  = $this->makeSegments($whr);
            $sql    = "delete from {$tablename} where {$where[0]};";
            $bind   = $where[1];
        }else{
            $sql = "delete from {$tablename};";
        }
        return $this->prepare($sql)->execute($bind);
    }

    /**
     * 查询表的数据
     * @param string $tablename
     * @param string|array|null $fields
     * @param string|array|null $whr
     * @return array|bool
     * @throws KbylinException
     */
    public function select($tablename=null,$fields=null,$whr=null){
        $bind = null;
        //设置选取字段
        if(null === $fields){
            $fields = ' * ';
        }elseif($fields and is_array($fields)){
            //默认转义
            array_map(function($param){
                return $this->driver->escape($param);
            },$fields);
            $fields = implode(',',$fields);
        }elseif(!is_string($fields)){
            throw new KbylinException('Parameter 2 require the type of "null","array","string" ,now is invalid!');
        }

        if(null === $whr){
            $sql = "select {$fields} from {$tablename};";
        }elseif(is_array($whr)){
            $whr  = is_string($whr)? [$whr,null] :$this->makeSegments($whr);
            $sql = "select {$fields} from {$tablename} where {$whr[0]};";
            $bind = $whr[1];
        }elseif(is_string($whr)){
            $sql = "select {$fields} from {$tablename} where {$whr};";
        }else{
            throw new KbylinException('Parameter 3 require the type of "null","array","string" ,now is invalid!');
        }


        if(false === $this->prepare($sql)->execute($bind) ){
            return false;
        }
        return $this->fetchAll();
    }

    /**
     * 综合字段绑定的方法
     * <code>
     *      $operator = '='
     *          $fieldName = :$fieldName
     *          :$fieldName => trim($fieldValue)
     *
     *      $operator = 'like'
     *          $fieldName = :$fieldName
     *          :$fieldName => dowithbinstr($fieldValue)
     *
     *      $operator = 'in|not_in'
     *          $fieldName in|not_in array(...explode(...,$fieldValue)...)
     * </code>
     * @param string $fieldName 字段名称
     * @param string|array $fieldValue 字段值
     * @param string $operator 操作符
     * @param bool $translate 是否对字段名称进行转义,MSSQL中使用[]
     * @return array
     * @throws KbylinException
     */
    private function makeFieldBind($fieldName,$fieldValue,$operator='=',$translate=false){
        $fieldName = trim($fieldName,' :[]');
        $bindFieldName = null;
        if(false !== strpos($fieldName,'.')){
            $arr = explode('.',$fieldName);
            $bindFieldName = ':'.array_pop($arr);
        }elseif(mb_strlen($fieldName,'utf-8') < strlen($fieldName)){//其他编码
            $bindFieldName = ':'.md5($fieldName);
        }else{
            $bindFieldName = ":{$fieldName}";
        }

        $operator = strtolower(trim($operator));
        $sql = $translate?" [{$fieldName}] ":" {$fieldName} ";
        $bind = array();

        switch($operator){
            case '=':
                $sql .= " = {$bindFieldName} ";
                $bind[$bindFieldName] = $fieldValue;
                break;
            case 'like':
                $sql .= " like {$bindFieldName} ";
                $bind[$bindFieldName] = $fieldValue;
                break;
            case 'in':
            case 'not in':
                if(is_string($fieldValue)){
                    $sql .= " {$operator} ({$fieldValue}) ";
                }elseif(is_array($fieldValue)){
                    $sql .= " {$operator} ('".implode("','",$fieldValue)."')";
                }else{
                    throw new KbylinException("The parameter 1 '{$fieldValue}' is invalid!");
                }
                break;
            default:
                throw new KbylinException("The parameter 2 '{$operator}' is invalid!");
        }
        return [$sql,$bind];
    }

    /**
     * 片段设置
     * <note>
     *      片段准则
     *      $map == array(
     *           //第一种情况,连接符号一定是'='//
     *          'key' => $val,
     *          'key' => array($val,$operator,true),
     *          //第二种情况，数组键，数组值//
     *          array('key','val','like|=',true),//参数4的值为true时表示对key进行[]转义
     *          //第三种情况，字符键，数组值//
     *          'assignSql' => array(':bindSQLSegment',value)//与第一种情况第二子目相区分的是参数一以':' 开头
     *      );
     * </note>
     * @param $map
     * @param string $connect 表示是否使用and作为连接符，false时为,
     * @return array
     */
    public function makeSegments($map,$connect='and'){
        //初始值与参数检测
        $bind = [];
        $sql = '';
        if(empty($map)){
            return [$sql,$bind];
        }

        //元素连接
        foreach($map as $key=>$val){
            if(is_numeric($key)){
                //第二种情况
                $rst = $this->makeFieldBind(
                    $val[0],
                    $val[1],
                    isset($val[2])?$val[2]:' = ',
                    !empty($val[3])
                );
                if(is_array($rst)){
                    $sql .= " {$rst[0]} {$connect}";
                    $bind = array_merge($bind, $rst[1]);
                }
            }elseif(is_array($val) and strpos($val[0],':') === 0){
                //第三种情况,复杂类型，由用户自定义
                $sql .= " {$key} {$connect}";
                $bind[$val[0]] = $val[1];
            }else{
                //第一种情况
                $translate = false;
                $operator = '=';
                if(is_array($val)){
                    $translate = isset($val[2])?$val[2]:false;
                    $operator = isset($val[1])?$val[1]:'=';
                    $val = $val[0];
                }
                $rst = $this->makeFieldBind($key,trim($val),$operator,$translate);//第一种情况一定是'='的情况
                if(is_array($rst)){
                    $sql .= " {$rst[0]} {$connect}";
                    $bind = array_merge($bind, $rst[1]);
                }
            }
        }
        $result = array(
            substr($sql,0,strlen($sql)-strlen($connect)),//去除最后一个and
            $bind,
        );
        return $result;
    }
=======
<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2016/2/1
 * Time: 16:23
 */
namespace System\Core;
use PDO;
use System\Core\Dao\DaoAbstract;
use System\Core\Dao\MySQL;
use System\Core\Dao\OCI;
use System\Core\Dao\SQLServer;
use System\Traits\Crux;
use PDOStatement;

/**
 * Class Dao 数据入口对象(Data Access Object)
 * 一个Dao对应一个数据路的入口
 * 具体方法的实现以来于各个驱动
 *
 *
 * 可以通过Dao::getInstance()获取默认的Dao实例
 *
 * @package System\Core
 */
class Dao {
    use Crux;

    const CONF_NAME = 'dao';

    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,
        'DRIVER_CLASS_LIST' => [
            MySQL::class,
            OCI::class,
            SQLServer::class,
        ],
        'DRIVER_CONFIG_LIST' => [
            [
                'type'      => 'Mysql',//数据库类型
                'dbname'    => 'xor',//选择的数据库
                'username'  => 'lin',
                'password'  => '123456',
                'host'      => 'localhost',
                'port'      => '3306',
                'charset'   => 'UTF8',
                'dsn'       => null,//默认先检查差DSN是否正确,直接写dsn而不设置其他的参数可以提高效率，也可以避免潜在的bug
                'options'   => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//默认异常模式
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//结果集返回形式
                ],
            ],
            [
                'type'      => 'Oci',//数据库类型
                'dbname'    => 'xor',//选择的数据库
                'username'  => 'lin',
                'password'  => '123456',
                'host'      => 'localhost',
                'port'      => '3306',
                'charset'   => 'UTF8',
                'dsn'       => null,//默认先检查差DSN是否正确,直接写dsn而不设置其他的参数可以提高效率，也可以避免潜在的bug
                'options'   => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//默认异常模式
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//结果集返回形式
                ],
            ],
            [
                'type'      => 'Sqlsrv',//数据库类型
                'dbname'    => 'xor',//选择的数据库
                'username'  => 'lin',
                'password'  => '123456',
                'host'      => 'localhost',
                'port'      => '3306',
                'charset'   => 'UTF8',
                'dsn'       => null,//默认先检查差DSN是否正确,直接写dsn而不设置其他的参数可以提高效率，也可以避免潜在的bug
                'options'   => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//默认异常模式
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//结果集返回形式
                ],
            ],
        ],
    ];

    /**
     * 自身实例
     * @var Dao[]
     */
    private static $_instances = null;
    

    /**
     * @var DaoAbstract
     */
    private $driver = null;

    /**
     * 指向当前的PDOStatement对象
     * @var PDOStatement
     */
    private $curStatement = null;
    /**
     * SQL执行发生的错误信息
     * @var string|null
     */
    private $error = null;

    /**
     * 获取Dao实例
     * @param int|string|array $index int或者
     * @param array|null $config 自定义配置
     * @return Dao
     */
    public static function getInstance($index=null,array $config = null){
        if(null !== $config){
            //自己设定的数据库访问
            $key = md5(serialize($config));
            if(!isset(self::$_instances[$key])){
                self::$_instances[$key] = new Dao($index,$config);
            }
            return self::$_instances[$key];
        }elseif(!isset(self::$_instances[$index])){
            self::$_instances[$index] = new Dao($index);
        }
        return self::$_instances[$index];
    }

    /**
     * Dao constructor.
     * @param int|string $index 驱动器角标
     * @param array|null $config 自定义配置，可以作为自定义配置或者是对固定值的修正
     */
    private function __construct($index=null,array $config = null){
        if(is_array($config)){
            $info = self::getDriverInfo($index);
            $drivername = $info[0];
            $this->driver = new $drivername($config);
        }else{
            $this->driver = self::getDriverInstance($index);
        }
    }

    /**
     * 获取原生的PDO继承对象
     * @return DaoAbstract
     */
    public function getDriver(){
        return $this->driver;
    }

/********************************* 基本的查询功能 ***************************************************************************************/
    /**
     * 简单地查询一段SQL，并且将解析出所有的结果集合
     * @param string $sql 查询的SQL
     * @param array|null $inputs 输入参数
     *                          如果输入参数未设置或者为null（显示声明），则直接查询
     *                          如果输入参数为非空数组，则使用PDOStatement对象查询
     * @return array|false 返回array类型表述查询结果，返回false表示查询出错，可能是数据表不存在等数据库返回的错误信息
     */
    public function query($sql,array $inputs=null){
        if(null === $inputs){
            //直接使用PDO的查询功能
            try{
                $statement = $this->driver->query($sql);//返回PDOstatement,失败时返回false(或者抛出异常)，视错误的处理方式而定
                if(false !== $statement){
                    //query成功时返回PDOStatement对象
                    return $statement->fetchAll();//成功返回
                }else{
                    $this->setPdoError();
                }
            }catch(\PDOException $e){
                $this->setPdoError($e->getMessage());
            }
        }else{
            try {
                //简介调用PDOStatement的查询功能
                $statement = $this->driver->prepare($sql);
                if(false !== $statement and false !== $statement->execute($inputs)){
                    return $statement->fetchAll();
                }
            }catch(\PDOException $e){
                /* prepare可能失败,返回错误或者抛出异常视PDO::ERRMODE_EXCEPTION设置情况而定 */
                $this->setPdoStatementError($e->getMessage());
            }
        }
        return false;
    }
    /**
     * 简单地执行Insert、Delete、Update操作
     * @param string $sql 待查询的SQL语句，如果未设置输入参数则需要保证SQL已经被转义
     * @param array|null $inputs 输入参数,具体参考query方法的参数二
     * @return int|false 返回受到影响的行数，但是可能不会太可靠，需要用===判断返回值是0还是false
     *                   返回false表示了错误，可以用getError获取错误信息
     */
    public function exec($sql,array $inputs=null){
        if(null === $inputs){
            //调用PDO的查询功能
            try{
                $rst = $this->driver->exec($sql);
                if(false === $rst){
                    $this->setPdoError();
                }else{
                    return $rst;
                }
            }catch (\PDOException $e){
                $this->error = $e->getMessage();
            }
        }else{
            try {
                //简介调用PDOStatement的查询功能
                $statement = $this->driver->prepare($sql);
                if(false !== $statement and false !== $statement->execute($inputs)){
                    return $statement->rowCount();
                }
            }catch(\PDOException $e){
                /* prepare可能失败,返回错误或者抛出异常视PDO::ERRMODE_EXCEPTION设置情况而定 */
                $this->setPdoStatementError($e->getMessage());
            }
        }
        return false;
    }


/********************************* 高级查询功能 ***************************************************************************************/

    /**
     * 准备一段SQL
     *  <note>
     *      prepare('insert *****',$id='helo');  准备一段SQL并命名ID为helo
     *      prepare( null|false|''|0 ,$id='helo');  切换到该ID下，并将PDOStatement返回
     *      prepare('insert *****');  将SQL语句设置ID为0并默认指向0
     *  </note>
     * @param string $sql 查询的SQL，当参数二指定的ID存在，只有在参数一布尔值不为false时，会进行真正地prepare
     * @param array $option prepare方法参数二
     * @return $this 返回prepare返回的对象，如果失败则返回null
     */
    public function prepare($sql,array $option=[]){
        try{
            $this->curStatement = null;//如果之前有prepare的SQLStatement对象，隐式清空
            $this->curStatement = $this->driver->prepare($sql,$option);//prepare失败抛出异常后赋值过程结束,$this->curStatement可能依旧指向之前的SQLStatement对象（可能不为null）
        }catch(\PDOException $e){
            /* 当表不存在或者字段不存在时候 */
            $this->setPdoError($e->getMessage());
        }
        return $this;
    }

    /**
     * 执行查询功能，返回的结果是bool表示是否执行成功
     * @param array|null $input_parameters
     *                  一个元素个数和将被执行的 SQL 语句中绑定的参数一样多的数组。所有的值作为 PDO::PARAM_STR 对待。
     *                  不能绑定多个值到一个单独的参数,如果在 input_parameters 中存在比 PDO::prepare() 预处理的SQL 指定的多的键名，
     *                  则此语句将会失败并发出一个错误。(这个错误在PHP 5.2.0版本之前是默认忽略的)
     * @param PDOStatement|null $statement 该参数未设定或者为null时使用的PDOStatement为上次prepare的对象
     * @return bool|false bool值表示执行结果，当不存在执行对象时返回null，可以通过rowCount方法获取受到影响行数，或者getError获取错误信息
     */
    public function execute(array $input_parameters = null, PDOStatement $statement=null){
        null !== $statement and $this->curStatement = $statement;
        if(!$this->curStatement or !($this->curStatement instanceof PDOStatement)){
            $this->setError('No available PDOStatement in execute!');
            return false;
        }

        try{
            //出错时设置错误信息，注：PDOStatement::execute返回bool类型的结果
            if(false === $this->curStatement->execute($input_parameters)){//参数数目不正确时候会抛出异常"Invalid parameter number"
                $this->setPdoStatementError($this->curStatement);
                return false;
            }
        }catch(\PDOException $e){
            $this->setPdoStatementError($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 返回一个包含结果集中所有剩余行的数组
     * 此数组的每一行要么是一个列值的数组，要么是属性对应每个列名的一个对象
     * @param int|null $fetch_style
     *          想要返回一个包含结果集中单独一列所有值的数组，需要指定 PDO::FETCH_COLUMN ，
     *          通过指定 column-index 参数获取想要的列。
     *          想要获取结果集中单独一列的唯一值，需要将 PDO::FETCH_COLUMN 和 PDO::FETCH_UNIQUE 按位或。
     *          想要返回一个根据指定列把值分组后的关联数组，需要将 PDO::FETCH_COLUMN 和 PDO::FETCH_GROUP 按位或
     * @param int $fetch_argument
     *                  参数一为PDO::FETCH_COLUMN时，返回指定以0开始索引的列（组合形式如上）
     *                  参数一为PDO::FETCH_CLASS时，返回指定类的实例，映射每行的列到类中对应的属性名
     *                  参数一为PDO::FETCH_FUNC时，将每行的列作为参数传递给指定的函数，并返回调用函数后的结果
     * @param array $constructor_args 参数二为PDO::FETCH_CLASS时，类的构造参数
     * @return array
     */
    public function fetchAll($fetch_style = null, $fetch_argument = null, $constructor_args = null){
        $param = array();
        isset($fetch_style)         and $param[0] = $fetch_style;
        isset($fetch_argument)      and $param[1] = $fetch_argument;
        isset($constructor_args)    and $param[2] = $constructor_args;
        return call_user_func_array(array($this->curStatement,'fetchAll'),$param);
    }

    /**
     * 返回上一个由对应的 PDOStatement 对象执行DELETE、 INSERT、或 UPDATE 语句受影响的行数
     * 如果上一条由相关 PDOStatement 执行的 SQL 语句是一条 SELECT 语句，有些数据可能返回由此语句返回的行数
     * 但这种方式不能保证对所有数据有效，且对于可移植的应用不应依赖于此方式
     * @return int
     * @throws KbylinException
     */
    public function rowCount(){
        if(!$this->curStatement){
            throw new KbylinException('Invalid PDOStatement');
        }
        return $this->curStatement->rowCount();
    }


    /**
     * 返回PDO驱动或者上一个PDO语句对象上发生的错误的信息（具体驱动的错误号和错误信息）
     * 注意：调用此函数后会将错误信息清空
     * @return string 返回错误信息字符串，没有错误发生时返回空字符串
     */
    public function getError(){
        $temp =  $this->error;
        $this->error = null;
        return $temp;
    }

    /**
     * 清除错误标记以进行下一次查询
     * @param string $error 错误信息
     * @return void
     */
    public function setError($error){
        $this->error = $error;
    }

    /**
     * 设置PDO对象上发生的错误
     * [
     *      0   => SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).
     *      1   => Driver-specific error code.
     *      2   => Driver-specific error message.
     * ]
     * If the SQLSTATE error code is not set or there is no driver-specific error,
     * the elements following element 0 will be set to NULL .
     * @param null|string $errorInfo 设置错误信息，未设置时自动获取
     * @return bool 返回true表示发生了错误并成功设置错误信息，返回false表示模块未捕捉到错误
     */
    private function setPdoError($errorInfo=null){
        null === $errorInfo and $errorInfo = $this->getPdoError();
        return ($this->error = $errorInfo)===null?false:true;
    }

    /**
     * 获取PDO对象查询时发生的错误
     * @return string
     */
    public function getPdoError(){
        $pdoError = $this->driver->errorInfo();
        return isset($pdoError[0])?"Code:{$pdoError[0]} >>> [{$pdoError[1]}]:[{$pdoError[2]}]":null;
    }
    /**
     * 获取PDOStatemnent对象上查询时发生的错误
     * @param PDOStatement|null $statement
     * @return string
     */
    public function getStatementError(PDOStatement $statement=null){
        null === $statement and $statement = $this->curStatement;
        $stmtError = $statement->errorInfo();
        return isset($stmtError[1])?"[{$stmtError[1]}]:[{$stmtError[2]}]":'';
    }

    public function setPdoStatementError($errorString=null){
        null === $errorString and $errorString = $this->getStatementError();
        return ($this->error = $errorString)===null?false:true;
    }



/****************************** 事务功能 ***************************************************************************************

    /**
     * 开启事务
     * @return bool
     */
    public function beginTransaction(){
        return $this->driver->beginTransaction();
    }

    /**
     * 提交事务
     * @return bool
     */
    public function commit(){
        return $this->driver->commit();
    }
    /**
     * 回滚事务
     * @return bool
     */
    public function rollBack(){
        return $this->driver->rollBack();
    }
    /**
     * 确认是否在事务中
     * @return bool
     */
    public function inTransaction(){
        return $this->driver->inTransaction();
    }

    /**
     * 释放到数据库服务的连接，以便发出其他 SQL 语句(新的参数绑定)，使得该SQL语句处于一个可以被再次执行的状态
     * 当上一个执行的 PDOStatement 对象仍有未取行时，此方法对那些不支持再执行一个 PDOStatement 对象的数据库驱动非常有用。
     * 如果数据库驱动受此限制，则可能出现失序错误的问题
     * PDOStatement::Cursor() 要么是一个可选驱动的特有方法（效率最高）来实现，要么是在没有驱动特定的功能时作为一般的PDO 备用来实现
     * <note>
     *      ① 语意上相当于下面的语句的执行结果
     *          do {
     *              while ($stmt->fetch());
     *              if (!$stmt->nextRowset()) break;
     *          } while (true);
     * </note>
     * @param PDOStatement|null $statement
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE
     */
    public function closeCursor($statement=null){
        isset($statement) and $this->curStatement = $statement;
        return $this->curStatement->closeCursor();
    }

    /**
     * 获取预处理语句包含的信息
     * <note>
     *      ①实际不能获取参数的值，不像文档中写的那样
     *      ②无论是否发生了错误，信息都会存在
     * </note>
     * @return string
     */
    public function getStatementParams(){
        ob_start();//开始本层次的ob缓冲区
        $this->curStatement->debugDumpParams();
        return ob_get_clean();// 相当于ob_get_contents() 和 ob_end_clean()
    }


/************************************** 驱动实现扩展方法 ******************************************************************************************/

    /**
     * 转义保留字字段名称
     * @param string $fieldname 字段名称
     * @return string
     */
    public function escape($fieldname){
        return $this->driver->escape($fieldname);
    }

/************************************** 链式扩展方法 ******************************************************************************************/



    /**
     * 添加数据
     * <code>
     *      $fldsMap ==> array(
     *          'fieldName' => 'fieldValue',
     *          'fieldName' => array('fieldValue',boolean),//第二个元素表示是否对字段名称进行转义
     *      );
     *
     *     $data = ['a'=>'foo','b'=>'bar'];
     *     $keys = array_keys($data);
     *     $fields = '`'.implode('`, `',$keys).'`';
     *     #here is my way
     *     $placeholder = substr(str_repeat('?,',count($keys),0,-1));
     *     $pdo->prepare("INSERT INTO `baz`($fields) VALUES($placeholder)")->execute(array_values($data));
     * </code>
     *
     * 插入数据的sql可以是：
     * ①INSERT INTO 表名称 VALUES (值1, 值2,....)
     * ②INSERT INTO table_name (列1, 列2,...) VALUES (值1, 值2,....)
     *
     * @param string $tablename
     * @param array $fieldsMap
     * @return bool 返回true或者false
     * @throws KbylinException
     */
    public function create($tablename=null,array $fieldsMap=null){

        $fields = $placeholder = '';
        $sql = null;
        $bind  = [];
        $flag = true;//标记是否进行插入形式判断

        $dao  = $this;

        foreach($fieldsMap as $fieldName=>$fieldValue){
            $colnm = $fieldName;
            if($flag){
                if(is_numeric($fieldName)){
                    $placeholder  = rtrim(str_repeat(' ?,',count($fieldsMap)),',');
                    $sql = "INSERT INTO {$tablename} VALUES ( {$placeholder} );";
                    $bind = $fieldsMap;
                    break;
                }
                $flag = false;
            }
            if(is_array($fieldValue)){ //不设置字段名称进行插入时$fieldName无意义
                $colnm = $fieldValue[1]?$dao->escape($fieldName):$fieldName;
                $fieldValue = $fieldValue[0];
            }
            $fields .= " {$colnm} ,";
            $placeholder  .= " :{$fieldName} ,";
            $bind[":{$fieldName}"] = $fieldValue;
        }

        if(isset($sql)){
            $fields = rtrim($fields,',');
            $sql = "INSERT INTO {$tablename} ( {$fields} ) VALUES ( {$placeholder} );";
        }
        return $dao->prepare($sql)->execute($bind);
    }

    /**
     * 更新数据表
     * @param string $tablename
     * @param string|array $flds
     * @param string|array $whr
     * @return bool
     * @throws KbylinException
     */
    public function update($tablename,$flds,$whr){;
        $input_params = [];
        $fields = is_string($flds)?[$flds,[]]:$this->makeSegments($flds,false);
        $where  = is_string($whr) ?[$whr,[]] :$this->makeSegments($whr, false);
        empty($fields[1]) or $input_params = $fields[1];
        empty($where[1]) or array_merge($input_params,$where[1]);
        return $this->prepare("UPDATE {$tablename} SET {$fields[0]} WHERE {$where[0]};")->execute($input_params);
    }

    /**
     * 执行删除数据的操作
     * 如果不设置参数，则进行清空表的操作（谨慎使用）
     * @param string $tablename 数据表的名称
     * @param array $whr 字段映射数组
     * @return bool
     */
    public function delete($tablename,$whr=null){
        $bind = null;
        if(isset($whr)){
            $where  = $this->makeSegments($whr);
            $sql    = "delete from {$tablename} where {$where[0]};";
            $bind   = $where[1];
        }else{
            $sql = "delete from {$tablename};";
        }
        return $this->prepare($sql)->execute($bind);
    }

    /**
     * 查询表的数据
     * @param string $tablename
     * @param string|array|null $fields
     * @param string|array|null $whr
     * @return array|bool
     * @throws KbylinException
     */
    public function select($tablename=null,$fields=null,$whr=null){
        $bind = null;
        //设置选取字段
        if(null === $fields){
            $fields = ' * ';
        }elseif($fields and is_array($fields)){
            //默认转义
            array_map(function($param){
                return $this->driver->escape($param);
            },$fields);
            $fields = implode(',',$fields);
        }elseif(!is_string($fields)){
            throw new KbylinException('Parameter 2 require the type of "null","array","string" ,now is invalid!');
        }

        if(null === $whr){
            $sql = "select {$fields} from {$tablename};";
        }elseif(is_array($whr)){
            $whr  = is_string($whr)? [$whr,null] :$this->makeSegments($whr);
            $sql = "select {$fields} from {$tablename} where {$whr[0]};";
            $bind = $whr[1];
        }elseif(is_string($whr)){
            $sql = "select {$fields} from {$tablename} where {$whr};";
        }else{
            throw new KbylinException('Parameter 3 require the type of "null","array","string" ,now is invalid!');
        }


        if(false === $this->prepare($sql)->execute($bind) ){
            return false;
        }
        return $this->fetchAll();
    }

    /**
     * 综合字段绑定的方法
     * <code>
     *      $operator = '='
     *          $fieldName = :$fieldName
     *          :$fieldName => trim($fieldValue)
     *
     *      $operator = 'like'
     *          $fieldName = :$fieldName
     *          :$fieldName => dowithbinstr($fieldValue)
     *
     *      $operator = 'in|not_in'
     *          $fieldName in|not_in array(...explode(...,$fieldValue)...)
     * </code>
     * @param string $fieldName 字段名称
     * @param string|array $fieldValue 字段值
     * @param string $operator 操作符
     * @param bool $translate 是否对字段名称进行转义,MSSQL中使用[]
     * @return array
     * @throws KbylinException
     */
    private function makeFieldBind($fieldName,$fieldValue,$operator='=',$translate=false){
        $fieldName = trim($fieldName,' :[]');
        $bindFieldName = null;
        if(false !== strpos($fieldName,'.')){
            $arr = explode('.',$fieldName);
            $bindFieldName = ':'.array_pop($arr);
        }elseif(mb_strlen($fieldName,'utf-8') < strlen($fieldName)){//其他编码
            $bindFieldName = ':'.md5($fieldName);
        }else{
            $bindFieldName = ":{$fieldName}";
        }

        $operator = strtolower(trim($operator));
        $sql = $translate?" [{$fieldName}] ":" {$fieldName} ";
        $bind = array();

        switch($operator){
            case '=':
                $sql .= " = {$bindFieldName} ";
                $bind[$bindFieldName] = $fieldValue;
                break;
            case 'like':
                $sql .= " like {$bindFieldName} ";
                $bind[$bindFieldName] = $fieldValue;
                break;
            case 'in':
            case 'not in':
                if(is_string($fieldValue)){
                    $sql .= " {$operator} ({$fieldValue}) ";
                }elseif(is_array($fieldValue)){
                    $sql .= " {$operator} ('".implode("','",$fieldValue)."')";
                }else{
                    throw new KbylinException("The parameter 1 '{$fieldValue}' is invalid!");
                }
                break;
            default:
                throw new KbylinException("The parameter 2 '{$operator}' is invalid!");
        }
        return [$sql,$bind];
    }

    /**
     * 片段设置
     * <note>
     *      片段准则
     *      $map == array(
     *           //第一种情况,连接符号一定是'='//
     *          'key' => $val,
     *          'key' => array($val,$operator,true),
     *          //第二种情况，数组键，数组值//
     *          array('key','val','like|=',true),//参数4的值为true时表示对key进行[]转义
     *          //第三种情况，字符键，数组值//
     *          'assignSql' => array(':bindSQLSegment',value)//与第一种情况第二子目相区分的是参数一以':' 开头
     *      );
     * </note>
     * @param $map
     * @param string $connect 表示是否使用and作为连接符，false时为,
     * @return array
     */
    public function makeSegments($map,$connect='and'){
        //初始值与参数检测
        $bind = [];
        $sql = '';
        if(empty($map)){
            return [$sql,$bind];
        }

        //元素连接
        foreach($map as $key=>$val){
            if(is_numeric($key)){
                //第二种情况
                $rst = $this->makeFieldBind(
                    $val[0],
                    $val[1],
                    isset($val[2])?$val[2]:' = ',
                    !empty($val[3])
                );
                if(is_array($rst)){
                    $sql .= " {$rst[0]} {$connect}";
                    $bind = array_merge($bind, $rst[1]);
                }
            }elseif(is_array($val) and strpos($val[0],':') === 0){
                //第三种情况,复杂类型，由用户自定义
                $sql .= " {$key} {$connect}";
                $bind[$val[0]] = $val[1];
            }else{
                //第一种情况
                $translate = false;
                $operator = '=';
                if(is_array($val)){
                    $translate = isset($val[2])?$val[2]:false;
                    $operator = isset($val[1])?$val[1]:'=';
                    $val = $val[0];
                }
                $rst = $this->makeFieldBind($key,trim($val),$operator,$translate);//第一种情况一定是'='的情况
                if(is_array($rst)){
                    $sql .= " {$rst[0]} {$connect}";
                    $bind = array_merge($bind, $rst[1]);
                }
            }
        }
        $result = array(
            substr($sql,0,strlen($sql)-strlen($connect)),//去除最后一个and
            $bind,
        );
        return $result;
    }
>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
}