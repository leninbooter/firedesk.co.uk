<?php
function check_pas($result,$i)
   {
   global $conn;
   global $emp_pass_db;
   global $usr_name_db;
   global $bad_user;
   global $pass_only;
     $emp_pass_db_hash=mysql_result($result,$i,'pass');
     $emp_second_name_db=mysql_result($result,$i,'second_name');
     $emp_first_name_db=mysql_result($result,$i,'first_name');
     
	 $ps_sl=md5($emp_second_name_db.$emp_first_name_db);
     $emp_pass_to_db=crypt($emp_pass_db,'$6$'.$ps_sl);
	 //echo $emp_pass_to_db,' || ',$emp_pass_db_hash,' *** ';
	 if ($emp_pass_to_db==$emp_pass_db_hash)
	    {
		//suc login:
		$crypt_code=crypt($emp_first_name_db.$emp_second_name_db,'$6$Someunknownsecret');
		$crypt_code_arr=explode('$',$crypt_code);
		$crypt_code=$crypt_code_arr[3];
		unset($crypt_code_arr);
		if (!$pass_only) setcookie('user',$crypt_code,time()+60*60*24*30,'/');
		echo 'Welcome, '.$emp_first_name_db.' '.$emp_second_name_db.'!';
		mysql_close($conn);  
		exit;
		}
		else $bad_user=true;
   }


if (isset($_POST['log_usr_name']) and isset($_POST['log_pass']))
{
$bad_user=false;
//get db script:
require_once(__DIR__.'/db_func.php');

$crit_err=false;
if (!isset($_POST['log_usr_name']) or trim($_POST['log_usr_name'])=='') {echo "Bad username!\n"; $crit_err=true;}
if (!isset($_POST['log_pass']) or trim($_POST['log_pass'])=='') {echo "Bad pass!\n"; $crit_err=true;}

$pass_only=false;
if (isset($_POST['pass_only_check']) and trim(strip_tags($_POST['pass_only_check'])))
  {
  $pass_only=true;
  }

if ($crit_err) exit;

$usr_name=trim(strip_tags($_POST['log_usr_name']));
  if (!preg_match('/^[^`~!@#$%^&*()+=:><?]+$/u',$usr_name) or $usr_name=='')
    {
	echo "Bad username!\n";
	$crit_err=true;
	}
$usr_name=strtolower($usr_name);		
$emp_pass=trim(strip_tags($_POST['log_pass']));	

if ($crit_err) exit;

$conn=db_connect();
$usr_name_db=mysql_real_escape_string($usr_name);
unset($usr_name);
$emp_pass_db=mysql_real_escape_string($emp_pass);
unset($emp_pass);

if ($pass_only)
{
//allow to be admin:
$usr_name_db_2=$usr_name_db;
$emp_second_name_db_2=$emp_second_name_db;
$usr_name_db='superadmin';
}

//first check if admin:
$query="SELECT `pass`,`first_name` FROM `main_users` WHERE `second_name`='$usr_name_db'";
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
$id_max=mysql_num_rows($result);
if ($id_max!=0) 
{
$pass_db_hash=mysql_result($result,0,'pass');
$first_name_db=mysql_result($result,0,'first_name');
$ps_sl=md5(strtolower($first_name_db).strtolower($usr_name_db).'doyoureallywanttobeanadmin?');     
$emp_pass_hash=crypt($emp_pass_db,'$6$'.$ps_sl);
if ($emp_pass_hash==$pass_db_hash)
{
$crypt_code=crypt(strtolower($first_name_db).strtolower($usr_name_db),'$6$Someunknownadminsecret');
$crypt_code_arr=explode('$',$crypt_code);
$crypt_code=$crypt_code_arr[3];
unset($crypt_code_arr);
if (!$pass_only) setcookie('user',$crypt_code,time()+60*60*24*30,'/');
echo 'Welcome, admin!';
mysql_close($conn);  
exit;
}
}

if ($pass_only)
{
//set old values:
$usr_name_db=$usr_name_db_2;
unset($usr_name_db_2);
}

$query="SELECT `pass`,`first_name`,`second_name` FROM `users` WHERE `nick_name`='$usr_name_db'";
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
$id_max=mysql_num_rows($result);
if ($id_max==0) 
 {
 echo 'Bad username or password!';
 exit;
 }
  if ($id_max>1)
  {
  //if few user accounts:
  for ($i=0; $i<$id_max; $i++)
    {
    check_pas($result,$i);
    }
  }
  else
   {
   check_pas($result,0);
   }
if ($bad_user) echo 'Bad username or password!';	   
mysql_close($conn);  
}
?>