<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Respect\Validation\Validator as v;

class Invoices extends MY_Controller 
{   
    // Private
    function __construct() {
        
        parent::__construct();
        
        $this->load->model('invoices_m');
    }    
    
    function getChargingRateByDays( $days, $cb ) {
        
        if ($days == 0) {
            
            return 0;
        }
        
        if ( isset($cb["_{$days}day"]) ) {
                                  
            if ( floatval($cb["_{$days}day"]) > 0 ) {
                
                return $cb["_{$days}day"];
            }else {
                
                for($i=$days++; $i<=6; $i++) {
                        
                   if ( floatval($cb["_{$i}day"]) > 0 ) {
                        
                        return $cb["_{$days}day"];
                    }
                }                                
            }
        
        }
        
        if ( floatval($cb['week']) > 0 ) {
                    
            return $cb['week'];
        
        }elseif (  floatval($cb['subsequent_days']) > 0 ) {
            
            return $cb['subsequent_days'];
        }
        
        return false;                
    }
    
    // Public    
    public function accept() {
        
        $invoiceID = $this->input->post('iid', true);
        
        if ( v::int()->min(0)->validate($invoiceID) ){
            
             if ( $this->invoices_m->accept($invoiceID) ) {
                
                echo json_encode( array(
                                        'result' => 'ok'
                                    ));
                
            }else {
                
                echo json_encode( array(
                                        'result'    => 'ko',
                                        'message'  => 'There is a problem with this invoice; please, generate the invoice again from the contract'
                                    ));
            }
            
        }else {        
                        
            echo json_encode( array(
                                    'result' => 'ko',
                                    'message'=> 'Bad request'
                                )); 
        }
    }
    
    public function discard() {
        
        $invoiceID = $this->input->post('iid', true);
        
        if ( v::int()->min(0)->validate($invoiceID) ) {
             
            if ( $this->invoices_m->discard($invoiceID) ) {
            
            echo json_encode( array(
                                    'result' => 'ok'
                                ));
            
            }else {
                
                echo json_encode( array(
                                        'result' => 'ko'
                                    ));
            }
        }else {
            
            echo json_encode( array(
                                    'result' => 'ko',
                                    'message'=> 'Bad request'
                                ));   
        }           
    }
   
    function email( )
    {		                        
        $invoiceID  = isset($this->queryStrArr['iid']) ? $this->queryStrArr['iid']:false;
        $pdf        = isset($this->queryStrArr['pdf']) ? $this->queryStrArr['pdf']:false;
        $to         = isset($this->queryStrArr['to']) ? $this->queryStrArr['to']:false;
        
        if ( v::file()->validate($pdf) && v::int()->min(0)->validate($invoiceID) ) {
            
            if ( v::string()->length(4)->validate($to) ) {
                
                $to = explode( ',', $to );
                array_walk( $to, function( $item, $key ) {
                    
                    if ( !v::email()->validate( $item ) ) {
                        
                        echo 'Invalid destination on email()';
                        return;
                    }
                });
            }else {
                
                $to = false;
            }            
            
        }else {
            
            echo 'Invalid arguments on email()';
            return;
        }
        
        if ( !$to ) {
            
            $to = $this->invoices_m->getInvoiceDetails( $invoiceID )->customerEmailAddress;
            //$pdf = str_replace('.', base_url(), $pdf);
        }
		if ( $this->invoices_m->emailInvoice($pdf, $to) )
        {
            echo 'ok';
        }else {
            
            echo 'ko';
        }
	}
    
