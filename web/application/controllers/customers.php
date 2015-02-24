<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends CI_Controller 
{
	
	public function name_valid( $valor )
	{
		if( preg_match('/^[A-Za-zñÑ\-\_\.\,\s]{2,200}$/', $valor) == 1 )
		{
			return true;
		}else
		{
			$this->form_validation->set_message('name_valid', $this->lang->line('error_bad_name_account'));
			return false;
		}
	}
	
	public function shorttext_valid( $valor )
	{
		if( (strlen($valor) > 0 && preg_match('/^[A-Za-zñÑ0-9\-\_\.\,\s]{2,200}$/', $valor) == 1 ) || strlen($valor) == 0)
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
	
	public function list_names_address()
	{	
		$this->load->model('customers_m');
		$data['customers'] = $this->customers_m->get_names_address();
		$this->load->view('header_nav');		
		$this->load->view('list_customers', $data);
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
		$this->load->library('form_validation');
		$this->load->model('customers_m');
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
	
	public function new_existing()
	{
		$this->load->view('header_nav');
		$this->load->view('new_customer');
		$this->load->view('footer_common');
		$this->output->append_output("<script src=".base_url('assets/js/new_customer.js')."></script>");
		$this->load->view('new_customer_footer');
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function save_customer()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');			
		$this->lang->load('errors', 'english');
		$this->lang->load('messages', 'english');
		$this->lang->load('commands', 'english');
		$config = array(
               array(
                     'field'   => 'account_reference',
                     'label'   => 'Account Reference',
                     'rules'   => 'trim|xss_clean|max_length[10]|alpha_dash'
                  ),
			   array(
					'field'		=> 'email',
					'label'		=> 'E-mail',
					'rules'		=> 'trim|xss_clean|valid_email|min_length[5]|max_length[200]'
			   ),
			   array(
					'field'		=> 'credit_limit',
					'label'		=> 'Credit Limit',
					'rules'		=> 'trim|xss_clean|min_length[1]|max_length[10|integer'
			   ),
			   array(
					'field'		=> 'parent_account_id',
					'label'		=> 'Parent Account',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'type',
					'label'		=> 'Type Account',
					'rules'		=> 'trim|xss_clean|integer|required'
			   ),
			   array(
					'field'		=> 'order_number',
					'label'		=> 'Order No.',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'vat',
					'label'		=> 'VAT',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'days_week',
					'label'		=> 'Days/Week',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'invoicing',
					'label'		=> 'Invoicing',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'holiday_credit',
					'label'		=> 'Holiday Credit',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'prices_type',
					'label'		=> 'Prices Type',
					'rules'		=> 'trim|xss_clean|integer'
			   )			   
            );

		$this->form_validation->set_rules($config);
		$this->form_validation->set_rules('name', 'Name', 'callback_name_valid');
		$this->form_validation->set_rules('address', 'Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('contact_name', 'Contact Name', 'callback_shorttext_valid');
		//$this->form_validation->set_rules('representative', 'Representative', 'callback_shorttext_valid');
		$this->form_validation->set_rules('statement_address', 'Statement Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('account_department', 'Account Department', 'callback_shorttext_valid');
		$this->form_validation->set_rules('account_dept_number', 'Account Dept Number', 'callback_telephone_valid');
		$this->form_validation->set_rules('telephone', 'Telephone', 'callback_telephone_valid');
		$this->form_validation->set_rules('fax', 		'Fax', 		'callback_telephone_valid');
		
		$this->form_validation->set_message('required', 'The %s field is mandatory.');
		$this->form_validation->set_message('alpha', 'The %s field can only contain letters.');
		
		if ( !$this->form_validation->run() )
		{
			echo validation_errors();
		}
		else
		{
			$account_reference 	= $this->input->post('account_reference');
			$name 				= trim($this->input->post('name', true));
			$address 			= trim($this->input->post('address', true));
			$telephone 			= trim($this->input->post('telephone', true));
			$fax 				= trim($this->input->post('fax', true));
			$email 				= $this->input->post('email');
			$contact_name 		= trim($this->input->post('contact_name', true));
			$representative 	= trim($this->input->post('representative', true));
			$type 				= $this->input->post('type');
			$credit_limit 		= $this->input->post('credit_limit');
			$order_number 		= $this->input->post('order_number');
			$vat 				= $this->input->post('vat');
			$days_week 			= $this->input->post('days_week');
			$invoicing 			= $this->input->post('invoicing');
			$holiday_credit		= $this->input->post('holiday_credit');
			$prices_type 		= $this->input->post('prices_type');
			$statement_address	= trim($this->input->post('statement_address', true));
			$parent_account_id	= $this->input->post('parent_account_id');
			$address1 			= trim($this->input->post('address1',true));
			$address2			= trim($this->input->post('address2',true));
			$address3 			= trim($this->input->post('address3',true));
			$address4 			= trim($this->input->post('address4',true));
			$address5 			= trim($this->input->post('address5',true));			
			$post_code 			= trim($this->input->post('post_code',true));
			$mobile 			= trim($this->input->post('mobile',true));
			$disc_perc 			= trim($this->input->post('discount_perc',true));
			$account_department	= trim($this->input->post('account_department',true));
			$account_dept_number= trim($this->input->post('account_dept_number',true));
			$vars_array = compact(
								"account_reference",
								"name",
								"address",
								"telephone",
								"fax",
								"email",
								"contact_name",
								"representative",
								"type",
								"credit_limit",
								"order_number",
								"vat",
								"days_week",
								"invoicing",
								"holiday_credit",
								"prices_type",
								"statement_address",
								"parent_account_id",
								"address1",
								"address2",
								"address3",
								"address4",
								"address5",
								"post_code",
								"mobile",
								"disc_perc",
								"account_department",
								"account_dept_number"
								);
			$this->load->model('customers_m');
			$result = $this->customers_m->save_customer( $vars_array );
			if( is_numeric($result) )
			{
				$data['message'] = $this->lang->line('mess_customer_saved_successfully');
				$data['customer_id'] = $result;
				$this->load->view('header_nav');
				$this->load->view('new_customer_successfully_saved', $data);
				$this->load->view('footer_common');
				$this->load->view('footer_copyright');
				$this->load->view('footer');
			}else
			{
			
				$data['message'] = $this->lang->line('error_saving_customer');
				$this->load->view('header_nav');		
				$this->load->view('new_customer_error_saving', $data);
				$this->load->view('footer_common');
				$this->load->view('footer_copyright');
				$this->load->view('footer');
			}
		}
	}	
}