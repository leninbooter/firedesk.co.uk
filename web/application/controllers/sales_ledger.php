<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Respect\Validation\Validator as v;

class Sales_ledger extends MY_Controller
{
    function __construct() {
        
        parent::__construct();
    }
    
    public function salesAllBranches() {
        
        $startDate  =  isset($this->queryStrArr['startDate']) ? str_replace(' ', '', $this->queryStrArr['startDate']) : false;
        $endDate    =  isset($this->queryStrArr['endDate']) ? str_replace(' ', '', $this->queryStrArr['endDate']) : false;
            
        if ( !$startDate || !$endDate) {
            
            $startDate = $endDate = new DateTime("now");
            $startDate->sub(new DateInterval('P1D') );
            $endDate = clone $endDate;
            
            $startDate = $startDate->format('d/m/Y');
            $endDate   = $endDate->format('d/m/Y');
        }
        
        if (   v::date('d/m/Y')->validate($startDate) 
            && v::date('d/m/Y')->validate($endDate) ) {
                
            $this->load->model('sales_ledger_m');  

            $data = array(
            
                        'sales'  => $this->sales_ledger_m->getSalesProfitAllBranches( DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d 00:00:00'), DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d 23:59:59') ),
                        'startDate' => str_replace('/', ' / ', $startDate),
                        'endDate'   => str_replace('/', ' / ', $endDate)
                    );
            
            $this->load->view('header_nav');       
            $this->load->view('sales_all_branches', $data);
            $this->load->view('footer_common');
            $this->output->append_output("<script src=\"".base_url('assets/js/sales_ledger.js')."\"></script>");
            $this->load->view('footer_copyright');
            $this->load->view('footer');  
        }
    }
    
    /**
     * 
     * Shows all invoices between dates with its total sales, cost and gross profit
     * of the current branch
     * 
     * @return <type>
     */
    
    public function salesProfitByInvoice() {

        $this->load->library('nativesession');	        
        
        $branchID     = isset($this->queryStrArr['branchID']) ? $this->queryStrArr['branchID'] : false;
        
        if ( !$branchID ) {
            
            $this->load->model('branches_m');        
            
            $data = array(
                        'branches'      => $this->branches_m->retrieveBranches( $this->nativesession->get('user')['branch_id'] ),
                        'currentBranch' => $this->nativesession->get('user')['branch_name']
                    );
            
        }else {
            $startDate  =  isset($this->queryStrArr['startDate']) ? str_replace(' ', '', $this->queryStrArr['startDate']) : false;
            $endDate    =  isset($this->queryStrArr['endDate']) ? str_replace(' ', '', $this->queryStrArr['endDate']) : false;
            
            if ( !$startDate || !$endDate) {
                
                $startDate = $endDate = new DateTime("now");
                $startDate->sub(new DateInterval('P1D') );
                $endDate = clone $endDate;
                
                $startDate =  $startDate->format('d/m/Y');
                $endDate   = $endDate->format('d/m/Y');
            }
            
            if ( v::int()->validate($branchID)
                && v::date('d/m/Y')->validate($startDate) 
                && v::date('d/m/Y')->validate($endDate) ) {
                
                $branchID = $this->nativesession->get('user')['branch_id'] == $branchID ? true : $branchID;

                $this->load->model('sales_ledger_m');  

                $data = array(
                            'branch_id' => $branchID == true ? $this->nativesession->get('user')['branch_id'] : $branch,
                            'branch'    => $this->nativesession->get('user')['branch_name'],
                            'invoices'  => $this->sales_ledger_m->getSalesProfitOfBranch( DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d 00:00:00'), DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d 23:59:59'), $branchID ),
                            'startDate' => str_replace('/', ' / ', $startDate),
                            'endDate'   => str_replace('/', ' / ', $endDate)
                        );                
            }else {
                
                echo 'Bad request';
                return;
            }
        }                

        $this->load->view('header_nav');       
        $this->load->view('sales_by_branch', $data);        
        $this->load->view('footer_common');
        $this->output->append_output("<script src=\"".base_url('assets/js/sales_ledger.js')."\"></script>");
        $this->load->view('footer_copyright');
        $this->load->view('footer');        
    }
    
    /**
     * 
     * Shows the total sales, cost and gross profit between dats 
     * by customers
     * 
     * @return <type>
     */
    
    public function salesProfitByCustomer() {
        
        $this->load->library('nativesession');	        
        
        $branchID     = isset($this->queryStrArr['branchID']) ? $this->queryStrArr['branchID'] : false;
        $customerID     = isset($this->queryStrArr['customerID']) ? $this->queryStrArr['customerID'] : false;
        
        if ( !$branchID ) {
            
            $this->load->model('branches_m');        
            
            $data = array(
                        'branches'      => $this->branches_m->retrieveBranches( $this->nativesession->get('user')['branch_id'] ),
                        'currentBranch' => $this->nativesession->get('user')['branch_name']
                    );
            
        }else {
            
            $startDate  =  isset($this->queryStrArr['startDate']) ? str_replace(' ', '', $this->queryStrArr['startDate']) : false;
            $endDate    =  isset($this->queryStrArr['endDate']) ? str_replace(' ', '', $this->queryStrArr['endDate']) : false;
            
            if ( !$startDate || !$endDate) {
                
                $startDate = $endDate = new DateTime("now");
                $startDate->sub(new DateInterval('P1D') );
                $endDate = clone $endDate;
                
                $startDate =  $startDate->format('d/m/Y');
                $endDate   = $endDate->format('d/m/Y');
            }
            
            if ( v::int()->validate($branchID) 
                 && v::int()->validate($customerID) ) {
                
                $branchID = $this->nativesession->get('user')['branch_id'] == $branchID ? true : $branchID;

                $this->load->model('sales_ledger_m');  

                $data = array(
                            'branch_id'     => $branchID == true ? $this->nativesession->get('user')['branch_id'] : $branch,
                            'branch'        => $this->nativesession->get('user')['branch_name'],
                            'customer_id'   => $customerID,
                            'customer_sales'=> $this->sales_ledger_m->getSalesProfitByCustomer( DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d 00:00:00'), DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d 23:59:59'), $customerID, $branchID ),
                            'startDate'     => str_replace('/', ' / ', $startDate),
                            'endDate'       => str_replace('/', ' / ', $endDate)
                        );                
            }else {
                
                return;
            }
        }   
        
        $this->load->view('header_nav');       
        $this->load->view('sales_by_customers', $data);
        $this->load->view('footer_common');
        $this->output->append_output("<script src=\"".base_url('assets/js/sales_ledger.js')."\"></script>");        
        $this->load->view('footer_copyright');
        $this->load->view('footer');
        
    }
}