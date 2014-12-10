<?php
class panels_templates
{
public $cur_code=0;
public function show_search_bar()
  {
  echo "<div class='top_panel'>
<table class='top_table'>
<tr>";
$this->cur_code=1002;
echo "
<td style='font-weight:bold;'>Code</td><td style='font-weight:bold;'><input type='text' class='search_code' style='width:80px;' value='";
//echo $this->cur_code;
echo "'/></td><td>&nbsp;</td>
<td>Keyword</td><td><input class='search_keyword' type='text' style='width:120px;'/></td><td>&nbsp;</td>
<td class='act_links'><span class='search_all_act'>Search All Branches</span></td><td>&nbsp;</td>
<td>Level</td><td>
<select name='level_sl' style='width:80px;'>
<option>Unit</option>
<option selected>Family</option>
<option>Groups</option>
</select>
</td>
</tr>
<tr>
<td>Date from:</td><td><input type='text' class='date_from' style='width:80px;' /></td><td>&nbsp;</td>
<td>to</td><td><input type='text' class='date_to' style='width:80px;' /></td><td>&nbsp;</td>
<td class='act_links'><span class='search_onsite_act'>Search Onsite</span></td><td>&nbsp;</td>
<td>&nbsp;</td><td >&nbsp;</td>
</tr>
</table>
</div>";
  }

public function render_side_menu($lists_arr,$details_arr,$inp_edit_arr)
	{
	//$lists_arr, $details_arr, $inp_edit_arr -- arrays with menu items
	echo "<div class='side_menu'>
<div>
<span class='menu_header'>Lists</span>";
foreach ($lists_arr as $cur_list_item)
{
echo "<span class='menu_item'>$cur_list_item</span>";
}
echo "</div>
<div style='margin-top:10px;'>
<span class='menu_header'>Details</span>";
foreach ($details_arr as $cur_details_item)
{
echo "<span class='menu_item'>$cur_details_item</span>";
}
echo "
</div>
<div style='margin-top:10px;'>
<span class='menu_header'>Input/Edit</span>";
foreach ($inp_edit_arr as $cur_ie_item)
{
echo "<span class='menu_item'>$cur_ie_item</span>";
}
echo '
</div>
</div>';
	
	} 

public function generate_details_table($cur_cd,$tab_info,$cells_array)
  {
ob_start();
//make it as function:
?>
<table class='det_table'>
<tr><td>
<?php
if (!isset($tab_info[0]['Code'])) echo 'Id:';
 else echo 'Code:';
?>
</td><td><input type='text' name='det_code' style='width:80px;' value='<?php echo $cur_cd;?>'/></td></tr>
<?php
$found=false;
foreach ($tab_info as $cur_info)
{
$cur_db_code=isset($cur_info['Code']) ? $cur_info['Code']:$cur_info['id'];
if ($cur_db_code==$cur_cd)
   {
   $found=true;
   foreach ($cells_array as $cur_label=>$cur_db_name)
     {
	 if ((isset($cur_info['Unit']) and $cur_db_name=='Available') or (isset($cur_info['Family']) and $cur_db_name=='Min_1_Week') or $cur_db_name=='Reconciled' or $cur_db_name=='Affects_Hiring') $cur_info[$cur_db_name]=$cur_info[$cur_db_name]==1 ? 'Yes':'No';
	 if ($cur_db_name=='Disposal' or $cur_db_name=='Returned_date' or $cur_db_name=='Sold_date' or $cur_db_name=='Reconciliation_Date' or $cur_db_name=='Date_Opened' or $cur_db_name=='Date_Start' or $cur_db_name=='Date_End') 
		{
		$cur_info[$cur_db_name]=$cur_info[$cur_db_name]=='0' ? '-' : $cur_info[$cur_db_name];
		$cur_info[$cur_db_name]=trim($cur_info[$cur_db_name])=='' ? '-' : $cur_info[$cur_db_name];
		}
	 echo '<tr><td>'.$cur_label.':</td><td>'.$cur_info[$cur_db_name].'</td></tr>';
	 }
   }
}

?>
</table>
<button class='det_edit_but' attr-cur_code='<?php echo $cur_cd;?>'>Edit</button>
<button class='det_back_but'>Back to List</button>
<?php
if (!$found) echo '<span class="err_msg">No detailed data is avaible for this code!</span>';
$content_view=ob_get_clean();
return $content_view;
  }  

public function generate_list_table($tab_info,$cells_array)  
  {
  ob_start();
?>
<table class='list_table'>
<tr>
<?php
foreach ($cells_array as $cur_header=>$cur_db_header)
{
echo "<th>$cur_header</th>";
}
?>
<th>&nbsp;</th></tr>
<?php
foreach ($tab_info as $cur_info)
{
echo '<tr>';
  foreach ($cells_array as $cur_header=>$cur_db_header)
    {
	if ((isset($cur_info['Family']) and $cur_db_header=='Min_1_Week') or $cur_db_header=='Reconciled' or $cur_db_header=='Affects_Hiring') $cur_info[$cur_db_header]=$cur_info[$cur_db_header]==1 ? 'Yes':'No';
	if ($cur_db_header=='Reconciliation_Date'  or $cur_db_header=='Date_Start' or $cur_db_header=='Date_End') $cur_info[$cur_db_header]=$cur_info[$cur_db_header]=='0' ? '-' : $cur_info[$cur_db_header];
	$cur_val=$cur_info[$cur_db_header]=='' ? '-' : $cur_info[$cur_db_header];
	//if ($cur_db_header=='Avaible') $cur_val=$cur_info['Avaible']==1 ? 'Yes':'No';
	echo "<td>$cur_val</td>";
	}
if (isset($cur_info['Code'])) echo "<td><button class='detail_but' attr-row_id='".$cur_info['id']."' attr-row_code='".$cur_info['Code']."'>Detail</button></td></tr>";
	else echo "<td><button class='detail_but' attr-row_id='".$cur_info['id']."' attr-row_code='".$cur_info['id']."'>Detail</button></td></tr>";
}
?>
</table>
<?php
return ob_get_clean();
  }
  
public function generate_add_edit_table($tab_info,$cells_array,$types_array,$act_type,$select_array='',$al_empt_array,$check_type_array,$db_type_array,$db_table,$cur_cd)
  {
  global $admin_ac;
ob_start();
?>
<?php
if ($act_type=='new')
{
$form_id='add_item_form';
$sub_id='add_frm_submit';
$sub_val='Add';
}
else
{
$form_id='edit_item_form';
$sub_id='edit_frm_submit';
$sub_val='Save';
}
?>
<form name='<?php echo $form_id; ?>' id='<?php echo $form_id; ?>' method='post' action='./server_scripts/ajax_worker.php'>
<table class='det_table'>
<?php
if ($act_type=='edit')
{
if ((!isset($cur_cd) or $cur_cd==0) and $db_table=='fleet') $cur_cd=$tab_info[0]['Code'];
if ((!isset($cur_cd) or strpos($cur_cd,'FAM') === false) and $db_table=='family') $cur_cd=$tab_info[0]['Code'];
if ((!isset($cur_cd) or $cur_cd=='0') and ($db_table=='stock_list' or $db_table=='supplier_list' or $db_table=='customer_list' or $db_table=='nominal_ledger' or $db_table=='bank_ledger' or $db_table=='vat_list')) $cur_cd=$tab_info[0]['Code'];
//if ((!isset($cur_cd) or $cur_cd=='0') and $db_table=='supplier_list') $cur_cd='SP0001';
//if ((!isset($cur_cd) or $cur_cd=='0') and $db_table=='customer_list') $cur_cd='CU0001';
if ((!isset($cur_cd) or $cur_cd==0) and ($db_table=='contact_list' or $db_table=='audit_trail' or $db_table=='bank_trail' or $db_table=='holiday_list')) $cur_cd=1;
?>
<tr><td>
<?php
if ($db_table=='contact_list' or $db_table=='audit_trail' or $db_table=='bank_trail' or $db_table=='holiday_list') echo 'Id:';
 else echo 'Code:';
?>
</td><td><input type='text' name='edit_code' style='width:80px;' value='<?php echo $cur_cd;?>'/></td></tr>
<?php
$tab_this_code=array();
$found=false;
foreach ($tab_info as $cur_info)
{
$cur_db_code=isset($cur_info['Code']) ? $cur_info['Code']:$cur_info['id'];
if ($cur_db_code==$cur_cd)
   {
   $found=true;
   $tab_this_code=$cur_info;
   }
}  
}
foreach ($cells_array as $cell_label=>$cur_cell_name)
  {
  echo '<tr>';
  echo "<td>$cell_label:</td>";
  if ($types_array[$cell_label]=='text')
     {
	 if ($act_type=='new') 
	 {
	/* if ($cell_label=='Returned Date' or $cell_label=='Sold Date' or $cell_label=='Disposal Date') echo "<td><input type='text' name='$cur_cell_name' disabled='true'/></td>";
	 else */echo "<td><input type='text' name='$cur_cell_name'/></td>";
	 }
	   else if ($found) echo "<td><input type='text' name='$cur_cell_name' value='".$tab_this_code[$cur_cell_name]."'/></td>";
	 }
  if ($types_array[$cell_label]=='date')
     {
	 if ($act_type=='new') 
	 {
	 if ($cell_label=='Returned Date' or $cell_label=='Sold Date' or $cell_label=='Disposal Date' or $cell_label=='Reconciliation Date') echo "<td><input type='text' name='$cur_cell_name' class='date_field' disabled='true' /></td>";
		else echo "<td><input type='text' name='$cur_cell_name' class='date_field' /></td>";
	 }
	   else if ($found) echo "<td><input type='text' name='$cur_cell_name' class='date_field' value='".$tab_this_code[$cur_cell_name]."'/></td>";
	 }
  if ($types_array[$cell_label]=='checkbox')
     {
	 if ($act_type=='new') echo "<td><input type='checkbox' name='$cur_cell_name' value='1'/></td>";
	    else 
		{
		if ($found and ($tab_this_code[$cur_cell_name]==1 or $tab_this_code[$cur_cell_name]=='1')) echo "<td><input type='checkbox' name='$cur_cell_name' value='1' checked/></td>";
			else echo "<td><input type='checkbox' name='$cur_cell_name' value='1'/></td>";
		}
	 }
  if ($types_array[$cell_label]=='select' and $select_array!='')
     {
	 echo '<td><select name="'.$cur_cell_name.'" >';
	 if ($act_type=='edit' and $found)
	   {
	   $cur_val_arr=explode('"',$select_array[$cell_label]);
	   $cur_val=array();
	   for ($ik=1; $ik<count($cur_val_arr); $ik=$ik+2)
	     {
		 $cur_val[]=$cur_val_arr[$ik];
		 }
	   unset($cur_val_arr);
	   foreach ($cur_val as $cur_opt_val)
	     {
		 if ($cur_opt_val==$tab_this_code[$cur_cell_name]) $select_array[$cell_label]=str_ireplace('value="'.$cur_opt_val.'"', 'value="'.$cur_opt_val.'" selected',$select_array[$cell_label]);
		 }
	   }
	 if ($cell_label=='Active')
	 {
	 if (!$admin_ac) 
		{
		if (stripos($select_array[$cell_label],'select')!==false) 
			{
			$cur_opts_arr=explode('<option',$select_array[$cell_label]);
			foreach ($cur_opts_arr as $cur_opt)
				{
				if (stripos($cur_opt,'select')!==false) echo '<option'.$cur_opt.'</option>';
				}
			}
			else 
			{
			echo stristr($select_array[$cell_label],'option>',true).'option>';
			}
		}
		else echo $select_array[$cell_label];
	 }
		else echo $select_array[$cell_label];
	 echo '</select></td>';
	 }
	 else if ($types_array[$cell_label]=='select' and $select_array=='') echo "<td><input type='text' name='$cur_cell_name'/></td>";
  
  echo '</tr>';
  }

?>
</table>
<input type='submit' class='det_save_but' id='<?php echo $sub_id; ?>' value='<?php echo $sub_val; ?>' />
</form>
<button class='det_back_but'>Cancel</button>
<script type='text/javascript'>
$('#<?php echo $form_id;?>').ajaxForm({url:'./server_scripts/ajax_worker.php',type:'POST',data:{form_proceed:true,frm_type:'<?php echo$act_type;?>',fields_arr:'<?php echo json_encode($cells_array);?>',allows_arr:'<?php echo json_encode($al_empt_array);?>',checks_arr:'<?php echo json_encode($check_type_array);?>',types_arr:'<?php echo json_encode($db_type_array);?>',table:'<?php echo $db_table;?>'}, success:function (data)
	                    {
						alert(data);
						if ($.trim(data).indexOf('Successfully')!=-1) 
						     {
							 $('#<?php echo $form_id;?>').resetForm();
							 $('.det_back_but').trigger('click');
							 }
						$('#<?php echo $sub_id;?>').removeAttr('disabled');
						},beforeSubmit: function ()
						      {
							  $('#<?php echo $sub_id;?>').attr('disabled', 'disabled');
							  }});
</script>
<?php
if ($act_type=='edit')
{
if (!$found) echo '<span class="err_msg">No detailed data is avaible for this code!</span>';
}
$content_view=ob_get_clean();
return $content_view;
  }  

//gen any table:
public function generate_table($id='', $class='', $th_array='', $tbody_array='', $style='')
	{
	if (!is_array($tbody_array) or !is_array($tbody_array[0])) 
		{
		echo 'Empty table!';
		return false;
		}
	/*IMPORTANT:
	$tbody_array should be multi array:
	$tbody_array=array(0=>array(),1=>array());
	0,1 --- rows numbers
	*/
	ob_start();
	?>
	<table <?php 
	if (trim($id)!='') echo "id='$id'";
	if (trim($class)!='') echo "class='$class'";
	if (trim($style)!='') echo "style='$style'";
	?>>
	<?php
	if (is_array($th_array)) 
		{
		echo '<tr>';
		foreach ($th_array as $cur_th)
			{
			echo "<th>$cur_th</th>";
			}
		echo '</tr>';
		}
	foreach ($tbody_array as $cur_body)
		{
		echo '<tr>';
		foreach ($cur_body as $cur_td_data)
			{
			echo "<td>$cur_td_data</td>";
			}
		echo '</tr>';
		}
	?>
	</table>
	<?php
	$generated_table=ob_get_clean();
	return $generated_table;
	}
  
}

?>