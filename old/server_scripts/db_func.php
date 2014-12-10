<?php
date_default_timezone_set('Europe/London');
//function to work with db:
function db_connect($db_user='cl52-frdeskdb',$db_pass='2sCgrKN/M',$db_name='cl52-frdeskdb')
 {
 $connection=mysql_connect('localhost',$db_user,$db_pass) or die('<br><font style="color:DD4040">Couldn`t connect to mysql server</font> '.mysql_error());
 // on other server connection should be changed!
 mysql_select_db($db_name,$connection) or die('<br><font style="color:DD4040">Couldn`t connect to select db</font> '.mysql_error());
 $query='SET NAMES utf8';
 $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t set names</font> '.mysql_error());
 $query='SET CHARACTER SET utf8';
 $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t set encoding</font> '.mysql_error());
 return $connection;          
 }
 
//cookie check funtion:
function check_cookie($cookie_code,$conn,$conn_close, $return_user_id=false)
{
global $admin_ac;
global $emp_logged;
global $show_name;
global $user_only_view_allowed;
$user_id_to_return=0;
$query="SELECT `id`,`first_name`, `second_name`,`permissions` FROM `users`";
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
$id_max=mysql_num_rows($result);
for ($q=0; $q<$id_max; $q++)
{
$emp_first_name=mysql_result($result,$q,'first_name');
$emp_second_name=mysql_result($result,$q,'second_name');
$user_perm=mysql_result($result,$q,'permissions');
$usr_id=mysql_result($result,$q,'id');
$crypt_code=crypt($emp_first_name.$emp_second_name,'$6$Someunknownsecret');
$crypt_code_arr=explode('$',$crypt_code);
$cookie_db_code=$crypt_code_arr[3];
unset($crypt_code_arr,$crypt_code);
if ($cookie_code==$cookie_db_code)
  {
  if ($user_perm=='V') $user_only_view_allowed=true;
  $emp_logged=true;
  $show_name=$emp_first_name.' '.$emp_second_name;
  $user_id_to_return=$usr_id;
  break;
  }
}
if (!$emp_logged)
{
//check if admin:
  $query="SELECT `first_name`, `second_name` FROM `main_users`";
  $result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query</font> '.mysql_error());
  $admin_first_name=mysql_result($result,0,'first_name');
  $admin_second_name=mysql_result($result,0,'second_name');
  $crypt_code=crypt($admin_first_name.$admin_second_name,'$6$Someunknownadminsecret');
  $crypt_code_arr=explode('$',$crypt_code);
  $cookie_db_code=$crypt_code_arr[3];
  unset($crypt_code_arr,$crypt_code,$admin_first_name,$admin_second_name);
  if ($cookie_code==$cookie_db_code)
  {
  $admin_ac=true;
  $show_name='admin';
  $user_id_to_return=777;
  }
  else
  {
  echo 'Bad cookie!';
  setcookie("user", "", time()-3600);
  mysql_close($conn); 
  exit;
  }
}  
if ($conn_close=='close') mysql_close($conn); 
if ($return_user_id) return $user_id_to_return;
} 

//hours count:
function count_hours($arr_uni)  //returns hours
	       {
		   $total_hours=0;
		   foreach ($arr_uni as $cur_times_arr)
		    {
	        $cur_start_time=$cur_times_arr[0];
			$cur_end_time=$cur_times_arr[1];
			$cur_break_start_time=$cur_times_arr[2];
			$cur_break_end_time=$cur_times_arr[3];
			
			if ($cur_break_start_time=='N/A') continue;
			if ($cur_break_end_time=='N/A' or $cur_end_time=='N/A')
			  {
			  //take diffr betweeen start and break_start:
			  $start_time_unix=strtotime($cur_start_time);
			  $start_break_unix=strtotime($cur_break_start_time);
			  $hours_diffr=($start_break_unix-$start_time_unix)/60/60;
			  $total_hours+=$hours_diffr;
			  continue;
			  }
			//counting hours:
			$start_time_unix=strtotime($cur_start_time);
			$start_break_unix=strtotime($cur_break_start_time);
			$end_time_unix=strtotime($cur_end_time);
			$end_break_unix=strtotime($cur_break_end_time);
			$hours_diffr=round((($start_break_unix-$start_time_unix)+($end_time_unix-$end_break_unix))/60/60,1);
			$total_hours+=$hours_diffr;
	        }
			return $total_hours;
			}
