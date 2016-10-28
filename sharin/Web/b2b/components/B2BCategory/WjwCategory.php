<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-26
 * Time: 下午1:30
 */
class WjwCategory extends B2BCategory {

    private $addr = 'http://www.wjw.com/Services/TradeClassServices.asmx/GetTradeClassClientNavigationtype';
    protected $top_parent_id = 0;

    public function getCategoryLeaves() {
        if(false !== $this->seedCategory($this->top_parent_id)){
            return $this->cate_temp;
        }
        return false;
    }

    private $cate_temp = [];
    private function seedCategory($catID,$level=1,array $parent=[]){
        if(1 === $level) $this->cate_temp = [];
        if($level < 5){
            $list = $this->getCategory($catID,$level);
            if($list and is_array($list)){
                foreach ($list as $item){
                    $id = $item['id'];
                    $name = $item['name'];
                    if($this->hasChild($item,$level)){
                        $this->seedCategory($id,$level+1,$item);
                    }else{
                        empty($parent['name']) or $item['name'] = "{$parent['name']} > {$item['name']}";
                        //抵达末梢
                        $this->cate_temp[$id] = [
                            'name'  => $item['name'],
                            'id'    => $id,
                            'leaf'  => $name,
                        ];
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param $node
     * @param $level
     * @return bool
     */
    protected function hasChild($node,$level){
        static $_childMap = null;
        $key = $this->key($node['id'],$level);
        $tempfile = static::class.'.child.map';
        if(null === $_childMap) {
            $_childMap = Tempper::get($tempfile,[]);
//            dump('loadFrom cache',$_childMap);
        }
        if(!isset($_childMap[$key])){
            $content = $this->getCategory($node['id'],$level);
            $_childMap[$key] = $content?1:0;
//            dump('build cache',$content);
            Tempper::set($tempfile,$_childMap);
        }
//        dumpout($_childMap[$key]);
        return $_childMap[$key];
    }


    private function key($id,$level){
        return "[$id]-[$level]";
    }

    protected function requestCategory($catID,$level){
        $content = self::post($this->addr,json_encode([
                'tradeClassId'      => $catID,
                'tradeClassIstring'  => '3,4,53,62,75,86,91,100,110,122,146,203,208,217,230,284,313,327,336,366,373,377,378,387',//竟然是固定的
            ]),'',false,[],[
                'Content-Type: application/json; charset=gb2312',
            ]);

        $content = str_replace([
                '\'','&',
            ],[
                '"','&amp;'
            ],trim($content,' ";/*'));

        $content = json_decode($content,true);
        if(!isset($content['d'])){
            return false;
        }
        $dom=new DOMDocument('1.0');
        $content = trim(str_replace('&','&amp;',$content['d']));
        if(!$dom->loadXML("<asura>$content</asura>")){
            throw new Exception('加载失败');
        }
        $content = self::xml2ArrayInAdv($content);
        $root = $leaves = null;
        if(!empty($content['div'])){
            foreach ($content['div'] as $div) {
                if(empty($div['attrs']['id'])) continue;
                if($div['attrs']['id'] === 'chromemenuroot'){
                    $root = $div['children'];
                }elseif($div['attrs']['id'] === 'dropmenu0'){
                    $leaves = $div['children'];
                }
            }
        }

        $target = $leaves?$leaves:$root;
        if(!$target) {
            throw new Exception('无法获取参数');
        }

        $result = [];
        if(!empty($target['a']) and !empty($target['a'][0])){
            $flag = true;
            foreach ($target['a'] as $a) {
                if(preg_match('/\'(\d+)\'/',$a['attrs']['onclick'],$mats)){
                    $id = $mats[1];
                    if($flag){
                        if($catID == $id){
                            //获取的就是自己,之前必定获取过了?yes
                            return false;//没有子元素了
                        }
                        $flag = true;
                    }
                    $result[] = [
                        'id'    => $id,//ids
                        'name'  => $a['attrs']['title'],//names
                    ];
                }
            }
            return $result;
        }else{
            throw new Exception('空的列表');
        }
    }
}