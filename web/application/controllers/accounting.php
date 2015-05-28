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
    
}