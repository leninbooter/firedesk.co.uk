<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_stock extends CI_Controller
{
	
	public function new_existing()
	{
		$pk_id = trim($this->uri->segment(3));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$this->load->model('stock_m');			
			$data['item'] = $this->stock_m->get_item_details($pk_id);
			$data['editing'] = true;
		}
		
		$this->load->model('family_groups_m');
		$this->load->model('discount_groups_m');
		
		$data['suppliers'] = array();
		$data['family_groups'] = $this->family_groups_m->get_groups();
		$data['family_discounts'] = $this->discount_groups_m->get_groups();
		$this->load->view('header_nav');
		if( isset($data['item']) ) $this->load->view('new_existing_stock_item', $data);
			else	$this->load->view('new_existing_stock_item', $data);
		
		$this->load->view('footer_common');
		//$this->output->append_output("<script src=\"".base_url('assets/js/suppliers.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	
	public function name_valid( $valor )
	{
		if( preg_match('/^[A-Za-zñÑ0-9\-\_\.\,\s]{2,200}$/', $valor) == 1 )
		{
			return true;
		}else
		{
			$this->form_validation->set_message('name_valid', 'The %s field can only contain letters, numbers, dashes, underscores, dots and commas.');
			return false;
		}
	}

	public function shorttext_valid( $valor )
	{
		if( (strlen($valor) > 0 && preg_match('/^[A-Za-zñÑ0-9\-\_\.\,\#\s]{2,200}$/', $valor) == 1 ) || strlen($valor) == 0)
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

	public function list_suppliers_addresses()
	{
		$this->load->model('suppliers_m');
		$data['suppliers'] = $this->suppliers_m->get_suppliers_addresses();
		$this->load->view('header_nav');
		$this->load->view('list_suppliers_addresses', $data);
		$this->load->view('footer_common');
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}

	public function list_selectable_customers()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('customers_m');
		$name = trim($this->input->get('parent_account' ,true));
		$name = str_replace("_", "%", $name);
		if( ($results = $this->customers_m->get_names_like($name)) != false )
		{
			$data['customers'] = $results;
			$this->load->view('customers_list_selectable_dropdown', $data);
		}else
		{
			echo "none";
		}
	}

		public function get_customers_addresses_json()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->model('suppliers');
		$pk_id = trim($this->input->get('pk_id' ,true));
		$pk_id = str_replace("_", "%", $pk_id);
		if( ($results = $this->customers_m->get_customers_addresses($pk_id)) != false )
		{
			$data['addresses'] = $results;
			$this->load->view('customers_address_json', $data);
		}else
		{
			echo "none";
		}
	}

	public function save_supplier()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->lang->load('errors', 'english');
		$this->lang->load('messages', 'english');
		$this->lang->load('commands', 'english');
		$config = array(
               array(
					'field'		=> 'email',
					'label'		=> 'E-mail',
					'rules'		=> 'trim|required|xss_clean|valid_email|min_length[5]|max_length[200]'
			   ),
			   array(
					'field'		=> 'zipcode',
					'label'		=> 'ZIP CODE',
					'rules'		=> 'trim|xss_clean|min_length[1]|max_length[5|integer'
			   ),
			   array(
					'field'		=> 'bank_name',
					'label'		=> 'Bank Name',
					'rules'		=> 'trim|xss_clean|alpha_dash'
			   ),
			   array(
					'field'		=> 'account_number',
					'label'		=> 'Account Number',
					'rules'		=> 'trim|xss_clean|alpha_dash'
			   ),
			   array(
					'field'		=> 'swift_code',
					'label'		=> 'Swift Code',
					'rules'		=> 'trim|xss_clean|alpha_numeric'
			   ),
			   array(
					'field'		=> 'account_type',
					'label'		=> 'Account Type',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'account_credit',
					'label'		=> 'Account Credit',
					'rules'		=> 'trim|xss_clean|integer|min_length[0]|max_length[1]'
			   ),
			   array(
					'field'		=> 'account_payment_credit',
					'label'		=> 'Account Credit Payment',
					'rules'		=> 'trim|xss_clean|integer|min_length[0]|max_length[1]'
			   ),
			   array(
					'field'		=> 'from',
					'label'		=> 'Account Credit Payment Form',
					'rules'		=> 'trim|xss_clean|alpha'
			   ),
			   array(
					'field'		=> 'settlement',
					'label'		=> 'Account Credit Setlement',
					'rules'		=> 'trim|xss_clean|alpha'
			   )
            );

		$this->form_validation->set_rules($config);
		$this->form_validation->set_rules('name', 'Name', 'callback_name_valid');
		$this->form_validation->set_rules('telephone1', 'Telephone', 'callback_telephone_valid');
		$this->form_validation->set_rules('telephone2', 'Telephone', 'callback_telephone_valid');
		$this->form_validation->set_rules('fax', 		'Fax', 		'callback_telephone_valid');
		$this->form_validation->set_rules('address1', 'Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('address2', 'Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('address3', 'Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('address4', 'Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('address5', 'Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('address6', 'Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('contact', 'Contact Name', 'callback_shorttext_valid');
		$this->form_validation->set_rules('bank_acc_addr_1', 'Bank Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('bank_acc_addr_2', 'Bank Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('bank_acc_addr_3', 'Bank Address', 'callback_shorttext_valid');

		$this->form_validation->set_message('required', 'The %s field is mandatory.');
		$this->form_validation->set_message('alpha', 'The %s field can only contain letters.');

		if ( !$this->form_validation->run() )
		{
			echo validation_errors();
		}
		else
		{
			$name							= trim($this->input->post('name'), true);
			$email							= trim($this->input->post('email'));
			$telephone1						= trim($this->input->post('telephone1', true));
			$telephone2						= trim($this->input->post('telephone2', true));
			$fax							= trim($this->input->post('fax', true));
			$address1						= trim($this->input->post('address1', true));
			$address2						= trim($this->input->post('address2', true));
			$address3						= trim($this->input->post('address3', true));
			$address4						= trim($this->input->post('address4', true));
			$address5						= trim($this->input->post('address5', true));
			$address6						= trim($this->input->post('address6', true));
			$zipcode						= trim($this->input->post('zipcode'));
			$contact						= trim($this->input->post('contact', true));
			$banck_name						= trim($this->input->post('banck_name'));
			$account_number					= trim($this->input->post('account_number'));
			$swift_code						= trim($this->input->post('swift_code'));
			$account_type					= trim($this->input->post('account_type'));
			$bank_acc_addr_1				= trim($this->input->post('bank_acc_addr_1', true));
			$bank_acc_addr_2				= trim($this->input->post('bank_acc_addr_2', true));
			$bank_acc_addr_3				= trim($this->input->post('bank_acc_addr_3', true));
			$bank_telephone					= trim($this->input->post('bank_telephone', true));
			$account_credit					= trim($this->input->post('account_credit'));
			$account_payment_credit			= trim($this->input->post('account_payment_credit'));
			$account_payment_credit_from	= trim($this->input->post('account_payment_credit_from'));
			$account_credit_setlement		= trim($this->input->post('account_credit_setlement'));

			$vars_array = compact(
							"name",
							"email",
							"telephone1",
							"telephone2",
							"fax",
							"address1",
							"address2",
							"address3",
							"address4",
							"address5",
							"address6",
							"zipcode",
							"contact",
							"banck_name",
							"account_number",
							"swift_code",
							"account_type",
							"bank_acc_addr_1",
							"bank_acc_addr_2",
							"bank_acc_addr_3",
							"bank_telephone",
							"account_credit",
							"account_payment_credit",
							"account_payment_credit_from",
							"account_credit_setlement"
								);
			$this->load->model('suppliers_m');
			$result = $this->suppliers_m->save_supplier( $vars_array );
			if( $result != false )
			{
				$this->load->view('header_nav');
				switch($result['type'])
				{
					case "insert":
						$result['type'] = "inserted";
						break;
					case "update":
						$result['type'] = "updated";
						break;
				}
				$this->output->append_output("
											<div class=\"row\">
												<div class=\"col-md-12\">&nbsp;</div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-12\"><div class=\"alert alert-success\" role=\"alert\">".$name." was successfully ".$result['type'].".</div></div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-6\"></div>
												<div class=\"col-md-2\"><a href=\"".base_url('index.php/suppliers/new_existing')."\"><button type=\"button\" class=\"btn btn-default\">Add another supplier</button></a></div>
												<div class=\"col-md-2\"><a href=\"javascript:history.back()\"><button type=\"button\" class=\"btn btn-default\">Go back to the form</button></a></div>
												<div class=\"col-md-1\"><a href=\"".base_url('index.php')."\"><button type=\"button\" class=\"btn btn-default\">Back to main</button></a></div>
											</div>
											");
				$this->load->view('footer_common');
				$this->load->view('footer_copyright');
				$this->load->view('footer');
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
}