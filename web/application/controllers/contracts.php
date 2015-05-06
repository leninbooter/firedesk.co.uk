<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Respect\Validation\Validator as v;

class Contracts extends MY_Controller
{
    function __construct() {
        
        parent::__construct();
    }
    
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
		$contract_id = trim($this->input->post('contract_id', true));
        
        if ( v::int()->min(1)->validate($contract_id) ) {
        
            $param_arr = array (
                                'contractID' => $contract_id,
                                'datetime'  => date('Y-m-d H:i:s')
                                );
        
            $this->load->model('contracts_m');
            
            if( ($result = $this->contracts_m->set_contract_active( $param_arr )) == true )
            {
                echo "ok";
            }else
            {
                echo "ko";
            }
        }else {
            
            http_response_code(400);
            echo "Bad request.";
            
        }
	}

    public function getHireContractOf() {
        
        $contractID     = $this->queryStrArr['itemID'];
        
        if ( v::int()->validate($contractID) ) {
        
            $this->load->model('contracts_m');
            
            header('Content-type: application/json');
            echo json_encode($this->contracts_m->getContractDetails($contractID));
        }
        
    }
    
	public function contract_details_pdf()
	{
		$this->load->helper(array('dompdf', 'file', 'url'));
		// page info here, db calls, etc.
		$this->load->model('contracts_m');
		$data['contract_id'] = trim($this->input->get('contract_id', true));
		$data['contract_details'] = $this->contracts_m->getContractDetails( $data['contract_id'] );
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
		$this->load->model('contracts_m');
		$this->load->model('cross_hire_m');
				
		$data['contract_id'] = trim($this->input->get('id', true));
        
        if ( v::int()->min(1)->validate($data['contract_id']) ) {
        
            $data['contract_details']           = $this->contracts_m->getContractDetails( $data['contract_id'] );
            $data['customerID']                 = $data['contract_details']->fk_customer_id;
            $data['customer_name']              = $data['contract_details']->name;
            $data['contract_type']              = $data['contract_details']->type == 0 ? "Cash" : "Credit";
            $data['contract_type_sale_hire']    = $data['contract_details']->fk_contract_type_id ;
            $data['address']                    = $data['contract_details']->delivery_address;
            $data['delivery_charge']            = $data['contract_details']->delivery_charge;
            $data['contract_status']            = $data['contract_details']->fk_contract_status_id;
            $mode                               = $data['contract_status'] < 3 ? 'write':'read';
            // Available crossed hire items
            $data['hired_items']                = $this->cross_hire_m->get_hired_items();          
            // Items sold in the contract
            $data['soldItems']                  = $this->load->view('sold_items_table', 
                                                                    array(
                                                                        'mode' =>  $mode, 
                                                                        'items'=>  $this->contracts_m->selectSalesItems( $data['contract_id'] )
                                                                        ),
                                                                    true);
            // Items hired in the contract           
            $data['hiredItems']                 = $this->load->view('hired_items_table',
                                                                    array(
                                                                          'mode'  => $mode,
                                                                          'items' => $this->contracts_m->selectHiredItems( $data['contract_id'] )
                                                                        ),
                                                                    true);
            // Items crossed hired in the contract
            $data['CrossedHireItems']           = $this->contracts_m->selectCrossedHiredItems( $data['contract_id'] );
            //$data['contract_items']           = $this->contracts_m->get_contract_items( $data['contract_id'] );
                    
            $this->load->view('header_nav');
            $this->load->view('form_add_items_to_contract', $data);
            $this->load->view('footer_common');
            $this->load->view('new_contract_footer');
            $this->output->append_output("<script src=\"".base_url('assets/js/form_add_items_to_contract.js')."\"></script>");
            $this->load->view('footer_copyright');
            $this->load->view('footer');
            
        }else {
            
            http_response_code(400);
            echo "Bad request.";
        }        
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

			if(is_numeric($now))
			{
			
                $vars_array = compact("contract_id", "item_id", "now");
				$result = $this->contracts_m->save_item_supplied( $vars_array );
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
    
    public function contractPDF() {
        
        $this->load->helper('mpdf');
        $this->load->model('contracts_m');
		$this->load->model('cross_hire_m');
				
		$data['contractID'] = trim($this->input->get('id', true));
        
        if ( v::int()->min(1)->validate($data['contractID']) ) {
        
            $data['contract_details']           = $this->contracts_m->getContractDetails( $data['contractID'] );
            $data['customerID']                 = $data['contract_details']->fk_customer_id;
            $data['customer_name']              = $data['contract_details']->name;
            $data['contract_type']              = $data['contract_details']->type == 0 ? "Cash" : "Credit";
            $data['contract_type_sale_hire']    = $data['contract_details']->fk_contract_type_id ;
            $data['address']                    = $data['contract_details']->delivery_address;
            $data['delivery_charge']            = $data['contract_details']->delivery_charge;
            $data['contract_status']            = $data['contract_details']->fk_contract_status_id;
            // Available crossed hire items
            $data['hired_items']                = $this->cross_hire_m->get_hired_items();          
             
            // Items sold in the contract
            $data['soldItems']                  = $this->load->view('sold_items_table', 
                                                                    array(
                                                                        'mode' =>  'read', 
                                                                        'items'=>  $this->contracts_m->selectSalesItems( $data['contractID'] )
                                                                        ),
                                                                    true);
            
            // Items hired in the contract           
            $data['hiredItems']                 = $this->load->view('hired_items_table',
                                                                    array(
                                                                          'mode'  => 'read',
                                                                          'items' => $this->contracts_m->selectHiredItems( $data['contractID'] )
                                                                        ),
                                                                    true);
                                                                    
            // Items hired in the contract
            $data['HiredItems']                 = $this->contracts_m->selectHiredItems( $data['contractID'] );
            
            $data['chargingBands']                 = $this->load->view('charging_band_table',
                                                                    array(
                                                                          'items' => $this->contracts_m->selectChargingBandsUsed( $data['contractID'] )
                                                                        ),
                                                                    true);
            
            // Items crossed hired in the contract
            $data['CrossedHireItems']           = $this->contracts_m->selectCrossedHiredItems( $data['contractID'] );
           
            $htmlHeader = $this->load->view('/reports/pdf_header',
                                            array(
                                                    'logoURL' => $this->config->item('logos_path').$this->nativesession->get('user')['companyLogo'],
                                                    'companyName' => $this->nativesession->get('user')['company_name'] 
                                                ),
                                             true);
            
           
            $html = "";
            $html = $html.$this->load->view('/reports/pdf_html_head', '', true);
            $html = $html.$this->load->view('/reports/contract', $data, true);            
            $html = $html.$this->load->view('/reports/pdf_html_foot','', true);
           pdf_create($html, $htmlHeader, '', '');
        }else {
            
            http_response_code(400);
            echo "Bad request.";
        }
        
    }
    
    public function removeCrossHiredItem() {
        
        $crossHiredItemRowID    = $this->input->post('itemRowID', true);
        $date                   = date('Y-m-d H:i:s');
        $contractID             = $this->input->post('contractID', true);
        
        if ( v::int()->validate($crossHiredItemRowID)
            && v::int()->validate($contractID)) {
            
            $param_arr = compact("date", "crossHiredItemRowID", "contractID");
            
            $this->load->model('contracts_m');
            if ($this->contracts_m->deleteCrossHiredItem($param_arr)) {
                echo "ok";
            }else {
                
                echo "ko";
            }
        }else {
            
            http_response_code(400);
            echo "Bad request";
        }
    }
    
    public function removeHireItem() {
    
        $hireItemRowID  = $this->input->post('itemRowID', true);
        $date           = date('Y-m-d H:i:s');
        $contractID     = $this->input->post('contractID', true);
        
        if ( v::int()->validate($hireItemRowID)
            && v::int()->validate($contractID)) {
            
            $param_arr = compact("date", "hireItemRowID", "contractID");
            
            $this->load->model('contracts_m');
            if ($this->contracts_m->deleteHiredItem($param_arr)) {
                echo "ok";
            }else {
                
                echo "ko";
            }
        }else {
            
            http_response_code(400);
            echo "Bad request";
        }
    }
    
    public function removeSaleItem() {
    
        $saleItemRowID  = $this->input->post('itemRowID', true);
        $date           = date('Y-m-d H:i:s');
        $contractID     = $this->input->post('contractID', true);
        
        if ( v::int()->validate($saleItemRowID)
            && v::int()->validate($contractID)) {
            
            $param_arr = compact("date", "saleItemRowID", "contractID");
            
            $this->load->model('contracts_m');
            if ($this->contracts_m->deleteSoldItem($param_arr)) {
                echo "ok";
            }else {
                
                echo "ko";
            }
        }else {
            
            http_response_code(400);
            echo "Bad request";
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
    
    public function saveCrossHireItem() {
        
         if ( isset($_POST['chi_stock_id_in'])) {
            
            for ($i=0; $i < count($_POST['chi_stock_id_in']); $i++) {
               
                $contractID           = trim($this->security->xss_clean($_POST['contractID']));
                $itemID               = trim($this->security->xss_clean($_POST['chi_stock_id_in'][$i]));
                $crossHireOrderItemID = trim($this->security->xss_clean($_POST['chi_order_item_id'][$i]));
                $description          = trim($this->security->xss_clean($_POST['chi_description_in'][$i]));
                $qty                  = trim($this->security->xss_clean($_POST['chi_qty_in'][$i]));
                $rate                 = trim($this->security->xss_clean($_POST['rate'][$i]));
                $day1                 = preg_replace("/[^0-9\.]*/", "", $this->security->xss_clean($_POST['day1'][$i]) ); // str_replace("%", "",trim($this->security->xss_clean($_POST['day1'][$i])));
                $day2                 = preg_replace("/[^0-9\.]*/", "", $this->security->xss_clean($_POST['day2'][$i]) ); //  str_replace("%", "",trim($this->security->xss_clean($_POST['day2'][$i])));
                $day3                 = preg_replace("/[^0-9\.]*/", "", $this->security->xss_clean($_POST['day3'][$i]) ); //  str_replace("%", "",trim($this->security->xss_clean($_POST['day3'][$i])));
                $week                 = preg_replace("/[^0-9\.]*/", "", $this->security->xss_clean($_POST['week'][$i]) ); //  str_replace("%", "",trim($this->security->xss_clean($_POST['week'][$i])));
                $wend                 = preg_replace("/[^0-9\.]*/", "", $this->security->xss_clean($_POST['wend'][$i]) ); //   str_replace("%", "",trim($this->security->xss_clean($_POST['wend'][$i]))); 
                
                if ( v::int()->validate($qty) && $qty > 0) {
                
                    if ( v::int()->validate($itemID)
                        && v::string()->validate($description)
                        && v::numeric()->validate($rate)
                        && v::numeric()->validate($day1)
                        && v::numeric()->validate($day2)
                        && v::numeric()->validate($day3)
                        && v::numeric()->validate($week)
                        && v::numeric()->validate($wend) ) {
                    
                            $param_arr   = compact(
                                                'itemID',
                                                'crossHireOrderItemID',
                                                'description',
                                                'qty',    
                                                'rate',       
                                                'day1',      
                                                'day2',       
                                                'day3',       
                                                'week',       
                                                'wend',
                                                'contractID');
                            
                            $this->load->model('contracts_m');
                            if( $this->contracts_m->insCrossHireItem( $param_arr ) ) {
                                
                                echo "ok";
                                
                            }else {
                                
                                echo "ko";
                                
                            }
                    }else {
                        http_response_code(400);
                        echo "Please, check the format data.";
                    }
                }                
            }
        }
    }
    
    public function saveHireItem() {       
            
        $hireItemID             = trim($this->security->xss_clean($_POST['hire_item_id']));         
        $hireItemDescription    = trim($this->security->xss_clean($_POST['search_hire_item_field'])); 
        $hireItemPrice          = trim($this->security->xss_clean($_POST['hire_item_price'])); 
        $contractID             = trim($this->security->xss_clean($_POST['contractID']));
        $hireItemType           = trim($this->security->xss_clean($_POST['hireItemType']));
        $allocated              = trim($this->security->xss_clean($_POST['allocated']));
        
        if ( !empty($hireItemID) ) {

            if ( v::int()->validate($hireItemID)
                && v::string()->validate($hireItemDescription)
                && v::numeric()->validate($hireItemPrice) ) {
            
                if ( $hireItemType == "Kit" || $hireItemType == "Bundle" ) {
                    
                    $this->load->model('hire_stock_m');
                    
                    if ( $this->hire_stock_m->isMultipleTypeEmpty($hireItemID) ) {
                    
                        echo "The compound item is empty. It can not be added to the contract";
                        return;
                    }
                    
                    $hireItemQty = 1;   
                    
                }elseif ($hireItemType == "Multiple") {
                   
                   $hireItemQty            = trim($this->security->xss_clean($_POST['hire_item_qty'])); 
                   
                   if ( !v::int()->min(1)->validate($hireItemQty)) {                                            
                       
                       echo "For a multiple type item, you must specify a valid quantity";
                       return;
                   }   
                   
                }else {
                    
                    $hireItemQty = 1;
                }
                
                $this->load->model('contracts_m');
                
                if ( $allocated != "yes" && $hireItemType != "Multiple") {
                   
                    if ($this->contracts_m->isHireFleetItemAllocated($hireItemID)) {
                    
                        echo "allocated";
                        return;
                    }
                }
                                
                
                $param_array = compact( "hireItemID",
                                        "hireItemQty",
                                        "hireItemDescription",
                                        "hireItemPrice",
                                        "contractID",
                                        "allocated",
                                        "hireItemType");                       
                
                if ( $this->contracts_m->insHireItem( $param_array) ) {
                    
                    echo "ok";
                }else {
                    
                    echo "ko";
                }                
            }else {
                   
                http_response_code(400);
                echo "Please, check the format data.";
                return;
            } 
        }else {
            http_response_code(400);
            echo "Any item from the hire fleet has been selected";
        }                
    }
    
    public function saveHireItemAccesories() {        

        if (isset($_POST['item_id'])) {
            
            for ($i=0; $i < count($_POST['item_id']); $i++) {
               
               $this->load->model('contracts_m');
                
               $hireItemID    = trim($this->security->xss_clean($_POST['hireItemID'])); 
               $item_no       = trim($this->security->xss_clean($_POST['item_id'][$i]));
               $qty           = intval(trim($this->security->xss_clean($_POST['requestQty'][$i])));
               $description   = trim($this->security->xss_clean($_POST['description'][$i]));
               $disc          = floatval(preg_replace("/[^0-9\.]*/", "", trim($this->security->xss_clean($_POST['disc'][$i]))));
               $rate          = trim($this->security->xss_clean($_POST['price'][$i]));                              
               $contractID    = trim($this->security->xss_clean($_POST['contractID']));
               $itemType      = trim($this->security->xss_clean($_POST['item_type'][$i]));;          
               
               if( $qty > 0 ) {
               
               
                   if ( v::int()->validate($hireItemID)
                        && v::int()->validate($item_no)
                        && v::int()->validate($qty)                    
                        && v::numeric()->validate($rate)
                        && v::numeric()->validate($disc)
                        && v::int()->validate($contractID)
                        && v::int()->validate($itemType) ) {                  
                   
                        $rate = $rate - (($rate*$disc)/100);
                        $value         = $rate*$qty;
                        $date          = date('Y-m-d H:i:s');
                        
                           $parm_arry = compact("hireItemID",
                                                "item_no",
                                                 "qty",
                                                 "description",
                                                 "disc",
                                                 "rate",
                                                 "value",
                                                 "contractID",
                                                 "itemType",
                                                 "date");
                            
                           if ( !$this->contracts_m->insHireItemAccesory($parm_arry) ) {
                               echo "ko";
                               return;
                           }
                           
                    }else {
                       
                        http_response_code(400);
                        echo "Please, check the format data.";
                        return;
                    }
               }                    
            }
            
            echo "ok";
        }
    }

    /**
    *
    * Save the hire multipart item of contract
    *
    */
    public function saveMultipartItemsComponents() {
        
        if (isset($_POST['item_no'])) {
        
           $param_pack = array();
           for ($i=0; $i < count($_POST['item_no']); $i++) {
              
               $hireItemID    = trim($this->security->xss_clean($_POST['hireItemID'])); 
               $item_no       = trim($this->security->xss_clean($_POST['item_no'][$i]));
               $qty           = trim($this->security->xss_clean($_POST['requestQty'][$i]));
               $description   = trim($this->security->xss_clean($_POST['description'][$i]));
               $rate          = trim($this->security->xss_clean($_POST['rate'][$i]));
               $value         = $rate*$qty;
               $contractID    = trim($this->security->xss_clean($_POST['contractID']));
               $itemType      = 2;              
                              
               
               if ( v::int()->validate($hireItemID)
                    && v::int()->validate($item_no)
                    && v::int()->validate($qty)
                    && v::numeric()->validate($rate)
                    && v::int()->validate($contractID)
                    && v::int()->validate($itemType) ) {
                    
                    $param_arr = compact("hireItemID",
                                        "item_no",
                                         "qty",
                                         "description",
                                         "rate",
                                         "value",
                                         "contractID",
                                         "itemType");
                    
                    array_push( $param_pack, $param_arr);
                    
                }else {
                   
                    http_response_code(400);
                    echo "Please, check the format data.";
                    return;
                }
            }
            
            $this->load->model('contracts_m'); 
            
            foreach ( $param_pack as $i) {
                
                if ($i['qty'] > 0) {
                                                           
                    if ( !$this->contracts_m->insMultipartItemComponent($i) ) {
                       
                       echo "ko";
                       return;                           
                    }
                }
            }
            
            echo "ok";
        }
    }
    
    public function saveSaleItem() {
        
        if (isset($_POST['item_id'])) {
            
            $contractID      = $this->input->post('contractID', true);
            $saleStockItemID = $this->input->post('item_id', true);
            $description     = $this->input->post('sale_item_description', true);
            $qty             = $this->input->post('sale_item_qty', true);
            $discount        = floatval(preg_replace("/[^0-9\.]*/", "", $this->input->post('disc', true)));
            $price           = $this->input->post('price', true);                                    
            
            if ( v::int()->validate($saleStockItemID)
                && v::int()->validate($qty)
                && v::string()->validate($description)
                && v::numeric()->validate($price) ) {
                    
                $price  = $price - (($price*$discount)/100.00);
                $total  = number_format($qty*$price, 2);
                $date   = date('Y-m-d H:i:s');
                
                $param_arr = compact(
                                    "saleStockItemID",
                                    "description",    
                                    "qty",            
                                    "discount",          
                                    "price",          
                                    "total",
                                    "date",
                                    "contractID"                                    
                                    );
                
                $this->load->model('contracts_m');
                if ( $this->contracts_m->insSaleItem($param_arr) ) {
                   
                   echo "ok";
                   
                }else {
                    
                   echo "ko";
                }
                
            }else {
                   
                http_response_code(400);
                echo "Please, check the format data";
            }
        }else {
            
            http_response_code(400);
            echo "Bad request";
            
        }
    }	

	public function save_contract_item()
	{
		/*$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->lang->load('errors', 'english');
		$this->lang->load('messages', 'english');
		$this->lang->load('commands', 'english');*/

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
			$item_no        = $_POST["item_no"][$i];
			$qty            = $_POST["qty"][$i];
			$description    = $_POST["description"][$i];
			$entry          = $_POST["no_entries"][$i];
			$rate_per       = $_POST["rate_per"][$i];
			$item_type      = $_POST["item_type"][$i];
			
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