<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hire_stock extends CI_Controller
{
	public function get_eligible_items_for_group_json()
	{
		$this->load->model('stock_m');
		$this->load->model('hire_stock_m');
		
		header('Content-type: application/json');
		echo json_encode($this->hire_stock_m->select_elegible_items());
	}
	
	public function get_multiple_items_json()
	{
		$this->load->model('hire_stock_m');
		
		header('Content-type: application/json');
		echo json_encode($this->hire_stock_m->select_items_multiple());
	}
	
	public function get_single_items_json()
	{
		$this->load->model('hire_stock_m');
		
		header('Content-type: application/json');
		echo json_encode($this->hire_stock_m->select_single_items());
	}
	
	public function groups()
	{	
		$this->load->model('vats_m');
		$this->load->model('hire_stock_m');
		
		$data['groups'] = $this->hire_stock_m->get_groups_all();
		$data['vats'] = $this->vats_m->get_all_vats();
		$data['calc_codes'] = $this->hire_stock_m->get_charging_bands_all();
		
		$this->load->view('header_nav');
		$this->load->view('hire_stock_groups', $data);		
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/dhtmlx-4.13/codebase/dhtmlxgrid.js')."\"></script>");	
		$this->output->append_output("<script src=\"".base_url('assets/dhtmlx-4.13/sources/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js')."\"></script>");	
		$this->output->append_output("<script src=\"".base_url('assets/js/hire_stock_groups.js')."\"></script>");
		$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.4.custom/jquery-ui.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function groups_json()
	{
		$this->load->model('hire_stock_m');
		
		$groups = $this->hire_stock_m->get_groups_all();
		
		$data = array();
		$id = 1;
		foreach($groups as $g)
		{
			array_push($data, array( 'id' => $id, "data"=> array( $g->pk_id, $g->name, $g->total, $g->basic_rate, $g->calc_code, $g->vat, "Accesory Group^javascript:edit_qtys(".$g->pk_id.");^_self",
			"Remove group^javascript:remove_group(".$g->pk_id.");^_self")));
			$id++;
		}
		header('Content-type: application/json');
		echo json_encode(array( "rows" => $data));
	}

	public function group_accesories()
	{
		$pk_id = trim($this->input->get('id'));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$this->load->model('hire_stock_m');
			
			$data['group_id'] = $pk_id;
			$data['accesories'] = $this->hire_stock_m->get_accesories_from_group($pk_id);
			
			$this->load->view('hire_accesories_group', $data);
		}
	}
	
	public function ins_charging_band()
	{
		$this->load->helper(array('form', 'url'));
		
		$name 				= $this->input->post('name', true);
		$_4hr_perc 			= str_replace('%','', $this->input->post('_4hr_perc', true));
		$_8hr_perc 			= str_replace('%','', $this->input->post('_8hr_perc', true));
		$_1day_perc 		= str_replace('%','', $this->input->post('_1day_perc', true));
		$_2day_perc 		= str_replace('%','', $this->input->post('_2day_perc', true));
		$_3day_perc 		= str_replace('%','', $this->input->post('_3day_perc', true));
		$_4day_perc 		= str_replace('%','', $this->input->post('_4day_perc', true));
		$_5day_perc 		= str_replace('%','', $this->input->post('_5day_perc', true));
		$_6day_perc	 		= str_replace('%','', $this->input->post('_6day_perc', true));
		$_week_perc 		= str_replace('%','', $this->input->post('_week_perc', true));
		$_weekend_perc 		= str_replace('%','', $this->input->post('_weekend_perc', true));
		$_subsequent_perc 	= str_replace('%','', $this->input->post('subsequent_perc', true));
		$days_week 			= $this->input->post('days_week', true);
		$thereafter 		= $this->input->post('thereafter', true);
		$min_days 			= $this->input->post('min_days', true);
		
		$vars_array = compact("name",
		                      "_4hr_perc", 			
		                      "_8hr_perc", 			
		                      "_1day_perc", 		
		                      "_2day_perc", 		
		                      "_3day_perc", 		
		                      "_4day_perc", 		
		                      "_5day_perc", 		
		                      "_6day_perc",	 		
		                      "_week_perc", 		
		                      "_weekend_perc", 		
		                      "_subsequent_perc", 	
		                      "days_week", 			
		                      "thereafter", 		
		                      "min_days");
		
		$this->load->model('hire_stock_m');
		$result = $this->hire_stock_m->insert_charging_band( $vars_array );
		if( $result != false )
		{
			if(is_numeric($result))
			{
				$dataArray = array("result"=>"ok", "id" => $result, "name"=>$name);
			}else{
				$dataArray = array("result"=>"ko");
			}
			
			header('Content-type: application/json');
			echo json_encode($dataArray);		
		}
			   
	}
	
	public function save_group()
	{
		$this->load->helper(array('form', 'url'));
		
		$name 				= $this->input->post('name', true);
		$basic_rate 		= $this->input->post('basic_rate', true);		
		$charging_band_id 	= $this->input->post('charging_band', true);		
		$vat_code_id 		= $this->input->post('vat_code', true);
		$group_id = trim($this->security->xss_clean($_POST['group_id']));
		
		if($charging_band_id!="")
		{
			$vars_array = compact("name",
		                      "basic_rate", 			
		                      "charging_band_id", 			
		                      "vat_code_id"
							  );
		
			$this->load->model('hire_stock_m');
			
			
			if($group_id != "")
			{				
				$vars_array['group_id'] = $group_id;
				
				if($this->hire_stock_m->update_group( $vars_array ))
				{
					echo "ok";
					return;
				}else{
					echo "ko";
				}
			}else{
				$result = $this->hire_stock_m->insert_group( $vars_array );
			}
			
			if( $result != false )
			{
				if(is_numeric($result))
				{
					log_message('debug', 'count: '.count($_POST['accesory_group']));
					for($i = 0; $i < count($_POST['accesory_group']); $i++)
					{
						$data = trim($this->security->xss_clean($_POST['accesory_group'][$i]));	
						$data = explode(",", $data);
						$group_id = $result;
						$item_type = $data[1];
						$item_id = $data[0];
						if(isset($_POST['accesory_group_qty'][$i]))
							$qty = trim($this->security->xss_clean($_POST['accesory_group_qty'][$i]));	
						
						$vars_array = compact("group_id", "item_type", "item_id", "qty");
						if( $this->hire_stock_m->ins_accesory_to_group( $vars_array ) == false )
						{
							echo "ko";
							break;
						}
					}				
					echo "ok";
				}elseif($result == "exists"	)
				{
					echo "exists";
				}
			}
		}else{
			echo "Please, pick a charging band.";
		}		
	}
	
	public function items_from_family_from_json()
	{
		$pk_id = trim($this->input->get('id'));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$this->load->model('stock_m');			
			$data = array();
			foreach( $this->stock_m->get_items_from_family_from_item($pk_id) as $item)
			{
				$i = array("pk_id" => $item->pk_id, "label" => $item->description, "quantity_balance" => $item->quantity_balance, "quantity_on_order" => $item->quantity_on_order, "quantity_rec_level" => $item->quantity_rec_level );
				if($item->supplier_a_code != "NULL") {
					$i["cost_price"] = $item->cost_price_a ;
					$i["supplier_code"] = $item->supplier_a_code == "null" ? "" : $item->supplier_a_code ; }
				else if( $item->supplier_b_code != "NULL" ) {
					$i["cost_price"] = $item->cost_price_b;
					$i["supplier_code"] = $item->supplier_b_code == "null" ? "" : $item->supplier_b_code; }
				else if( $item->supplier_c_code != "NULL" ) {
					$i["cost_price"] = $item->cost_price_c;
					$item->supplier_c_code == "null" ? "" : $item->supplier_c_code; }
				
				array_push($data , $i);
			}
			header('Content-type: application/json');
			echo json_encode($data);
		}else
			echo "[]";
	}
	
	public function items_from_supplier_json()
	{
		$pk_id = trim($this->input->get('id'));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$this->load->model('stock_m');			
			$data = array();
			foreach( $this->stock_m->get_items_soled_by($pk_id) as $item)
			{
				$i = array("pk_id" => $item->pk_id, "label" => $item->description, "quantity_balance" => $item->quantity_balance == null ? 0:$item->quantity_balance, "quantity_on_order" => $item->quantity_on_order == null ? 0:$item->quantity_on_order, "quantity_rec_level" => $item->quantity_rec_level == null ? '0':$item->quantity_rec_level );
				if($item->supplier_a_code != "NULL") {
					$i["cost_price"] = $item->cost_price_a ;
					$i["supplier_code"] = $item->supplier_a_code == "null" ? "" : $item->supplier_a_code ; }
				else if( $item->supplier_b_code != "NULL" ) {
					$i["cost_price"] = $item->cost_price_b;
					$i["supplier_code"] = $item->supplier_b_code == "null" ? "" : $item->supplier_b_code; }
				else if( $item->supplier_c_code != "NULL" ) {
					$i["cost_price"] = $item->cost_price_c;
					$item->supplier_c_code == "null" ? "" : $item->supplier_c_code; }
				
				array_push($data , $i);
			}
			header('Content-type: application/json');
			echo json_encode($data);
		}else
			echo "[]";
		
	}
	
	public function massive_changes_form()
	{
		$this->load->model('family_groups_m');
		$this->load->model('vats_m');	
		
		$data['family_groups'] = $this->family_groups_m->get_groups();
		$data['vats'] = $this->vats_m->get_all_vats();
		
	
		$this->load->view('header_nav');
		$this->load->view('footer_common');
		$this->load->view('global_changes_forms', $data);
		$this->output->append_output("<script src=\"".base_url('assets/js/stock_items.js')."\"></script>");					
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function new_item()
	{	
		$this->load->model('hire_stock_m');
		$data['families'] = $this->hire_stock_m->get_groups_all();
		
		$this->load->view('header_nav');
		$this->load->view('hire_stock_new_item', $data);		
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/hire_stock.js')."\"></script>");
		$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.4.custom/jquery-ui.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function remove_accesory()
	{
		$pk_id = trim($this->input->post('id'));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$this->load->model('hire_stock_m');	
			if($this->hire_stock_m->delete_accesory($pk_id))
			{
				echo "ok";
			}else{
				echo "ko";
			}				
		}			
	}
	
	public function report_stock_levels()
	{
		$this->load->model('stock_m');
		$data['items'] = $this->stock_m->get_items_stock_levels();
		
		$this->load->view('header_nav');
		$this->load->view('list_stock_items_levels', $data);
		$this->load->view('footer_common');
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function save_components_members()
	{
		$this->load->model('hire_stock_m');
		
		for($i = 0; $i<count($_POST['new_item_id_in']); $i++)
		{
			$parent_item = $this->input->post('parent_item', true);
			$item_id	= trim($this->security->xss_clean($_POST['new_item_id_in'][$i]));
			$qty	= trim($this->security->xss_clean($_POST['new_item_qty_in'][$i]));
			
			$vars_array = compact("parent_item", "item_id", "qty");
			if($this->hire_stock_m->ins_item_to_family($vars_array) == false)
			{
				$return = array("result"=>"ko", "error"=>"error saving in the database");
			echo json_encode($return);
				return;
			}
			
		}
		$return = array("result"=>"ok");
		echo json_encode($return);
	}
	
	public function save_item()
	{
		$this->load->helper(array('form', 'url'));		

		$fleet_number			= $this->input->post('fleet_number', true);
		$description			= $this->input->post('description', true);
		$hire_item_type           	= $this->input->post('hire_item_type', true);
		$group_id      			= $this->input->post('group_id', true);
		$basic_rate    		= $this->input->post('basic_rate', true);
		$purchase_date      	= $this->input->post('purchase_date', true);
		$cost_price     	= $this->input->post('cost_price', true);
		$serial_number     	= $this->input->post('serial_number', true);
		$power     			= $this->input->post('power', true);
		$earth     				= $this->input->post('earth', true);
		$fuse     			= $this->input->post('fuse', true);
		$flash_test     	= $this->input->post('flash_test', true);
		$engine_speed     	= $this->input->post('engine_speed', true);
		$output_spindle     	= $this->input->post('output_spindle', true);
		$cable_type     	= $this->input->post('cable_type', true);
		$cable_length     	= $this->input->post('cable_length', true);
		$ppe_kit     		= "NULL";
		$safety_leaflet     	= $this->input->post('safety_leaflet', true);
		$test_frequency     	= $this->input->post('test_frecuency', true);		

		$vars_array = compact(
						"fleet_number",
						"description",
						"hire_item_type",
						"group_id",
						"basic_rate",
						"purchase_date",
						"cost_price",
						"serial_number",
						"power",
						"earth",
						"fuse",
						"flash_test",
						"engine_speed",
						"output_spindle",
						"cable_type",
						"cable_length",
						"ppe_kit",
						"safety_leaflet",
						"test_frequency"
					);
		$this->load->model('hire_stock_m');
		$result = $this->hire_stock_m->ins_hire_item( $vars_array );
		if( $result != false )
		{
				$return = array("result"=>"ok", "new_item_id"=>$result, "type"=>$hire_item_type);
				echo json_encode($return);
		}
	}
	
	/*public function save_item_prices()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$item_id = trim($this->input->post('stock_item_id', true));
		
		$prices = array();
		for($i = 0; $i < count($_POST['customers_pk_id']); $i++)
		{
			$customer_id = trim($this->security->xss_clean($_POST["customers_pk_id"][$i])) == "0" ? "" : trim($this->security->xss_clean($_POST["customers_pk_id"][$i]));
			$price_type = trim($this->security->xss_clean($_POST["price_type"][$i]));
			$min_qty = trim($this->security->xss_clean($_POST["min_qty"][$i]));
			$max_qty = trim($this->security->xss_clean($_POST["max_qty"][$i]));
			$price = trim($this->security->xss_clean($_POST["price"][$i]));													
			
			if( (is_numeric($customer_id) || $customer_id == "") &&
				((is_numeric($min_qty) && $min_qty > 0)  || $min_qty == "" ) && 
				((is_numeric($max_qty) && $max_qty > 0) || $max_qty == "" ) &&
				((is_numeric($price) && $price > 0 ) || $price == "" ) &&
				(is_numeric($price_type) && $price_type >= 0 && $price_type <= 2) &&
				( ($price_type == 2 && is_numeric($customer_id)) || (($price_type == 0 || $price_type == 1) && $customer_id == ""))
				)
			{
				$prices_item = array("stock_item_id"=>$item_id, "customer_id"=>$customer_id , "price_type"=>$price_type, "min"=>$min_qty, "max"=>$max_qty, "price"=>$price);
				array_push($prices, $prices_item);							
			}else
			{
				echo "ko-validation";
				return;
			}
						
		}
		
		$this->load->model('stock_m');
		
		foreach( $prices as $item )
		{
			if( !$this->stock_m->ins_up_item_price($item) )
			{
				echo "ko-db";
				return;
			}
		}
		echo base_url('index.php/sales_stock/new_existing/'.$item_id);
	}*/
	
	public function save_accesory_group()
	{
		if($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->load->model('hire_stock_m');
		
			if(isset($_POST['item_id']))
			{
				for($i=0; $i<count($_POST['item_id']); $i++)
				{
					if(isset($_POST['qty_in'][$i]))
					{	
						$acc_id = trim($this->security->xss_clean($_POST['item_id'][$i]));
						$acc_qty = trim($this->security->xss_clean($_POST['qty_in'][$i]));				
						
						if($this->hire_stock_m->update_accesory_qty( $acc_id, $acc_qty ) != true)
						{
							echo "ko";
						}
					}								
				}
			}
			
			if(isset($_POST['new_item_id_in']))
			{
				for($i=0; $i<count($_POST['new_item_id_in']); $i++)
				{
					$this->load->model('hire_stock_m');
					
					$group_id = trim($this->security->xss_clean($_POST['group_id']));
					$item_type = trim($this->security->xss_clean($_POST['new_item_origin_in'][$i]));
					$item_id = trim($this->security->xss_clean($_POST['new_item_id_in'][$i]));
					$hire_item_type = trim($this->security->xss_clean($_POST['new_hire_item_type_in'][$i]));
					
					if(intval($hire_item_type) > 1)
					{
						$qty = "NULL";
					}elseif($_POST['new_item_qty_in'][$i]== "")
					{
						$qty = 0;
					}else {
						$qty = trim($this->security->xss_clean($_POST['new_item_qty_in'][$i]));
					}
										
					$vars_array = compact("group_id", "item_type", "item_id", "qty");
					if(!$this->hire_stock_m->ins_accesory_to_group($vars_array))
						echo "ko";
				}
			}			
			echo "ok";			
		}		
	}
	
	public function shorttext_valid( $valor )
	{
		if( (strlen($valor) > 0 && preg_match('/^[A-Za-zñÑ0-9\/\\\-\_\.\,\#\s]{2,200}$/', $valor) == 1 ) || strlen($valor) == 0)
		{
			return true;
		}else
		{
			$this->form_validation->set_message('shorttext_valid', "Invalid characteres");
			return false;
		}
	}	
	
	public function update_qty_manually()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$config = array(
               array(
					'field'		=> 'type',
					'label'		=> 'Type',
					'rules'		=> 'trim|required|xss_clean|alpha'
			   ),
			   array(
					'field'		=> 'qty',
					'label'		=> 'Quantity',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'cost_price',
					'label'		=> 'Cost Price',
					'rules'		=> 'trim|xss_clean|decimal'
			   ),
			   array(
					'field'		=> 'stock_item_id',
					'label'		=> 'ITEM ID',
					'rules'		=> 'trim|xss_clean|integer'
			   ));
		
		$this->form_validation->set_rules($config);

		if ( !$this->form_validation->run() )
		{
			echo validation_errors();
		}
		else
		{ 
			$fk_item_id = $this->input->post('stock_item_id');			
			$qty = $this->input->post('qty');
			$cost = $this->input->post('cost_price');
			$description = "manual update";
			$date = date('Y-m-d H:i:s');
			$type = $this->input->post('type');
			
			if($type == "remove")
				$qty *= -1;
			
			$vars_array = compact("fk_item_id", "qty", "cost", "cost_price_b", "cost_price_c", "description", "date");
			
			$this->load->model('stock_m');
			$result = $this->stock_m->update_qty( $vars_array );
			if($result)
			{
				redirect(base_url('index.php/sales_stock/new_existing/'.$fk_item_id),'refresh');
			}else
			{
				redirect(base_url('index.php/sales_stock/new_existing/'.$fk_item_id.'/'.urlencode('There was a problem updating the item. Please, try again.')),'refresh');
			}
		}
	}

	public function update_balances_massive()
	{
		$this->load->library('form_validation');
	
		if($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->load->model('stock_m');
			log_message('debug', $this->input->post('apply_to', true));
			
			$apply_to = $this->input->post('apply_to', true);
			$family_group_id = $this->input->post('family_groups', true);
			$set_balance = $this->input->post('set_balance', true);
			$date = date('Y-m-d H:i:s');
			$negative_balances = ($set_balance) == "Zero all" ? "false":"true" ;
			
			$vars_array = compact("apply_to", "family_group_id", "negative_balances", "date");
			$result = $this->stock_m->upd_balances_massive( $vars_array );
			if($result != false)
			{
				echo $result;
			}else
			{
				echo "ko-db";
			}
			
		}else{
			echo "No post received";
		}
	}
	
	public function update_locations_massive()
	{
		$this->load->library('form_validation');
	
		if($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->load->model('stock_m');
			log_message('debug', $this->input->post('apply_to', true));
			
			$family_group_id = $this->input->post('family_groups', true) == false ? null:$this->input->post('family_groups', true);
			$location = $this->input->post('location', true);
			$date = date('Y-m-d H:i:s');
			
			$vars_array = compact("family_group_id", "location", "date");
			$result = $this->stock_m->upd_locations_massive( $vars_array );
			if($result != false)
			{
				echo $result;
			}else
			{
				echo "ko-db";
			}
			
		}else{
			echo "No post received";
		}
	}
	
	public function update_prices_massive()
	{
		$this->load->library('form_validation');
	
		if($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->load->model('stock_m');
			
			$apply_to = $this->input->post('apply_to', true);
			$family_group_id = $this->input->post('family_groups', true) == false ? null:$this->input->post('family_groups', true);
			$date = date('Y-m-d H:i:s');			
			$set_raised_by = $this->input->post('set_raised_by', true);			
			$by_percentage = ($set_raised_by) == "Percentages" ? "true":"false";
			
			$standard = $this->input->post('standard_price', true) == '' ? null:$this->input->post('standard_price', true);
			$standard = $by_percentage == "true" && $standard == null ? 0:$standard;
			
			$special = $this->input->post('special_price', true) == '' ? null:$this->input->post('special_price', true);
			$special = $by_percentage == "true" && $special == null ? 0:$special;
			
			$cost_price_a = $this->input->post('cost_price_a', true) == '' ? null:$this->input->post('cost_price_a', true);
			$cost_price_a = $by_percentage == "true" && $cost_price_a == null ? 0:$cost_price_a;
			
			$cost_price_b = $this->input->post('cost_price_b', true) == '' ? null:$this->input->post('cost_price_b', true);
			$cost_price_b = $by_percentage == "true" && $cost_price_b == null ? 0:$cost_price_b;
			
			$cost_price_c = $this->input->post('cost_price_c', true) == '' ? null:$this->input->post('cost_price_c', true);
			$cost_price_c = $by_percentage == "true" && $cost_price_c == null ? 0:$cost_price_c;
			
			log_message('debug', "$family_group_id $by_percentage $standard $special $cost_price_a $cost_price_b $cost_price_c $date");
			$vars_array = compact("family_group_id", "by_percentage", "standard", "special", "cost_price_a", "cost_price_b", "cost_price_c", "date");
			$result = $this->stock_m->upd_prices_massive( $vars_array );
			if($result != false)
			{
				echo $result;
			}else
			{
				echo "ko-db";
			}
			
		}else{
			echo "No post received";
		}
	}
	
	public function update_vats_massive()
	{
		$this->load->library('form_validation');
	
		if($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->load->model('stock_m');
			log_message('debug', $this->input->post('apply_to', true));
			
			$family_group_id = $this->input->post('family_groups', true) == false ? null:$this->input->post('family_groups', true);
			$vat_id = $this->input->post('fk_vat_code', true) == "0" ? '': $this->input->post('fk_vat_code', true);
			$date = date('Y-m-d H:i:s');
			
			$vars_array = compact("family_group_id", "vat_id", "date");
			$result = $this->stock_m->upd_vats_massive( $vars_array );
			if($result != false)
			{
				echo $result;
			}else
			{
				echo "ko-db";
			}
			
		}else{
			echo "No post received";
		}
	}
}