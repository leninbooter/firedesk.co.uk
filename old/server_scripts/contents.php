<?php
require_once(__DIR__.'/commons.php');
//getting user info:
$conn=db_connect();
get_user_info();   
get_settings();  
get_family_table();
get_fleet_table();
get_address_table();
get_active_user_info();
get_stock_table();
get_supplier_table();
get_contact_table();
get_branches();
get_customer_table();
get_nominal_ledger_table();
get_audit_trail_table();
get_bank_trail_table();
get_bank_ledger_table();
get_vat_list_table();
get_holiday_list_table();
mysql_close($conn);
unset($q);
/*----------------------------------------------------------------------------------------*/
//unit_list:
$cells_array=array('Unit Code'=>'Code','Name'=>'Unit',
'Description'=>'Description','Active'=>'Active','Location'=>'Branch');
$temp_handler=new panels_templates();
$content_unit_list=$temp_handler->generate_list_table($fleets_info,$cells_array);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
//family_list:
$cells_array=array('Family Code'=>'Code','Family'=>'Family','Description'=>'Description',
'1 Day'=>'Price_1_day','2 Days'=>'Price_2_days','1 Week'=>'Price_1_week',
'Min 1 Week'=>'Min_1_Week','Availabile'=>'Avaible','Total Hire'=>'Total_hire');
$temp_handler=new panels_templates();
$content_family_list=$temp_handler->generate_list_table($family_tab_info,$cells_array);
unset($temp_handler);

/*----------------------------------------------------------------------------------------*/
//view_unit:
$cells_array=array('Unit'=>'Unit','Cost'=>'Cost',
'Date Hired'=>'Date_Hired','Hire'=>'Available','Active'=>'Active',
'Weight (kg)'=>'Weight','Income'=>'Income','Disposal Date'=>'Disposal',
'Description'=>'Description','Total Days Hired'=>'Total_Days_Hired',
'Branch'=>'Branch','Stock'=>'Stock','Current Available Multi'=>'Current_Available_Multi',
'Sales'=>'Sale','Creation date'=>'creation_date','Modified date'=>'mod_date',
'Returned Date'=>'Returned_date','Sold Date'=>'Sold_date');
$temp_handler=new panels_templates();
if ($id_code==0) $cur_cd=$fleets_info[0]['Code'];
  else $cur_cd=$id_code;
$content_view_unit=$temp_handler->generate_details_table($cur_cd,$fleets_info,$cells_array);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
//view_family:
$cells_array=array('Family'=>'Family','1 Day'=>'Price_1_day',
'2 Days'=>'Price_2_days','1 Week'=>'Price_1_week','Min 1 Week'=>'Min_1_Week',
'Description'=>'Description'/*,'Extra day'=>'Extra_day'*/,'Sell price'=>'Sell_price',
'Fee'=>'Fee','Avaible'=>'Avaible','Total hire'=>'Total_hire',
'Creation date'=>'creation_date','Modified date'=>'mod_date');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=$family_tab_info[0]['Code'];
  else $cur_cd=$id_code;
$content_view_family=$temp_handler->generate_details_table($cur_cd,$family_tab_info,$cells_array);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
//new_unit:
$cells_array=array('Unit'=>'Unit','Cost'=>'Cost',
'Family'=>'Family_id','Supplier Name'=>'Supplier_adress_id',
'Date Hired'=>'Date_Hired','Hire'=>'Available','Active'=>'Active',
'Weight (kg)'=>'Weight','Income'=>'Income','Disposal Date'=>'Disposal',
'Description'=>'Description','Total Days Hired'=>'Total_Days_Hired',
'Branch'=>'Branch','Stock'=>'Stock','Current Available Multi'=>'Current_Available_Multi',
'Sales'=>'Sale','Returned Date'=>'Returned_date','Sold Date'=>'Sold_date');
$types_array=array('Unit'=>'text','Cost'=>'text',
'Family'=>'select','Supplier Name'=>'select',
'Date Hired'=>'date','Hire'=>'checkbox','Active'=>'select',
'Weight (kg)'=>'text','Income'=>'text','Disposal Date'=>'date',
'Description'=>'text','Total Days Hired'=>'text',
'Branch'=>'select','Stock'=>'text','Current Available Multi'=>'text',
'Sales'=>'text','Returned Date'=>'date','Sold Date'=>'date');
$cur_branches='';
foreach ($settings_info as $cur_arr)
  {
  if ($cur_arr['name']=='branches')
    {
	$cur_branches=$cur_arr['values'];
	}
  }
