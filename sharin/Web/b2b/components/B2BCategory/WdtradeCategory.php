<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-26
 * Time: 下午5:33
 */
class WdtradeCategory extends B2BCategory {

    protected $seedAddr = 'http://www.wdtrade.com/selectcategory.aspx?catalog=&catalogid=&path=1&catId=';

    public function getCategoryLeaves(){
        $content = self::get($this->seedAddr.'0');
        $result = [];
        if(preg_match_all('/<a\shref=[\'\"](selectcategory.aspx\?catId=(\d+).*?)[\'\"]>(.*?)<\/a>/',$content,$matches)){
            $len = count($matches[0]);
            for($i = 0 ; $i < $len; $i++){
                $url= $matches[1][$i];
                $id = $matches[2][$i];
                $name=$matches[3][$i];
                $list = $this->getUrlContent($id,$url,$name);
                foreach ($list as $item){
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    private function getUrlContent($catid,$url,$parentnm){
        static $_cache = null;
        $cachefile = static::class.'.urltemp';
        if(null === $_cache){
            $_cache = Tempper::get($cachefile,[]);
        }
        if(!isset($_cache[$catid])){
            $content = self::get("http://www.wdtrade.com/{$url}");
            $colle = [];
            if(preg_match_all('/selectcontrol\(this,\'(.*?)\'.*?value=\'(\d+)\'/',$content,$ms)){
                $len = count($ms[0]);
                for ($i = 0 ; $i < $len; $i++){
                    $id = $ms[2][$i];
                    $colle[$id] = [
                        'id'        => $id,
                        'name'      => $parentnm.' > '.$ms[1][$i],
                        'leaf'      => $ms[1][$i],
                    ];
                }
            }
            $_cache[$catid] = $colle;
            Tempper::set($cachefile,$_cache);
        }
        return $_cache[$catid];
    }

}