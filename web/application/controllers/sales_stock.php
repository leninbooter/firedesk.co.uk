<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_stock extends CI_Controller
{
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
	
	public function new_existing()
	{
		$pk_id = trim($this->uri->segment(3));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$this->load->model('stock_m');			
			$data['item'] = $this->stock_m->get_item_details($pk_id);
			$data['editing'] = true;
			$data['prices'] = $this->stock_m->get_item_prices($pk_id);
		}
		
		$this->load->model('customers_m');
		$this->load->model('family_groups_m');
		$this->load->model('discount_groups_m');
		$this->load->model('suppliers_m');
		$this->load->model('vats_m');
		
		$data['customers'] = $this->customers_m->get_customers();
		$data['vats'] = $this->vats_m->get_all_vats();
		$data['family_groups'] = $this->family_groups_m->get_groups();
		$data['family_discounts'] = $this->discount_groups_m->get_groups();
		$data['suppliers'] = $this->suppliers_m->get_suppliers_addresses();		
		
		$this->load->view('header_nav');
		$message = urldecode(trim($this->uri->segment(4)));
		if( $message != false )
		{
			$this->output->append_output("<div class=\"alert alert-danger\" role=\"alert\">".$danger."</div>");
		}
		
		$this->load->view('new_existing_stock_item', $data);		
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/stock_items.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
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
	
	public function save_item()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$config = array(
               array(
					'field'		=> 'stock_number',
					'label'		=> 'Stock Number',
					'rules'		=> 'trim|required|xss_clean|min_length[1]|max_length[15]|alpha_numeric'
			   ),
			   array(
					'field'		=> 'location',
					'label'		=> 'Location',
					'rules'		=> 'trim|xss_clean|min_length[1]|max_length[45|alpha_numeric'
			   ),
			   array(
					'field'		=> 'standard_price',
					'label'		=> 'Standard Price',
					'rules'		=> 'trim|xss_clean|decimal'
			   ),
			   array(
					'field'		=> 'special_price',
					'label'		=> 'Special Price',
					'rules'		=> 'trim|xss_clean|decimal'
			   ),
			   array(
					'field'		=> 'units_of_for_special',
					'label'		=> 'Units of',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'cost_price_a',
					'label'		=> 'Cost Price A',
					'rules'		=> 'trim|xss_clean|decimal'
			   ),
			   array(
					'field'		=> 'cost_price_b',
					'label'		=> 'Cost Price B',
					'rules'		=> 'trim|xss_clean|decimal'
			   ),
			   array(
					'field'		=> 'cost_price_c',
					'label'		=> 'Cost Price C',
					'rules'		=> 'trim|xss_clean|decimal'
			   ),
			   array(
					'field'		=> 'fk_vat_code',
					'label'		=> 'VAT Code',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'fk_supplier_a',
					'label'		=> 'Supplier A',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'fk_supplier_b',
					'label'		=> 'Supplier B',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'fk_supplier_c',
					'label'		=> 'Supplier C',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'fk_family_group',
					'label'		=> 'Family Group',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'fk_discount_group',
					'label'		=> 'Discount Group',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'pk_id',
					'label'		=> 'Stock Item ID',
					'rules'		=> 'trim|xss_clean|integer'
			   )
			   
            );

		$this->form_validation->set_rules($config);
		$this->form_validation->set_rules('description', 'Name', 'callback_shorttext_valid');

		$this->form_validation->set_message('required', 'The %s field is mandatory.');

		if ( !$this->form_validation->run() )
		{
			echo validation_errors();
		}
		else
		{ 
			$stock_number			= $this->input->post('stock_number');
			$location				= $this->input->post('location');
			$description           	= trim($this->input->post('description', true));
			$stock_cost_total      	= $this->input->post('stock_cost_total');
			$stock_cost_average    	= $this->input->post('stock_cost_average');
			$quantity_balance      	= $this->input->post('quantity_balance');
			$quantity_on_order     	= $this->input->post('quantity_on_order');
			$quantity_rec_level    	= $this->input->post('quantity_rec_level');
			$last_moverment        	= $this->input->post('last_moverment');
			$standard_price        	= $this->input->post('standard_price');
			$special_price         	= $this->input->post('special_price');
			$units_of_for_special  	= $this->input->post('units_of_for_special');
			$cost_price_a          	= $this->input->post('cost_price_a');
			$cost_price_b          	= $this->input->post('cost_price_b');
			$cost_price_c          	= $this->input->post('cost_price_c');
			$fk_vat_code           	= $this->input->post('fk_vat_code') == "0" ? "NULL" : $this->input->post('fk_vat_code');
			$fk_family_group       	= $this->input->post('fk_family_group') == "0" ? "NULL" : $this->input->post('fk_family_group');
			$fk_discount_group     	= $this->input->post('fk_discount_group') == "0" ? "NULL" : $this->input->post('fk_discount_group');
			$fk_supplier_a         	= $this->input->post('fk_supplier_a') == "0" ? "NULL" : $this->input->post('fk_supplier_a');
			$fk_supplier_b         	= $this->input->post('fk_supplier_b') == "0" ? "NULL" : $this->input->post('fk_supplier_b');
			$fk_supplier_c          = $this->input->post('fk_supplier_c') == "0" ? "NULL" : $this->input->post('fk_supplier_c');					
			$pk_id 					= $this->input->post('pk_id');

			$vars_array = compact(
							"stock_number",
							"location",
							"description",
							"stock_cost_total",
							"stock_cost_average",
							"quantity_balance",
							"quantity_on_order",
							"quantity_rec_level",
							"last_moverment",
							"standard_price",
							"special_price",
							"units_of_for_special",
							"cost_price_a",
							"cost_price_b",
							"cost_price_c",
							"fk_vat_code",
							"fk_family_group",
							"fk_discount_group",
							"fk_supplier_a",
							"fk_supplier_b",
							"fk_supplier_c"
								);
								
			$this->load->model('stock_m');
			$result = $this->stock_m->save_item( $vars_array );
			if( $result != false )
			{
				$this->load->view('header_nav');
				switch($result['type'])
				{
					case "inserted":
						$result['type'] = "inserted";
						$this->output->append_output("
											<div class=\"row\">
												<div class=\"col-md-12\">&nbsp;</div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-12\"><div class=\"alert alert-success\" role=\"alert\">The item was successfully ".$result['type'].".</div></div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-6\"></div>
												<div class=\"col-md-2\"><a href=\"".base_url('index.php/sales_stock/new_existing')."\"><button type=\"button\" class=\"btn btn-default\">Add another item</button></a></div>
												<div class=\"col-md-2\"><a href=\"javascript:history.back()\"><button type=\"button\" class=\"btn btn-default\">Go back to the form</button></a></div>
												<div class=\"col-md-1\"><a href=\"".base_url('index.php')."\"><button type=\"button\" class=\"btn btn-default\">Back to main</button></a></div>
											</div>
											");
						$this->load->view('footer_common');
						$this->load->view('footer_copyright');
						$this->load->view('footer');
						break;
					case "updated":
						$result['type'] = "updated";
						redirect(base_url('index.php/sales_stock/new_existing/'.$pk_id),'refresh');
						break;
				}
				
			}else
			{
				$this->load->view('header_nav');
				$this->output->append_output("
											<div class=\"row\">
												<div class=\"col-md-12\">&nbsp;</div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-12\"><div class=\"alert alert-danger\" role=\"alert\">There was a problem saving the supplier; please, try again.</div></div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-10\"></div>
												<div class=\"col-md-2\"><a href=\"javascript:history.back()\"><button type=\"button\" class=\"btn btn-default\">Go back to the form</button></a></div>
											</div>");
				$this->load->view('footer_common');
				$this->load->view('footer_copyright');
				$this->load->view('footer');
			}
		}
	}
	
	public function save_item_prices()
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