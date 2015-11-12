<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Respect\Validation\Validator as v;

class Accounting extends MY_Controller
{
    
    function __construct() {
        
        parent::__construct();
        $this->load->model('accounting_m');
    }
    
    public function addAccount() {
        
        $accountCode = $this->input->post('inputCode', true);
        $accountName = $this->input->post('inputDescription', true);
        
        if (v::int()->validate($accountCode)
            && v::string()->validate($accountName) ){
                
                if ( !v::string()->length(1,5)->validate($accountCode) ) {

                    echo json_encode( array( 
                                'result' => 'ko',
                                'message' => 'Account code cannot exceeds 5 characters length'
                            ));
                    
                }else {
                    
                    
                    if ( strlen($accountCode) > 1 &&  !$this->accounting_m->canBeRelated( substr($accountCode,0,strlen($accountCode)-1) )  ) {
                        
                        echo json_encode( array( 
                                        'result' => 'ko',
                                        'message' => 'The account has no defined group'
                                        ));
                        
                    }else {
                        
                        $r = $this->accounting_m->insAccount($accountCode, $accountName);
                        if ( $r  === 1 ) {
                            
                            echo json_encode( array( 
                                        'result' => 'ko',
                                        'message' => 'Account duplicated'
                                    ));
                        }elseif ( $r === false ) {
                            
                            http_response(500);
                            echo json_encode( array( 
                                        'result' => 'ko',
                                        'message' => 'Internal error'
                                    ));
                        }elseif ( $r === true ) {
                            
                            echo json_encode( array( 
                                        'result' => 'ok'
                                    ));
                        }                        
                    }                    
                }
                
                
        }else {
            
            echo json_encode( array( 
                                'result' => 'ko',
                                'message' => 'Bad format'
                            ));
        }
    }
    
    public function cashBook() {
        
        $accCode     = isset($this->queryStrArr['accCode']) ? $this->queryStrArr['accCode'] : false;
        
        if ( !$accCode ) {
            
            $this->load->model('accounting_m');        
            
            $data = array(
                        'banksAcc'      => $this->accounting_m->getBankAccounts()
                    );
            
        }else {
            
            $details = $this->accounting_m->getBankAccountDtls( $accCode );
            $data = array(
                        'bankAccDtls'    => $details,
                        'accMovmnts'    => $this->accounting_m->getAccMvmnts( $details->pk_id )
                    );
            
        }
        
        $this->load->view('header_nav');
		$this->load->view('acc_cash_book', $data);
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
    }
    
    public function defaultAccounts() {
        
        $data = array(
                        'accounts' => $this->accounting_m->getDefaultAccounts()
                    );
        
        $this->load->view('header_nav');
		$this->load->view('acc_default_accounts', $data);
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/acc_def_accounts.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');

        
    }
    
    public function getCOAli() {
        
        $accounts = $this->accounting_m->getAccounts();                     
        
        $data = array(
                    'accounts' => $accounts
                    );        
        $this->load->view('acc_cao_li', $data);
    }
    
    public function getCOAjson()
	{		
		$accounts = $this->accounting_m->getAccounts();
		
		$data = array();
		foreach($accounts as $acc)
		{
			array_push($data, array("id"=>intval($acc->code), "label" => $acc->code ." " . $acc->name ));
		}
		
		header('Content-type: application/json');
		echo json_encode($data);
	}
    
    public function setDefAccount() {
        
        $defAccount = $this->input->post('defAcc', true);
        $accCode    = $this->input->post('accCode', true);
        
        if ( v::int()->validate($defAccount)
            && v::int()->validate($accCode) ) {
                
            if ( $this->accounting_m->updDefAccount($defAccount, $accCode) ) {
                
                echo json_encode( array( 
                    'result' => 'ok'
                ));
            }else {
                
                echo json_encode( array( 
                    'result' => 'ko',
                    'message' => 'Wrong selected account. Please try again.'
                ));
            }
               
        }else {
            
            echo json_encode( array( 
                    'result' => 'ko',
                    'message' => 'Bad format'
                ));
        }
    }   

    public function remAccount() {
        
        $accountCode = $this->input->post('accountCode', true);
        $confirmated = $this->input->post('confirmated', true);
        
        if ( $confirmated == 'false' ) {
            
            echo json_encode( array( 
                    'result'  => 'confirmation',
                    'message' => 'Are you sure you want to delete this account?'
                )); 
            
        }else {
            
            if ( $confirmated == 'yes') {
                     
                    if ( $this->accounting_m->accountHasChildren( $accountCode ) ) {
                        
                        echo json_encode( array( 
                            'result' => 'ko',
                            'message' => 'This account has children accounts; you have to remove them first'
                        ));
                        
                    }else {
                     
                        if ( $this->accounting_m->delAccount( $accountCode ) ) {
                            
                            echo json_encode( array( 
                                'result' => 'ok'
                            )); 
                            
                        }else {
                            
                            echo json_encode( array( 
                                'result' => 'ko',
                                'message' => 'This account has transactions. It cannot be deleted'
                            )); 
                        }
                    }
                
            }else {
                
                echo json_encode( array( 
                    'result' => 'ko',
                    'message' => 'Bad format'
                )); 
            }
            
        }
        
    }    
}