//get week range:
function rangeWeek($datestr,$fr_date_format='Y-m-d',$snd_date_format='Y-m-d') 
    {
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
	if ($snd_date_format=='j Y')
	  {
	  $tmp_start = date('N', $dt)==1 ? date('m', $dt) : date('m', strtotime('last monday', $dt));
      $tmp_stop = date('N', $dt)==7 ? date('m', $dt) : date('m', strtotime('next sunday', $dt));
      if ($tmp_start!=$tmp_stop) $snd_date_format='M j Y';
	  unset($tmp_stop,$tmp_start);
	  }
    $res[0] = date('N', $dt)==1 ? date($fr_date_format, $dt) : date($fr_date_format, strtotime('last monday', $dt));
    $res[1] = date('N', $dt)==7 ? date($snd_date_format, $dt) : date($snd_date_format, strtotime('next sunday', $dt));
    return $res;
    }	
//getting users info all:
function get_user_info()
{
global $user_info;
$query='SELECT * FROM `users`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating user info array:
for ($q=0;$q<$id_max;$q++)
  {
  $user_info[$q]['id']=mysql_result($result,$q,'id');
  $user_info[$q]['first_name']=mysql_result($result,$q,'first_name');
  $user_info[$q]['second_name']=mysql_result($result,$q,'second_name');
  $user_info[$q]['mail']=mysql_result($result,$q,'mail');
  $user_info[$q]['branch']=mysql_result($result,$q,'branch');
  $user_info[$q]['active']=mysql_result($result,$q,'active');
  $user_info[$q]['permissions']=mysql_result($result,$q,'permissions');
  $user_info[$q]['nick_name']=mysql_result($result,$q,'nick_name');
  for ($i=1; $i<=8; $i++)
    {
    $user_info[$q]['permission'.$i]=mysql_result($result,$q,'permission'.$i);
	}
  }  
if ($id_max==0) $user_info='';     
}
//getting loged user info:
function get_active_user_info()
{
global $logged_user_info;
global $conn;
global $user_info;
$show_name='';
$cookie_code=$_COOKIE['user'];
//$conn=db_connect();
$user_id=check_cookie($cookie_code,$conn,'not close',true);
if ($user_id==777) $logged_user_info='superadmin';
  else
  {
  //making array of user info:
  foreach ($user_info as $cur_usr_info)
     {
	 if ($user_id==$cur_usr_info['id']) $logged_user_info=$cur_usr_info;
	 }
  }
}
//getting settings
function get_settings()
{
global $settings_info;
$query='SELECT * FROM `settings`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating user entrys array:
for ($q=0;$q<$id_max;$q++)
  {
  $settings_info[$q]['id']=mysql_result($result,$q,'id');
  $settings_info[$q]['name']=mysql_result($result,$q,'name');
  $settings_info[$q]['values']=mysql_result($result,$q,'values');
  }  
if ($id_max==0) $settings_info='';    
}
//getting branches
function get_branches()
{
global $branches_info;
$query='SELECT * FROM `branches`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating branches entries array:
for ($q=0;$q<$id_max;$q++)
  {
  $branches_info[$q]['id']=mysql_result($result,$q,'id');
  $branches_info[$q]['branch_name']=mysql_result($result,$q,'branch_name');
  $branches_info[$q]['address_box']=mysql_result($result,$q,'address_box');
  $branches_info[$q]['phone_number']=mysql_result($result,$q,'phone_number');
  }  
if ($id_max==0) $branches_info='';    
}
//getting fleets
function get_fleet_table()
{
global $fleets_info;
$query='SELECT * FROM `fleet`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating user entrys array:
for ($q=0;$q<$id_max;$q++)
  {
  $fleets_info[$q]['id']=mysql_result($result,$q,'id');
  $fleets_info[$q]['Code']=mysql_result($result,$q,'Code');
  $fleets_info[$q]['Unit']=mysql_result($result,$q,'Unit');
  $fleets_info[$q]['Cost']=mysql_result($result,$q,'Cost');
  $fleets_info[$q]['Family_id']=mysql_result($result,$q,'Family_id');
  $fleets_info[$q]['Supplier_adress_id']=mysql_result($result,$q,'Supplier_adress_id');
  $fleets_info[$q]['Date_Hired']=mysql_result($result,$q,'Date_Hired');
  $fleets_info[$q]['Available']=mysql_result($result,$q,'Available');
  $fleets_info[$q]['Active']=mysql_result($result,$q,'Active');
  $fleets_info[$q]['Weight']=mysql_result($result,$q,'Weight');
  $fleets_info[$q]['Income']=mysql_result($result,$q,'Income');
  $fleets_info[$q]['Disposal']=mysql_result($result,$q,'Disposal');
  $fleets_info[$q]['Description']=mysql_result($result,$q,'Description');
  $fleets_info[$q]['Total_Days_Hired']=mysql_result($result,$q,'Total_Days_Hired');
  $fleets_info[$q]['Branch']=mysql_result($result,$q,'Branch');
  $fleets_info[$q]['Stock']=mysql_result($result,$q,'Stock');
  $fleets_info[$q]['Current_Available_Multi']=mysql_result($result,$q,'Current_Available_Multi');
  $fleets_info[$q]['Sale']=mysql_result($result,$q,'Sale');
  $fleets_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $fleets_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  $fleets_info[$q]['Returned_date']=mysql_result($result,$q,'Returned_date');
  $fleets_info[$q]['Sold_date']=mysql_result($result,$q,'Sold_date');
  }  
if ($id_max==0) $fleets_info='';    
}
//getting family table
function get_family_table()
{
global $family_tab_info;
$query='SELECT * FROM `family`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $family_tab_info[$q]['id']=mysql_result($result,$q,'id');
  $family_tab_info[$q]['Code']=mysql_result($result,$q,'Code');
  $family_tab_info[$q]['Family']=mysql_result($result,$q,'Family');
  $family_tab_info[$q]['Price_1_day']=mysql_result($result,$q,'Price_1_day');
  $family_tab_info[$q]['Price_2_days']=mysql_result($result,$q,'Price_2_days');
  $family_tab_info[$q]['Price_1_week']=mysql_result($result,$q,'Price_1_week');
  $family_tab_info[$q]['Min_1_Week']=mysql_result($result,$q,'Min_1_Week');
  $family_tab_info[$q]['Description']=mysql_result($result,$q,'Description');
  $family_tab_info[$q]['Extra_day']=mysql_result($result,$q,'Extra_day');
  $family_tab_info[$q]['Sell_price']=mysql_result($result,$q,'Sell_price');
  $family_tab_info[$q]['Fee']=mysql_result($result,$q,'Fee');
  $family_tab_info[$q]['Avaible']=mysql_result($result,$q,'Avaible');
  $family_tab_info[$q]['Total_hire']=mysql_result($result,$q,'Total_hire');
  $family_tab_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $family_tab_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  }  
