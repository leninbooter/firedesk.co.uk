<?php
//get db script:
require_once(__DIR__.'/db_func.php');
date_default_timezone_set('Europe/London');
//add new user:
if (isset($_POST['emp_first_name']))
  {
  for ($ik=1; $ik<=8; $ik++)
	{
	if (!isset($_POST['permission_'.$ik])) $_POST['permission_'.$ik]=0;
	}
  
  $filtered_vars=new vars_checker();
  $filtered_vars->check_vars('POST',array('emp_first_name','emp_second_name','emp_usr_name','emp_pass','emp_mail','ed_adm_perm','permission_1','permission_2','permission_3','permission_4','permission_5','permission_6','permission_7','permission_8','ed_branch','emp_type'),array('no','no','no','yes','yes','no','yes','yes','yes','yes','yes','yes','yes','yes','no','no'),array('name','name','nickname','no check','mail','no check','no check','no check','no check','no check','no check','no check','no check','no check','nickname','no check'),array('First name','Second name','Username','Password','E-mail','Admin permissions','Permission 1','Permission 2','Permission 3','Permission 4','Permission 5','Permission 6','Permission 7','Permission 8','Branch','Action type'),true);
  if ($exit_in_the_end) exit;
  
  $emp_first_name=$filtered_vars->get_var('emp_first_name');
  $emp_second_name=$filtered_vars->get_var('emp_second_name');
  $emp_usr_name=strtolower($filtered_vars->get_var('emp_usr_name'));
  $emp_pass=$filtered_vars->get_var('emp_pass');
  $emp_mail=strtolower($filtered_vars->get_var('emp_mail'));
  $ed_adm_perm=$filtered_vars->get_var('ed_adm_perm');
  $permission_1=$filtered_vars->get_var('permission_1');
  $permission_2=$filtered_vars->get_var('permission_2');
  $permission_3=$filtered_vars->get_var('permission_3');
  $permission_4=$filtered_vars->get_var('permission_4');
  $permission_5=$filtered_vars->get_var('permission_5');
  $permission_6=$filtered_vars->get_var('permission_6');
  $permission_7=$filtered_vars->get_var('permission_7');
  $permission_8=$filtered_vars->get_var('permission_8');
  $ed_branch=$filtered_vars->get_var('ed_branch');
  $emp_type=$filtered_vars->get_var('emp_type');
  unset($filtered_vars);
  
   if ($emp_type=='edit')
   {
   $emp_id=trim(strip_tags($_POST['emp_id']));
   }
  
  /*echo $ed_adm_perm,'<br>emp_usr_name: ',$emp_usr_name,'<br>permission_1: ',$permission_1,'<br>permission_7: ',$permission_7,'<br>ed_branch: ',$ed_branch;
  exit;*/
  $ed_adm_perm=$ed_adm_perm=='Admin' ? 'A':'U' ;  
  
  //adding user to db:
  $conn=db_connect();
  //check if got the same:
  $query="SELECT `id` FROM `users` WHERE `first_name`='$emp_first_name' AND `second_name`='$emp_second_name' AND `nick_name`='$emp_usr_name'";
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
  $id_max=mysql_num_rows($result);
  if ($emp_type=='add')
  {
  if ($id_max>0)
    {
	echo "Already got the same user\n";
	exit;
	}
  } 
  if ($emp_type=='add')
  {
  //hash user pass:
  $ps_sl=md5($emp_second_name.$emp_first_name);
  $emp_pass_db=crypt($emp_pass,'$6$'.$ps_sl);
  //add to db:
  $query="INSERT INTO `users`(`id`, `first_name`, `second_name`, `pass`, `mail`, `branch`, `active`, `permissions`, `nick_name`, `permission1`, `permission2`, `permission3`, `permission4`, `permission5`, `permission6`, `permission7`, `permission8`) VALUES (null,'$emp_first_name','$emp_second_name','$emp_pass_db','$emp_mail','$ed_branch',1,'$ed_adm_perm','$emp_usr_name',$permission_1,$permission_2,$permission_3,$permission_4,$permission_5,$permission_6,$permission_7,$permission_8)";
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  if ($result) echo 'Successfully added!';
    else echo "\nGot some problems!\nuser hasn`t been added!";
  }
  if ($emp_type=='edit')
  {
  $emp_id=mysql_real_escape_string($emp_id);
  //check if password depends values changed:
  $query="SELECT `first_name`, `second_name` FROM `users` WHERE `id`=$emp_id";
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  $first_name_db=mysql_result($result,0,'first_name');
  $second_name_db=mysql_result($result,0,'second_name');
  
  $ps_sl=md5($emp_second_name.$emp_first_name);
  $emp_pass_db=crypt($emp_pass,'$6$'.$ps_sl);
  
  if ($first_name_db!=$emp_first_name or $second_name_db!=$emp_second_name)
     {
	 if ($emp_pass=='')
	   {
	   echo 'Got problems with password!';
	   exit;
	   }
	 //change pas in db:
	 $query="UPDATE `users` SET `pass`='$emp_pass_db' WHERE `id`=$emp_id";
     $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
	 }
	 else
	 {
  if ($emp_pass!='')
    {
	//change pas in db:
	 $query="UPDATE `users` SET `pass`='$emp_pass_db' WHERE `id`=$emp_id";
     $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
    }
     }	
  $query="UPDATE `users` SET `first_name`='$emp_first_name',`second_name`='$emp_second_name', `mail`='$emp_mail',`branch`='$ed_branch',`permissions`='$ed_adm_perm',`nick_name`='$emp_usr_name',`permission1`=$permission_1,`permission2`=$permission_2,`permission3`=$permission_3,`permission4`=$permission_4,`permission5`=$permission_5,`permission6`=$permission_6,`permission7`=$permission_7,`permission8`=$permission_8  WHERE `id`=$emp_id";
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  if ($result) echo 'Successfully saved!';
    else echo "\nGot some problems!\nuser hasn`t been added!";
  }
  mysql_close($conn);
  }
  
