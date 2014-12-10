<?php
//get db script:
require_once(__DIR__.'/db_func.php');
require_once(__DIR__.'/commons.php');
//user tab worker:

//getting user info:
$conn=db_connect();
get_user_info();   
get_settings();  
get_family_table();
get_fleet_table();
get_branches();
mysql_close($conn);
unset($q);

//check user cookie:
if (isset($_COOKIE['user']))
{
$admin_ac=false;
$emp_logged=false;
$user_only_view_allowed=false;
$show_name='';
$cookie_code=$_COOKIE['user'];
$conn=db_connect();
check_cookie($cookie_code,$conn,'close');
}

/*------------------------------------------------------------------------------------------------*/

//user tab:
ob_start();
?>
<div class='tab_container'>
<button id='new_emp' style='font-size:12pt;'>Add user</button>

<div id='add_new_user_box'>
<span style='font-size:18pt;'>New user:</span><br><br>
<form name='add_emp_form' id='add_emp_form' method='post' action='./server_scripts/ajax_worker.php' enctype="multipart/form-data">
<table id='tb_form' style='text-align:center; margin:0 auto;'>
<tr>
<td>First name:</td>
<td><input type='text' name='emp_first_name'/></td>
</tr>
<tr>
<td>Second name:</td>
<td><input type='text' name='emp_second_name'/></td>
</tr>
<tr>
<td>Username:</td>
<td><input type='text' name='emp_usr_name'/></td>
</tr>
<td>Password:</td>
<td><input type='password' name='emp_pass'/></td>
</tr>
<tr>
<td>E-mail:</td>
<td><input type='e-mail' name='emp_mail'/></td>
</tr>
<td>Admin permissions:</td>
<td>
<select name='ed_adm_perm'>
<option>Admin</option>
<option selected>User</option>
</select>
</td>
</tr>
<?php
$permis_types=array('Sales Point','Purchase Point','Hire Stock','Sales Stock','Accounts',
'Maintenance','User Administration','Administration Point');
for ($k=1; $k<=8; $k++)
{
echo "
<tr>
<td>".$permis_types[$k-1]." Permission:</td>
<td>
<input type='checkbox' name='permission_$k' value='1'>
</td>
</tr>";
}
?>
<tr>
<td>Branch:</td>
<td>
<select name='ed_branch'>
<?php
foreach ($branches_info as $cur_br)
  {
  echo "<option>".$cur_br['branch_name']."</option>";
  }
?>
</select>
</td>
</tr>
</table>
<div style='text-align:center; margin-top:15px;'>
<input type='submit' id='emp_add_submit' value='Add'/>
</div>
</form>
</div>

<br><br>
<?php
if ($user_info!='')
{?>
<div style="font-weight:bold; overflow-x:scroll; font-size:14pt; ">
Current users:
<br><br>
<table id='emp_list'>
<tr>
<th>№</th><th>First name</th><th>Second name</th><th>Username</th><th>E-mail</th><th>Active</th><th>Admin</th><th>Branch</th>
<?php
for ($i=1; $i<=8; $i++)
  {
  echo "<th>".$permis_types[$i-1]." Permission</th>";
  }
?>
</tr>
<?php
foreach ($user_info as $key=>$cur_user_info)
{
if ($cur_user_info['active']==0) 
 {
 $active_class=' class="inactive" ';
 $active_type='No';
 }
   else 
   {
   $active_class='';
   $active_type='Yes';
   }
if ($cur_user_info['permissions']=='U') 
 {
 $edit_class='';
 $edit_view_type='User';
 }
   else 
   {
   $edit_class=' class="edit_perm" ';
   $edit_view_type='Admin';
   }
   
echo '<tr data-id="'.$cur_user_info['id'].'"'.$active_class.$edit_class.'><td>'.($key+1).'</td><td>'.$cur_user_info['first_name'].'</td><td>'.$cur_user_info['second_name'].'</td><td>'.$cur_user_info['nick_name'].'</td><td>'.$cur_user_info['mail'].'</td><td>'.$active_type.'</td><td>'.$edit_view_type.'</td><td>'.$cur_user_info['branch'].'</td>';
for ($i=1; $i<=8; $i++)
  {
  $cur_perm=$cur_user_info['permission'.$i] == 0 ? 'No' : 'Yes';
  echo '<td>'.$cur_perm.'</td>';
  }
echo '</tr>';  
}
?>
</table>
</div>
<?php
}
?>
<div id='emp_toolbar'>
<a id='emp_edit'><i class="icon-edit"></i></a>
<a id='emp_act'><i class="icon-off"></i></a>
<a id='emp_del'><i class="icon-trash"></i></a>
</div>
<br><br>
<?php
$box_maker=new b_box_maker();
$branch_table="<tr>
<td>Branch:</td>
<td><input type='text' name='new_branch_name'/></td>
</tr>
<tr>
<td>Address Boxes:</td>
<td><input type='text' name='new_address_box'/></td>
</tr>
<tr>
<td>Phone Number:</td>
<td><input type='text' name='new_phone_number'/></td>
</tr>";
$box_maker->add_button_with_box('add_branch','Add branches','add_branch_box','Add new branch','add_branch_form',$branch_table,'branch_add_submit','Add');
unset($branch_table);

