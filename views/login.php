<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" > 
<head> 
	<meta http-equiv="content-type" content="text/html;charset=utf-8" /> 
	<title>Student Center | Kingston University</title> 
	<base href="<?php echo url::base(TRUE,'http'); ?>"> 
<?php
	echo StaticJs::instance()->getJsAll();
    echo StaticCss::instance()->getCssAll();
?>
	<link rel="shortcut icon" href="favicon.ico" />
</head> 
<body id="p" class="home full notloggedin "> 
<div id="container"> 
	<div id="header"> 
		<div id="logo-profile">
			<div id="logo"><a href="<?php url::base(); ?>" class="logo"></a></div>
		</div>
		<div class="pad"> 
			<!-- END div#toprow --> 
		</div><!-- END div.pad --> 
<?php echo Message::render(); ?>
		</div><!-- END div#header --> 
<?=$content?>
<div id="footer"> 
		<div class="wrap"> 
			<div class="pad"> 
				<p class="copyright"> 
					&copy; 2011 <a href="<?php url::base(); ?>">Kingston University</a>&nbsp;  <a href="<?php url::base(); ?>">School Business and Technology</a> 
				</p> 
						</div><!-- END div.pad --> 
		</div><!-- END div.wrap --> 
	</div><!-- END div#footer --> 
</div><!-- END div#container --> 
</body> 
</html>