//make active/unactive user:
if (isset($_POST['act_user']) and trim(strip_tags($_POST['act_user'])) and isset($_POST['emp_info']))
  {
  $cur_state=trim(strip_tags($_POST['cur_el_state']));
  if ($cur_state=='inactive') 
  {
  $new_state=1;
  $state_text='activated';
  }
    else 
	{
	$new_state=0;
	$state_text='inactivated';
	}
  $cur_id=trim(strip_tags($_POST['cur_el_id']));
  $emp_info_arr=$_POST['emp_info'];
  //print_r($emp_info_arr);
  $conn=db_connect();
  $cur_id_db=mysql_real_escape_string($cur_id);
  $emp_first_name_db=mysql_real_escape_string(trim(strip_tags($emp_info_arr[1])));
  $emp_second_name_db=mysql_real_escape_string(trim(strip_tags($emp_info_arr[2])));
  $emp_mail_db=mysql_real_escape_string(trim(strip_tags($emp_info_arr[4])));
   if ($cur_id_db=='' or $emp_first_name_db=='' or $emp_second_name_db=='') 
     {
	 echo 'Some error happened!';
	 exit;
	 }
 $query="UPDATE `users` SET `active`=$new_state WHERE `id`=$cur_id_db AND`first_name`='$emp_first_name_db' AND `second_name`='$emp_second_name_db' AND `mail`='$emp_mail_db'";	 
 $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
 if ($result) echo 'Succesfully '.$state_text.'!';
    else echo "\nGot some problems!\nuser profile hasn`t been $state_text!";
  mysql_close($conn);
  }  
  
//delete user:
if (isset($_POST['del_user']) and trim(strip_tags($_POST['del_user'])) and isset($_POST['emp_info']))
  {
  $cur_id=trim(strip_tags($_POST['cur_el_id']));
  $emp_info_arr=$_POST['emp_info'];
  //print_r($emp_info_arr);
  $conn=db_connect();
  $cur_id_db=mysql_real_escape_string($cur_id);
  $emp_first_name_db=mysql_real_escape_string(trim(strip_tags($emp_info_arr[1])));
  $emp_second_name_db=mysql_real_escape_string(trim(strip_tags($emp_info_arr[2])));
   if ($cur_id_db=='' or $emp_first_name_db=='' or $emp_second_name_db=='') 
     {
	 echo 'Some error happened!';
	 exit;
	 }
 $query="DELETE FROM `users` WHERE `id`=$cur_id_db AND`first_name`='$emp_first_name_db' AND `second_name`='$emp_second_name_db'";	 
 $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
 if ($result) echo 'Succesfully deleted!';
    else echo "\nGot some problems!\nuser profile hasn`t been deleted!";
  mysql_close($conn);
  }

if (isset($_POST['new_branch_name']))
  {
  $filtered_vars=new vars_checker();
  $filtered_vars->check_vars('POST',array('new_branch_name','new_address_box','new_phone_number','submit_but'),array('no','yes','yes','no'),array('nickname','no check','no check','nickname'),array('Branch','Address Box','Phone Number','Mode type'),true);
  if ($exit_in_the_end) exit;
  $new_branch_name=$filtered_vars->get_var('new_branch_name');
  $new_address_box=$filtered_vars->get_var('new_address_box');
  $new_phone_number=$filtered_vars->get_var('new_phone_number');
  $action_type=$filtered_vars->get_var('submit_but');
  unset($filtered_vars);
  $edit_mode=$action_type=='Add' ? false:true;
  $conn=db_connect();
  $query="SELECT `branch_name` FROM `branches`";	 
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  $id_max=mysql_num_rows($result);
  if ($edit_mode) $got_branch=false;
  if ($id_max>0)
    {
	for ($k=0; $k<$id_max; $k++)
		{
		if (mysql_result($result,$k,'branch_name')==$new_branch_name)
			{
			if ($edit_mode) $got_branch=true;
				else
				{
				//if add new branch:
				echo 'Such branch already exists!';
				mysql_close($conn);
				exit;
				}
			}
		}
	}
  if ($edit_mode and !$got_branch)
	{
	echo 'There is nothing to edit!';
	mysql_close($conn);
	exit;
	}
  if (!$edit_mode) $query="INSERT INTO `branches`(`id`, `branch_name`, `address_box`, `phone_number`) VALUES (null,'$new_branch_name','$new_address_box','$new_phone_number')";	 
	else $query="UPDATE `branches` SET `address_box`='$new_address_box',`phone_number`='$new_phone_number' WHERE `branch_name`='$new_branch_name'";
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  if ($result) 
  {
  if (!$edit_mode) echo 'Successfully added!';
	else echo 'Successfully edited!';
  }
    else echo "\nGot some problems!";
  mysql_close($conn);
  }  