/*$delete_box_maker=new b_box_maker();
$del_br_table="<tr>
<td>Branch to delete:</td>
<td>
<select name='delete_branch'>";
foreach ($branches_info as $cur_br)
  {
  $del_br_table.="<option>".$cur_br['branch_name']."</option>";
  }
$del_br_table.="</select>
</td>
</tr>";
$delete_box_maker->add_button_with_box('del_branch','Delete branches','del_branch_box','Delete branch','del_branch_form',$del_br_table,'branch_del_submit','Delete');
unset($del_br_table);*/
?>

<br><br>
<span class='tb_label'>Branches:</span>
<table class='branches_tb'>
<tr>
<th>Name</th><th>Address Box</th><th>Phone Number</th>
</tr>
<?php
foreach ($branches_info as $cur_branch)
	{
	echo '<tr data-id="'.$cur_branch['id'].'"><td>'.$cur_branch['branch_name'].'</td><td>'.$cur_branch['address_box'].'</td><td>'.$cur_branch['phone_number'].'</td></tr>';
	}
?>
</table>
<div id='branch_toolbar'>
<a id='branch_edit'><i class="icon-edit"></i></a>
<a id='branch_del'><i class="icon-trash"></i></a>
</div>

</div>
<script type='text/javascript'>
$('#branch_toolbar').hide();
$('#add_new_user_box').hide();
$('#tb_form td:even').css('text-align','right');
$('#tb_form td:odd').css('text-align','left');
$('#new_emp').button().bind('click', function ()
     {
	 $('#add_new_user_box').show().dialog({
                                                                   height:600,
																   width:950,
																   modal:true,
																   title:'Add new user',
																   resizable:false,
																   draggable:false,
	                                                               close:function ()
																          {
																		  for (u=1; u<=7; u++)
										                                                      {
										                                                      $('input[name="permission_'+u+'"]').attr('checked',false);
										                                                      }
																		  $('#add_emp_form').resetForm();
																		  if ($('#add_new_user_box span:first').text()=='Edit user:')
											                                             {
											                                             //fallback:
										                                                 $('#add_new_user_box span:first').text('New user:');
											                                             $('#emp_add_submit').val('Add');
											                                             }
																		  },open:function ()
																		       {
																			   $('#emp_add_submit').removeAttr('disabled');
																			   }});										   
	 });	  
//new emp fields config:
var cur_date=$.datepicker.formatDate('dd-mm-yy', new Date());
 
$('#emp_add_submit').button().unbind('click').bind('click', function ()
     {
	 if ($('#emp_add_submit').val()=='Save') emp_ed_add_type='edit';
	 if ($('#emp_add_submit').val()=='Add') emp_ed_add_type='add';
	 console.log(emp_ed_add_type);
	 if (typeof emp_id=='undefined') emp_id='';
	// console.log(emp_id);
	 $('#add_emp_form').ajaxForm({url:'./server_scripts/ajax_worker.php',type:'POST', data:{emp_type:emp_ed_add_type,emp_id:emp_id}, success:function (data)
	                    {
						alert(data);
						if ($.trim(data).indexOf('Successfully')!=-1) 
						     {
							 $('#add_emp_form').resetForm();
							 window.location.reload();
							 }
						$('#emp_add_submit').removeAttr('disabled');
						},beforeSubmit: function ()
						      {
							  $('#emp_add_submit').attr('disabled', 'disabled');
							  }});
	 });	 
