<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_ledger_m extends CI_Model
{
	var $company_db;
	var $dWH;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);		
	}
    
    /**
     *
     * Retrieves the sales by invoice and day of the selected branch
     *
     * @param <mixed> $branchID The ID of the branch to query; true if it is about
     * the current branch
     *
     */
    function getSalesProfitOfBranch( $startDate, $endDate, $branchID = true ) {
        
        $q = 'SELECT 
                cu.pk_id    as customer_id,
                cu.name as customer_name,
                sl.invoice_ID,
                datetime,
                total_sales,
                total_cost,
                total_gross_profit,
                sl.gross_margin as total_gross_margin,
                item_number,
                description,
                qty,
                sales_price,
                cost,
                gross_profit,
                sld.gross_margin,
                ifnull(sld.vat,0) as vat,
                ifnull(sl.total_vat,0) as total_vat
            FROM sales_ledger sl
                INNER JOIN sales_ledger_details as sld ON sl.invoice_ID = sld.invoice_ID
                INNER JOIN invoices as i on i.pk_id = sl.invoice_ID
                INNER JOIN contracts as c ON c.pk_id = i.fk_contract_id
                INNER JOIN customers as cu ON cu.pk_id = c.fk_customer_id
            WHERE sl.datetime BETWEEN \''.$startDate.'\' AND \''.$endDate.'\'';
                        
        if ( !is_numeric($branchID) ) {
            
            return $this->company_db->query($q)->result();
        }else {
            
            $this->load->model('branches_m');
            
            $branchDetails = $this->branches_m->retrieveBranchDetails($branchID);
            
            $branchDB       = array(
                                    'hostname' => $branchDetails->db_host,
                                    'username' => $branchDetails->db_user,
                                    'password' => $branchDetails->db_pwd,
                                    'database' => $branchDetails->db,
                                    'dbdriver' => 'mysqli',
                                    'dbprefix' => '',
                                    'pconnect' => TRUE,
                                    'db_debug' => TRUE,
                                    'cache_on' => FALSE,
                                    'cachedir' => '',
                                    'char_set' => 'utf8',
                                    'dbcollat' => 'utf8_general_ci',
                                    'swap_pre' => '',
                                    'autoinit' => TRUE,
                                    'stricton' => FALSE
                                );                       
           $branchDBCon = $this->load->database($branchDB, true);
           return  $branchDBCon->query($q)->result();
        }
    }
    
    function getSalesProfitByCustomer ( $startDate, $endDate, $customerID, $branchID ) {
        
        $q = 'SELECT
                cu.name,
                customer_ID,
                DATE_FORMAT(datetime, \'%d %M %Y\') as datetime,
                sum(ifnull(total_sales,0)) as total_sales,
                sum(ifnull(total_cost,0)) as total_cost,
                sum(ifnull(total_gross_profit,0)) as total_gross_profit,
                avg(ifnull(gross_margin,0)) as gross_margin,
                sum(ifnull(sl.total_vat,0)) as total_vat
            FROM sales_ledger as sl
                INNER JOIN customers as cu ON cu.pk_id = customer_ID
            WHERE datetime BETWEEN \''.$startDate.'\' AND \''.$endDate.'\' AND customer_ID = '.$customerID.'
            GROUP BY 
                DATE_FORMAT(datetime, \'%d %M %Y\'),
                customer_ID';
            
        if ( !is_numeric($branchID) ) {
            
            return $this->company_db->query($q)->result();
        }else {
            
            $this->load->model('branches_m');
            
            $branchDetails = $this->branches_m->retrieveBranchDetails($branchID);
            
            $branchDB       = array(
                                    'hostname' => $branchDetails->db_host,
                                    'username' => $branchDetails->db_user,
                                    'password' => $branchDetails->db_pwd,
                                    'database' => $branchDetails->db,
                                    'dbdriver' => 'mysqli',
                                    'dbprefix' => '',
                                    'pconnect' => TRUE,
                                    'db_debug' => TRUE,
                                    'cache_on' => FALSE,
                                    'cachedir' => '',
                                    'char_set' => 'utf8',
                                    'dbcollat' => 'utf8_general_ci',
                                    'swap_pre' => '',
                                    'autoinit' => TRUE,
                                    'stricton' => FALSE
                                );                       
           $branchDBCon = $this->load->database($branchDB, true);
           return  $branchDBCon->query($q)->result();
        }
    }
    
    function getSalesProfitAllBranches( $startDate, $endDate ) {
        
        $this->dWH = $this->load->database(dWH_str_conn(), true);
        
        $q = '
            SELECT 
                b.branch_id,
                b.branch_name,
                DATE_FORMAT(datetime, \'%d %M %Y\') as datetime,
                sum(ifnull(total_sales,0)) as total_sales,
                sum(ifnull(total_cost,0)) as total_cost,
                sum(ifnull(total_gross_profit,0)) as total_gross_profit,
                avg(ifnull(gross_margin,0)) as gross_margin,
                sum(ifnull(s.total_vat,0)) as total_vat
            FROM sales as s
                INNER JOIN branches as b ON b.pk_id = s.branch_id
            WHERE datetime BETWEEN \''.$startDate.'\' AND \''.$endDate.'\'
            GROUP BY 
                DATE_FORMAT(datetime, \'%d %M %Y\')';
        
        return $this->dWH->query($q)->result();
    }
}