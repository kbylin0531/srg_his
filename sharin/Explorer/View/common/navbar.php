<div class="init_loading"><div><img src="./static/images/loading_simple.gif"/></div></div>
<div class="topbar">
	<div class="content">
		<div class="top_left">
			<a href="./" class="topbar_menu title"><i class="icon-cloud"></i><?php echo $L['kod_name'];?></a>
		<?php 
			foreach ($config['setting_system']['menu'] as $key=>$value) {
				if ($value['use']!='1') continue;
				$has = ST==$value['name']?'this':'';

				$target = " target='".$value['target']."'" ;
				if ($value['type'] == 'system') {					
					$value['name'] = "<i class='font-icon menu-".$value['name']."'></i>".$L['ui_'.$value['name']];
				}
				echo "<a class='topbar_menu ".$has."' href='".urldecode($value['url'])."'"
					.$target.">".urldecode($value['name'])."</a>";
			}
		?>
		</div>
		<div class="top_right">
			<div class="menu_group">
				<a id='topbar_language' data-toggle="dropdown" href="#" class="topbar_menu">
				<i class='font-icon icon-flag'></i>&nbsp;<b class="caret"></b>
				</a>
				<ul class="dropdown-menu topbar_language fadein pull-right" role="menu" aria-labelledby="topbar_language">
				  	<?php 
						$tpl="<li><a href='javascript:core.language(\"{0}\");' class='{this}'>{1}</a></li>";
						echo getTplList(',',':',$config['setting_all']['language'],$tpl,LANGUAGE_TYPE);
					?>
				</ul>
			</div>
			<div class="menu_group">
				<a href="#" id='topbar_user' data-toggle="dropdown" class="topbar_menu"><i class="font-icon icon-user"></i><?php echo $_SESSION['kod_user']['name'];?>&nbsp;<b class="caret"></b></a>
				<ul class="dropdown-menu menu-topbar_user fadein pull-right" role="menu" aria-labelledby="topbar_user">
					<?php if($_SESSION['kod_user']['role']=='root'){ ?>
						<li><a href="javascript:core.setting('system');"><i class="font-icon icon-cog"></i><?php echo $L['system_setting'];?></a></li>
						<li><a href="javascript:core.setting('member');"><i class="font-icon icon-group"></i><?php echo $L['setting_member'];?></a></li>
					<?php } ?>
					<li><a href="javascript:core.setting('user');"><i class="font-icon icon-user"></i><?php echo $L['setting_user'];?></a></li>
					<li><a href="javascript:core.setting('theme');"><i class="font-icon icon-dashboard"></i><?php echo $L['setting_theme'];?></a></li>
					<li><a href="javascript:core.fullScreen();"><i class="font-icon icon-fullscreen"></i><?php echo $L['full_screen'];?></a></li>
					<li><a href="javascript:core.setting('help');"><i class="font-icon icon-question"></i><?php echo $L['setting_help'];?></a></li>
					<li><a href="javascript:core.setting('about');"><i class="font-icon icon-info-sign"></i><?php echo $L['setting_about'];?></a></li>
					<li role="presentation" class="divider"></li>
					<li><a href="./<?php echo ENTRY_NAME; ?>?user/logout"><i class="font-icon icon-signout"></i><?php echo $L['ui_logout'];?></a></li>
				</ul>
			</div>
		</div>
		<div style="clear:both"></div>
	</div>
</div>