<?php

defined('BG_CATE_TEMP') or define('BG_CATE_TEMP',PATH_RUNTIME.'/temp/');
defined('BG_CATE_DATA_MAP') or define('BG_CATE_DATA_MAP',PATH_DATA.'/maps/');

/**
 * Class B2BCategoryGetter B2B群发平台行业目录获取
 */
abstract class B2BCategory extends B2BPlatform{
    /**
     * @var array 平台ID和实现类的对应关系
     */
    private static $platformMap = [];

    public static function import(array $pfconf){
        foreach ($pfconf as $key=>$item) {
            self::$platformMap[$key] = $item;
        }
    }

    protected $address = '';
    /**
     * @var mixed 行业目录第一层的parentid，不同的平台要求的值可能不一样（有的是0有的是空字符串）
     */
    protected $top_parent_id = 0;
    /**
     * @var int 行业目录的最深层次（防止程序bug导致的无线嵌套引起程序崩溃）
     */
    protected $max_level = 5;

    /**
     * @var B2BCategory[]
     */
    private static $instances = [];
    /**
     * @var B2BCategorySaveInterface
     */
    protected $driver = null;

    public function __construct(){
        $drivernm = CATE_DATA_SAVER;
        $this->driver = new $drivernm();
    }

    final public static function getPlatformMap($code=null){
        $data = self::$platformMap;
        unset($data[0]);
        if(isset($code) and isset($data[$code])){
            return $data[$code];
        }
        return $data;
    }

    /**
     * 获取平台实例
     * @param int|string $pfid 平台ID
     * @return B2BCategory
     * @throws Exception
     */
    final public static function getInstance($pfid){
        if(!isset(self::$instances[$pfid])){
            if(!isset(self::$platformMap[$pfid])) throw new Exception("不存在ID号为'{$pfid}'的平台");
            $clsnm = self::$platformMap[$pfid][0];
            self::$instances[$pfid] = new $clsnm();
        }
        return self::$instances[$pfid];
    }

    /**
     * 将XML转为array
     * @param string $xml
     * @return array|false 解析失败返回false
     */
    protected static function xml2Array($xml) {
        libxml_disable_entity_loader(true);
        $val = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $val? $val : false;
    }

    /**
     * @param DOMNode $topnode
     * @return array
     */
    public static function travel(DOMNode $topnode){
        $values = [];
        if($topnode->hasChildNodes()) foreach ($topnode->childNodes as $node){
            $name = $node->nodeName;
            if($name === '#text'){
                continue;
            }
            //get basic info
            $info = [
                'name'  => $name,
                'attrs' => [],
                'children'  => [],
            ];
            //travel attr
            foreach ($node->attributes as $attr){
                $info['attrs'][$attr->nodeName] = $attr->nodeValue;
            }
            if(method_exists($node,'hasChildNodes') and $node->hasChildNodes()){
                $info['children'] = self::travel($node);;
            }
            if(!isset($values[$name])) $values[$name] = [];
            $values[$name][] = $info;
        }
        return $values;
    }

    /**
     * 进一步解析属性
     * @param $xml
     * @return array
     */
    protected static function xml2ArrayInAdv($xml){
        $dom=new DOMDocument('1.0');
        $xml = trim(str_replace('&','&amp;',$xml));
        if(!$dom->loadXML("<asura>$xml</asura>")){
            return [];
        }
        return self::travel($dom->firstChild);
    }


    protected static function json2Array($json){
        return json_decode($json,true);

    }

    /**
     * 缓存分类数据
     * @param string|int $catid 上级分类ID
     * @param int $level 分类层级
     * @param bool|array $data 是否加载，false时表示缓存数据
     * @return bool|array
     */
    protected static function cacheCategory($catid,$level=null,$data=false){
        $clsnm = static::class;
        if('' === $catid) $catid = 0;
        if(null === $level){
            $level = '';
        }else{
            $level = '-'.$level;
        }
        $file = PATH_RUNTIME."/temp/{$clsnm}/{$catid}{$level}.php";//注意的是类名称不能带命名空间
        if(false === $data){
            if(is_file($file)){
                return include $file;
            }else{
                return false;
            }
        }else{
            return self::saveArrayInto($file,$data);
        }
    }

    /**
     * 获取映射关系
     * @return array
     */
    public function getMapping(){
        return $this->driver->get(static::class);
    }

    /**
     * @param $id
     * @return bool
     */
    public function removeMapping($id){
        $map = $this->getMapping();
        unset($map[$id]);
        return $this->saveMapping($map,true);
    }

    protected static $saved_keys = [];

    /**
     * @param int|string $id
     * @return bool
     */
    public function isSaved($id){
        $clsnm = static::class;
        if(!isset(self::$saved_keys[$clsnm])){
            self::$saved_keys[$clsnm] = $this->getMapping();
        }
        return isset(self::$saved_keys[$clsnm][$id]);
    }

    /**
     * 保存映射关系
     * @param array $mapping 映射关系，健为bossgoo的行业ID，值中的name为bossgoo的行业名称，cateid为对应平台的行业ID，catename为对应平台的行业名称
     * @param bool $clear 是否直接覆盖，如果是则不读取原先的数据(直接算作空)直接写入参数1的映射关系
     * @return bool
     */
    public function saveMapping(array $mapping, $clear=false){
        $data = $clear?[]:$this->getMapping();
        if(!$data){
            $data = $mapping;
        }else{
            //数字健名不使用array_merge
            foreach ($mapping as $key=>$value){
                $data[$key] = $value;
            }
        }
        return $this->driver->set(static::class,$data);
    }

    /**
     * 将数组保存进文件中
     * @param string $file 文件路径
     * @param array $array 保存的数据
     * @return bool
     */
    private static function saveArrayInto($file,array $array){
        SEK::touch($file);
        return file_put_contents($file,'<?php return '.var_export($array,true).';')?true:false;
    }

    /**
     * 获取末梢列表
     * 列表的健是行业ID，值是行业ID和名称在内的行业信息数组
     * @return array|bool
     */
    abstract public function getCategoryLeaves();

    /**
     * 获取单个分类的子分类
     * @param int $catID 上级分类ID ，没有上级分类默认为0
     * @param int $level 分类层级，默认获取第一层级的分类列表
     * @return array|false
     */
    public function getCategory($catID,$level=null){
        CATE_CACHE and $data = self::cacheCategory($catID,$level,false);
        if(empty($data)){
            $data = $this->requestCategory($catID,$level);
            CATE_CACHE and self::cacheCategory($catID,$level,$data);
        }
        return $data;
    }
    /**
     * 请求获取子分类数据数据
     * @param mixed $catID
     * @param $level
     * @return bool|array
     */
    protected function requestCategory($catID,$level){
        return false;
    }

}