if ($id_max==0) $family_tab_info='';    
}
//getting maintenance table
function get_maintenance_table()
{
global $maintenance_info;
$query='SELECT * FROM `maintenance`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $maintenance_info[$q]['id']=mysql_result($result,$q,'id');
  $maintenance_info[$q]['Date']=mysql_result($result,$q,'Date');
  $maintenance_info[$q]['Family_id']=mysql_result($result,$q,'Family_id');
  $maintenance_info[$q]['Unit_fleet_id']=mysql_result($result,$q,'Unit_fleet_id');
  $maintenance_info[$q]['Value']=mysql_result($result,$q,'Value');
  $maintenance_info[$q]['Note']=mysql_result($result,$q,'Note');
  }  
if ($id_max==0) $maintenance_info='';    
}
//getting address table
function get_address_table()
{
global $address_info;
$query='SELECT * FROM `address`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $address_info[$q]['id']=mysql_result($result,$q,'id');
  $address_info[$q]['Code']=mysql_result($result,$q,'Code');
  $address_info[$q]['Company_Name']=mysql_result($result,$q,'Company_Name');
  $address_info[$q]['Street1']=mysql_result($result,$q,'Street1');
  $address_info[$q]['Street2']=mysql_result($result,$q,'Street2');
  $address_info[$q]['City']=mysql_result($result,$q,'City');
  $address_info[$q]['Post_Code']=mysql_result($result,$q,'Post_Code');
  $address_info[$q]['Class']=mysql_result($result,$q,'Class');
  $address_info[$q]['Primary_Contact_id']=mysql_result($result,$q,'Primary_Contact_id');
  $address_info[$q]['Secondary_Contact_id']=mysql_result($result,$q,'Secondary_Contact_id');
  $address_info[$q]['Current_Balance']=mysql_result($result,$q,'Current_Balance');
  $address_info[$q]['Supplier_Credit_Limit']=mysql_result($result,$q,'Supplier_Credit_Limit');
  $address_info[$q]['Days_sup']=mysql_result($result,$q,'Days_sup');
  $address_info[$q]['Customer_Credit_Limit']=mysql_result($result,$q,'Customer_Credit_Limit');
  $address_info[$q]['Days_cust']=mysql_result($result,$q,'Days_cust');
  }  
