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
		switch($type)
		{
			case 1:
				$data['invoice_items'] = $this->contracts_m->get_all_items_not_invoiced($contract_id);
			break;
			
			case 2:
				$data['invoice_items'] = $this->contracts_m->get_all_items_not_invoiced_not_supplied($contract_id);
			break;
		}
		
		
		if(empty($data['invoice_items']))
		{
			$this->load->view('header_nav');		
			$this->output->append_output("
				<div class=\"row\">
					<div class=\"col-md-12\">&nbsp;</div>
				</div>
				<div class=\"row\">
					<div class=\"col-md-12\"><div class=\"alert alert-warning\" role=\"alert\">The selected contract has no more items uninvoiced.</div></div>
				</div>");
			$this->load->view('footer_common');
			$this->load->view('footer_invoices');
			$this->load->view('footer_copyright');
			$this->load->view('footer');
		}else
		{		
			$data['customer_name'] = $data['contract_details']->name;
			$data['contract_type'] = $data['contract_details']->type == 0 ? "Cash" : "Credit";
			$data['address'] = $data['contract_details']->address;
			$data['delivery_charge'] = $data['contract_details']->delivery_charge;
			$data['contract_status'] = $data['contract_details']->fk_contract_status_id;
			
			$this->load->view('header_nav');		
			$this->load->view('invoice_form', $data);
			$this->load->view('footer_common');
			$this->load->view('footer_invoices');
			$this->load->view('footer_copyright');
			$this->load->view('footer');
		}
	}

	function invoice_pdf_to_email( $invoice_id , $email_to )
	{
		$this->load->helper(array('dompdf', 'file', 'url'));
		$this->load->model('contracts_m');
		$this->load->model('invoices_m');				
		
		$data['invoice_id'] = $invoice_id;
		$data['invoice_details'] = $this->invoices_m->get_invoice_details($invoice_id);
		$data['invoice_items'] 		= $this->invoices_m->get_all_invoice_items($invoice_id);		
		$data['contract_details'] 	= $this->contracts_m->get_contract_details( $data['invoice_details']->fk_contract_id );
		$data['contract_id'] = $data['invoice_details']->fk_contract_id;
		$data['customer_name'] = $data['contract_details']->name;
		$data['contract_type'] = $data['contract_details']->type == 0 ? "Cash" : "Credit";
		$data['address'] = $data['contract_details']->address;
		$data['delivery_charge'] = $data['contract_details']->delivery_charge;		
		//$data['contract_status'] = $data['contract_details']->fk_contract_status_id;		
		$html = $this->load->view('report_invoice_pdf', $data, true);
		//$this->load->view('report_invoice_pdf', $data);
		$file_name = "invoices/".$data['customer_name']."_".$data['contract_id']."_".$data['invoice_id'].".pdf";
		$data = pdf_create($html, '', false);
		write_file($file_name, $data);
		return $this->invoices_m->email_invoice($email_to, $file_name);
	}
	
	public function process()
	{
		$failed = false;
		
		$this->load->helper(array('url'));
		$this->load->model('invoices_m');
		$this->load->model('contracts_m');
		
		$contract_id = trim($this->input->post('contract_id',true));
		$invoice_id = trim($this->input->post('invoice_id',true));
		$subtotal = trim($this->input->post('subtotal',true));
		$vat = trim($this->input->post('vat',true));
		$total = trim($this->input->post('total',true));
		
		for($i = 0; $i < count($_POST['qty']); $i++)
		{
			if(isset($_POST["regularity"][$i]))
			{
				switch($_POST["regularity"][$i])
				{
					case "year":
						$reg_post = 1;
					break;
					case "month":
						$reg_post = 2;
					break;
					case "week":
						$reg_post = 3;
					break;
					case "day":
						$reg_post = 4;
					break;
				}
			}
			$pk_id = $_POST["item_id"][$i];
			$item_no = $_POST["item_no"][$i];
			$qty = $_POST["qty"][$i];
			$description = $_POST["description"][$i];			
			$entries_no = $_POST["entries_no"][$i];
			$rate_per = $_POST["rate_per"][$i];
			$item_type = $_POST["item_type"][$i];
			if( $item_type == "2" )
				$regularity = $reg_post;
			else
				$regularity = "";
			$discount_perc = $_POST["disc"][$i];
			$value = $_POST["value"][$i];			
			
			$vars_array = compact(
								"invoice_id",
								"pk_id",
								"item_no",
								"qty",
								"description",
								"entries_no",
								"rate_per",
								"regularity",
								"discount_perc",
								"value",
								"item_type"
							);			
			$result = $this->invoices_m->save_invoice_item( $vars_array );
			if( !$result )
			{
				$failed = true;
				break;
			}
		}
		if(!$failed)
		{
			$subtotal = trim($this->input->post('subtotal',true));
			$vat = trim($this->input->post('vat',true));
			$total = trim($this->input->post('total',true));
			
			$type = trim($this->input->post('contract_type', true));
			$cash = number_format(floatval(trim($this->input->post('cash', true))),2);
			$cheque = number_format(floatval(trim($this->input->post('cheque', true))),2);
			$card = number_format(floatval(trim($this->input->post('card', true))),2);
			
			$total_paid = $cash + $cheque + $card;
			
			switch($type)
			{
				case 'Cash':
					$unpaid_cash_invoice = $total_paid == 0.00 ? 1 : 0;
					
					if($total_paid > 0)
					{
						$unpaid_ammount = $total - $total_paid;
						
					}else
						$unpaid_ammount = $total;
						
					
					$vars_array = compact( "subtotal", "vat", "total", "unpaid_ammount", "unpaid_cash_invoice", "invoice_id");
					if( $this->invoices_m->save_invoice_data( $vars_array ) )
					{				
						$ammounts = compact("cash", "cheque", "card");
						for($i = 1; $i<4; $i++)
						{
							$payment_type = $i;
							$ammount = $ammounts[$i];
							$date = trim($this->input->post('payment_date', true));
							$reference = trim($this->input->post('payment_reference', true));
							$vars_array = compact("invoice_id", "payment_type", "ammount", "date", "reference");
							
							if( $this->invoices_m->save_payment( $vars_array ) )
							{	
								$generation = true;
								
							}else
							{
								$generation = false;
							}
						}					
							
					}				
				break;
				
				case 'Credit':
					$unpaid_cash_invoice = 0;
					$unpaid_ammount = $total;					
					
					$vars_array = compact( "subtotal", "vat", "total", "unpaid_ammount", "unpaid_cash_invoice", "invoice_id");
					if( $this->invoices_m->save_invoice_data( $vars_array ) )
					{
						$generation = true;
					}else
					{
						$generation = false;
					}
				break;
			}
			if($generation)
			{
				$this->load->view('header_nav');		
				$this->output->append_output("<p class=\"text-center\"><div class=\"alert alert-success\" role=\"alert\">The invoice was succesfully recorded.</div></p>");
				$email_to = $this->contracts_m->get_contract_details( $contract_id )->email;
				if( $this->input->post('email_invoice', true) == 1 )
				{				
					if( $this->invoice_pdf_to_email($invoice_id, $email_to) )
					{
						$this->output->append_output("<p class=\"text-center\"><div class=\"alert alert-success\" role=\"alert\">The invoice was also succesfully sent to ".$email_to.".</div></p>");
					}else
					{
						$this->output->append_output("<p class=\"text-center\"><div class=\"alert alert-danger\" role=\"alert\">The invoice could not be sent to ".$email_to."; please, try again, later.</div></p>");
					}
				}
				$this->output->append_output("<div class=\"row\">
					<div class=\"col-md-12\">
						<a href=\"".base_url('index.php/contracts/edit?id='.$contract_id)."\"><button type=\"button\" class=\"btn btn-default\">Go back to the contract</button></a>									
					</div>
				</div>");
				$this->load->view('footer_common');
				$this->load->view('footer_copyright');
				$this->load->view('footer');
			}else
			{
				$this->load->view('header_nav');		
				$this->output->append_output("<p class=\"text-center\"><div class=\"alert alert-danger\" role=\"alert\">There was a problem processing the invoice; please, try again.</div></p>
				<div class=\"row\">
					<div class=\"col-md-12\">
						<a href=\"".base_url('index.php/contracts/edit?id='.$contract_id)."\"><button type=\"button\" class=\"btn btn-default\">Go back to the contract</button></a>									
					</div>
				</div>");
				$this->load->view('footer_common');
				$this->load->view('footer_copyright');
				$this->load->view('footer');
			}
			
		}	
		
	}
	
	public function invoice_pdf()
	{
		$this->load->helper(array('dompdf', 'file', 'url'));
		$this->load->model('contracts_m');
		$this->load->model('invoices_m');
		
		$invoice_id = $this->input->get('id', true);
		$email_to = $this->input->get('email', true);
		
		$data['invoice_id'] = $invoice_id;
		$data['invoice_details'] = $this->invoices_m->get_invoice_details($invoice_id);
		$data['invoice_items'] 		= $this->invoices_m->get_all_invoice_items($invoice_id);		
		$data['contract_details'] 	= $this->contracts_m->get_contract_details( $data['invoice_details']->fk_contract_id );
		$data['contract_id'] = $data['invoice_details']->fk_contract_id;
		$data['customer_name'] = $data['contract_details']->name;
		$data['contract_type'] = $data['contract_details']->type == 0 ? "Cash" : "Credit";
		$data['address'] = $data['contract_details']->address;
		$data['delivery_charge'] = $data['contract_details']->delivery_charge;		
		//$data['contract_status'] = $data['contract_details']->fk_contract_status_id;		
		$html = $this->load->view('report_invoice_pdf', $data, true);
		//$this->load->view('report_invoice_pdf', $data);
		if(empty($email_to))
		{
			pdf_create($html, 'Invoice '.$invoice_id);
		}else
		{		
			$file_name = "invoices/".$data['customer_name']."_".$data['contract_id']."_".$data['invoice_id'].".pdf";
			$data = pdf_create($html, '', false);
			write_file($file_name, $data);
			$this->invoices_m->email_invoice($email_to, $file_name);
		}
	}	
	
	public function past()
	{
		$this->load->model('invoices_m');
		
		$contract_id = trim($this->security->xss_clean($this->uri->segment(3)));
		if(is_numeric($contract_id))
		{
			$data['invoices'] = $this->invoices_m->get_invoices_of($contract_id);			
			$this->load->view('past_invoices', $data);
		}
	}
	
	public function preview()
	{
		$this->load->helper(array('url'));
		$this->load->model('invoices_m');
		$this->load->model('contracts_m');
		
		$contract_id = trim($this->security->xss_clean($this->uri->segment(3)));
		$type = trim($this->security->xss_clean($this->uri->segment(4)));
		$date = trim($this->security->xss_clean($this->uri->segment(5)));
		
		$data['contract_id'] = $contract_id;
		$data['contract_details'] = $this->contracts_m->get_contract_details( $contract_id );
		switch($type)
		{
			case 1:
				$data['invoice_items'] = $this->contracts_m->get_all_items_not_invoiced($contract_id);
			break;
			
			case 2:
				$data['invoice_items'] = $this->contracts_m->get_all_items_not_invoiced_not_supplied($contract_id);
			break;
		}
		
		
		if(empty($data['invoice_items']))
		{
			$this->load->view('header_nav');		
			$this->output->append_output("
				<div class=\"row\">
					<div class=\"col-md-12\">&nbsp;</div>
				</div>
				<div class=\"row\">
					<div class=\"col-md-12\"><div class=\"alert alert-warning\" role=\"alert\">The selected contract has no more items uninvoiced.</div></div>
				</div>");
			$this->load->view('footer_common');
			$this->load->view('footer_invoices');
			$this->load->view('footer_copyright');
			$this->load->view('footer');
		}else
		{		
			$data['customer_name'] = $data['contract_details']->name;
			$data['contract_type'] = $data['contract_details']->type == 0 ? "Cash" : "Credit";
			$data['address'] = $data['contract_details']->address;
			$data['delivery_charge'] = $data['contract_details']->delivery_charge;
			$data['contract_status'] = $data['contract_details']->fk_contract_status_id;
			
			$this->load->view('header_nav');		
			$this->load->view('invoice_form_preview', $data);
			$this->load->view('footer_common');
			$this->load->view('footer_invoices');
			$this->load->view('footer_copyright');
			$this->load->view('footer');
		}
	}
}