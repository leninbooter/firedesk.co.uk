<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts extends CI_Controller 
{
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
	
	public function new_contract()
	{
		$this->load->view('header_nav');		
		$this->load->view('new_contract');
		$this->load->view('footer_common');
		$this->load->view('new_contract_footer');
		$this->load->view('footer_copyright');
		$this->load->view('footer');		
	}
	
	public function save_contract()
	{		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');			
		$this->lang->load('errors', 'english');
		$this->lang->load('messages', 'english');
		$this->lang->load('commands', 'english');
		$config = array(
               array(
                     'field'   => 'account_reference_id',
                     'label'   => 'Customer id',
                     'rules'   => 'trim|xss_clean|integer|required'
                  ),
			   array(
					'field'		=> 'contract_type',
					'label'		=> 'Contract type',
					'rules'		=> 'trim|required|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'time',
					'label'		=> 'Time',
					'rules'		=> 'trim|xss_clean|max_length[5]'
			   ),
			   array(
					'field'		=> 'identification_type',
					'label'		=> 'Identification Type',
					'rules'		=> 'trim|xss_clean|integer|required'
			   ),
			   array(
					'field'		=> 'identification',
					'label'		=> 'Identification number',
					'rules'		=> 'trim|xss_clean|required|alpha_dash'
			   ),
			   array(
					'field'		=> 'date',
					'label'		=> 'Date',
					'rules'		=> 'trim|xss_clean|max_length[10]'
			   ),
			   array(
					'field'		=> 'payment_method',
					'label'		=> 'Payment Method',
					'rules'		=> 'trim|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'payment_ammount',
					'label'		=> 'Payment Ammount',
					'rules'		=> 'trim|xss_clean|decimal'
			   ),
			   array(
					'field'		=> 'due_back',
					'label'		=> 'Due back date',
					'rules'		=> 'trim|xss_clean|max_length[10]'
			   ),
			   array(
					'field'		=> 'delivery_charge',
					'label'		=> 'Delivery charge',
					'rules'		=> 'trim|xss_clean|decimal'
			   ),
			   array(
					'field'		=> 'notes',
					'label'		=> 'Notes',
					'rules'		=> 'trim|xss_clean|alpha_dash'
			   )			   
            );

		$this->form_validation->set_rules($config);
		
		$this->form_validation->set_message('required', 'The %s field is mandatory.');
		$this->form_validation->set_message('alpha', 'The %s field can only contain letters.');
		$this->form_validation->set_rules('saved_addresses', 'Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('new_address', 'Address', 'callback_shorttext_valid');
		
		if ( !$this->form_validation->run() )
		{
			echo validation_errors();
		}
		else
		{
		$account_reference_id	= $this->input->post('account_reference_id');					
		$contract_type			= $this->input->post('contract_type');					
		$time					= $this->input->post('time');					
		$identification_type	= $this->input->post('identification_type');					
		$identification			= $this->input->post('identification');					
		$date					= date('Y-m-d', strtotime($this->input->post('date')));					
		$payment_method			= $this->input->post('payment_method');					
		$payment_ammount		= $this->input->post('payment_ammount');
		$payment_notes			= $this->input->post('payment_notes');					
		$due_back				= date('Y-m-d', strtotime($this->input->post('due_back')));					
		$saved_addresses		= $this->input->post('saved_addresses');					
		$new_address			= $this->input->post('new_address');					
		$delivery_charge		= $this->input->post('delivery_charge');					
		$notes					= $this->input->post('notes');					
			
		$vars_array = compact(
								"account_reference_id",
								"contract_type",
								"time",
								"identification_type",
								"identification",
								"date",
								"payment_method",
								"payment_ammount",
								"payment_notes",
								"due_back",
								"saved_addresses",
								"new_address",
								"delivery_charge",
								"notes"
							);
			$this->load->model('contracts_m');
			$result = $this->contracts_m->save_contract( $vars_array );
			if( is_numeric($result) )
			{
				$data['contract_id'] = $result;
				$data['customer_name'] = $this->input->post('account_reference');
				$data['contract_type'] = $contract_type == 0 ? "Cash" : "Credit";
				$data['address'] = $saved_addresses;
				$data['delivery_charge'] = $delivery_charge;
				$data['contract_status'] = 1;
				$this->load->view('header_nav');		
				$this->load->view('form_add_items_to_contract', $data);
				$this->load->view('footer_common');
				$this->load->view('footer_copyright');
				$this->load->view('footer');
			}else
			{
			
				echo "failed";
			}
		}
	}
	
	public function form_add_items()
	{
		$this->load->view('header_nav');		
		$this->load->view('form_add_items_to_contract');
		$this->load->view('footer_common');
		$this->load->view('footer_copyright');
		$this->load->view('footer');	
	}
	
	public function list_selectable_customersName_refID()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('contracts_m');
		$text_search = trim($this->input->get('account_reference' ,true));
		$text_search = str_replace("_", "%", $text_search);
		if( ($results = $this->contracts_m->get_accounts_like($text_search)) != false )
		{
			$data['customers'] = $results;
			$this->load->view('customers_list_selectable_dropdown_accRef_name', $data);
		}else
		{
			echo "none";
		}
	}

}