if ($id_max==0) $address_info='';    
}
//getting contact table
function get_contact_table()
{
global $contact_info;
$query='SELECT * FROM `contact_list`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $contact_info[$q]['id']=mysql_result($result,$q,'id');
  $contact_info[$q]['First_Name']=mysql_result($result,$q,'First_Name');
  $contact_info[$q]['Surname']=mysql_result($result,$q,'Surname');
  $contact_info[$q]['Email']=mysql_result($result,$q,'Email');
  $contact_info[$q]['Phone1']=mysql_result($result,$q,'Phone1');
  $contact_info[$q]['Phone2']=mysql_result($result,$q,'Phone2');
  $contact_info[$q]['Type']=mysql_result($result,$q,'Type');
  $contact_info[$q]['Address_Code']=mysql_result($result,$q,'Address_Code');
  $contact_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $contact_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  }  
if ($id_max==0) $contact_info='';    
}
//getting sales_ledger table
function get_sales_ledger_table()
{
global $sales_ledger_info;
$query='SELECT * FROM `sales_ledger`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $sales_ledger_info[$q]['id']=mysql_result($result,$q,'id');
  $sales_ledger_info[$q]['Customer']=mysql_result($result,$q,'Customer');
  $sales_ledger_info[$q]['Item']=mysql_result($result,$q,'Item');
  $sales_ledger_info[$q]['Date']=mysql_result($result,$q,'Date');
  $sales_ledger_info[$q]['Net']=mysql_result($result,$q,'Net');
  $sales_ledger_info[$q]['VAT']=mysql_result($result,$q,'VAT');
  $sales_ledger_info[$q]['Gross']=mysql_result($result,$q,'Gross');
  $sales_ledger_info[$q]['Paid']=mysql_result($result,$q,'Paid');
  }  
if ($id_max==0) $sales_ledger_info='';    
}
//getting purchase_ledger table
function get_purchase_ledger_table()
{
global $purchase_ledger_info;
$query='SELECT * FROM `purchase_ledger`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $purchase_ledger_info[$q]['id']=mysql_result($result,$q,'id');
  $purchase_ledger_info[$q]['Supplier']=mysql_result($result,$q,'Supplier');
  $purchase_ledger_info[$q]['Item']=mysql_result($result,$q,'Item');
  $purchase_ledger_info[$q]['Type']=mysql_result($result,$q,'Type');
  $purchase_ledger_info[$q]['Date']=mysql_result($result,$q,'Date');
  $purchase_ledger_info[$q]['Net']=mysql_result($result,$q,'Net');
  $purchase_ledger_info[$q]['VAT']=mysql_result($result,$q,'VAT');
  $purchase_ledger_info[$q]['Gross']=mysql_result($result,$q,'Gross');
  $purchase_ledger_info[$q]['Paid']=mysql_result($result,$q,'Paid');
  }  
if ($id_max==0) $purchase_ledger_info='';    
}
//getting stock table
function get_stock_table()
{
global $stock_list_info;
$query='SELECT * FROM `stock_list`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $stock_list_info[$q]['id']=mysql_result($result,$q,'id');
  $stock_list_info[$q]['Code']=mysql_result($result,$q,'Code');
  $stock_list_info[$q]['Name']=mysql_result($result,$q,'Name');
  $stock_list_info[$q]['Description']=mysql_result($result,$q,'Description');
  $stock_list_info[$q]['In_Stock']=mysql_result($result,$q,'In_Stock');
  $stock_list_info[$q]['Price']=mysql_result($result,$q,'Price');
  $stock_list_info[$q]['On_Order']=mysql_result($result,$q,'On_Order');
  $stock_list_info[$q]['Requires_Payment']=mysql_result($result,$q,'Requires_Payment');
  $stock_list_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $stock_list_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  }  