$branches_arr=explode('|',$cur_branches);
unset($cur_branches); 
$branch_opts='';
foreach ($branches_arr as $cur_branch)
{
$branch_opts.="<option value=\"$cur_branch\">$cur_branch</option>";
}
unset($branches_arr);
$fam_opts='';
foreach ($family_tab_info as $cur_fam)
{
$fam_opts.='<option value="'.$cur_fam['id'].'">'.$cur_fam['Family'].'</option>';
}
$supl_opts='';
foreach ($address_info as $cur_addr)
{
$supl_opts.='<option value="'.$cur_addr['id'].'">'.$cur_addr['Company_Name'].'</option>';
}
$select_array=array('Active'=>'<option value="Available">Available</option><option value="On Hire">On Hire</option><option value="Returned">Returned</option><option value="Sold">Sold</option><option value="Disposed">Disposed</option>',
'Branch'=>$branch_opts,'Family'=>$fam_opts,'Supplier Name'=>$supl_opts);
$al_empt_array=array('Unit'=>'no','Cost'=>'no',
'Family'=>'no','Supplier Name'=>'no',
'Date Hired'=>'no','Hire'=>'yes','Active'=>'no',
'Weight (kg)'=>'no','Income'=>'no','Disposal Date'=>'yes',
'Description'=>'yes','Total Days Hired'=>'no',
'Branch'=>'no','Stock'=>'no','Current Available Multi'=>'yes',
'Sales'=>'yes','Returned Date'=>'yes','Sold Date'=>'yes');
$check_type_array=array('Unit'=>'nickname','Cost'=>'num',
'Family'=>'num','Supplier Name'=>'num',
'Date Hired'=>'no check','Hire'=>'num','Active'=>'nickname',
'Weight (kg)'=>'num','Income'=>'num','Disposal Date'=>'no check',
'Description'=>'no check','Total Days Hired'=>'num',
'Branch'=>'nickname','Stock'=>'num','Current Available Multi'=>'no check',
'Sales'=>'num','Returned Date'=>'no check','Sold Date'=>'no check');
$db_type_array=array('Unit'=>'text','Cost'=>'int',
'Family'=>'int','Supplier Name'=>'int',
'Date Hired'=>'text','Hire'=>'bool','Active'=>'text',
'Weight (kg)'=>'int','Income'=>'int','Disposal Date'=>'text',
'Description'=>'text','Total Days Hired'=>'int',
'Branch'=>'text','Stock'=>'int','Current Available Multi'=>'text',
'Sales'=>'int','Returned Date'=>'text','Sold Date'=>'text');
$temp_handler=new panels_templates();
$content_new_unit=$temp_handler->generate_add_edit_table($fleets_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'fleet',0);
$content_edit_unit=$temp_handler->generate_add_edit_table($fleets_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'fleet',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
//new_family:
$cells_array=array('Family'=>'Family','1 Day'=>'Price_1_day',
'2 Days'=>'Price_2_days','1 Week'=>'Price_1_week','Min 1 Week'=>'Min_1_Week',
'Description'=>'Description'/*,'Extra day'=>'Extra_day'*/,'Sell price'=>'Sell_price',
'Fee'=>'Fee','Avaible'=>'Avaible','Total hire'=>'Total_hire');
$types_array=array('Family'=>'text','1 Day'=>'text',
'2 Days'=>'text','1 Week'=>'text','Min 1 Week'=>'checkbox',
'Description'=>'text'/*,'Extra day'=>'text'*/,'Sell price'=>'text',
'Fee'=>'text','Avaible'=>'text','Total hire'=>'text');
$select_array='';
$al_empt_array=array('Family'=>'no','1 Day'=>'yes',
'2 Days'=>'yes','1 Week'=>'yes','Min 1 Week'=>'yes',
'Description'=>'yes'/*,'Extra day'=>'yes'*/,'Sell price'=>'yes',
'Fee'=>'yes','Avaible'=>'yes','Total hire'=>'yes');
$check_type_array=array('Family'=>'no check','1 Day'=>'num',
'2 Days'=>'num','1 Week'=>'num','Min 1 Week'=>'num',
'Description'=>'no check'/*,'Extra day'=>'no check'*/,'Sell price'=>'num',
'Fee'=>'num','Avaible'=>'num','Total hire'=>'num');
$db_type_array=array('Family'=>'text','1 Day'=>'float',
'2 Days'=>'float','1 Week'=>'float','Min 1 Week'=>'bool',
'Description'=>'text'/*,'Extra day'=>'text'*/,'Sell price'=>'float',
'Fee'=>'float','Avaible'=>'float','Total hire'=>'text');
$temp_handler=new panels_templates();
$content_new_family=$temp_handler->generate_add_edit_table($family_tab_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'family',0);
$content_edit_family=$temp_handler->generate_add_edit_table($family_tab_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'family',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//product_list:
$cells_array=array('Stock Code'=>'Code','Name'=>'Name',
'Description'=>'Description','In Stock'=>'In_Stock','Price'=>'Price');
$temp_handler=new panels_templates();
$content_stock_list=$temp_handler->generate_list_table($stock_list_info,$cells_array);
unset($temp_handler);
//view_product:
$cells_array=array('Stock Code'=>'Code','Name'=>'Name',
'Description'=>'Description','In Stock'=>'In_Stock','Price'=>'Price',
'On Order'=>'On_Order','Requires Payment'=>'Requires_Payment',
'Creation Date'=>'creation_date','Modified Date'=>'mod_date');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=$stock_list_info[0]['Code'];
  else $cur_cd=$id_code;
$content_view_stock=$temp_handler->generate_details_table($cur_cd,$stock_list_info,$cells_array);
unset($temp_handler);
//new_product:
$cells_array=array('Stock Code'=>'Code','Name'=>'Name',
'Description'=>'Description','In Stock'=>'In_Stock','Price'=>'Price',
'On Order'=>'On_Order','Requires Payment'=>'Requires_Payment');
$types_array=array('Stock Code'=>'text','Name'=>'text',
'Description'=>'text','In Stock'=>'text','Price'=>'text',
'On Order'=>'text','Requires Payment'=>'text');
$select_array='';
$al_empt_array=array('Stock Code'=>'no','Name'=>'no',
'Description'=>'yes','In Stock'=>'yes','Price'=>'yes',
'On Order'=>'yes','Requires Payment'=>'yes');
$check_type_array=array('Stock Code'=>'no check','Name'=>'no check',
'Description'=>'no check','In Stock'=>'num','Price'=>'num',
'On Order'=>'num','Requires Payment'=>'num');
$db_type_array=array('Stock Code'=>'text','Name'=>'text',
'Description'=>'text','In Stock'=>'int','Price'=>'float',
'On Order'=>'float','Requires Payment'=>'float');
$temp_handler=new panels_templates();
$content_new_stock=$temp_handler->generate_add_edit_table($stock_list_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'stock_list',0);
$content_edit_stock=$temp_handler->generate_add_edit_table($stock_list_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'stock_list',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//supplier_list:
$cells_array=array('Supplier Code'=>'Code','Supplier Name'=>'Supplier_Name',
'Supplier Town'=>'Supplier_Town','Supplier Landline'=>'Supplier_Landline',
'Credit Limit'=>'Credit_Limit','Credit Due'=>'Credit_Due',
'Current Balance'=>'Current_Balance');
$temp_handler=new panels_templates();
$content_supplier_list=$temp_handler->generate_list_table($supplier_list_info,$cells_array);
unset($temp_handler);
//view_product:
$cells_array=array('Supplier Code'=>'Code','Supplier Name'=>'Supplier_Name',
'Supplier Street 1'=>'Supplier_Street_1','Supplier Street 2'=>'Supplier_Street_2',
'Supplier Street 3'=>'Supplier_Street_3','Supplier Town'=>'Supplier_Town',
'Supplier County'=>'Supplier_County','Supplier Post Code'=>'Supplier_Post_Code',
'Supplier Main Branch'=>'Supplier_Main_Branch','Supplier Landline'=>'Supplier_Landline',
'Credit Limit'=>'Credit_Limit','Credit Due'=>'Credit_Due',
'Current Balance'=>'Current_Balance','Contacts'=>'Contacts',
'Creation Date'=>'creation_date','Modified Date'=>'mod_date');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=$supplier_list_info[0]['Code'];
  else $cur_cd=$id_code;
$content_view_supplier=$temp_handler->generate_details_table($cur_cd,$supplier_list_info,$cells_array);
unset($temp_handler);
//new_supplier:
$cells_array=array('Supplier Code'=>'Code','Supplier Name'=>'Supplier_Name',
'Supplier Street 1'=>'Supplier_Street_1','Supplier Street 2'=>'Supplier_Street_2',
'Supplier Street 3'=>'Supplier_Street_3','Supplier Town'=>'Supplier_Town',
'Supplier County'=>'Supplier_County','Supplier Post Code'=>'Supplier_Post_Code',
'Supplier Main Branch'=>'Supplier_Main_Branch','Supplier Landline'=>'Supplier_Landline',
'Credit Limit'=>'Credit_Limit','Credit Due'=>'Credit_Due',
'Current Balance'=>'Current_Balance','Contacts'=>'Contacts');
$types_array=array('Supplier Code'=>'text','Supplier Name'=>'text',
'Supplier Street 1'=>'text','Supplier Street 2'=>'text',
'Supplier Street 3'=>'text','Supplier Town'=>'text',
'Supplier County'=>'text','Supplier Post Code'=>'text',
'Supplier Main Branch'=>'select','Supplier Landline'=>'text',
'Credit Limit'=>'text','Credit Due'=>'text',
'Current Balance'=>'text','Contacts'=>'select');
$sup_branch_opts='';
foreach ($branches_info as $cur_branch)
	{
	$sup_branch_opts.='<option value="'.$cur_branch['branch_name'].'">'.$cur_branch['branch_name'].'</option>';
	}
$contact_opts='';
foreach ($contact_info as $cur_cont)
	{
	$contact_opts.='<option value="'.$cur_cont['id'].'">'.$cur_cont['First_Name'].' '.$cur_cont['Surname'].'</option>';
	}
$select_array=array('Supplier Main Branch'=>$sup_branch_opts,'Contacts'=>$contact_opts);
unset($sup_branch_opts,$contact_opts);

$al_empt_array=array('Supplier Code'=>'no','Supplier Name'=>'no',
'Supplier Street 1'=>'no','Supplier Street 2'=>'yes',
'Supplier Street 3'=>'yes','Supplier Town'=>'yes',
'Supplier County'=>'yes','Supplier Post Code'=>'yes',
'Supplier Main Branch'=>'no','Supplier Landline'=>'yes',
'Credit Limit'=>'yes','Credit Due'=>'yes',
'Current Balance'=>'yes','Contacts'=>'yes');
$check_type_array=array('Supplier Code'=>'no check','Supplier Name'=>'no check',
'Supplier Street 1'=>'no check','Supplier Street 2'=>'no check',
'Supplier Street 3'=>'no check','Supplier Town'=>'nickname',
'Supplier County'=>'nickname','Supplier Post Code'=>'no check',
'Supplier Main Branch'=>'nickname','Supplier Landline'=>'no check',
'Credit Limit'=>'num','Credit Due'=>'num',
'Current Balance'=>'num','Contacts'=>'nickname');
$db_type_array=array('Supplier Code'=>'text','Supplier Name'=>'text',
'Supplier Street 1'=>'text','Supplier Street 2'=>'text',
'Supplier Street 3'=>'text','Supplier Town'=>'text',
'Supplier County'=>'text','Supplier Post Code'=>'text',
'Supplier Main Branch'=>'text','Supplier Landline'=>'text',
'Credit Limit'=>'float','Credit Due'=>'int',
'Current Balance'=>'float','Contacts'=>'text');
$temp_handler=new panels_templates();
$content_new_supplier=$temp_handler->generate_add_edit_table($supplier_list_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'supplier_list',0);
$content_edit_supplier=$temp_handler->generate_add_edit_table($supplier_list_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'supplier_list',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//customer_list:
$cells_array=array('Customer Code'=>'Code','Customer Name'=>'Customer_Name',
'Customer Town'=>'Customer_Town','Customer Landline'=>'Customer_Landline',
'Credit Limit'=>'Credit_Limit','Credit Due'=>'Credit_Due',
'Current Balance'=>'Current_Balance');
$temp_handler=new panels_templates();
$content_customer_list=$temp_handler->generate_list_table($customer_list_info,$cells_array);
unset($temp_handler);
//view_product:
$cells_array=array('Customer Code'=>'Code','Customer Name'=>'Customer_Name',
'Customer Street 1'=>'Customer_Street_1','Customer Street 2'=>'Customer_Street_2',
'Customer Street 3'=>'Customer_Street_3','Customer Town'=>'Customer_Town',
'Customer County'=>'Customer_County','Customer Post Code'=>'Customer_Post_Code',
'Customer Main Branch'=>'Customer_Main_Branch','Customer Landline'=>'Customer_Landline',
'Credit Limit'=>'Credit_Limit','Credit Due'=>'Credit_Due',
'Current Balance'=>'Current_Balance','Contacts'=>'Contacts',
'Creation Date'=>'creation_date','Modified Date'=>'mod_date');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=$customer_list_info[0]['Code'];
  else $cur_cd=$id_code;
$content_view_customer=$temp_handler->generate_details_table($cur_cd,$customer_list_info,$cells_array);
unset($temp_handler);
//new_customer:
$cells_array=array('Customer Code'=>'Code','Customer Name'=>'Customer_Name',
'Customer Street 1'=>'Customer_Street_1','Customer Street 2'=>'Customer_Street_2',
'Customer Street 3'=>'Customer_Street_3','Customer Town'=>'Customer_Town',
'Customer County'=>'Customer_County','Customer Post Code'=>'Customer_Post_Code',
'Customer Main Branch'=>'Customer_Main_Branch','Customer Landline'=>'Customer_Landline',
'Credit Limit'=>'Credit_Limit','Credit Due'=>'Credit_Due',
'Current Balance'=>'Current_Balance','Contacts'=>'Contacts');
$types_array=array('Customer Code'=>'text','Customer Name'=>'text',
'Customer Street 1'=>'text','Customer Street 2'=>'text',
'Customer Street 3'=>'text','Customer Town'=>'text',
'Customer County'=>'text','Customer Post Code'=>'text',
'Customer Main Branch'=>'select','Customer Landline'=>'text',
'Credit Limit'=>'text','Credit Due'=>'text',
'Current Balance'=>'text','Contacts'=>'select');
$sup_branch_opts='';
foreach ($branches_info as $cur_branch)
	{
	$sup_branch_opts.='<option value="'.$cur_branch['branch_name'].'">'.$cur_branch['branch_name'].'</option>';
	}
$contact_opts='';
foreach ($contact_info as $cur_cont)
	{
	$contact_opts.='<option value="'.$cur_cont['id'].'">'.$cur_cont['First_Name'].' '.$cur_cont['Surname'].'</option>';
	}
$select_array=array('Customer Main Branch'=>$sup_branch_opts,'Contacts'=>$contact_opts);
unset($sup_branch_opts,$contact_opts);

$al_empt_array=array('Customer Code'=>'no','Customer Name'=>'no',
'Customer Street 1'=>'no','Customer Street 2'=>'yes',
'Customer Street 3'=>'yes','Customer Town'=>'yes',
'Customer County'=>'yes','Customer Post Code'=>'yes',
'Customer Main Branch'=>'no','Customer Landline'=>'yes',
'Credit Limit'=>'yes','Credit Due'=>'yes',
'Current Balance'=>'yes','Contacts'=>'yes');
$check_type_array=array('Customer Code'=>'no check','Customer Name'=>'no check',
'Customer Street 1'=>'no check','Customer Street 2'=>'no check',
'Customer Street 3'=>'no check','Customer Town'=>'nickname',
'Customer County'=>'nickname','Customer Post Code'=>'no check',
'Customer Main Branch'=>'nickname','Customer Landline'=>'no check',
'Credit Limit'=>'num','Credit Due'=>'num',
'Current Balance'=>'num','Contacts'=>'nickname');
$db_type_array=array('Customer Code'=>'text','Customer Name'=>'text',
'Customer Street 1'=>'text','Customer Street 2'=>'text',
'Customer Street 3'=>'text','Customer Town'=>'text',
'Customer County'=>'text','Customer Post Code'=>'text',
'Customer Main Branch'=>'text','Customer Landline'=>'text',
'Credit Limit'=>'float','Credit Due'=>'int',
'Current Balance'=>'float','Contacts'=>'text');
$temp_handler=new panels_templates();
$content_new_customer=$temp_handler->generate_add_edit_table($customer_list_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'customer_list',0);
$content_edit_customer=$temp_handler->generate_add_edit_table($customer_list_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'customer_list',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//contact_list:
$cells_array=array('First Name'=>'First_Name','Surname'=>'Surname',
'Email'=>'Email','Phone 1'=>'Phone1',
'Phone 2'=>'Phone2','Type'=>'Type');
$temp_handler=new panels_templates();
$content_contact_list=$temp_handler->generate_list_table($contact_info,$cells_array);
unset($temp_handler);
//view_product:
$cells_array=array('First Name'=>'First_Name','Surname'=>'Surname',
'Email'=>'Email','Phone 1'=>'Phone1',
'Phone 2'=>'Phone2','Type'=>'Type','Address Code'=>'Address_Code',
'Creation Date'=>'creation_date','Modified Date'=>'mod_date');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=1;
  else $cur_cd=$id_code;
$content_view_contact=$temp_handler->generate_details_table($cur_cd,$contact_info,$cells_array);
unset($temp_handler);
//new_contact:
$cells_array=array('First Name'=>'First_Name','Surname'=>'Surname',
'Email'=>'Email','Phone 1'=>'Phone1',
'Phone 2'=>'Phone2','Type'=>'Type','Address Code'=>'Address_Code');
$types_array=array('First Name'=>'text','Surname'=>'text',
'Email'=>'text','Phone 1'=>'text',
'Phone 2'=>'text','Type'=>'select','Address Code'=>'select');

$addr_code_opts='';
foreach ($supplier_list_info as $cur_sup)
	{
	$addr_code_opts.='<option value="'.$cur_sup['Code'].'">'.$cur_sup['Supplier_Name'].'</option>';
	}
foreach ($customer_list_info as $cur_cust)
	{
	$addr_code_opts.='<option value="'.$cur_cust['Code'].'">'.$cur_cust['Customer_Name'].'</option>';
	}
$select_array=array('Type'=>'<option value="Supplier">Supplier</option><option value="Customer">Customer</option><option value="Repairs">Repairs</option>','Address Code'=>$addr_code_opts);
unset($addr_code_opts);

$al_empt_array=array('First Name'=>'no','Surname'=>'no',
'Email'=>'yes','Phone 1'=>'yes',
'Phone 2'=>'yes','Type'=>'no','Address Code'=>'yes');
$check_type_array=array('First Name'=>'nickname','Surname'=>'nickname',
'Email'=>'mail','Phone 1'=>'no check',
'Phone 2'=>'no check','Type'=>'nickname','Address Code'=>'no check');
$db_type_array=array('First Name'=>'text','Surname'=>'text',
'Email'=>'text','Phone 1'=>'text',
'Phone 2'=>'text','Type'=>'text','Address Code'=>'text');
$temp_handler=new panels_templates();
$content_new_contact=$temp_handler->generate_add_edit_table($contact_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'contact_list',0);
$content_edit_contact=$temp_handler->generate_add_edit_table($contact_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'contact_list',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//nominal_ledger_list:
$cells_array=array('Nominal Code'=>'Code','Name'=>'Name',
'Debit'=>'Debit','Credit'=>'Credit');
$temp_handler=new panels_templates();
$content_nominal_ledger_list=$temp_handler->generate_list_table($nominal_ledger_info,$cells_array);
unset($temp_handler);
//view_nominal_ledger:
$cells_array=array('Nominal Code'=>'Code','Name'=>'Name',
'Debit'=>'Debit','Credit'=>'Credit',
'Creation Date'=>'creation_date','Modified Date'=>'mod_date');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=$nominal_ledger_info[0]['Code'];
  else $cur_cd=$id_code;
$content_view_nominal_ledger=$temp_handler->generate_details_table($cur_cd,$nominal_ledger_info,$cells_array);
unset($temp_handler);
//new_nominal_ledger:
$cells_array=array('Nominal Code'=>'Code','Name'=>'Name',
'Debit'=>'Debit','Credit'=>'Credit');
$types_array=array('Nominal Code'=>'text','Name'=>'text',
'Debit'=>'text','Credit'=>'text');
$select_array='';
$al_empt_array=array('Nominal Code'=>'no','Name'=>'no',
'Debit'=>'yes','Credit'=>'yes');
$check_type_array=array('Nominal Code'=>'no check','Name'=>'nickname',
'Debit'=>'num','Credit'=>'num');
$db_type_array=array('Nominal Code'=>'text','Name'=>'text',
'Debit'=>'float','Credit'=>'float');
$temp_handler=new panels_templates();
$content_new_nominal_ledger=$temp_handler->generate_add_edit_table($nominal_ledger_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'nominal_ledger',0);
$content_edit_nominal_ledger=$temp_handler->generate_add_edit_table($nominal_ledger_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'nominal_ledger',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//audit_trail_list:
$cells_array=array('Reference'=>'Reference','Description'=>'Description',
'Ledger'=>'Ledger','Sub Ledger'=>'Sub_Ledger',
'Debit'=>'Debit','Credit'=>'Credit','Date'=>'Date');
$temp_handler=new panels_templates();
$content_audit_trail_list=$temp_handler->generate_list_table($audit_trail_info,$cells_array);
unset($temp_handler);
//view_audit_trail:
$cells_array=array('Reference'=>'Reference','Description'=>'Description',
'Ledger'=>'Ledger','Sub Ledger'=>'Sub_Ledger',
'Debit'=>'Debit','Credit'=>'Credit','Date'=>'Date',
'Creation Date'=>'creation_date','Location Stamp'=>'Location_Stamp',
'User Stamp'=>'User_Stamp');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=1;
  else $cur_cd=$id_code;
$content_view_audit_trail=$temp_handler->generate_details_table($cur_cd,$audit_trail_info,$cells_array);
unset($temp_handler);
//new_audit_trail:
$cells_array=array('Reference'=>'Reference','Description'=>'Description',
'Ledger'=>'Ledger','Sub Ledger'=>'Sub_Ledger',
'Debit'=>'Debit','Credit'=>'Credit','Date'=>'Date');
$types_array=array('Reference'=>'text','Description'=>'text',
'Ledger'=>'select','Sub Ledger'=>'text',
'Debit'=>'text','Credit'=>'text','Date'=>'date');

$nom_code_opts='';
foreach ($nominal_ledger_info as $cur_nom_ledg)
	{
	$nom_code_opts.='<option value="'.$cur_nom_ledg['Code'].'">'.$cur_nom_ledg['Name'].'</option>';
	}
$select_array=array('Ledger'=>$nom_code_opts);
$al_empt_array=array('Reference'=>'no','Description'=>'yes',
'Ledger'=>'no','Sub Ledger'=>'yes',
'Debit'=>'yes','Credit'=>'yes','Date'=>'yes');
$check_type_array=array('Reference'=>'no check','Description'=>'no check',
'Ledger'=>'no check','Sub Ledger'=>'no check',
'Debit'=>'num','Credit'=>'num','Date'=>'date(dd/-mm/-yyyy)');
$db_type_array=array('Reference'=>'text','Description'=>'text',
'Ledger'=>'text','Sub Ledger'=>'text',
'Debit'=>'float','Credit'=>'float','Date'=>'text');
$temp_handler=new panels_templates();
$content_new_audit_trail=$temp_handler->generate_add_edit_table($audit_trail_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'audit_trail',0);
$content_edit_audit_trail=$temp_handler->generate_add_edit_table($audit_trail_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'audit_trail',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//bank_trail_list:
$cells_array=array('Reference'=>'Reference','Description'=>'Description',
'Bank Code'=>'Bank_Code','Debit'=>'Debit','Credit'=>'Credit',
'Date'=>'Date','Reconciled'=>'Reconciled','Reconciliation Date'=>'Reconciliation_Date');
$temp_handler=new panels_templates();
$content_bank_trail_list=$temp_handler->generate_list_table($bank_trail_info,$cells_array);
unset($temp_handler);
//view_bank_trail:
$cells_array=array('Reference'=>'Reference','Description'=>'Description',
'Bank Code'=>'Bank_Code','Debit'=>'Debit','Credit'=>'Credit',
'Date'=>'Date','Reconciled'=>'Reconciled','Reconciliation Date'=>'Reconciliation_Date',
'Creation Date'=>'creation_date','Location Stamp'=>'Location_Stamp',
'User Stamp'=>'User_Stamp');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=1;
  else $cur_cd=$id_code;
$content_view_bank_trail=$temp_handler->generate_details_table($cur_cd,$bank_trail_info,$cells_array);
unset($temp_handler);
//new_bank_trail:
$cells_array=array('Reference'=>'Reference','Description'=>'Description',
'Bank Code'=>'Bank_Code','Debit'=>'Debit','Credit'=>'Credit',
'Date'=>'Date','Reconciled'=>'Reconciled','Reconciliation Date'=>'Reconciliation_Date');
$types_array=array('Reference'=>'text','Description'=>'text',
'Bank Code'=>'select','Debit'=>'text','Credit'=>'text',
'Date'=>'date','Reconciled'=>'checkbox','Reconciliation Date'=>'date');

$bank_code_opts='';
foreach ($bank_ledger_info as $cur_bank_ledg)
	{
	$bank_code_opts.='<option value="'.$cur_bank_ledg['Code'].'">'.$cur_bank_ledg['Bank_Account_Name'].'</option>';
	}
$select_array=array('Bank Code'=>$bank_code_opts);
$al_empt_array=array('Reference'=>'no','Description'=>'yes',
'Bank Code'=>'no','Debit'=>'yes','Credit'=>'yes',
'Date'=>'yes','Reconciled'=>'yes','Reconciliation Date'=>'yes');
$check_type_array=array('Reference'=>'no check','Description'=>'no check',
'Bank Code'=>'no check','Debit'=>'num','Credit'=>'num',
'Date'=>'date(dd/-mm/-yyyy)','Reconciled'=>'no check','Reconciliation Date'=>'no check');
$db_type_array=array('Reference'=>'text','Description'=>'text',
'Bank Code'=>'text','Debit'=>'float','Credit'=>'float',
'Date'=>'text','Reconciled'=>'bool','Reconciliation Date'=>'text');
$temp_handler=new panels_templates();
$content_new_bank_trail=$temp_handler->generate_add_edit_table($bank_trail_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'bank_trail',0);
$content_edit_bank_trail=$temp_handler->generate_add_edit_table($bank_trail_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'bank_trail',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//bank_ledger_list:
$cells_array=array('Bank Code'=>'Code','Bank Account Name'=>'Bank_Account_Name',
'Balance'=>'Balance');
$temp_handler=new panels_templates();
$content_bank_ledger_list=$temp_handler->generate_list_table($bank_ledger_info,$cells_array);
unset($temp_handler);
//view_bank_ledger:
$cells_array=array('Bank Code'=>'Code','Bank Account Name'=>'Bank_Account_Name',
'Balance'=>'Balance', 'Date Opened'=>'Date_Opened',
'Creation Date'=>'creation_date', 'Modified Date'=>'mod_date',
'Location Stamp'=>'Location_Stamp', 'User Stamp'=>'User_Stamp');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=$bank_ledger_info[0]['Code'];
  else $cur_cd=$id_code;
$content_view_bank_ledger=$temp_handler->generate_details_table($cur_cd,$bank_ledger_info,$cells_array);
unset($temp_handler);
//new_bank_ledger:
$cells_array=array('Bank Code'=>'Code','Bank Account Name'=>'Bank_Account_Name',
'Balance'=>'Balance', 'Date Opened'=>'Date_Opened');
$types_array=array('Bank Code'=>'text','Bank Account Name'=>'text',
'Balance'=>'text', 'Date Opened'=>'date');
$select_array='';
$al_empt_array=array('Bank Code'=>'no','Bank Account Name'=>'no',
'Balance'=>'yes', 'Date Opened'=>'yes');
$check_type_array=array('Bank Code'=>'no check','Bank Account Name'=>'no check',
'Balance'=>'num', 'Date Opened'=>'date(dd/-mm/-yyyy)');
$db_type_array=array('Bank Code'=>'text','Bank Account Name'=>'text',
'Balance'=>'float', 'Date Opened'=>'text');
$temp_handler=new panels_templates();
$content_new_bank_ledger=$temp_handler->generate_add_edit_table($bank_ledger_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'bank_ledger',0);
$content_edit_bank_ledger=$temp_handler->generate_add_edit_table($bank_ledger_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'bank_ledger',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//vat_list:
$cells_array=array('VAT Code'=>'Code','Percentage'=>'Percentage',
'Note'=>'Note');
$temp_handler=new panels_templates();
$content_vat_list=$temp_handler->generate_list_table($vat_list_info,$cells_array);
unset($temp_handler);
//view_vat:
$cells_array=array('VAT Code'=>'Code','Percentage'=>'Percentage',
'Note'=>'Note','Creation Date'=>'creation_date', 'Modified Date'=>'mod_date');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=$vat_list_info[0]['Code'];
  else $cur_cd=$id_code;
$content_view_vat=$temp_handler->generate_details_table($cur_cd,$vat_list_info,$cells_array);
unset($temp_handler);
//new_vat:
$cells_array=array('VAT Code'=>'Code','Percentage'=>'Percentage',
'Note'=>'Note');
$types_array=array('VAT Code'=>'text','Percentage'=>'text',
'Note'=>'text');
$select_array='';
$al_empt_array=array('VAT Code'=>'no','Percentage'=>'no',
'Note'=>'yes');
$check_type_array=array('VAT Code'=>'no check','Percentage'=>'num',
'Note'=>'no check');
$db_type_array=array('VAT Code'=>'text','Percentage'=>'float',
'Note'=>'text');
$temp_handler=new panels_templates();
$content_new_vat=$temp_handler->generate_add_edit_table($vat_list_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'vat_list',0);
$content_edit_vat=$temp_handler->generate_add_edit_table($vat_list_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'vat_list',$id_code);
unset($temp_handler);
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
//holiday_list:
$cells_array=array('Holiday'=>'Holiday','Date Start'=>'Date_Start',
'Date End'=>'Date_End','Affects Hiring'=>'Affects_Hiring');
$temp_handler=new panels_templates();
$content_holiday_list=$temp_handler->generate_list_table($holiday_list_info,$cells_array);
unset($temp_handler);
//view_holiday:
$cells_array=array('Holiday'=>'Holiday','Date Start'=>'Date_Start',
'Date End'=>'Date_End','Affects Hiring'=>'Affects_Hiring',
'Creation Date'=>'creation_date', 'Modified Date'=>'mod_date');
$temp_handler=new panels_templates();
if ($id_code=='0') $cur_cd=1;
  else $cur_cd=$id_code;
$content_view_holiday=$temp_handler->generate_details_table($cur_cd,$holiday_list_info,$cells_array);
unset($temp_handler);
//new_holiday:
$cells_array=array('Holiday'=>'Holiday','Date Start'=>'Date_Start',
'Date End'=>'Date_End','Affects Hiring'=>'Affects_Hiring');
$types_array=array('Holiday'=>'text','Date Start'=>'date',
'Date End'=>'date','Affects Hiring'=>'checkbox');
$select_array='';
$al_empt_array=array('Holiday'=>'no','Date Start'=>'yes',
'Date End'=>'yes','Affects Hiring'=>'yes');
$check_type_array=array('Holiday'=>'no check','Date Start'=>'date(dd/-mm/-yyyy)',
'Date End'=>'date(dd/-mm/-yyyy)','Affects Hiring'=>'no check');
$db_type_array=array('Holiday'=>'text','Date Start'=>'text',
'Date End'=>'text','Affects Hiring'=>'bool');
$temp_handler=new panels_templates();
$content_new_holiday=$temp_handler->generate_add_edit_table($holiday_list_info,$cells_array,$types_array,'new',$select_array,$al_empt_array,$check_type_array,$db_type_array,'holiday_list',0);
$content_edit_holiday=$temp_handler->generate_add_edit_table($holiday_list_info,$cells_array,$types_array,'edit',$select_array,$al_empt_array,$check_type_array,$db_type_array,'holiday_list',$id_code);
unset($temp_handler);

/*---------------------------------SALES INTERFACE---------------------------------*/
/*---------------------------------SALES INTERFACE---------------------------------*/
/*---------------------------------SALES INTERFACE---------------------------------*/
$cells_array=array('Product Name'=>'Unit','Number Available'=>'Stock',
'Net Price'=>'Cost');
$temp_handler=new panels_templates();
$sales_tab_table=$temp_handler->generate_list_table($fleets_info,$cells_array);
unset($temp_handler);
//generate array:
$hire_gen_arr='';
$k_count=0;
foreach ($family_tab_info as $cur_fam)
	{
	$hire_gen_arr[$k_count]['id']=$cur_fam['id'];
	$hire_gen_arr[$k_count]['name']=$cur_fam['Family'];
	$hire_gen_arr[$k_count]['1weekcost']=$cur_fam['Price_1_week'];
	$hire_gen_arr[$k_count]['min1week']=$cur_fam['Min_1_Week']==1 ? 'Yes':'No';
	$num_found=false;
	foreach ($fleets_info as $cur_fleet)	
		{
		if ($cur_fam['id']==$cur_fleet['Family_id'])
			{
			$hire_gen_arr[$k_count]['avblnum']=$cur_fleet['Stock'];
			$num_found=true;
			}
		}
	if (!$num_found) $hire_gen_arr[]['avblnum']=0;
	$k_count++;
	}
unset($k_count);
$cells_array=array('Family Name'=>'name','Number Available'=>'avblnum',
'1 week cost'=>'1weekcost','Min 1 Week'=>'min1week');
$temp_handler=new panels_templates();
$hire_tab_table=$temp_handler->generate_list_table($hire_gen_arr,$cells_array);
unset($temp_handler);
ob_start();
?>
<div id='right_hand_tabs'>
<ul>
<li><!--onClick="change_hash('repair',['repair','hire','sale']);"--><a href="#right_tabs-1"  class="repair" title="repair">Repair</a></li>
<li><!--onClick="change_hash('hire',['repair','hire','sale']);"--><a href="#right_tabs-2"  class="hire" title="hire">Hire</a></li>
<li><!--onClick="change_hash('sale',['repair','hire','sale']);"--><a href="#right_tabs-3"  class="sale" title="sale">Sale</a></li>
</ul>
<div id='right_tabs-1'>tab1</div>
<div id='right_tabs-2'><?php echo $hire_tab_table; ?></div>
<div id='right_tabs-3'><?php echo $sales_tab_table; ?></div>
</div>

<div id='left_hand_tabs'>
<button id='cash_sale'>Cash Sale</button>
<button id='new_customer'>New Customer</button>
<button id='find_customer'>Find Customer</button>
<div>
<?php
$temp_handler=new panels_templates();
$cur_table_th=array('Customer','-');
$cur_table_body=array(array('Address','-'),
array('Contact','-<br>-'),
array('Net Balance','£- (of £-)'));
echo $temp_handler->generate_table('customer_info','sales_table',$cur_table_th,$cur_table_body);
unset($temp_handler,$cur_table_th,$cur_table_body);
?>
</div>
</div>
<div id='new_cust_container'><?php echo $content_new_customer;?></div>
<div id='list_cust_container'><?php echo $content_customer_list;?></div>

<div id='left_hand_bottom_tabs'>
test
</div>
<script type='text/javascript'>
$( "#right_hand_tabs").tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
$( "#right_hand_tabs li").removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
$( "#right_hand_tabs ul").css('height','31px');
$('#new_cust_container').add('#list_cust_container').hide();
$('#new_customer').add('#find_customer').add('#cash_sale').button().css('font-size','10pt');
$('#find_customer').bind('click', function ()
	{
	$('#list_cust_container').show().dialog({
                                                            height:500,
															width:950,
															modal:true,
															title:'Choose customer',
															resizable:false,
															draggable:false
															});	
	});
$('#new_customer').bind('click', function ()
	{
	$('#new_cust_container').show().dialog({
                                                            height:500,
															width:950,
															modal:true,
															title:'Add new customer',
															resizable:false,
															draggable:false
															});	
	});
$('#list_cust_container .detail_but').text('Choose');
var info_tabl_selector=$('#left_hand_tabs').children('div').children('#customer_info');
		
$('#list_cust_container .detail_but').bind('click', function ()
		{
		$.post('./server_scripts/ajax_worker.php',{get_customer_info:true,cust_id:$(this).attr('attr-row_id')}, function (data)
			{
			var cur_cust_info_arr=data.split('||');
			if (cur_cust_info_arr.length<=1)
				{
				alert(data);
				return false;
				}
			info_tabl_selector.attr('attr-cur_id',cur_cust_info_arr[0]);
			info_tabl_selector.children('tbody').children('tr').each(function (k, val)
				{
				if (k==0) $(this).children('th:last').html(cur_cust_info_arr[k+1]);
					else $(this).children('td:last').html(cur_cust_info_arr[k+1]);
				});
			$('#list_cust_container').dialog('close');
			});
		});
$('#new_cust_container .det_back_but').bind('click', function ()
	{
	$('#new_cust_container').dialog('close');
	});		
/*function change_hash(value,pos_values_arr)
	{
	var cur_val=location.hash;
	var cur_val_arr=cur_val.split('/');
	if (cur_val_arr.length>0)
		{
		var last_val=cur_val_arr[cur_val_arr.length-1];
		//console.log($.inArray(last_val, pos_values_arr));
		if ($.inArray(last_val, pos_values_arr)!=-1) 
		{
		last_val=cur_val_arr[cur_val_arr.length-1];
		location.hash=cur_val.replace(new RegExp(last_val+'$'),value);
		console.log(last_val);
		}
			else location.hash=cur_val.replace(last_val,last_val+'/'+value);
		
		
		}
		else
		{
		location.hash=value;
		}
	}*/
</script>
<?php
$content_sales_interface=ob_get_clean();

/*----------------------------------------TEST NEW INTERFACE---------------------*/
/*----------------------------------------TEST NEW INTERFACE---------------------*/
/*----------------------------------------TEST NEW INTERFACE---------------------*/
ob_start();
?>
<div class='left_table_cont'>
<?php
$tbody_arr=array(array('Number','3345N'),array('Type','Kit Header'),
array('Quantity', 'N/A'),array('Available','N/A'),
array('Location','N/A'), array('Status','N/A'),
array('Date','N/A'));
$temp_handler=new panels_templates();
echo $temp_handler->generate_table(null, $class='small_det_table', null, $tbody_arr);
unset($temp_handler);
?>
<span class='item_name'>10.2M N/S TOWER family</span>
<?php
$tbody_arr=array(array('Weight Kgs','N/A'),array('Basic rate','As components'),
array('Hol. credit', 'Yes'),array('Cacl. code','N/A'),
array('VAT code','N/A'), array('Acc. group',' '),
array('Deposit','0.00'),array('Purchased','N/A'),
array('Cost','N/A'),array('Deprec Type','N/A'),array('Value','N/A'));
$temp_handler=new panels_templates();
echo $temp_handler->generate_table(null, $class='small_det_table', null, $tbody_arr, 'margin-top:0px;');
unset($temp_handler);
?>
</div>
<div class='right_table_cont'>
<?php
$tbody_arr=array(array('Service group','N/A'),array('Serial Number','N/A'),
array('Power', 'N/A'),array('Earth','N/A'),
array('Fuse','N/A'), array('Flash test','N/A'),
array('Engine speed','N/A'),array('Output spindle','N/A'),
array('Cable type','N/A'),array('Cable length','N/A'),
array('PPE kit','Y'),array('Safety leaflet','N/A'),
array('Std. Test Type','N/A'),array('Test frequency','N/A'),
array('Last test date','N/A'),array('External test','N/A'),
array('Repairs','N/A'),array('Income','1550.46'),
array('Since','31/12/02'),array('Days','106'),
);
$temp_handler=new panels_templates();
echo $temp_handler->generate_table(null, $class='small_det_table', null, $tbody_arr);
unset($temp_handler);
?>
</div>
<?php
$content_fleet_records=ob_get_clean();
/*----------------------------------------TEST NEW INTERFACE---------------------*/
ob_start();
?>
<div class='left_table_cont'>
<?php
$tbody_arr=array(array('Long',' GLUTTON 3" PUMP','Weight, Kgs','Min. Rate','VAT Rate','Deposit'),
array('NAME',' ATTACHMENT','162.400','-','T1 20%','-'));
$temp_handler=new panels_templates();
echo $temp_handler->generate_table(null, 'tbl2', null, $tbody_arr,'margin-top:-40px; margin-bottom:20px;');
unset($temp_handler);

$tbody_arr=array(array('10.2M N/S TOWER','2','Yes','201.60','MIN WEEK','-','-','-'),
array('10.2M TOWER','1','No','190.00','STANDART','-','-','-'));
$cur_table_th=array('Family group','Total','Hol','Rate','Calc code','Nominal','Accessory','Group');
$temp_handler=new panels_templates();
echo $temp_handler->generate_table(null, $class='sales_table', $cur_table_th, $tbody_arr);
unset($temp_handler);
?>
</div>
<?php
$content_group_changes=ob_get_clean();