$('#emp_toolbar').hide();	 	 
$('#emp_list tr td').not('#emp_list tr:first td').not('.file_link_td').toolbar({
                                 content:'#emp_toolbar',
								 position:'top',
								 hideOnClick:true
								 })
								 .bind('toolbarItemClick', function (e, element)
								      {
									  //getting user data:
									  emp_info_arr=new Array();
									  for (k=0; k<$(this).parent('tr').children('td').length; k++)
									     {
										 emp_info_arr[k]=$(this).parent('tr').children('td:eq('+k+')').text();
										 }
										 cur_id=$(this).parent('tr').attr('data-id');
										 if($(this).parent('tr').attr('class').indexOf('inactive')!=-1) cur_state='inactive';
										    else cur_state='active';
									  if ($(element).attr('id')=='emp_edit')
									      {
										 // console.log('Edit!');
										 //console.log(emp_info_arr);
										  $('#add_new_user_box span:first').text('Edit user:');
										  $('input[name="emp_first_name"]').val(emp_info_arr[1]);
										  $('input[name="emp_second_name"]').val(emp_info_arr[2]);
										  $('input[name="emp_usr_name"]').val(emp_info_arr[3]);
										  $('input[name="emp_mail"]').val(emp_info_arr[4]);
										  $('select[name="ed_adm_perm"]').val(emp_info_arr[6]);
										  var ak=8;
										  for (k=1; k<=8; k++)
										  {
										  if (emp_info_arr[ak]=='Yes') $('input[name="permission_'+k+'"]').attr('checked',true);
										    else $('input[name="permission_'+k+'"]').attr('checked',false);
										  ak++;
										  }
										  $('select[name="ed_branch"]').val(emp_info_arr[7]);
										  
										  $('#emp_add_submit').val('Save');
										  $('#new_emp').trigger('click');
										  emp_id=$(this).parent('tr').attr('data-id');
										  }
									  if ($(element).attr('id')=='emp_act')
									      {
										  console.log('Active/Unactive in edit or view!');
										  $.post('./server_scripts/ajax_worker.php',{act_user:true,emp_info:emp_info_arr,cur_el_state:cur_state,cur_el_id:cur_id}, function (data)
										                     {
															 alert(data);
															 if ($.trim(data).indexOf('Succesfully')!=-1)
															    {
																window.location.reload();
																}
															 });
										  }
										  emp_del
										  if ($(element).attr('id')=='emp_del')
									      {
										  console.log('Delete user!');
										  if (confirm('Do you really want to delete this user?')) $.post('./server_scripts/ajax_worker.php',{del_user:true,emp_info:emp_info_arr,cur_el_id:cur_id}, function (data)
										                     {
															 alert(data);
															 if ($.trim(data).indexOf('Succesfully')!=-1)
															    {
																window.location.reload();
																}
															 });
										  }
									  });	 								  
$('#emp_list tr').bind('click', function ()
								      {
									  $('#emp_list tr').not(this).removeClass('emp_list_clicked');
									  if ($(this).hasClass('emp_list_clicked')) $(this).removeClass('emp_list_clicked');
									     else $(this).addClass('emp_list_clicked');
									  $('#emp_list tr:first').removeClass('emp_list_clicked');
									  });	

<?php
//make js:
$before_open_script="
if ($('#branch_add_submit').val()=='Save') 
	{
	$('#add_branch_box').dialog('option', 'title', 'Edit branch');
	}
";
$close_script="
if ($('#branch_add_submit').val()=='Save') 
	{
	//edit mode was used, go back to add mode:
	$('#add_branch_box span:first').text('Add new branch:');
	$('input[name=\"new_branch_name\"]').val('').attr('readonly',false);
	$('input[name=\"new_address_box\"]').val('');
	$('input[name=\"new_phone_number\"]').val('');
	$('#branch_add_submit').val('Add');
	$('#add_branch_box').dialog('option', 'title', 'Add new branch');
	}
