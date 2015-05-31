<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Respect\Validation\Validator as v;

class Accounting extends MY_Controller
{
    
    function __construct() {
        
        parent::__construct();
        $this->load->model('accounting_m');
    }
    
    
    public function getCOAli() {
        
        $accounts = $this->accounting_m->getAccounts();                     
        
        $data = array(
                    'accounts' => $accounts
                    );        
        $this->load->view('acc_cao_li', $data);
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
            
            if ( v::int()->validate($accountCode)
                 && $confirmated == 'yes') {
                     
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