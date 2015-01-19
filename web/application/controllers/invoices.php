<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices extends CI_Controller 
{
	public function generate()
	{
		$this->load->helper(array('url'));
		$this->load->model('invoices_m');
		$this->load->model('contracts_m');
		
		$type = trim($this->input->get('type', true));
		$contract_id = trim($this->input->get('contract_id', true));
		
		$data['contract_id'] = $contract_id;
		$data['invoice_id'] = $this->invoices_m->generate_invoice($contract_id);
		$data['contract_details'] = $this->contracts_m->get_contract_details( $contract_id );
		$data['invoice_items'] = $this->contracts_m->get_all_items_not_invoiced($contract_id);
		$data['customer_name'] = $data['contract_details']->name;
		$data['contract_type'] = $data['contract_details']->type == 0 ? "Cash" : "Credit";
		$data['address'] = $data['contract_details']->address;
		$data['delivery_charge'] = $data['contract_details']->delivery_charge;
		$data['contract_status'] = $data['contract_details']->fk_contract_status_id;
		
		$this->load->view('header_nav');		
		$this->load->view('invoice_form', $data);
		$this->load->view('footer_common');
		$this->load->view('footer_copyright');
		$this->load->view('footer');	
	}
	
}