if (isset($_POST['delete_branch']))
  {
  $filtered_vars=new vars_checker();
  $filtered_vars->check_vars('POST',array('delete_branch'),array('no'),array('nickname'),array('Branch'),true);
  if ($exit_in_the_end) exit;
  $delete_branch_name=$filtered_vars->get_var('delete_branch');
  unset($filtered_vars);
  $conn=db_connect();
  $query="SELECT `branch_name` FROM `branches` WHERE `branch_name`='$delete_branch_name'";	 
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  $id_max=mysql_num_rows($result);
  if ($id_max==0)
    {
	echo 'Such branch doesn\'t exists!';
	mysql_close($conn);
	exit;
	}
  $query="DELETE FROM `branches` WHERE `branch_name`='$delete_branch_name'";	 
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  if ($result) echo 'Successfully deleted!';
    else echo "\nGot some problems!";
  mysql_close($conn);
  }  

//sending content to front-end:
if (isset($_POST['get_content']) and $_POST['get_content'])
{
$filtered_vars=new vars_checker();
$filtered_vars->check_vars('POST',array('content_name','id_code'),array('no','no'),array('nickname','no check'),array('Content type','Item code'),true);
if ($exit_in_the_end) exit;
$content_name=$filtered_vars->get_var('content_name');
$id_code=$filtered_vars->get_var('id_code');
unset($filtered_vars);
require_once(__DIR__.'/contents.php');
switch ($content_name)
  {
  case 'unit_list': echo $content_unit_list;
                         break;
  case 'family_list': echo $content_family_list;
                         break;
  case 'view_unit': echo $content_view_unit;
                         break;
  case 'view_family': echo $content_view_family;
                         break;
  case 'new_unit': echo $content_new_unit;
                         break;
  case 'new_family': echo $content_new_family;
                         break;
  case 'edit_unit': echo $content_edit_unit;
                         break;
  case 'edit_family': echo $content_edit_family;
                         break;
  case 'stock_list': echo $content_stock_list;
                         break;
  case 'view_stock': echo $content_view_stock;
                         break;
  case 'new_stock': echo $content_new_stock;
                         break;
  case 'edit_stock': echo $content_edit_stock;
                         break;
  case 'supplier_list': echo $content_supplier_list;
                         break;
  case 'view_supplier': echo $content_view_supplier;
                         break;
  case 'new_supplier': echo $content_new_supplier;
                         break;
  case 'edit_supplier': echo $content_edit_supplier;
                         break;
  case 'customer_list': echo $content_customer_list;
                         break;
  case 'view_customer': echo $content_view_customer;
                         break;
  case 'new_customer': echo $content_new_customer;
                         break;
  case 'edit_customer': echo $content_edit_customer;
                         break;
  case 'contact_list': echo $content_contact_list;
                         break;
  case 'view_contact': echo $content_view_contact;
                         break;
  case 'new_contact': echo $content_new_contact;
                         break;
  case 'edit_contact': echo $content_edit_contact;
                         break;
  case 'nominal_ledger_list': echo $content_nominal_ledger_list;
                         break;
  case 'view_nominal_ledger': echo $content_view_nominal_ledger;
                         break;
  case 'new_nominal_ledger': echo $content_new_nominal_ledger;
                         break;
  case 'edit_nominal_ledger': echo $content_edit_nominal_ledger;
                         break;
  case 'audit_trail_list': echo $content_audit_trail_list;
                         break;
  case 'view_audit_trail': echo $content_view_audit_trail;
                         break;
  case 'new_audit_trail': echo $content_new_audit_trail;
                         break;
  case 'edit_audit_trail': echo $content_edit_audit_trail;
                         break;
  case 'bank_trail_list': echo $content_bank_trail_list;
                         break;
  case 'view_bank_trail': echo $content_view_bank_trail;
                         break;
  case 'new_bank_trail': echo $content_new_bank_trail;
                         break;
  case 'edit_bank_trail': echo $content_edit_bank_trail;
                         break;
  case 'bank_ledger_list': echo $content_bank_ledger_list;
                         break;
  case 'view_bank_ledger': echo $content_view_bank_ledger;
                         break;
  case 'new_bank_ledger': echo $content_new_bank_ledger;
                         break;
  case 'edit_bank_ledger': echo $content_edit_bank_ledger;
                         break;
  case 'vat_list': echo $content_vat_list;
                         break;
  case 'view_vat': echo $content_view_vat;
                         break;
  case 'new_vat': echo $content_new_vat;
                         break;
  case 'edit_vat': echo $content_edit_vat;
                         break;
  case 'holiday_list': echo $content_holiday_list;
                         break;
  case 'view_holiday': echo $content_view_holiday;
                         break;
  case 'new_holiday': echo $content_new_holiday;
                         break;
  case 'edit_holiday': echo $content_edit_holiday;
                         break;
  case 'sales_interface': echo $content_sales_interface;
                         break;
  case 'fleet_records': echo $content_fleet_records;
                         break;
  case 'group_changes': echo $content_group_changes;
                         break;
  default: echo 'No content for current item.';
  }

}
//proceed add/edit items:
if (isset($_POST['form_proceed']) and $_POST['form_proceed'] and isset($_POST['frm_type']))
{
$act_type=trim(strip_tags($_POST['frm_type']));
if ($act_type!='edit' and $act_type!='new')
  {
  echo 'Got some problems with a form!';
  exit;
  }
$db_table_name=trim(strip_tags($_POST['table']));
if ($db_table_name=='')
  {
  echo 'Got some problems with a form!';
  exit;
  }  
$fields_arr=json_decode($_POST['fields_arr'],true);  
$filelds_keys_arr=array_keys($fields_arr);
$filelds_values_arr=array_values($fields_arr);
$allows_arr=json_decode($_POST['allows_arr'],true);  
$allows_values_arr=array_values($allows_arr);
$checks_arr=json_decode($_POST['checks_arr'],true);  
$allows_checks_arr=array_values($checks_arr);
$db_types_arr=json_decode($_POST['types_arr'],true);  
foreach ($filelds_values_arr as $cur_val)
 {
 if (!isset($_POST[$cur_val])) $_POST[$cur_val]=0;
   else if ($_POST[$cur_val]=='on') $_POST[$cur_val]=1;
 }
/*print_r($db_types_arr);
echo "\n";
print_r($allows_arr);
echo "\n";
print_r($checks_arr);*/
$filtered_vars=new vars_checker();
$filtered_vars->check_vars('POST',$filelds_values_arr,$allows_values_arr,$allows_checks_arr,$filelds_keys_arr,true);
if ($exit_in_the_end) exit;

if ($act_type=='new') 
  {
  $query_str="INSERT INTO `$db_table_name` (`id`";
  $query_str_end=' VALUES (null';
  }
if ($act_type=='edit') 
  {
  if (!isset($_POST['edit_code']) or trim(strip_tags($_POST['edit_code']))=='')
    {
	echo 'Got problems with code!';
	exit;
	}
  $cur_edit_code=trim(strip_tags($_POST['edit_code']));
  $query_str="UPDATE `$db_table_name` SET ";
  if ($db_table_name=='contact_list' or $db_table_name=='audit_trail' or $db_table_name=='bank_trail' or $db_table_name=='holiday_list') 
	{
	$cur_edit_code=intval($cur_edit_code);
	$query_str_end=" WHERE `id`=$cur_edit_code";
	}
		else $query_str_end=" WHERE `Code`='$cur_edit_code'";
  }
$conn=db_connect();
if ($db_table_name=='fleet' and $act_type=='new')
  {
  //getting the last code:
  $query="SELECT `Code` FROM `fleet`ORDER BY id DESC LIMIT 1";	 
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  $last_code=mysql_result($result,0,'Code');
  $new_code=$last_code+1;
  unset($last_code);
  $query_str.=', `Code`';
  $query_str_end.=", $new_code";
  }
if ($db_table_name=='family' and $act_type=='new')
  {
  //getting the last code:
  $query="SELECT `Code` FROM `family`ORDER BY id DESC LIMIT 1";	 
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  $last_code=mysql_result($result,0,'Code');
  $last_code=intval(str_ireplace('FAM','',$last_code),10);
  $new_code=$last_code+1;
  $new_code='FAM'.str_pad($new_code, 4, '0', STR_PAD_LEFT);
  unset($last_code);
  $query_str.=', `Code`';
  $query_str_end.=", '$new_code'";
  }
$fr_iter=true;
foreach ($fields_arr as $cur_key=>$cur_db_label)
 {
 //make db query:
 if ($act_type=='new')
   {
   $cur_val=$filtered_vars->get_var($cur_db_label);
   $cur_db_type=$db_types_arr[$cur_key];
   if ($cur_val=='' and ($cur_db_type=='float' or $cur_db_type=='int' or $cur_db_type=='bool')) $cur_val=0;
   $query_str.=", `$cur_db_label`";
   if ($cur_db_type=='float' or $cur_db_type=='int' or $cur_db_type=='bool') $query_str_end.=", $cur_val";
      else $query_str_end.=", '$cur_val'";
   if ($db_table_name=='family' and $cur_db_label=='Description' and trim($filtered_vars->get_var('Price_1_week'))!='')
		{
		$query_str.=", `Extra_day`";
		$cur_val=round($filtered_vars->get_var('Price_1_week')/7,2);
		$query_str_end.=", '$cur_val'";
		}
   /*if ($cur_db_label=='Available' and $db_table_name=='fleet')
      {
	  $query_str.=", `Active`";
      if ($filtered_vars->get_var('Available')==1) $query_str_end.=", 'Available'";
	    else $query_str_end.=", '".$filtered_vars->get_var('Date_Hired')."'";
	  }*/
   }
 if ($act_type=='edit') 
   {
   $cur_val=$filtered_vars->get_var($cur_db_label);
   $cur_db_type=$db_types_arr[$cur_key];
   if ($cur_val=='' and ($cur_db_type=='float' or $cur_db_type=='int' or $cur_db_type=='bool')) $cur_val=0;
   if (!$fr_iter) $query_str.=", `$cur_db_label`=";
     else $query_str.="`$cur_db_label`=";
   if ($cur_db_type=='float' or $cur_db_type=='int' or $cur_db_type=='bool') $query_str.=$cur_val;
      else $query_str.="'$cur_val'";
   if ($db_table_name=='family' and $cur_db_label=='Description' and trim($filtered_vars->get_var('Price_1_week'))!='')
		{
		$query_str.=", `Extra_day`=";
		$cur_val=round($filtered_vars->get_var('Price_1_week')/7,2);
		$query_str.="'$cur_val'";
		}
   /*if ($cur_db_label=='Available' and $db_table_name=='fleet')
      {
	  $query_str.=", `Active`=";
      if ($filtered_vars->get_var('Available')==1) $query_str.="'Available'";
	    else $query_str.="'".$filtered_vars->get_var('Date_Hired')."'";
	  }*/
   }
 $fr_iter=false;
 }
if ($act_type=='edit')
{ 
if ($db_table_name=='audit_trail' or $db_table_name=='bank_trail' or $db_table_name=='bank_ledger')
	{
	$id_code=0;
	require_once(__DIR__.'/contents.php');
	if ($logged_user_info!='superadmin') 
	{
	$cur_location=$logged_user_info['branch'];
	$cur_user_nick=$logged_user_info['nick_name'];
	}
		else 
		{
		$cur_location='Cyber';
		$cur_user_nick=$logged_user_info;
		}
	if ($db_table_name=='bank_ledger') 
	{
	$cur_date=date('d-m-Y');
	$query_str.=", `mod_date`='$cur_date', `Location_Stamp`='$cur_location', `User_Stamp`='$cur_user_nick'";
	}
		else $query_str.=", `Location_Stamp`='$cur_location', `User_Stamp`='$cur_user_nick'";
	$conn=db_connect();
	}
	else
	  {
/*if ($db_table_name=='family')
      {*/
	  $query_str.=",`mod_date`=";
	  $cur_date=date('d-m-Y');
      $query_str.="'$cur_date'";
	/*  } */
	  }
}
if ($act_type=='new')
{
if ($db_table_name=='audit_trail' or $db_table_name=='bank_trail' or $db_table_name=='bank_ledger')
	{
	$id_code=0;
	require_once(__DIR__.'/contents.php');
	if ($logged_user_info!='superadmin') 
	{
	$cur_location=$logged_user_info['branch'];
	$cur_user_nick=$logged_user_info['nick_name'];
	}
		else 
		{
		$cur_location='Cyber';
		$cur_user_nick=$logged_user_info;
		}
	if ($db_table_name=='bank_ledger') $query_str.=", `creation_date`,`mod_date`,`Location_Stamp`,`User_Stamp`";
		else $query_str.=", `creation_date`,`Location_Stamp`,`User_Stamp`";
	$cur_date=date('d-m-Y');
	if ($db_table_name=='bank_ledger') $query_str_end.=", '$cur_date','$cur_date','$cur_location','$cur_user_nick'";
		else $query_str_end.=", '$cur_date','$cur_location','$cur_user_nick'";
	$conn=db_connect();
	}
	else
	  {
/*if ($db_table_name=='family')
      {*/
	  $query_str.=", `creation_date`, `mod_date`";
	  $cur_date=date('d-m-Y');
      $query_str_end.=", '$cur_date', '$cur_date'";
	  }
	/*  } */
$query_str.=')'; 
$query_str_end.=')';	
}  
unset($filtered_vars);
//add to db:
/*echo $query_str.$query_str_end;
exit;*/
$result=mysql_query($query_str.$query_str_end) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  if ($result) 
  {
  if ($act_type=='new') echo 'Successfully added!';
    else echo 'Successfully edited!';
  }
    else echo "\nGot some problems!\nNew item hasn`t been added!";
mysql_close($conn);
}

