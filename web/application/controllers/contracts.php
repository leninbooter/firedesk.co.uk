<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts extends CI_Controller
{
	public function abandon_contract()
	{
		$this->load->helper(array('file', 'url'));
		$contract_id = trim($this->input->get('contract_id', true));
		$this->load->model('contracts_m');
		if( ($result = $this->contracts_m->set_contract_abandoned( $contract_id )) == true )
		{
			echo "ok";
		}else
		{
			echo "ko";
		}

	}

	public function activate_contract()
	{
		$this->load->helper(array('file', 'url'));
		$contract_id = trim($this->input->get('contract_id', true));
		$this->load->model('contracts_m');
		if( ($result = $this->contracts_m->set_contract_active( $contract_id )) == true )
		{
			echo "ok";
		}else
		{
			echo "ko";
		}

	}


	public function contract_details_pdf()
	{
		$this->load->helper(array('dompdf', 'file', 'url'));
		// page info here, db calls, etc.
		$this->load->model('contracts_m');
		$data['contract_id'] = trim($this->input->get('contract_id', true));
		$data['contract_details'] = $this->contracts_m->get_contract_details( $data['contract_id'] );
		$data['contract_items'] = $this->contracts_m->get_contract_items( $data['contract_id'] );
		$html = $this->load->view('report_contract_details', $data, true);
		// $html = $html.$this->load->view('footer_common', true);
		// $html = $html.$this->load->view('footer_copyright', true);
		// $html = $html.$this->load->view('footer', true);
		 pdf_create($html, 'Contract Details');
		// $this->load->view('report_contract_details', $data );
        // $this->load->view('footer_common');
		// $this->load->view('footer_copyright');
        // $this->load->view('footer');
	}

	public function edit()
	{		
		$this->load->helper(array('url'));
		$this->load->model('contracts_m');
		$this->load->model('cross_hire_m');
		
		$data['hired_items'] = $this->cross_hire_m->get_hired_items();
		$data['contract_id'] = trim($this->input->get('id', true));
		$data['contract_details'] = $this->contracts_m->get_contract_details( $data['contract_id'] );
		$data['contract_items'] = $this->contracts_m->get_contract_items( $data['contract_id'] );
		$data['customer_name'] = $data['contract_details']->name;
		$data['contract_type'] = $data['contract_details']->type == 0 ? "Cash" : "Credit";
		$data['contract_type_sale_hire'] = $data['contract_details']->fk_contract_type_id ;
		$data['address'] = $data['contract_details']->address;
		$data['delivery_charge'] = $data['contract_details']->delivery_charge;
		$data['contract_status'] = $data['contract_details']->fk_contract_status_id;
		
		
		$this->load->view('header_nav');
		$this->load->view('form_add_items_to_contract', $data);
		$this->load->view('footer_common');
		$this->load->view('new_contract_footer');
		$this->load->view('footer_copyright');
		$this->load->view('footer');

	}

	public function edit_outstanding_items()
	{
		$this->load->helper(array('url'));

		$this->load->model('contracts_m');

		$order = trim($this->input->get('contract_no_field', true));
		if( !is_numeric($order) )
			$order = 0;

		$data['outstanding_items'] = $this->contracts_m->get_outstanding_items( $order );
		$data['contract_id'] = $order;
		$data['contract_details'] = $this->contracts_m->get_contract_details( $data['contract_id'] );
		$data['contract_items'] = $this->contracts_m->get_contract_items( $data['contract_id'] );
		$data['customer_name'] = $data['contract_details']->name;
		$data['contract_type'] = $data['contract_details']->type == 0 ? "Cash" : "Credit";
		$data['address'] = $data['contract_details']->address;
		$data['delivery_charge'] = $data['contract_details']->delivery_charge;
		$data['contract_status'] = $data['contract_details']->fk_contract_status_id;

		$this->load->view('header_nav');
		$this->load->view('form_add_items_to_contract', $data);
		$this->load->view('footer_common');
		$this->load->view('footer_edit_contract');
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}

	public function save_items_supplied()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->lang->load('errors', 'english');
		$this->lang->load('messages', 'english');
		$this->lang->load('commands', 'english');

		$contract_id = trim($this->input->post('contract_id', true));
		$this->load->model('contracts_m');
		$errors = false;
		for($i = 0; $i < count($_POST['now']); $i++)
		{
			$item_id = $_POST["item_id"][$i];
			$now = $_POST["now"][$i];
			log_message('debug', "processing: ".$i." item_id:  ".$item_id. "now: ".$now." contract_id: ".$contract_id."");
			if(is_numeric($now))
			{
				log_message('debug', "is numeric");
				$vars_array = compact("contract_id", "item_id", "now");
				log_message('debug', "compacted");
				$result = $this->contracts_m->save_item_supplied( $vars_array );
				log_message('debug', $result);
				if( !$result )
				{
					$errors=true;
					break;
				}
			}else
				$errors = true;
		}
		if(!$errors)
			echo "ok";
		else
			echo "ko";
	}

	public function list_all_contracts()
	{
		$this->load->model('contracts_m');

		$data['contracts'] = $this->contracts_m->get_contract_list();

		$this->load->view('header_nav');
		$this->load->view('list_all_contracts', $data);
		$this->load->view('footer_common');
		$this->load->view('new_contract_footer');
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}

	public function list_live_contracts()
	{
		$this->load->model('contracts_m');
		$this->load->model('customers_m');

		$this->load->helper(array('url'));

		$customer_id = trim($this->input->get('customer_id', true));

		if( !is_numeric($customer_id) )
			$customer_id = 0;

		$data['contracts'] = $this->contracts_m->get_live_contracts($customer_id);
		$data['customers'] = $this->customers_m->get_customers();

		$this->load->view('header_nav');
		$this->load->view('list_live_contracts', $data);
		$this->load->view('footer_common');
		$this->load->view('new_contract_footer');
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}

	public function list_balance_orders()
	{
		$this->load->model('contracts_m');
		$this->load->model('customers_m');

		$this->load->helper(array('url'));

		$order = trim($this->security->xss_clean($this->uri->segment(3)));

		if( !is_numeric($order) )
			$order = 0;

		$data['contracts'] = $this->contracts_m->get_outstanding_contracts_orderBy( $order );

		$this->load->view('header_nav');
		$this->load->view('list_balance_orders', $data);
		$this->load->view('footer_common');
		$this->load->view('new_contract_footer');
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

	public function new_contract()
	{
		$this->load->view('header_nav');
		$this->load->view('new_contract');
		$this->load->view('footer_common');
		$this->load->view('new_contract_footer');
		$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.4.custom/jquery-ui.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
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
		$date					= date('Y-m-d', strtotime($this->input->post('date')));
		$saved_addresses		= $this->input->post('saved_addresses');
		$new_address			= $this->input->post('new_address');
		$delivery_charge		= $this->input->post('delivery_charge');
		$notes					= $this->input->post('notes');
		$type					= $this->input->post('cash') == 'yes' ? '0' : '1';

		$vars_array = compact(
								"account_reference_id",
								"contract_type",
								"time",								
								"date",
								"saved_addresses",
								"new_address",
								"delivery_charge",
								"notes",
								"type"
							);
			$this->load->model('contracts_m');
			$result = $this->contracts_m->save_contract( $vars_array );
			if( is_numeric($result) )
			{
				redirect(base_url('index.php/contracts/edit?id='.$result),'refresh');							
			}else
			{
				echo "failed";
			}
		}
	}

	public function save_contract_item()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->lang->load('errors', 'english');
		$this->lang->load('messages', 'english');
		$this->lang->load('commands', 'english');

		$contract_id = $this->input->post('contract_id', true);

		$this->load->model('contracts_m');

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
			$item_no = $_POST["item_no"][$i];
			$qty = $_POST["qty"][$i];
			$description = $_POST["description"][$i];
			$entry = $_POST["no_entries"][$i];
			$rate_per = $_POST["rate_per"][$i];
			$item_type = $_POST["item_type"][$i];
			if( $item_type == "2" )
				$regularity = $reg_post;
			else
				$regularity = "";
			$disc = $_POST["disc"][$i];
			$value = $_POST["value"][$i];

			$vars_array = compact(
								"contract_id",
								"item_no",
								"qty",
								"description",
								"entry",
								"rate_per",
								"regularity",
								"disc",
								"value",
								"item_type"
							);
			$result = $this->contracts_m->save_contract_item( $vars_array );
			if( !$result )
			{
				echo "failed";
				break;
			}
		}
				$data['contract_id'] = $this->input->post('contract_id');
				$data['customer_name'] = $this->input->post('customer_name');
				$data['contract_type'] = $this->input->post('contract_type') == 0 ? "Cash" : "Credit";
				$data['address'] = $this->input->post('saved_address');
				$data['delivery_charge'] = $this->input->post('delivery_charge');
				$data['contract_status'] = $this->contracts_m->get_contract_status( $data['contract_id'] );
				$data['contract_items'] = $this->contracts_m->get_contract_items( $data['contract_id'] );
				$this->load->view('header_nav');
				$this->load->view('form_add_items_to_contract', $data);
				$this->load->view('footer_common');
				$this->load->view('new_contract_footer');
				$this->load->view('footer_copyright');
				$this->load->view('footer');
	}


}