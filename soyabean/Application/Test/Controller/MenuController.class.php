<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/7/1
 * Time: 15:56
 */

namespace Application\Test\Controller;
use Application\System\Config\Model\MenuItemModel;
use Application\System\Config\Model\MenuModel;

/**
 * Class MenuController
 * @package Application\Test\Controller
 */
class MenuController {
    /**
     * @var MenuModel
     */
    protected $model = null;

    public function index(){
//        $this->model = new MenuModel();
//        $this->model = new MenuItemModel();
//        $this->testMenuItem();

        $this->model = new MenuModel();
        $this->testMenu();

        \Soya\dumpout($this->model->error());
    }

    public function testMenuItem(){
//        $result = $this->model->createMenuItem([
//            'title' => '测试标题',
//            'value' => 'dsds',
//            'icon'  => 'fass',
//        ]);
//        $result = $this->model->createMenuItem([]);//返回false并提示没有要插入的数据（一切准村默认是错误的做法）
//        $result = $this->model->deleteMenuItem(3);
//        $result = $this->model->updateMenuItem([
//            'id'    => 3,
//            'title' => '32323 bnea',
//        ]);
        $result = $this->model->selectMenuItem();
        $result2 = $this->model->selectMenuItem(true);
        $result3 = $this->model->hasMenuItem(1);

        \Soya\dump($result,$result2,$result3);

    }


    public function testMenu(){

//        \Soya\dump($this->model->getHeaderMenu());
//        \Soya\dump($this->model->getSidebarMenu());
//        \Soya\dump($this->model->selectSideMenu());
//        \Soya\dump($this->model->deleteSideMenu(12));
        \Soya\dump($this->model->updateMenu([
            'id'    => 2,
            'value' => serialize([
                [
                    'id'    => 2,
                ],
                [
                    'id'    => 1,
                ]
            ]),
        ]));
//        \Soya\dump($this->model->createSidedMenu([
//            'title' => '其他',
//            'value' => [
//                [
//                    'id'    => 2,
//                    'children'  => [
//                        [
//                            'id'    => 3,
//                        ],
//                        [
//                            'id'    => 4,
//                        ],
//                    ]
//                ],
//            ],
//            'icon'  => 'dsds',
//            'order' => null,
//            'status'=> null,
//        ]));
//        $value = [
//            [
//                'id'    => 2,
//            ],
//            [
//                'id'    => 5,
//                'children'  => [
//                    [
//                        'id'    => 6,
//                        'children'  => [
//                            [
//                                'id'    => 7,
//                                'children'  => [
//                                    [
//                                        'id'    => 3,
//                                    ],
//                                    [
//                                        'id'    => 4,
//                                    ],
//                                ]
//                            ],
//                        ]
//                    ],
//                ]
//            ],
//        ];
//        \Soya\dump(serialize($value),$this->model->updateMenu([
//            'id'    => 1,
//            'value' => serialize($value),
//        ]));
//        \Soya\dump($this->model->deleteSideMenu(6));
    }

}