//sending search results to front-end:
if (isset($_POST['search_content']) and $_POST['search_content'])
{
$filtered_vars=new vars_checker();
$filtered_vars->check_vars('POST',array('search_type','find_code','search_level','find_word','find_from_date','find_to_date'),array('no','yes','no','yes','yes','yes'),array('nickname','no check','nickname','no check','date(dd/-mm/-yyyy)','date(dd/-mm/-yyyy)'),array('Search type','Item code','Search level','Keyword','Date From','Date To'),true);
if ($exit_in_the_end) exit;
$search_type=$filtered_vars->get_var('search_type');
$find_code=$filtered_vars->get_var('find_code');
$search_level=$filtered_vars->get_var('search_level');
$find_word=$filtered_vars->get_var('find_word');
$find_from_date=$filtered_vars->get_var('find_from_date');
$find_to_date=$filtered_vars->get_var('find_to_date');
//echo $find_from_date,' - ',$find_to_date;
unset($filtered_vars);
if (trim($find_code)!='') $search_mode='code';
  else if (trim($find_word)!='') $search_mode='keyword';
     else if (trim($find_from_date)!='' and trim($find_to_date)!='') $search_mode='dates';
	    else 
		{
		echo 'Bad input data!';
		exit;
		}
//echo 'mode: ',$search_mode,'<br>';
if ($search_mode=='code')
{
//detecting code type:
$find_code=strtoupper($find_code);
if (strpos($find_code,'FAM')!==false) $code_type='Family';
   else $code_type='Unit';
$id_code=$find_code;
}
if ($search_mode=='keyword' or $search_mode=='dates')
{
$id_code=0;
}
if ($search_mode=='code') echo '<span class="search_results_label">Search results (for code="'.$find_code.'"):</span><br>';
if ($search_mode=='keyword') echo '<span class="search_results_label">Search results (for keyword="'.$find_word.'"):</span><br>';
if ($search_mode=='dates') echo '<span class="search_results_label">Search results (for date range from '.$find_from_date.' to '.$find_to_date.'):</span><br>';
require_once(__DIR__.'/contents.php');
/**************************************/
function generate_unit_lists_table($results_arr)
{
$cells_array=array('Unit Code'=>'Code','Name'=>'Unit',
'Description'=>'Description','Active'=>'Active','Location'=>'Branch');
$temp_handler=new panels_templates();
echo $temp_handler->generate_list_table($results_arr,$cells_array);
unset($temp_handler);
}
function generate_family_lists_table($fam_results)
{
$cells_array=array('Family Code'=>'Code','Family'=>'Family','Description'=>'Description',
'1 Day'=>'Price_1_day','2 Days'=>'Price_2_days','1 Week'=>'Price_1_week',
'Min 1 Week'=>'Min_1_Week','Availabile'=>'Avaible','Total Hire'=>'Total_hire');
$temp_handler=new panels_templates();
echo $temp_handler->generate_list_table($fam_results,$cells_array);
unset($temp_handler);
}
function search_key_word($table_info,$search_in_fields,$find_word)
{
global $search_type;
global $cur_location;
global $search_found;
$results_arr=array();
foreach ($table_info as $cur_table)
  {
  foreach ($search_in_fields as $cur_search_field)
    {
	if (stripos($cur_table[$cur_search_field], $find_word)!==false)
		{
		if (!isset($cur_table['Branch']) and isset($cur_table['Family']))
			{
			//so we got family table here...
			global $fleets_info;
			foreach ($fleets_info as $cur_unit)
				{
				if ($cur_table['id']==$cur_unit['Family_id'])
					{
					$cur_table['Branch']=$cur_unit['Branch'];
					}
				}	
			}
		if ($search_type=='sr_onsite' and $cur_location!='all')
			{
			if (isset($cur_table['Branch']) and $cur_table['Branch']!=$cur_location) continue;
			}	
			
		$search_found=true;
		if ((count($results_arr)-1)>=0 and $results_arr[(count($results_arr)-1)]['id']==$cur_table['id']) continue;
		$results_arr[]=$cur_table;
		}
	}
  }
return $results_arr;
}
function search_date_range($table_info,$search_field,$start_date_unix,$end_date_unix)
{
global $search_type;
global $cur_location;
global $search_found;
$results_arr=array();
foreach ($table_info as $cur_table)
  {
	$cur_unix_date=strtotime($cur_table[$search_field]);
	if ($cur_unix_date>=$start_date_unix and $cur_unix_date<=$end_date_unix)	
		{
		if ($search_type=='sr_onsite' and $cur_location!='all')
			{
			if (!isset($cur_table['Branch']) and isset($cur_table['Family']))
				{
				//so we got family table here...
				global $fleets_info;
				foreach ($fleets_info as $cur_unit)
					{
					if ($cur_table['id']==$cur_unit['Family_id'])
						{
						$cur_table['Branch']=$cur_unit['Branch'];
						}
					}	
				}
			if (isset($cur_table['Branch']) and $cur_table['Branch']!=$cur_location) continue;
			}	
			
		$search_found=true;
		if ((count($results_arr)-1)>=0 and $results_arr[(count($results_arr)-1)]['id']==$cur_table['id']) continue;
		$results_arr[]=$cur_table;
		}
  }
return $results_arr;
}
function show_results($results_arr, $results_arr2)
{
global $search_level;
global $fleets_info;
global $family_tab_info;
  //remade arrays, according to search level:
  if ($search_level=='Unit')
		{
		//add family results:
		if (count($results_arr2)!=0)
			{
			foreach ($results_arr2 as $cur_fam_result)
				{
				foreach ($fleets_info as $cur_fleet)
					{
					if ($cur_fleet['Family_id']!=$cur_fam_result['id']) continue;
					$got_same=false;
					foreach ($results_arr as $cur_unit_result)
						{
						if ($cur_fleet['id']==$cur_unit_result['id']) $got_same=true;
						}
					if (!$got_same) $results_arr[]=$cur_fleet;
					}
				}
			}
		//show table:
		if (count($results_arr)!=0) 
			{
			generate_unit_lists_table($results_arr);
			}
		}
  if ($search_level=='Family')
		{
		//add unit results:
		if (count($results_arr)!=0)
			{
			foreach ($results_arr as $cur_unit_result)
				{
				foreach ($family_tab_info as $cur_fam)
					{
					if ($cur_fam['id']!=$cur_unit_result['Family_id']) continue;
					$got_same=false;
					foreach ($results_arr2 as $cur_fam_result)
						{
						if ($cur_fam['id']==$cur_fam_result['id']) $got_same=true;
						}
					if (!$got_same) $results_arr2[]=$cur_fam;
					}
				}
			}
		//show table:
		if (count($results_arr2)!=0) 
			{
			generate_family_lists_table($results_arr2);
			}
		}
}

class search_renderer
{
public function render_simple_results($search_level,$results_arr)
    {
    if ($search_level=='Unit')
       {
       //show as unit list:
       generate_unit_lists_table($results_arr);
       }
    if ($search_level=='Family')
       {
       //show as family list:
       generate_family_lists_table($results_arr);
       }
    }
public function render_complex_results($search_level,$results_arr,$family_tab_info, $nothing_found_msg, $fleets_info)
    {
	$second_search_found=false;
	$complex_results=array();
	if ($search_level=='Family') $db_table_info=$family_tab_info;
	if ($search_level=='Unit') $db_table_info=$fleets_info;
	foreach ($db_table_info as $cur_info)
		{
		foreach ($results_arr as $cur_results)
			{
			if (($search_level=='Family' and $cur_results['Family_id']==$cur_info['id']) or ($search_level=='Unit' and $cur_results['id']==$cur_info['Family_id']))
				{
				$complex_results[]=$cur_info;
				$second_search_found=true;
				}
			}
		}
	if ($second_search_found) 
	{
	if ($search_level=='Family') generate_family_lists_table($complex_results);
	if ($search_level=='Unit') generate_unit_lists_table($complex_results);
	}
		else echo $nothing_found_msg;
	/*if ($search_level=='Family')
	{
	$fam_results=array();
	foreach ($family_tab_info as $cur_fam)
		{
		foreach ($results_arr as $cur_results)
			{
			if ($cur_results['Family_id']==$cur_fam['id'])
				{
				$fam_results[]=$cur_fam;
				}
			}
		}
	generate_family_lists_table($fam_results);
	}
	if ($search_level=='Unit')
	{
	$second_search_found=false;
	$unit_results=array();
	foreach ($fleets_info as $cur_unit)
		{
		foreach ($results_arr as $cur_results)
			{
			if ($cur_results['id']==$cur_unit['Family_id'])
				{
				$unit_results[]=$cur_unit;
				$second_search_found=true;
				}
			}
		}
	if ($second_search_found) generate_unit_lists_table($unit_results);
		else echo $nothing_found_msg;
	}*/
    }	
}
/**************************************/
$nothing_found_msg='<span class="search_nothing_label">Nothing has been found!</span>';

if ($logged_user_info!='superadmin') $cur_location=$logged_user_info['branch'];
  else $cur_location='all';
/*echo $cur_location;  
echo 'type: ',$search_type;*/
$renderer_handler=new search_renderer();

if ($search_mode=='code')
{
if ($code_type=='Unit')
{
$search_found=false;
//find if we got such code:
$results_arr=array();
foreach ($fleets_info as $cur_fleet)
  {
  if ($cur_fleet['Code']==$find_code)
     {
	 if ($search_type=='sr_onsite' and $cur_location!='all')
	    {
		if ($cur_fleet['Branch']!=$cur_location) continue;
		}
	 $search_found=true;
	 $results_arr[]=$cur_fleet;
	 }
  }
if (!$search_found) echo $nothing_found_msg;
  else
  {
  //show table with results:
  if ($search_level=='Unit') $renderer_handler->render_simple_results($search_level,$results_arr);
  if ($search_level=='Family') $renderer_handler->render_complex_results($search_level,$results_arr,$family_tab_info, $nothing_found_msg, $fleets_info);
  }
}
if ($code_type=='Family')
{
$search_found=false;
//find if we got such code:
$results_arr=array();
foreach ($family_tab_info as $cur_fleet)
  {
  if ($cur_fleet['Code']==$find_code)
     {
	 $cur_branch='';
	 foreach ($fleets_info as $cur_unit)
	 {
	 if ($cur_fleet['id']==$cur_unit['Family_id'])
	    {
		$cur_branch=$cur_unit['Branch'];
		}
	 }
	 if ($search_type=='sr_onsite' and $cur_location!='all')
	    {
		if ($cur_branch!=$cur_location) continue;
		}
	 $search_found=true;
	 $results_arr[]=$cur_fleet;
	 }
  }
if (!$search_found) echo $nothing_found_msg;
  else
  {
  //show table with results:
  if ($search_level=='Family') $renderer_handler->render_simple_results($search_level,$results_arr);
  if ($search_level=='Unit') $renderer_handler->render_complex_results($search_level,$results_arr,$family_tab_info, $nothing_found_msg, $fleets_info);
  }
}
}
if ($search_mode=='keyword')
{
//echo 'keyword search';
$search_found=false;
//find if we got such code:
//unit search
$results_arr=array();
$search_in_fields=array('Unit','Active','Description','Branch');
$results_arr=search_key_word($fleets_info,$search_in_fields,$find_word);

//family search
$search_found_1=$search_found;
$results_arr2=array();
$search_in_fields2=array('Family','Description');
$results_arr2=search_key_word($family_tab_info,$search_in_fields2,$find_word);
$search_found_2=$search_found;
if (!$search_found_1 and !$search_found_2) echo $nothing_found_msg;
  else
  {
  show_results($results_arr, $results_arr2);
  }
}
if ($search_mode=='dates')
{
//make dates search!
$start_date_unix=strtotime($find_from_date);
$end_date_unix=strtotime($find_to_date);
$search_field='creation_date';

$search_found_1=false;
if ($search_level=='Unit')
{
$search_found=false;
$results_unit_arr=array();
$results_unit_arr=search_date_range($fleets_info,$search_field,$start_date_unix,$end_date_unix);
$search_found_1=$search_found;
}

$search_found_2=false;
if ($search_level=='Family')
{
$search_found=false;
$results_fam_arr=array();
$results_fam_arr=search_date_range($family_tab_info,$search_field,$start_date_unix,$end_date_unix);
$search_found_2=$search_found;
}
if (!$search_found_1 and !$search_found_2) echo $nothing_found_msg;
	else
	{
	/*print_r($results_unit_arr);
	echo '<br><br>';
	print_r($results_fam_arr);*/
	//show_results($results_unit_arr, $results_fam_arr);
	if ($search_level=='Unit')
		{
		//show table:
		if (count($results_unit_arr)!=0) generate_unit_lists_table($results_unit_arr);
		}
	if ($search_level=='Family')
		{
		//show table:
		if (count($results_fam_arr)!=0) generate_family_lists_table($results_fam_arr);
		}
	}
}

//echo $search_type,'<br>Code: ',$find_code, '<br>Level: ',$search_level, '<br>Code type: ',$code_type;
}
//proceed customer table:
if (isset($_POST['get_customer_info']) and $_POST['get_customer_info'])
{
$filtered_vars=new vars_checker();
$filtered_vars->check_vars('POST',array('cust_id'),array('no'),array('num'),array('Customer id'),true);
if ($exit_in_the_end) exit;
$cust_id=$filtered_vars->get_var('cust_id');
unset($filtered_vars);
//require_once(__DIR__.'/contents.php');
$conn=db_connect();
get_customer_table();
mysql_close($conn);
$found_id=false;
foreach ($customer_list_info as $cur_cust)
	{
	if ($cur_cust['id']==$cust_id)
		{
		$found_id=true;
		echo $cur_cust['id'],'||',$cur_cust['Customer_Name'],'||',$cur_cust['Customer_Street_1'].', '.$cur_cust['Customer_Town'].', '.$cur_cust['Customer_County'],'||',$cur_cust['Customer_Name'],'<br>',$cur_cust['Customer_Landline'],'|| £ ',$cur_cust['Current_Balance'],' (of £ ',$cur_cust['Credit_Limit'],')';
		}
	}
if (!$found_id) echo 'Nothing found!';
}

?> 