if ($id_max==0) $stock_list_info='';    
}
//getting supplier table
function get_supplier_table()
{
global $supplier_list_info;
$query='SELECT * FROM `supplier_list`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $supplier_list_info[$q]['id']=mysql_result($result,$q,'id');
  $supplier_list_info[$q]['Code']=mysql_result($result,$q,'Code');
  $supplier_list_info[$q]['Supplier_Name']=mysql_result($result,$q,'Supplier_Name');
  $supplier_list_info[$q]['Supplier_Street_1']=mysql_result($result,$q,'Supplier_Street_1');
  $supplier_list_info[$q]['Supplier_Street_2']=mysql_result($result,$q,'Supplier_Street_2');
  $supplier_list_info[$q]['Supplier_Street_3']=mysql_result($result,$q,'Supplier_Street_3');
  $supplier_list_info[$q]['Supplier_Town']=mysql_result($result,$q,'Supplier_Town');
  $supplier_list_info[$q]['Supplier_County']=mysql_result($result,$q,'Supplier_County');
  $supplier_list_info[$q]['Supplier_Post_Code']=mysql_result($result,$q,'Supplier_Post_Code');
  $supplier_list_info[$q]['Supplier_Main_Branch']=mysql_result($result,$q,'Supplier_Main_Branch');
  $supplier_list_info[$q]['Supplier_Landline']=mysql_result($result,$q,'Supplier_Landline');
  $supplier_list_info[$q]['Credit_Limit']=mysql_result($result,$q,'Credit_Limit');
  $supplier_list_info[$q]['Credit_Due']=mysql_result($result,$q,'Credit_Due');
  $supplier_list_info[$q]['Current_Balance']=mysql_result($result,$q,'Current_Balance');
  $supplier_list_info[$q]['Contacts']=mysql_result($result,$q,'Contacts');
  $supplier_list_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $supplier_list_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  }  
if ($id_max==0) $supplier_list_info='';    
}
//getting customer table
function get_customer_table()
{
global $customer_list_info;
$query='SELECT * FROM `customer_list`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $customer_list_info[$q]['id']=mysql_result($result,$q,'id');
  $customer_list_info[$q]['Code']=mysql_result($result,$q,'Code');
  $customer_list_info[$q]['Customer_Name']=mysql_result($result,$q,'Customer_Name');
  $customer_list_info[$q]['Customer_Street_1']=mysql_result($result,$q,'Customer_Street_1');
  $customer_list_info[$q]['Customer_Street_2']=mysql_result($result,$q,'Customer_Street_2');
  $customer_list_info[$q]['Customer_Street_3']=mysql_result($result,$q,'Customer_Street_3');
  $customer_list_info[$q]['Customer_Town']=mysql_result($result,$q,'Customer_Town');
  $customer_list_info[$q]['Customer_County']=mysql_result($result,$q,'Customer_County');
  $customer_list_info[$q]['Customer_Post_Code']=mysql_result($result,$q,'Customer_Post_Code');
  $customer_list_info[$q]['Customer_Main_Branch']=mysql_result($result,$q,'Customer_Main_Branch');
  $customer_list_info[$q]['Customer_Landline']=mysql_result($result,$q,'Customer_Landline');
  $customer_list_info[$q]['Credit_Limit']=mysql_result($result,$q,'Credit_Limit');
  $customer_list_info[$q]['Credit_Due']=mysql_result($result,$q,'Credit_Due');
  $customer_list_info[$q]['Current_Balance']=mysql_result($result,$q,'Current_Balance');
  $customer_list_info[$q]['Contacts']=mysql_result($result,$q,'Contacts');
  $customer_list_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $customer_list_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  }  
if ($id_max==0) $customer_list_info='';    
}
//getting nominal ledger table
function get_nominal_ledger_table()
{
global $nominal_ledger_info;
$query='SELECT * FROM `nominal_ledger`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $nominal_ledger_info[$q]['id']=mysql_result($result,$q,'id');
  $nominal_ledger_info[$q]['Code']=mysql_result($result,$q,'Code');
  $nominal_ledger_info[$q]['Name']=mysql_result($result,$q,'Name');
  $nominal_ledger_info[$q]['Debit']=mysql_result($result,$q,'Debit');
  $nominal_ledger_info[$q]['Credit']=mysql_result($result,$q,'Credit');
  $nominal_ledger_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $nominal_ledger_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  }  
