<?php
namespace {

    use Sharin\Core\Storage;

    include __DIR__.'/../../Sharin/console.module';

    $newdir = SR_PATH_RUNTIME.'/Sharin2';
    $dir = SR_PATH_BASE.'/Sharin';

    Storage::unlink($newdir);
    $files = Storage::readDir($dir,true);
    \Sharin\dump(count($files));
//    $replace = '.class.php';
    const FROM = [
        '.srg',
    ];
    const TO = [
        '.psrg',
    ];
    $ignores = [
        //如果以此开头，则原样拷贝
        '/Vendor','/Plugins'
    ];

    foreach ($files as $file=>$path){
        if(is_dir($path)){
            continue;
        }
        $do = true;
        foreach ($ignores as $ignore){
            if(strpos($file,$ignore) === 0){
                $do  = false;break;
            }
        }
        if($do){
            $file = str_replace(FROM,TO,$file);
//            if(is_array($replace)){
//                foreach ($replace as $rpl){
//                    strpos($file,$rpl) and $file = str_replace($rpl,'.class.php',$file);
//                }
//            }else{
//                strpos($file,$replace) and $file = str_replace($replace,'.srg',$file);
//            }
        }
        $newpath = $newdir.$file;//str_replace('.class.php','');

        $dir = dirname($newpath);
        is_dir($dir) or mkdir($dir,0777,true);
        if(!copy($path,$newpath)){
            throw new Exception(" $path =X-> $newpath ");
        }
    }

}


