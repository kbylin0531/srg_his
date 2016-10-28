<?php

class TradeKeyCategory extends B2BCategory {

    protected $address = 'http://www1.tradekey.com/index.html?action=buyoffer_postv2';

    private $cate_tmp = [];

    public function getCategoryLeaves() {
        $content = self::get($this->address);
//        if(preg_match_all('/new\s+C\s*\([^\)]*?\)/',$content,$matches)){
//            dumpout($matches[0]);
//        }
        //出现遗漏的几个分类，可以忽略.
        if(preg_match_all('/new\s+C\s*\(\s*\w+?(\d*)?\s*,\s*(\d*)\s*,\s*[\'\"]([^\'\"]*?)[\'\"]\s*\)/',$content,$matches)){
            $parentids = $matches[1];
            $selfids = $matches[2];
            $names = $matches[3];

//            dumpout($parentids,$selfids,$names);

            $this->cate_tmp = [
                0=>[
                    'id'    => '',
                    'name'  => '',
                    'leaf'  => '',
                    'hasChild'  => false,
                ],
            ];
            $count = 10;
            $len = count($matches[0]);
            while($count --){//5次遍历
                for($i = 0 ; $i < $len; $i++){
                    if(!isset($selfids[$i])) continue;//被遍历过

                    $id = $selfids[$i];
                    $name = $names[$i];
                    $pid = $parentids[$i]?$parentids[$i]:0;


                    if(isset($this->cate_tmp[$pid])){
                        $this->cate_tmp[$pid]['hasChild'] = true;
                        if($this->cate_tmp[$pid]['name']){
                            $name = $this->cate_tmp[$pid]['name'].' > '.$name;
                        }
                    }

                    $this->cate_tmp[$id] = [
                        'id'    => $id,
                        'name'  => $name,
                        'leaf'  => $names[$i],
                        'hasChild'  => false,
                    ];
                    unset($parentids[$i],$selfids[$i],$names[$i]);
                }
            }

            foreach ($this->cate_tmp as $k=>&$v){
                if($v['hasChild']){
                    unset($this->cate_tmp[$k]);
                }else{
                    unset($v['hasChild']);
                }
            }

            return array_values($this->cate_tmp);
        }
        return false;
    }

    protected function requestCategory($catID,$level)
    {
    }

    protected function hasChild($node)
    {
    }

}