if ($id_max==0) $nominal_ledger_info='';    
}
//getting audit trail table
function get_audit_trail_table()
{
global $audit_trail_info;
$query='SELECT * FROM `audit_trail`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $audit_trail_info[$q]['id']=mysql_result($result,$q,'id');
  $audit_trail_info[$q]['Reference']=mysql_result($result,$q,'Reference');
  $audit_trail_info[$q]['Description']=mysql_result($result,$q,'Description');
  $audit_trail_info[$q]['Ledger']=mysql_result($result,$q,'Ledger');
  $audit_trail_info[$q]['Sub_Ledger']=mysql_result($result,$q,'Sub_Ledger');
  $audit_trail_info[$q]['Debit']=mysql_result($result,$q,'Debit');
  $audit_trail_info[$q]['Credit']=mysql_result($result,$q,'Credit');
  $audit_trail_info[$q]['Date']=mysql_result($result,$q,'Date');
  $audit_trail_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $audit_trail_info[$q]['Location_Stamp']=mysql_result($result,$q,'Location_Stamp');
  $audit_trail_info[$q]['User_Stamp']=mysql_result($result,$q,'User_Stamp');
  }  
if ($id_max==0) $audit_trail_info='';    
}
//getting bank trail table
function get_bank_trail_table()
{
global $bank_trail_info;
$query='SELECT * FROM `bank_trail`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $bank_trail_info[$q]['id']=mysql_result($result,$q,'id');
  $bank_trail_info[$q]['Reference']=mysql_result($result,$q,'Reference');
  $bank_trail_info[$q]['Description']=mysql_result($result,$q,'Description');
  $bank_trail_info[$q]['Bank_Code']=mysql_result($result,$q,'Bank_Code');
  $bank_trail_info[$q]['Debit']=mysql_result($result,$q,'Debit');
  $bank_trail_info[$q]['Credit']=mysql_result($result,$q,'Credit');
  $bank_trail_info[$q]['Date']=mysql_result($result,$q,'Date');
  $bank_trail_info[$q]['Reconciled']=mysql_result($result,$q,'Reconciled');
  $bank_trail_info[$q]['Reconciliation_Date']=mysql_result($result,$q,'Reconciliation_Date');
  $bank_trail_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $bank_trail_info[$q]['Location_Stamp']=mysql_result($result,$q,'Location_Stamp');
  $bank_trail_info[$q]['User_Stamp']=mysql_result($result,$q,'User_Stamp');
  }  
if ($id_max==0) $bank_trail_info='';    
}
//getting bank ledger table
function get_bank_ledger_table()
{
global $bank_ledger_info;
$query='SELECT * FROM `bank_ledger`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $bank_ledger_info[$q]['id']=mysql_result($result,$q,'id');
  $bank_ledger_info[$q]['Code']=mysql_result($result,$q,'Code');
  $bank_ledger_info[$q]['Bank_Account_Name']=mysql_result($result,$q,'Bank_Account_Name');
  $bank_ledger_info[$q]['Balance']=mysql_result($result,$q,'Balance');
  $bank_ledger_info[$q]['Date_Opened']=mysql_result($result,$q,'Date_Opened');
  $bank_ledger_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $bank_ledger_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  $bank_ledger_info[$q]['Location_Stamp']=mysql_result($result,$q,'Location_Stamp');
  $bank_ledger_info[$q]['User_Stamp']=mysql_result($result,$q,'User_Stamp');
  }  
if ($id_max==0) $bank_ledger_info='';    
}
//getting VAT list table
function get_vat_list_table()
{
global $vat_list_info;
$query='SELECT * FROM `vat_list`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $vat_list_info[$q]['id']=mysql_result($result,$q,'id');
  $vat_list_info[$q]['Code']=mysql_result($result,$q,'Code');
  $vat_list_info[$q]['Percentage']=mysql_result($result,$q,'Percentage');
  $vat_list_info[$q]['Note']=mysql_result($result,$q,'Note');
  $vat_list_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $vat_list_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  }  
if ($id_max==0) $vat_list_info='';    
}
//getting holiday list table
function get_holiday_list_table()
{
global $holiday_list_info;
$query='SELECT * FROM `holiday_list`';
$result=mysql_query($query) or die('<br><font style="color:DD4040">Couldn`t execute query!</font> '.mysql_error());
$id_max=mysql_num_rows($result);
//creating array:
for ($q=0;$q<$id_max;$q++)
  {
  $holiday_list_info[$q]['id']=mysql_result($result,$q,'id');
  $holiday_list_info[$q]['Holiday']=mysql_result($result,$q,'Holiday');
  $holiday_list_info[$q]['Date_Start']=mysql_result($result,$q,'Date_Start');
  $holiday_list_info[$q]['Date_End']=mysql_result($result,$q,'Date_End');
  $holiday_list_info[$q]['Affects_Hiring']=mysql_result($result,$q,'Affects_Hiring');
  $holiday_list_info[$q]['creation_date']=mysql_result($result,$q,'creation_date');
  $holiday_list_info[$q]['mod_date']=mysql_result($result,$q,'mod_date');
  }  
if ($id_max==0) $holiday_list_info='';    
}