	public function generate()
	{       		
	
		$type       = trim($this->input->post('type', true));
		$contractID = trim($this->input->post('contract_id', true));
		
        if ( v::int()->min(0)->validate($contractID)
            && v::int()->min(0)->validate($contractID) ) {
            
            $this->load->model('contracts_m');
            $this->load->model('collects_m');
            $this->load->model('hire_stock_m');
            
            if ( $type == "All") {            
                
                // This includes hired and sold items from the contract that has been 
                // collected but not returned.
                $forInvoiceAll['notReturned']   =  $this->contracts_m->selCollectedNotInvoicedItems($contractID);
                        
            }elseif ( $type == "Off") {
                
                // This includes sold items that have been collected but not invoiced
                $forInvoiceAll['collectedSoldItems'] = $this->contracts_m->selCollectedSoldItems( $contractID );
                
            }
            
            // This includes just hired items that has been returned or off hired.
            $forInvoiceAll['returned']           = $this->contracts_m->selReturnedNotInvoicedItems($contractID);
            
            // This includes just sold items that has been returned and are to be
            // credited to the customer in this invoice
            $forInvoiceAll['returnedSales']     = $this->contracts_m->selReturnedInvoicedSoldItems($contractID);
            
            $currentDate    = new DateTime("now");
                    
            $param_arr = array(
                'currentDate' => $currentDate->format('Y-m-d H:i:s'),
                'contractID'  => $contractID
            );
        
            $invoiceID = $this->invoices_m->generate($param_arr);
                      
            $count = 0;
            foreach ( $forInvoiceAll as $key => $forInvoice ) {
               
                $count += count($forInvoice);
            
            }
            if ( $count == 0 ) {
                echo json_encode(array(
                                    'result'    => 'ko',
                                    'message'   => 'Nothing to invoice.'
                                ));
                return;
            }
            
            
            foreach ( $forInvoiceAll as $key => $forInvoice ) {
                
                 if ( $key == 'notReturned' || $key == 'collectedSoldItems' ) {
                    
                    $offHiredDatetime       = new DateTime("now");
                    $invoiceOrigin          = "1";
                    
                }elseif ( $key == 'returned' || $key == 'returnedSales') {
                    
                    $invoiceOrigin          = "2";
                }
                
                if ( count($forInvoice) > 0 ) {                                                                                          
                    
                    $invoiceItems = array();
                    $origDocs     = array();
                
                    foreach( $forInvoice as $i ) {                       
                                                                        
                        
                        if ( $i->item_type == 1  ) { // Sales
                            
                            if ( $key == 'returnedSales') {
                                
                                $qty = $i->returned_qty;
                                
                            }else {
                                
                                $qty = intval($i->requested_qty-$i->sold_invoiced_qty);
                            }
                            $item = array(
                            
                                        //'collectItemID'         => $i->collect_item_id,
                                        'invoiceID'             => $invoiceID ,
                                        'fk_contract_item_id'   => $i->contract_items_id,
                                        'item_no'               => $i->stock_item_id,
                                        'qty'                   => $qty,
                                        'description'           => $i->item_description,
                                        'rate'                  => $i->item_rate,
                                        'unit_cost'              => $i->cost,
                                        'per'                   => '',
                                        'discount_perc'         => $i->item_discount,
                                        'value'                 => round(($i->item_rate-(($i->item_rate*$i->item_discount)/100.00))*$qty,2),
                                        'item_type'             => 1,
                                        'hours'                 => '',
                                        'days'                  => '',
                                        'weeks'                 => '',
                                        'hire_date_from'        => '',
                                        'hire_date_to'          => $currentDate->format('Y-m-d H:i:s'),
                                        'vat'                   => $i->vat
                                    );
                            
                            array_push($invoiceItems, $item);
                            
                        }elseif ( $i->item_type == 2 ) { // Hire
                            
                            $hireStartDate  = new DateTime($i->collect_datetime);
                            
                            if ( $type == "All" ) {
                                
                                $interval       = $currentDate->diff($hireStartDate, true);

                                if ( $key == 'returned' ) {
                                   
                                   $qty                 = $i->returned_qty;
                                   $offHiredDatetime    = DateTime::createFromFormat('Y-m-d H:i:s', $i->return_datetime);
                                   
                                }else {
                                    $qty             = intval($i->requested_qty-$i->returned_qty);
                                }
                                
                            }elseif ( $type == "Off" ) {
                                
                                $offHiredDatetime   = DateTime::createFromFormat('Y-m-d H:i:s', $i->return_datetime);
                                $interval           = $offHiredDatetime->diff($hireStartDate, true);
                                
                                if ( $key == 'returned' ) {
                                    
                                    $qty             = $i->returned_qty;
                                }else {
                                    
                                    $qty             = intval($i->requested_qty);
                                }
                            }
                                    
                            $hours          = $interval->h;                                
                            $days           = $interval->days % 7;
                            $weeks          = ($interval->y * 52); // Retrieve weeks of years
                            $weeks         += ($interval->m * 4); // Retrieve weeks of months
                            $weeks         += intval($interval->d / 7); // Retrieve weeks of remaing days                                
                            
                            if ( floatval($i->item_rate) > 0 ) {
                                                                                                
                                $chargingBand   = $this->hire_stock_m->selChargingBand($i->stock_item_id);
                                $daysWeek       = $chargingBand['days_week'];                                
                                                                
                               
                                $weeklyrate     = $this->getChargingRateByDays($daysWeek, $chargingBand);
                                $daylyRate      = $this->getChargingRateByDays($days, $chargingBand);
                                
                                if ($weeklyrate == false ) {
                                    
                                    http_response_code(500);
                                    echo "Incorrect charging band";
                                    return;
                                }
                                
                                $value  = round((($i->item_rate*$weeklyrate)/100)*$weeks,2);
                                $value += round((($i->item_rate*$daylyRate)/100),2);
                                
                            }else {
                                
                               $value = 0.00;                               
                            }
                            
                            
                            $item = array(
                                'collectItemID'         => $i->collect_item_id ,
                                'invoiceID'             => $invoiceID ,
                                'fk_contract_item_id'   => $i->contract_items_id,
                                'item_no'               => $i->stock_item_id,
                                'qty'                   => $qty,
                                'description'           => $i->item_description,
                                'rate'                  => $i->item_rate,
                                'per'                   => 3,
                                'discount_perc'         => $i->item_discount,
                                'value'                 => $value,
                                'item_type'             => 2,
                                'hours'                 => $hours,
                                'days'                  => $days,
                                'weeks'                 => $weeks,
                                'hire_date_from'        => $hireStartDate->format('Y-m-d H:i:s'),
                                'hire_date_to'          => $offHiredDatetime->format('Y-m-d H:i:s'),
                                'vat'                   => $i->vat
                            );
                            array_push($invoiceItems, $item);                           
                        }
                        
                        if ( !isset($origDocs[$i->origDocID]) ) {
                            
                            $origDocs[$i->origDocID] = $i->origDocID ;
                        }                                                

                    }
                    
                    if ( !$this->invoices_m->insInvoiceItem($invoiceItems) ) {
                            
                        echo json_encode(array(
                                            'result'    => 'ko',
                                            'message'   => 'There was a problem saving the invoice in the database. Please, try again.'
                                        ));
                    }
                        
                    $this->invoices_m->relate($invoiceID, $origDocs, $invoiceOrigin);                                                     
                }
            
            } //

            echo json_encode(array(
                                            'result'    => 'ok',
                                            'invoiceID' => $invoiceID
                                        ));
            return;   
        }
        
            http_response_code(400);
            echo "Bad request.";
        
	}		
	
