<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/7/16
 * Time: 6:10 PM
 */

namespace Application\Home\Controller;
use Application\System\Common\Library\HomeController;
use Soya\Core\URI;
use Soya\Extend\Page;
use Soya\Util\SEK;

/**
 * Class MaterialController 素材管理
 * @package Application\Home\Controller
 */
class MaterialController extends HomeController {

    public function test(){
        echo 'test';
    }

    private function getNav() {
        $nav = [];
        $act = strtolower ( REQUEST_ACTION );
//        $param = array('mdm'=>I('mdm'));
//        \Soya\dumpout($act,stripos ( $act, 'material' ) !== false  );
        //& 前面为选择请，后面为属性，如果属性为空，则为innerHTML
        $res ['a&'] = '图文素材';
        $res ['a&href'] =  URI::url( 'materialLists' );
        $res ['li&class'] = stripos ( $act, 'material' ) !== false  ? 'current' : '';
        $nav [] = $res;

        $res ['a&text'] = '图片素材';
        $res ['a&href'] = URI::url ( 'pictureLists' );
        $res ['li&class'] = stripos ( $act, 'picture' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['a&'] = '语音素材';
        $res ['a&href'] = URI::url ( 'voiceLists' );
        $res ['li&class'] = stripos ( $act, 'voice' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['a&'] = '视频素材';
        $res ['a&href'] = URI::url ( 'videoLists' );
        $res ['li&class'] = stripos ( $act, 'video' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['a&'] = '文本素材';
        $res ['a&href'] = URI::url ( 'textListsData' );
        $res ['li&class'] = stripos ( $act, 'text' ) !== false ? 'current' : '';
        $nav [] = $res;
        return $nav;
    }

    protected function show($template=null){
        $nav = $this->getNav();
        $this->assign('info',json_encode([
            '.nav_item'     => $nav,
            '.subnav_item'  => [],
            '.snitem' => [
                [
                    'img&src'   => __PUBLIC__.'/assets/app/home/images/ico1.png',
                    '.sidenav_parent>span'      => '管理',
                    '.snsubitem'  => [
                        [
                            'a&href'    => URI::url ( 'materialLists' ),
                            'span'      => '素材管理'
                        ],
                    ]
                ],
            ],
        ]));
//        \Soya\dumpout($nav);
        null === $template and $template = SEK::backtrace(SEK::ELEMENT_FUNCTION,SEK::PLACE_FORWARD);
        parent::show($template);
    }

    public function materialLists(){
        $this->show();
    }


    public function add(){
        $this->show();
    }

    public function addMaterial(){
        $this->show();
    }

    public function materialData(){
        $this->show();
    }

    public function newsDetail(){
        $this->show();
    }

    public function pictureData(){
        $this->show();
    }

    public function pictureLists(){
        $this->show();
    }

    public function textListsData(){
        $this->show();
    }

    public function videoData(){
        $this->show();
    }

    public function videoLists(){
        $this->show();
    }

    public function voiceData(){
        $this->show();
    }

    public function voiceLists(){
        $this->show();
    }


}