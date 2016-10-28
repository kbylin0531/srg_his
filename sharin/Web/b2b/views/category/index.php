<div class="container">

    <h1>B2B Platform</h1>
    <ol>
        <?php
        if(!empty($map)){
            foreach ($map as $code=>$item) {
                $name = $item[0];
                $url = isset($item[1])?$item[1]:'';
                if(!empty($item[2])){
                    $name .= "({$item[2]})";
                }
                echo '<li><code><a href="'.SCRIPT_URL.'/category/set?code='.$code.'" target="_blank">'.$name.'</a></code>&nbsp;&nbsp;<code><a href="http://'.$url.'" target="_blank">'.$url.'</a></code></li>';
            }
        }
        ?>
    </ol>
</div>
