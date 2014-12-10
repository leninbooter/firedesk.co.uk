<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Firedesk System</title>
<link href='http://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>
<link rel='stylesheet' type='text/css' href='./css/main.css<?php echo '?ver=1.0.'.filemtime(__DIR__.'/css/main.css');?>'>
<link rel='stylesheet' type='text/css' href='./css/jquery-ui-1.10.3.custom.css<?php echo '?ver=1.0.'.filemtime(__DIR__.'/css/jquery-ui-1.10.3.custom.css');?>'>
<link rel='stylesheet' type='text/css' href='./css/jquery.toolbars.css<?php echo '?ver=1.0.'.filemtime(__DIR__.'/css/jquery.toolbars.css');?>'>
<link rel='stylesheet' type='text/css' href='./css/bootstrap.icons.css<?php echo '?ver=1.0.'.filemtime(__DIR__.'/css/bootstrap.icons.css');?>'>
<link rel='stylesheet' type='text/css' href='./css/jquery.ui.timepicker.css<?php echo '?ver=1.0.'.filemtime(__DIR__.'/css/jquery.ui.timepicker.css');?>'>
<script type='text/javascript' src='./scripts/all.min.js<?php echo '?ver=1.0.'.filemtime(__DIR__.'/scripts/all.min.js');?>'></script>
<script type='text/javascript' src='./scripts/main.js<?php echo '?ver=1.0.'.filemtime(__DIR__.'/scripts/main.js');?>'></script>
<!-- Preloader -->
<script type="text/javascript">
    //<![CDATA[
        $(window).load(function() 
		{ // makes sure the whole site is loaded
            $('#status').fadeOut(); // will first fade out the loading animation
            $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
            $('body').delay(350).css({'overflow':'visible'});
        })
    //]]>
</script>
</head>
<body>
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<?php
ini_set('display_errors', 'On');
//getting tabs content:
require_once(__DIR__.'/server_scripts/tab_worker.php');
require_once(__DIR__.'/server_scripts/error_checker.php');
$check_hand=new sys_checker();
//$check_hand->check_dir_permissions(__DIR__,array('server_scripts','scripts','css'),true,array('read','read','read'),false);
//$check_hand->check_files_permissions(__DIR__, 'read',false);
if (isset($_GET['sys_ch'])) $check_hand->read_cm($_GET['sys_ch'],__DIR__.'/');
unset($check_hand);
?>

<div class='site_main_wrapper'>
<?php
if (!isset($_COOKIE['user']))
{
?>
<img id='login_logo' src='./css/images/login-screen-logo.jpg'/>
<div id='login_wrapper' style='font-size:14pt; font-weight:bold; text-align:center; position:absolute; top:30%; left:40%; border: 1px solid #006895; padding:20px; border-radius:20px; background-color: rgba(46, 132, 168, 0.05);'>
Login:
<form name='log_form' id='log_form' method='post' action='/server_scripts/login.php'>
<table id='log_tabl'>
<tr>
<td>Username:</td>
<td><input type='text' name='log_usr_name'/></td>
</tr>
<tr>
<td>Password:</td>
<td><input type='password' name='log_pass'/></td>
</tr>
</table>
<input type='submit' value='Log in' id='log_submit' style='margin-top:10px;'/>
</form>
<script type='text/javascript'>
$('#log_tabl tr td:even').css('text-align','right');
$('#log_submit').button();
$('#log_form').ajaxForm({url:'./server_scripts/login.php',type:'POST', success:function (data)
	                    {
						if ($.trim(data).indexOf('Welcome')!=-1) 
						     {
							 $('#log_form').resetForm();
							 window.location.reload();
							 }
							 else alert(data);
						$('#log_submit').removeAttr('disabled');
						},beforeSubmit: function ()
						      {
							  $('#log_submit').attr('disabled', 'disabled');
							  }});
</script>
</div>
<?php
echo '</div></body></html>';
exit;
}
else
{
$admin_ac=false;
$emp_logged=false;
$user_only_view_allowed=false;
$show_name='';
$cookie_code=$_COOKIE['user'];
$conn=db_connect();
get_active_user_info();
check_cookie($cookie_code,$conn,'close');
}
?>
<div class='status_up_bar'><span style='float:left;'>Welcome, <?php echo $show_name;?></span> <span id='logout_but'>Logout</span></div>

