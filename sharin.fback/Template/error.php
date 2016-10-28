<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Error occur</title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        html{ overflow-y: scroll; }
        body{ background: #fff;  color: #333; font-size: 16px; }
        img{ border: 0; }
        .error{ padding: 24px 48px; }
        h1{ font-size: 32px; line-height: 48px; }
        .error .content{ padding-top: 10px}
        .error .info{ margin-bottom: 12px; }
        .error .info .title{ margin-bottom: 3px; }
        .error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }
        .error .info .text{ line-height: 24px; }
        .copyright a{ color: #000; text-decoration: none; }
    </style>
</head>
<body>
<div class="error">
    <h1>
        <?php if(isset($message))echo strip_tags($message);?>
    </h1>
    <div class="content">
        <?php if(isset($position)) {?>
            <div class="info">
                <div class="title">
                    <h3>Location：</h3>
                </div>
                <div class="text">
                    <p>
                        <?php echo $position;?>
                    </p>
                </div>
            </div>
        <?php }?>
        <?php if(isset($trace)) {?>
            <div class="info">
                <div class="title">
                    <h3>Backtrace：</h3>
                </div>
                <div class="text">
                    <p><?php \Sharin\println($trace); ?></p>
                </div>
            </div>
        <?php }?>
    </div>
</div>
</body>
</html>
