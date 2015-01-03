<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts extends CI_Controller 
{
	public function new_contract()
	{
		$this->load->view('header_nav');		
		$this->load->view('new_contract');
		$this->load->view('footer_common');
		$this->load->view('new_contract_footer');
		$this->load->view('footer_copyright');
		$this->load->view('footer');		
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