<div id="tabs">
<ul>
<?php
if ($user_only_view_allowed and !$admin_ac)
{
//show only sales calendar to view only users:
echo '<li><a href="#tabs-1" onClick="location.href=\'#sales_stock\'" class="sales_stock" title="sales_stock">Sales Stock</a></li>';
}
else
{
?>
<li><img src='./css/images/logged-in-logo-top-left.jpg' class='tabs_logo'/></li>
<?php
$allow_links=array('Sales Point'=>false,'Purchase Point'=>false,
'Hire Stock'=>false,'Sales Stock'=>false,'Accounts'=>false,
'Maintenance'=>false,'User Administration'=>false,'Administration Point'=>true);
if ($logged_user_info=='superadmin') $allow_links=array('Sales Point'=>true,
'Purchase Point'=>true,'Hire Stock'=>true,'Sales Stock'=>true,'Accounts'=>true,
'Maintenance'=>true,'User Administration'=>true,'Administration Point'=>true);
if ($logged_user_info!='superadmin')
   {
   if (is_array($logged_user_info))
		{
		$cntr=1;
		foreach ($allow_links as $cur_key=>$cur_allow_val)
			{
		    $allow_links[$cur_key]=$logged_user_info['permission'.$cntr];
			$cntr++;
			}
		unset($cntr);
		}
   }
//print_r($allow_links);   
if ($allow_links['Sales Point']) echo '<li><a href="#tabs-0" onClick="location.href=\'#sales_point\';" class="sales_point" title="sales_point">Sales Point</a></li>';
if ($allow_links['Purchase Point']) echo '<li><a href="#tabs-5" onClick="location.href=\'#purchase_point\';" class="purchase_point" title="purchase_point">Purchase Point</a></li>';
if ($allow_links['Hire Stock']) echo '<li><a href="#tabs-6" onClick="location.href=\'#hire_stock\';" class="hire_stock" title="hire_stock">Hire Stock</a></li>';
if ($allow_links['Sales Stock']) echo '<li><a href="#tabs-1" onClick="location.href=\'#sales_stock\';" class="sales_stock" title="sales_stock">Sales Stock</a></li>';
if ($allow_links['Accounts']) echo '<li><a href="#tabs-3" onClick="location.href=\'#accounts\'" class="accounts" title="accounts">Accounts</a></li>';
if ($allow_links['Maintenance']) echo '<li><a href="#tabs-4" onClick="location.href=\'#maintenance\'" class="maintenance" title="maintenance">Maintenance</a></li>';
if ($allow_links['User Administration']) echo '<li><a href="#tabs-2" onClick="location.href=\'#users\'" class="users" title="users">User Administration</a></li>';
if ($allow_links['Administration Point']) echo '<li><a href="#tabs-7" onClick="location.href=\'#admin_point\'" class="admin_point" title="admin_point">Administration Point</a></li>';
}
?>
</ul>
<?php
if ($user_only_view_allowed and !$admin_ac)
{
echo '<div id="tabs-1">'.$sales_stock_tab.'</div>';
}
else
{
if ($allow_links['Sales Point']) echo "<div id='tabs-0'>$sales_point_tab</div>";
if ($allow_links['Purchase Point']) echo "<div id='tabs-5'>$purchase_point_tab</div>";
if ($allow_links['Hire Stock']) echo "<div id='tabs-6'>$hire_stock_tab</div>";
if ($allow_links['Sales Stock']) echo "<div id='tabs-1'>$sales_stock_tab</div>";
if ($allow_links['Accounts']) echo "<div id='tabs-3'>$accounts_tab</div>";
if ($allow_links['Maintenance']) echo "<div id='tabs-4'>$maintenance_tab</div>";
if ($allow_links['User Administration']) echo "<div id='tabs-2'>$emploee_tab</div>";
if ($allow_links['Administration Point']) echo "<div id='tabs-7'>$admin_point_tab</div>";
}
?>
</div>

</div>
<script type='text/javascript'>
$('#logout_but').unbind('click').bind('click', function ()
   {
   document.cookie = 'user=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
   window.location.reload();
   });
</script>
</body>
</html>