/*
simple class use:
$filtered_vars=new vars_checker();
$filtered_vars->check_vars('POST',array('log_usr_name','log_pass'),array('no','no'),array('nickname','no check'),array('username','password'),true);
$usr_name_db=strtolower($filtered_vars->get_var('log_usr_name'));
$emp_pass_db=$filtered_vars->get_var('log_pass');

*/
//check class:
class vars_checker
{
private $all_vars_arr=array();

// function for variable verification:
public function check_vars($input_type,$var_name_arr,$vars_allow_empty,$vars_check_type,$var_err_name,$prepare_for_db)
  {
  $exit_in_the_end=false;
  /*
  $input_type (string) -- POST or GET or REQUEST
  $var_name_arr -- array with variable names (for POST and GET)
  $vars_allow_empty -- array with empty allow ('yes' or 'no') rules.
  $vars_check_type -- array with regex check types 
  (may contain type name or regex itself) ('no check' also allowed)
  $var_err_name -- array with names for "Please, enter" messages.
  $prepare_for_db (boolean) -- if variable should be prepared for db.
  */
  if ($prepare_for_db) $conn=db_connect();
  //proceed each variable:
  foreach ($var_name_arr as $key=>$cur_var)
    {
	$current_variable_value=$this->check_if_set($input_type,$cur_var,$var_err_name[$key]);
	if (!$exit_in_the_end) $current_variable_value=$this->check_if_empty($vars_allow_empty[$key],$current_variable_value,$var_err_name[$key]);
	if ($vars_check_type[$key]!='no check' and !$exit_in_the_end and $current_variable_value!='')
	   {
	   $this->check_regex($vars_check_type[$key],$current_variable_value,$var_err_name[$key]);
	   }
	 if ($prepare_for_db and !$exit_in_the_end) $current_variable_value=mysql_real_escape_string($current_variable_value);
	if (!$exit_in_the_end) $this->all_vars_arr[$cur_var]=$current_variable_value;
	}
  if ($prepare_for_db) mysql_close($conn);	
  if ($exit_in_the_end) exit;
  }
  
private function check_if_set($input_type,$cur_var,$err_name_only)
  {
  global $exit_in_the_end;
  //check if set:
	if ($input_type=='POST')
	    {
		if (!isset($_POST[$cur_var]))
		  {
		  echo 'Please set '.$err_name_only.'!'."\n";
		  $exit_in_the_end=true;
		  }
		  else return $_POST[$cur_var];
		}
	if ($input_type=='GET')
	    {
		if (!isset($_GET[$cur_var]))
		  {
		  echo 'Please set '.$err_name_only.'!'."\n";
		  $exit_in_the_end=true;
		  }
		  else return $_GET[$cur_var];
		}	
	if ($input_type=='REQUEST')
	    {
		if (!isset($_REQUEST[$cur_var]))
		  {
		  echo 'Please set '.$err_name_only.'!'."\n";
		  $exit_in_the_end=true;
		  }
		  else return $_REQUEST[$cur_var];
		}	
	if ($input_type!='POST' and $input_type!='GET' and $input_type!='REQUEST')
	    {
		echo 'Bad input type!'."\n";
		$exit_in_the_end=true;
		}
  }  
  
private function check_if_empty($empty_allow,$cur_var_value,$cur_err_name)
  {
  global $exit_in_the_end;
  //check if empty:
  $cur_val=trim(strip_tags($cur_var_value));
	if ($empty_allow=='no' and $cur_val=='')
	   {
	   echo 'Please enter or choose '.$cur_err_name.'!'."\n";
	   $exit_in_the_end=true;
	   }
	   else return $cur_val;
  }
  
public function check_regex($check_type,$current_variable_value,$var_err_name)
  {
  global $exit_in_the_end;
  $regex_patterns=array(
                 'nickname'=>'/^[^`~!@#$%^&*()+=:><?]+$/u',
				 'name'=>'/^[^0-9_`~!@#$%^&*()+=:><?]+$/u',
				 'mail'=>'/^([a-zA-Z0-9\._%-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4})*$/',
				 'num'=>'/^[0-9\.,]*$/',
				 'url'=>'/^(((http|https|ftp):\/\/)?([[a-zA-Z0-9]\-\.])+(\.)([[a-zA-Z0-9]]){2,4}([[a-zA-Z0-9]\/+=%&_\.~?\-]*))*$/',
				 'date(mm/-dd/-yyyy)'=>'/^((0?[1-9]|1[012])[- /\.](0?[1-9]|[12][0-9]|3[01])[- /\.](19|20)?[0-9]{2})*$/',
				 'date(dd/-mm/-yyyy)'=>'/^((0?[1-9]|[12][0-9]|3[01])[- \.](0?[1-9]|1[012])[- \.](19|20)?[0-9]{2})*$/');

  //check on regex:
	   if (array_key_exists($check_type,$regex_patterns))
	      {
		  $regex_for_check=$regex_patterns[$check_type];
		  }
		  else $regex_for_check=$check_type;
	  if (!preg_match($regex_for_check,$current_variable_value))
	     {
		 echo ucfirst($var_err_name).' has bad value!';
		 $exit_in_the_end=true;
		 }
  }
  
//return variable or array of variables  
public function get_var($key_name)
  {
  if ($key_name=='array') return $this->all_vars_arr;
     else return $this->all_vars_arr[$key_name];
  }  
}  