";
$box_maker->make_js_script(array('width'=>350, 'height'=>270),'',$before_open_script,$close_script);
unset($box_maker);

/*$delete_box_maker->make_js_script(array('width'=>300, 'height'=>180));
unset($delete_box_maker);*/
?>
$('.branches_tb tr td').not('.branches_tb tr:first td').toolbar({
                                 content:'#branch_toolbar',
								 position:'top',
								 hideOnClick:true
								 })
								 .bind('toolbarItemClick', function (e, element)
								      {
									  //getting user data:
									  var branch_info_arr=new Array();
									  for (k=0; k<$(this).parent('tr').children('td').length; k++)
									     {
										 branch_info_arr[k]=$(this).parent('tr').children('td:eq('+k+')').text();
										 }
										 var cur_id=$(this).parent('tr').attr('data-id');
									  console.log(branch_info_arr);
									  console.log(cur_id);
									  if ($(element).attr('id')=='branch_edit')
									      {
										  console.log('Edit!');
										 //console.log(emp_info_arr);
										  $('#add_branch_box span:first').text('Edit branch:');
										  $('input[name="new_branch_name"]').val(branch_info_arr[0]).attr('readonly',true);
										  $('input[name="new_address_box"]').val(branch_info_arr[1]);
										  $('input[name="new_phone_number"]').val(branch_info_arr[2]);
										  $('#branch_add_submit').val('Save');
										  $('#add_branch').trigger('click');
										  }
										  if ($(element).attr('id')=='branch_del')
									      {
										  console.log('Delete branch!');
										  if (confirm('Do you really want to delete this branch?')) $.post('./server_scripts/ajax_worker.php',{delete_branch:branch_info_arr[0]}, function (data)
										                     {
															 alert(data);
															 if ($.trim(data).indexOf('Successfully')!=-1)
															    {
																window.location.reload();
																}
															 });
										  }
									  });	

</script>
<?php
$emploee_tab=ob_get_clean();

/*------------------------------------------------------------------------------------------------*/

//Sales Stock tab:
ob_start();
?>
<div class='tab_container'>
<?php
$template_handler=new panels_templates();
//render side menu:
$template_handler->render_side_menu(array('Stock List'),array('View Stock'),array('New Stock','Edit Stock'));
//render searchbar:
$template_handler->show_search_bar();
$cur_code=$template_handler->cur_code;
unset($template_handler);
?>
<div class='main_content'>
</div>

</div>
<?php
$sales_stock_tab=ob_get_clean();

/*------------------------------------------------------------------------------------------------*/

//Sales Point tab:
ob_start();
?>
<div class='tab_container'>
<?php
$template_handler=new panels_templates();
//render side menu:
$template_handler->render_side_menu(array('Customer List','Sales Interface'),array('View Customer'),array('New Customer','Edit Customer'));
//render searchbar:
$template_handler->show_search_bar();
$cur_code=$template_handler->cur_code;
unset($template_handler);
?>
<div class='main_content'>
</div>

</div>
<?php
$sales_point_tab=ob_get_clean();

/*------------------------------------------------------------------------------------------------*/

//Ledgers tab:
ob_start();
?>
<div class='tab_container'>
<?php
$template_handler=new panels_templates();
//render side menu:
$template_handler->render_side_menu(array('Audit Trail List', 'Nominal Ledger List', 'Bank Trail List', 'Bank Ledger List'),array('View Audit Trail', 'View Nominal Ledger', 'View Bank Trail', 'View Bank Ledger'),array('New Audit Trail','Edit Audit Trail','New Nominal Ledger','Edit Nominal Ledger','New Bank Trail','Edit Bank Trail','New Bank Ledger','Edit Bank Ledger'));
//render searchbar:
$template_handler->show_search_bar();
$cur_code=$template_handler->cur_code;
unset($template_handler);
?>
<div class='main_content'>
</div>

</div>
<?php
$accounts_tab=ob_get_clean();

/*------------------------------------------------------------------------------------------------*/