    public function past() {
        
         $contractID = $this->input->get('contractID', true);
        
        if ( v::int()->min(0)->validate($contractID)) {
        
            $invoices = $this->invoices_m->selectPastInvoices( $contractID );
            $data = array(
                            'invoices' => $invoices,
                            'invoicesCount' => count($invoices)
                        );
            echo $this->load->view('invoices_past', $data, true);
        }else {
                
                http_response_code(400);
                echo "Bad request";
        } 
    }
    
    /**
     * 
     * Retrieves html with the form payment
     * 
     * @return <String> html form
     */    
    public function pay() {
        
        if ( isset($_POST['invoiceID']) ) {
            
            $cash       = $this->input->post('cash', true);
            $cash       = $cash == '' ? 0:$cash;
            $cheque     = $this->input->post('cheque', true);
            $cheque     = $cheque == '' ? 0:$cheque;
            $card       = $this->input->post('card', true);
            $card       = $card == '' ? 0:$card;
            $ref        = $this->input->post('payment_reference', true);
            $invoiceID  = $this->input->post('invoiceID', true);
            $contractID  = $this->input->post('contractID', true);
            $datetime   = date('Y-m-d H:i:s');
            
            if ( v::numeric()->validate($cash)
                && v::numeric()->validate($cheque)
                && v::numeric()->validate($card)
                && v::string()->validate($ref)
                && v::int()->validate($invoiceID)
                && v::int()->validate($contractID) ) {
                
                $ammount = round($cash + $cheque + $card, 2);
                
                if ( $ammount < 1 ) {
                    
                    echo json_encode(array(
                                        'result' => 'ko',
                                        'message' => 'The total of the payment is zero'
                                    ));
                    return;
                }
                
                $param_arr = array(
                    'ammount'    => $ammount,
                    'cash'      => $cash,
                    'cheque'    => $cheque,
                    'card'      => $card,
                    'ref'       => $ref,
                    'datetime'  => $datetime,
                    'invoiceID' => $invoiceID,
                    'contractID'=> $contractID                    
                );
                 
                if ( $this->invoices_m->insPayment($param_arr) ) {
                    
                    echo json_encode(array(
                                        'result' => 'ok'
                                    ));
                }else {
                    
                    echo json_encode(array(
                                        'result' => 'ko',
                                        'message' => 'Error saving to the database; please, try again'
                                    ));
                }
                    
            }else {
                
                echo json_encode(array(
                                        'result' => 'ko',
                                        'message' => 'Please, verify the format of the data'
                                    ));
            } 
            
        }else { 

            $invoiceID = $this->input->get('invoiceID', true);
        
            if ( v::int()->min(0)->validate($invoiceID)) {
                
                $data = array(
                                'invoice' => $this->invoices_m->getInvoiceDetails($invoiceID)
                            );
                            
                echo $this->load->view('payment_form', $data, true);
            }
        }
    }
    