class b_box_maker
{
private $cur_box_sets_array=array();
public function add_button_with_box($b_id, $b_name, $box_id, $box_label, $form_id, $table_html_content, $submit_id, $submit_label)
{
/*$b_id -- button id
$b_name -- button label
$box_id -- box id
$box_label -- box label
*/
//save vars to array for the next use:
$cur_sets_arr['button_id']=$b_id;
$cur_sets_arr['button_label']=$b_name;
$cur_sets_arr['box_id']=$box_id;
$cur_sets_arr['box_label']=$box_label;
$cur_sets_arr['form_id']=$form_id;
$cur_sets_arr['submit_id']=$submit_id;
$cur_sets_arr['submit_label']=$submit_label;
$this->cur_box_sets_array=$cur_sets_arr;
unset($cur_sets_arr);
echo "
<button id='$b_id'>$b_name</button>
<div id='$box_id'>
<span>$box_label</span>:
<form name='$form_id' id='$form_id' method='post' action='./server_scripts/ajax_worker.php' >
<table id='tb_form' style='text-align:center; margin:0 auto;'>
$table_html_content
</table>
<div style='text-align:center; margin-top:15px;'>
<input type='submit' name='submit_but' id='$submit_id' value='$submit_label'/>
</div>
</form>
</div>";
}

public function make_js_script($box_sizes, $form_additional_data='',$sc_dialog_open='',$sc_dialog_close='',$sc_suc_submit='',$sc_before_form='')
{
//$box_sizes -- array('width'=>950, 'height'=>500);
//$form_additional_data --- data to be passed thought POST
$form_data='';
if ($form_additional_data!='') $form_data=', data:{'.$form_additional_data.'}';
//getting some values from settings:
$cur_sets_arr=$this->cur_box_sets_array;
echo "$('#".$cur_sets_arr['box_id']."').hide();						  
$('#".$cur_sets_arr['button_id']."').button().bind('click', function ()
    {
	$('#".$cur_sets_arr['box_id']."').show().dialog({
                                                                   height:".$box_sizes['height'].",
																   width:".$box_sizes['width'].",
																   modal:true,
																   title:'".$cur_sets_arr['box_label']."',
																   resizable:false,
																   draggable:false,
	                                                               close:function ()
																          {
																		  $sc_dialog_close
																		  $('#".$cur_sets_arr['form_id']."').resetForm();
																		  },open:function ()
																		       {
																			   $sc_dialog_open
																			   $('#".$cur_sets_arr['submit_id']."').removeAttr('disabled');
																			   }});	
	});	
$('#".$cur_sets_arr['submit_id']."').button().unbind('click').bind('click', function ()
     {
	 $sc_before_form
	 $('#".$cur_sets_arr['form_id']."').ajaxForm({url:'./server_scripts/ajax_worker.php',type:'POST' $form_data, success:function (data)
	                    {
						alert(data);
						if ($.trim(data).indexOf('Successfully')!=-1) 
						     {
							 $sc_suc_submit
							 $('#".$cur_sets_arr['form_id']."').resetForm();
							 window.location.reload();
							 }
						$('#".$cur_sets_arr['submit_id']."').removeAttr('disabled');
						},beforeSubmit: function ()
						      {
							  $('#".$cur_sets_arr['submit_id']."').attr('disabled', 'disabled');
							  }});
	 });	 ";
}

}

?>