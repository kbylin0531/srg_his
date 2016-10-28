<?php

/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/19/16
 * Time: 11:02 AM
 */

class CategoryController extends SController {

//------------------------------------- 页面 --------------------------------------------------------------------//

    public function __construct($id, $module){
        parent::__construct($id, $module);
        B2BCategory::import(CATE_CONF);
    }

    /**
     * Index
     */
    public function actionIndex(){
        $this->render('index',[
            'map'   =>  B2BCategory::getPlatformMap(),
        ]);
    }

    /**
     * @param int $code platform code
     */
    public function actionSet($code){
        $info = B2BCategory::getPlatformMap($code);
        $this->render('set',[
            'code' =>  $code,
            'url'   => $info[1],
        ]);
    }

    /**
     * @param int $code
     */
    public function actionDone($code){
        $info = B2BCategory::getPlatformMap($code);
        $this->render('done',[
            'code' =>  $code,
            'url'   => $info[1],
        ]);
    }

//------------------------------------- 异步调用 --------------------------------------------------------------------//

    /**
     * 获取平台行业目录末梢分类
     * @param $code
     * @param $b2bcode
     */
    public function actionList($code,$b2bcode=0){
        $platform = B2BCategory::getInstance($code);
        $setting = B2BCategory::getPlatformMap($code);
        define('CATE_USE_CATE',!empty($setting[3])?1:0);
        if(CATE_USE_CATE){
            //开发模式
            if(($data = Tempper::get($code,false)) === false){
                $data = $platform->getCategoryLeaves();
                Tempper::set($code,$data);
            }
        }else{
            $data = $platform->getCategoryLeaves();
        }

        if($b2bcode){
            $b2bc = B2BCategory::getInstance($b2bcode);
            //bossgoo 删除已经添加的行业分类
            $saved = $b2bc->getMapping();
            foreach ($saved as $k=>$v){
                unset($data[$k]);
            }
        }
        SEK::ajaxBack(array_values($data));
    }

    /**
     * 获取已经保存的列表
     * @param $code
     */
    public function actionSavedList($code){
        $b2bc = B2BCategory::getInstance($code);
        SEK::ajaxBack(array_values($b2bc->getMapping()));
    }

    public function actionRemoveSaved($id,$code){
        $b2bc = B2BCategory::getInstance($code);
        SEK::ajaxBack([
            'type'  => $b2bc->removeMapping($id)?1:0,
        ]);
    }

    /**
     * 自动匹配
     * @param int $code b2b代号
     */
    public function actionAutoFetch($code){
        //获取bossgoo列表
        $bglist = B2BCategory::getInstance(0)->getCategoryLeaves();
        //获取其他b2b平台数据
        $b2bc = B2BCategory::getInstance($code);
        if(CATE_USE_CATE){
            //开发模式
            if(($b2blist = Tempper::get($code,false)) === false){
                $b2blist = $b2bc->getCategoryLeaves();
                Tempper::set($code,$b2blist);
            }
        }else{
            $b2blist = $b2bc->getCategoryLeaves();
        }

        //bossgoo 删除已经添加的行业分类
        $saved = $b2bc->getMapping();
        foreach ($saved as $k=>$v){
            unset($bglist[$k]);
        }

        $map = call_user_func_array([$this,'getSimilar04'],[$bglist,$b2blist]);
        SEK::ajaxBack([
            'type'  => $b2bc->saveMapping($map)?1:0,
            'count' => count($map),
        ]);
    }

    protected function getSimilar04($bglist,$b2blist){
        $map = [];
        foreach ($bglist as $item) {
            //比较算法
            $bgname = $this->_buildName($item);
            $max = [
                0,//用于比较最高匹配度
                [],//info
                '',//key
            ];
            foreach ($b2blist as $k => $item2) {
                $b2bname = $this->_buildName($item2);
                similar_text($bgname,$b2bname,$pt);
//                $info['c'] ++;
                if($pt >= SIMILAR_SCALA ){//相似度达到一定值
//                    $info['c2'] ++;
                    if($pt > $max[0]){//匹配度最高
                        $max[0] = $pt;
                        $max[1] = $item2;
                        $max[2] = $k;
                    }
                }
            }

            if($max[0]){//存在匹配
                $i = $max[1];
                $k = $max[2];
                $map[$i['id']] = [
                    'id'        => $i['id'],
                    'name'      => $i['name'],
                    'cateid'    => $item['id'],
                    'catename'  => $item['name'],
                ];
            }
        }
        return $map;
    }