//Maintenance tab:
ob_start();
?>
<div class='tab_container'>
<span style='font-weight:bold; font-size:14pt;'>Maintenance:</span>

</div>
<?php
$maintenance_tab=ob_get_clean();

/*------------------------------------------------------------------------------------------------*/

//Purchase Point tab:
ob_start();
?>
<div class='tab_container'>
<?php
$template_handler=new panels_templates();
//render side menu:
$template_handler->render_side_menu(array('Supplier List'),array('View Supplier'),array('New Supplier','Edit Supplier'));
//render searchbar:
$template_handler->show_search_bar();
$cur_code=$template_handler->cur_code;
unset($template_handler);
?>
<div class='main_content'>
</div>

</div>
<?php
$purchase_point_tab=ob_get_clean();

/*------------------------------------------------------------------------------------------------*/

//Hire Stock tab:
ob_start();
?>
<div class='tab_container'>
<?php
$template_handler=new panels_templates();
//render side menu:
//$template_handler->render_side_menu(array('Unit List','Family List'),array('View Unit','View Family'),array('New Unit','Edit Unit','New Family','Edit Family'));
?>
<div class='side_menu' style='margin-top:-40px;'>
<div>
<span class='menu_item'>Fleet records</span>
<div class='sub_menus'>
<span class='sub_menu_item'>New item</span>
<span class='sub_menu_item'>Changes</span>
<span class='sub_menu_item'>Activity</span>
<span class='sub_menu_item'>History</span>
<span class='sub_menu_item'>Test</span>
<span class='sub_menu_item'>Notes</span>
<span class='sub_menu_item'>Browse</span>
<span class='sub_menu_item'>Find</span>
<span class='sub_menu_item'>Visits</span>
<span class='sub_menu_item'>Hide</span>
<span class='sub_menu_item'>Home</span>
</div>
</div>
<div>
<span class='menu_item'>Group changes</span>
<div class='sub_menus'>
<div class='complex_item'>
<span class='sub_menu_item'>Group changes</span>
<div class='sub_menus'>
<span class='sub_sub_menu_item'>Activity</span>
<span class='sub_sub_menu_item'>Hol</span>
<span class='sub_sub_menu_item'>Rate</span>
<span class='sub_sub_menu_item'>Calc Code</span>
<span class='sub_sub_menu_item'>VAT</span>
<span class='sub_sub_menu_item'>Acc Gp</span>
<span class='sub_sub_menu_item'>Nominal</span>
<span class='sub_sub_menu_item'>Members</span>
<span class='sub_sub_menu_item'>New</span>
<span class='sub_sub_menu_item'>Remove</span>
<span class='sub_sub_menu_item'>Exit</span>
</div>
</div>
<span class='sub_menu_item'>Rate explorer</span>
<span class='sub_menu_item'>Family names</span>
</div>
</div>
<span class='menu_item'>Net rates</span>
<span class='menu_item'>Global</span>
<span class='menu_item'>Fleet status</span>
<span class='menu_item'>Price list</span>
<span class='menu_item'>Reports</span>
<span class='menu_item'>Transfer Stock</span>
<span class='menu_item'>Charged since</span>
<span class='menu_item'>Additional Detail</span>
</div>
<?php
//render searchbar:
//$template_handler->show_search_bar();
$cur_code=$template_handler->cur_code;
unset($template_handler);
?>
<div class='main_content'>
</div>

</div>
<?php
$hire_stock_tab=ob_get_clean();
/*------------------------------------------------------------------------------------------------*/

//Admin point tab:
ob_start();
?>
<div class='tab_container'>
<?php
$template_handler=new panels_templates();
//render side menu:
$template_handler->render_side_menu(array('Contact List','VAT List','Holiday List'),array('View Contact','View VAT','View Holiday'),array('New Contact','Edit Contact','New VAT','Edit VAT','New Holiday','Edit Holiday'));
//render searchbar:
$template_handler->show_search_bar();
$cur_code=$template_handler->cur_code;
unset($template_handler);
?>
<div class='main_content'>
</div>

</div>
<?php
$admin_point_tab=ob_get_clean();

/*------------------------------------------------------------------------------------------------*/

?>