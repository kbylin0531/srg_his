<?php


return [
    'STATIC_ROUTE_ON'   => false,
    'WILDCARD_ROUTE_ON' => true,
    'WILDCARD_ROUTE_RULES'    => [
//        '/wechat/[num]'   => [
//            'm' => 'Wechat',
//            'c' => 'Index',
//            'a' => 'index',
//            '$1'    => 'p.id',
//        ],
        '/wechat/[num]'   => function($id){
            $wechat = new \Application\Wechat\Common\Library\Wechat($id);
            \Soya::closeTrace();
            if(isset($_GET['echostr'])){
                //valid
                if($wechat->checkSignature()){
                    exit($_GET['echostr']);
                }
            }else{
                $message = new \Application\Wechat\Common\Library\MessageInterface();
                $message->receive() and $message->response(function($type,$entity)use($message){
                    $content = "消息类型是'$type':   \n消息体：";
                    return $message->responseText($content.var_export($entity,true));
                });
            }
            exit();
        },
    ],
];