	public function pdf()
	{
		$this->load->helper('mpdf');
				
		$invoiceID = isset($this->queryStrArr['iid']) ? $this->queryStrArr['iid']:false;
        $disk      = isset($this->queryStrArr['disk']) ? true:false;
        
        if ( v::int()->min(0)->validate($invoiceID)) {
            
            $invoiceDetails  = $this->invoices_m->getInvoiceDetails($invoiceID);            
            $data            = array(
                                    'invoiceItems' => $this->invoices_m->selInvoiceItems($invoiceID),
                                    'mode'         => 'pdf'
                                    );
            $invoiceItems   = $this->load->view('invoice_items_table', $data, true) ;            
            $data           = array(
                                    'invoiceDetails' => $invoiceDetails,
                                    'invoiceItems'   => $invoiceItems,
                                    'logoURL' => $this->config->item('logos_path').$this->nativesession->get('user')['companyLogo'],
                                    'companyName' => $this->nativesession->get('user')['company_name'] 
                                    );
            
            $htmlHeader = $this->load->view('/reports/pdf_header',
                                            array(
                                                    'logoURL' => $this->config->item('logos_path').$this->nativesession->get('user')['companyLogo'],
                                                    'companyName' => $this->nativesession->get('user')['company_name'] 
                                                ),
                                             true);
            $html = "";
            $html = $html.$this->load->view('/reports/pdf_html_head',   '',     true);
            $html = $html.$this->load->view('/reports/invoice',        $data,  true);            
            $html = $html.$this->load->view('/reports/pdf_html_foot',   '',     true);
            
            if ( $disk ) {
                //pdf_create($html, '', '', 'invoice', false);
                echo  pdf_create($html, '', '', 'invoice', false);
            }else {
                pdf_create($html, '', '', '');
            }            
           
        }else {
            
            http_response_code(400);
            echo "Bad request.";            
        }
	}	
	
	/*public function past()
	{
		$this->load->model('invoices_m');
		
		$contract_id = trim($this->security->xss_clean($this->uri->segment(3)));
		if(is_numeric($contract_id))
		{
			$data['invoices'] = $this->invoices_m->get_invoices_of($contract_id);			
			$this->load->view('past_invoices', $data);
		}
	}*/
	
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

    public function removeItem() {
        
        $rowID      = $this->input->post('rowid', true);
        
        if ( v::int()->min(0)->validate($rowID)) {
            
            if ( $this->invoices_m->delItem($rowID)  ) {
                
                echo "ok";
            }else {
                
                echo "ko";
            }
        }else {
            
             http_response_code(400);
            echo "Bad request.";    
        }
    }
        
    
    public function view() {
                
        $invoiceID      = isset($this->queryStrArr['iid']) ? $this->queryStrArr['iid']:false;
        
        if ( v::int()->min(0)->validate($invoiceID)) {
            
            $invoiceDetails  = $this->invoices_m->getInvoiceDetails($invoiceID);

            if ( count($invoiceDetails) <= 0) {
                 header('Location: '.base_url('index.php/desk')); 
              
            }

            if ( !v::date('Y-m-d H:i:s')->validate($invoiceDetails->creation_date) ) {
                
                header('Location: '.base_url('index.php/desk'));               
            }             
            
            $data            = array(
                                    'invoiceItems' => $this->invoices_m->selInvoiceItems($invoiceID),
                                    'mode'         => 'read' //$invoiceDetails->accepted == null ? 'write':'read'
                                    );
            $invoiceItems   = $this->load->view('invoice_items_table', $data, true) ;            
            $data           = array(
                                    'invoiceDetails' => $invoiceDetails,
                                    'invoiceItems'   => $invoiceItems,
                                    'payments'      => $this->invoices_m->selPayments($invoiceID)
                                    );
            
            header("Cache-Control: no-store, no-cache, must-revalidate");                                
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
            
            $this->load->view('header_nav');		
			$this->load->view('invoice_form', $data);
			$this->load->view('footer_common');
			$this->output->append_output("<script src=\"".base_url('assets/js/invoices.js')."\"></script>");	
			$this->load->view('footer_copyright');
			$this->load->view('footer');            
        }else {
            
            http_response_code(400);
            echo "Bad request.";            
        }       
        
    }
    
}