    protected function getSimilar02($bglist,$b2blist){
        $map = [];
        $info = [
            '$bglist' => count($bglist),
            '$b2blist'=> count($b2blist),
            'c'         => 0,
            'c2'         => 0,
        ];
        foreach ($b2blist as $item) {
            $b2bname = $this->_buildName($item);
            $max = [
                0,//用于比较最高匹配度
                [],//info
                '',//key
            ];
            foreach ($bglist as $k=>$i) {
                //比较算法
                $bgname = $this->_buildName($i);
                similar_text($bgname,$b2bname,$pt);
//                $info['c'] ++;
                if($pt >= SIMILAR_SCALA ){//相似度达到90%
//                    $info['c2'] ++;
                    if($pt > $max[0]){//匹配度最高
                        $max[0] = $pt;
                        $max[1] = $i;
                        $max[2] = $k;
                    }
                }
            }

            if($max[0]){//存在匹配
                $i = $max[1];
                $k = $max[2];
                $map[$i['id']] = [
                    'id'        => $i['id'],
                    'name'      => $i['name'],
                    'cateid'    => $item['id'],
                    'catename'  => $item['name'],
                ];
                unset($bglist[$k]);//释放解锁完成的
            }
        }
        return $map;
    }

    protected function getSimilar03($bglist,$b2blist){
        $map = [];
        foreach ($b2blist as $item) {
            $b2bname = $this->_buildName($item);
            foreach ($bglist as $k=>$i) {
                //比较算法
                $bgname = $this->_buildName($i);
                similar_text($bgname,$b2bname,$pt);
                if($pt >= SIMILAR_SCALA ){//相似度达到90%
                    $map[$i['id']] = [
                        'id'        => $i['id'],
                        'name'      => $i['name'],
                        'cateid'    => $item['id'],
                        'catename'  => $item['name'],
                    ];
                    unset($bglist[$k]);//释放解锁完成的
                }
            }
        }
        return $map;
    }

    /**
     * 字符完全匹配
     * @param $bglist
     * @param $b2blist
     * @return array
     */
    protected function getSimilar01($bglist,$b2blist){
        $temp = [];
        foreach ($bglist as $item) {
            //比较算法
            $key = $this->_buildName($item);
            $temp[$key] = $item;
        }

        $map = [];
        foreach ($b2blist as $item){
            $key = $this->_buildName($item);
            if(isset($temp[$key])){
                $map[$temp[$key]['id']] = [
                    'id'        => $temp[$key]['id'],
                    'name'      => $temp[$key]['name'],
                    'cateid'    => $item['id'],
                    'catename'  => $item['name'],
                ];
            }
        }
        return $map;
    }

    /**
     * 产品名称处理
     * 删除 > & ..等不相关的字符
     * @param $node
     * @return string
     */
    private function _buildName($node){
        return trim(strtolower(str_replace(
            [
                ' ',
                '&',
                '>'
            ],
            '',
            $node['leaf'])));
//        $str = strtolower(str_replace([
//            '','>'
//        ],'',$node['leaf']));
//        if(strpos($str,'&')!==false){
//            $arr = explode('&',$str);
//            if(count($arr) > 1){
//                sort($arr,SORT_STRING);
//                $str = implode('',$arr);
//            }
//        }
//        return $str;
    }

    /**
     * 保存映射关系
     * @param int|string $code
     */
    public function actionSave($code){
        $data = $_POST['data'];
        $b2b = B2BCategory::getInstance($code);
        if($data and is_array($data)){
            $temp = [];
            foreach ($data as $item){
                $id = $item['id'];
                $temp[$id] = [
                    'id'        => $id,
                    'name'      => $item['name'],
                    'cateid'    => $item['id2'],
                    'catename'  => $item['name2'],
                ];
            }
            $b2b->saveMapping($temp)?
                SEK::ajaxBack([
                    'type'  => 1,
                    'msg'   => '保存成功',
                ]): SEK::ajaxBack([
                'type'  => 0,
                'msg'   => '保存失败',
            ]);
        }
    }
}