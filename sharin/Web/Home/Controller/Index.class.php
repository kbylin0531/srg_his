<?php
namespace Web\Home\Controller;
use Sharin\Core\Storage;

class Index {

    public function index(){
        $dir = Storage::readDir(SR_PATH_APP);
        \Sharin\dumpout($dir);
    }

}