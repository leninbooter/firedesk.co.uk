<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchases_orders extends CI_Controller
{
	
	public function edit()
	{
		$this->load->model('purchases_orders_m');
		
		$pk_id = trim($this->uri->segment(3));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$data['order_id'] = $pk_id;
			$data['order_details'] = $this->purchases_orders_m->get_order_details( $pk_id );			
			if($data['order_details'] != false) {
				$data['supplier_id'] = $data['order_details']->fk_supplier_id;			
				
				$this->load->view('header_nav');		
				$this->load->view('purchase_order_edit', $data);		
				$this->load->view('footer_common');
				$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.3.custom/jquery-ui.js')."\"></script>");
				$this->output->append_output("<script src=\"".base_url('assets/js/purchase_orders_edit.js')."\"></script>");		
				$this->load->view('footer_copyright');
				$this->load->view('footer');
			}
		}			
	}
	
	public function name_valid( $valor )
	{
		if( preg_match('/^[A-Za-zñÑ0-9\-\_\.\,\s]{0,200}$/', $valor) == 1 )
		{
			return true;
		}else
		{
			$this->form_validation->set_message('name_valid', 'The %s field can only contain letters, numbers, dashes, underscores, dots and commas.');
			return false;
		}
	}
	
	public function new_order()
	{
		$pk_id = trim($this->uri->segment(3));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$this->load->model('stock_m');			
			$data['item'] = array();
			$data['editing'] = true;
			$data['items'] = array();
		}		

		// Views
		$this->load->view('header_nav');
		$message = urldecode(trim($this->uri->segment(4)));
		if( $message != false )
		{
			$this->output->append_output("<div class=\"alert alert-danger\" role=\"alert\">".$danger."</div>");
		}
		
		$this->load->view('purchase_order_new');		
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.3.custom/jquery-ui.js')."\"></script>");
		$this->output->append_output("<script src=\"".base_url('assets/js/purchases.js')."\"></script>");		
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}	
	
	public function generate_order()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$config = array(
               array(
					'field'		=> 'supplier_pk_id',
					'label'		=> 'Supplier ID',
					'rules'		=> 'trim|required|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'contact_email',
					'label'		=> 'Contact e-mail',
					'rules'		=> 'trim|xss_clean|valid_email|min_length[5]|max_length[200]'
			   ));

		$this->form_validation->set_rules($config);
		$this->form_validation->set_rules('delivery_address', 'Delivery Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('contact_name', 'Contact Name', 'callback_name_valid');
		$this->form_validation->set_rules('contact_telephone', 'Contact Telephone', 'callback_telephone_valid');

		$this->form_validation->set_message('required', 'The %s field is mandatory.');

		if ( !$this->form_validation->run() )
		{
			echo validation_errors();
		}
		else
		{ 
			$supplier_id		= $this->input->post('supplier_pk_id');
			$delivery_address	= trim($this->input->post('delivery_address', true));
			$contact_name		= trim($this->input->post('contact_name', true));
			$contact_phone		= trim($this->input->post('contact_telephone', true));
			$contact_email		= $this->input->post('contact_email');			
            $creation_date		= date('Y-m-d H:i:s');
			
			
			$vars_array = compact(
							"supplier_id",
							"delivery_address",
							"contact_name",
							"contact_phone",
							"contact_email",
							"creation_date"
								);
								
			$this->load->model('purchases_orders_m');
			$result = $this->purchases_orders_m->generate_order( $vars_array );
			if( $result != false )
			{
				redirect('','refresh');
				echo $result;
				
				/*$this->load->view('header_nav');
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
				}*/
				
			}else
			{
				/*$this->load->view('header_nav');
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
				$this->load->view('footer');*/
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

	public function telephone_valid( $valor )
	{
		if( (strlen($valor) > 0 && preg_match( '/^(?:\d|\+){1}[0-9]{1,19}$/', $valor )  == 1) || $valor == "" )
		{
			return true;
		}else
		{
			$this->form_validation->set_message('telephone_valid', $this->lang->line('error_bad_phone_account'));
			return false;
		}
	}	
}