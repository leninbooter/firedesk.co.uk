<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices_m extends CI_Model
{	
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}
    
    function accept( $invoiceID ) {
        
        $this->company_db->trans_start();
        
        $q = 'SELECT \'yes\' as saved FROM invoices WHERE accepted = true AND pk_id = '.$invoiceID;
        $row = $this->company_db->query($q)->row();
        if ( !empty($row)) {
            
            if ( $row->saved == 'yes' ) {
                
                return false;
            }
        }
        
        $q = 'UPDATE invoices SET accepted = true WHERE pk_id = '.$invoiceID;
        $this->company_db->query($q);
        
        $q = 'SELECT 
                        item_type, 
                        item_no,
                        fk_contract_item,
                        qty, 
                        fk_invoice_id, 
                        hire_date_from, 
                        hire_date_to,
                        fk_contract_id,
                        i.creation_date,
                        hic.origin_doc_id,
                        hic.type
                    FROM invoices_details
                        inner join invoices as i on i.pk_id = fk_invoice_id
                        left join hiring_invoices_control as hic ON hic.fk_invoice_id = i.pk_id
                    WHERE
                        creation_date is not null
                        and fk_contract_id is not null
                        and total is not null
                        and fk_invoice_id = '.$invoiceID;
        $invoiceItems = $this->company_db->query($q)->result();
        
        if ( empty($invoiceItems) ) {
            
            return false;
        }
        
        foreach( $invoiceItems as $i) {

            // Update items hire activity
            if ( $i->item_type == 2 ) {

                $hireDaysArr = $this->getMonthlyHiredDays( $i->hire_date_from, $i->hire_date_to );
            
                foreach( $hireDaysArr as $p ) {                

                    $q = 'SELECT pk_id FROM hire_items_activity 
                            WHERE fk_hire_item_id = '.$i->item_no.' 
                                AND year = '.$p['year'].
                               ' AND month = '.$p['month'];
                    $row = $this->company_db->query($q)->row();                                

                    if( empty($row) ) {
                        
                        $q = 'INSERT INTO hire_items_activity(fk_hire_item_id, year, month, hired_days) 
                                VALUES ('.$i->item_no.','.$p['year'].','.$p['month'].','.$p['days'].')';
                    }else {
                        
                        $q = 'UPDATE hire_items_activity SET hired_days = ifnull(hired_days,0) + '.$p['days'].'
                                WHERE pk_id = '.$row->pk_id;
                    }
                    $this->company_db->query($q);
                }
            }
                
            if ( $i->item == 1 ) {
            
                // Set items of collections as invoiced             
                $query = 'UPDATE collections_items ';
                if ( $i->item_type == 1 ) {
                    
                    $query .= 'SET qty_sold_items_invoiced = ifnull(qty_sold_items_invoiced,0) + '. $this->company_db->escape_str($i->qty).
                               ', last_invoiced_date = \''. $this->company_db->escape_str($i->hire_date_to).'\'';
                               
                }elseif ( $i->item_type == 2 ) {
                    
                    $query .= 'SET  last_invoiced_date = \''. $this->company_db->escape_str($i->hire_date_to).'\'';
                }            
                
                $query .= ' WHERE fk_collection_id = '.$this->company_db->escape_str($i->origin_doc_id).'
                                    AND fk_contract_id = '.$i->fk_contract_item;
                $this->company_db->query($query); 
            
            }
        }
        
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
    }
    
    /**
     * Deletes an item fro an invoice. Not used so far 'cause it is not specified
     * in HireDesk
     * 
     * @param Int $rowID PK of the row in the database of the item 
     * 
     * @return Bool true on success,f alse otherwise
     */          
        
    function delItem( $rowID ) {
        
        $query = 'SELECT item_type, fk_contract_item, qty, fk_invoice_id FROM invoices_details WHERE pk_id = '.$rowID;
        $contractItem = $this->company_db->query($query)->row();
        $this->company_db->trans_start();
        if ( $contractItem->item_type == 1) {
            
            $q = 'UPDATE collections_items SET qty_sold_items_invoiced =  ifnull(qty_sold_items_invoiced,0) -'.$contractItem->qty.' WHERE fk_contract_item_id = '.$contractItem->fk_contract_item;
            $this->company_db->query($q);
        }
        
        $q = 'DELETE FROM invoices_details WHERE pk_id = '.$rowID;
        $this->company_db->query($q);
        
        $q = 'UPDATE collections_items 
                SET last_invoiced_date = (SELECT max(creation_date) FROM invoices WHERE pk_id = '.$contractItem->fk_invoice_id.' AND creation_date != last_invoiced_date  )
                WHERE fk_contract_item_id = '.$contractItem->fk_contract_item;
        $this->company_db->query($q);
        
        $this->company_db->trans_start();
        return $this->company_db->trans_complete();
    }
	
    /**
     * 
     * Deletes the invoice items of the invoice, clean up the invoice details 
     * and leave the invoice number available for the next transaction.
     * 
     * @param <Int> $invoiceID  
     * 
     * @return <Bool> True on success, false otherwise
     */    
    function discard( $invoiceID ) {
        
        $this->company_db->trans_start();       
        
        $q = 'DELETE FROM invoices_details WHERE fk_invoice_id = '.$invoiceID;        
        $this->company_db->query($q);
        
        $q = 'DELETE FROM hiring_invoices_control WHERE fk_invoice_id = '.$invoiceID;        
        $this->company_db->query($q);
        
        $q = 'UPDATE invoices 
                SET creation_date = null, 
                    fk_contract_id = null, 
                    subtotal = null, 
                    vat = null,
                    total = null,
                    accepted = null
                WHERE pk_id = '.$invoiceID;
        $this->company_db->query($q);
        
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
    }
    
	function emailInvoice( $pdf, $to )
	{	    
		$this->load->library('email');

		$this->email->from('info@lenin.com', 'Firedesk');
		$this->email->to($to);		
		$this->email->subject('Firedesk Invoice');
		$this->email->message('');
		$this->email->attach( $pdf );                
		$this->email->send();
        return true;
	}

	function generate( $param_arr )
	{		
		$queryFreeEnvoice = 'SELECT pk_id FROM invoices WHERE accepted is null LIMIT 1';
        $queryFreeEnvoice =  $this->company_db->query($queryFreeEnvoice)->row();       
        
        if ( count( $queryFreeEnvoice ) > 0 ) {
            
            $query                  = 'UPDATE 
                                        invoices 
                                    SET creation_date = \''.$param_arr['currentDate'].'\', 
                                        fk_contract_id = '.$this->company_db->escape_str($param_arr['contractID']).',
                                        type = '.$param_arr['type'].'
                                    WHERE pk_id = '.$queryFreeEnvoice->pk_id;
            $this->company_db->query($query);
            
            $query = 'DELETE FROM invoices_details WHERE fk_invoice_id = '.$queryFreeEnvoice->pk_id;
            $this->company_db->query($query);
            
            return $queryFreeEnvoice->pk_id;
            
        }else {
            
            $query = 'INSERT INTO invoices (creation_date, fk_contract_id) 
                        VALUES (\''.$param_arr['currentDate'].'\',
                                '.$this->company_db->escape_str($param_arr['contractID']).')';		
            $query =  $this->company_db->query($query);
            $query = "select last_insert_id() as ID";
            $query =  $this->company_db->query($query)->row();
            return $query->ID;
        }        
	}
	
	function getInvoiceDetails( $invoiceID )
	{
		
		$query = "
                SELECT 
                    i.pk_id,
                    i.fk_contract_id as contractID, 
                    i.creation_date, 
                    i.subtotal, 
                    i.vat, 
                    i.total, 
                    p.payment_ammount, 
                    p.payment_reference, 
                    t.name,
                    cu.name     as customerName,
                    cu.address  as customerAddress,
                    cu.email    as customerEmailAddress,
                    c.delivery_address as deliveryAddress,
                    i.accepted
                FROM 
                    invoices as i
                        inner join contracts as c   ON c.pk_id  = i.fk_contract_id
                        inner join customers as cu  ON cu.pk_id = c.fk_customer_id 
                        left join invoices_payments as p on i.pk_id = p.fk_invoice_id
                        left join payment_types as t on p.fk_payment_type_id = t.pk_id
                WHERE i.pk_id = ".$invoiceID;
		return $this->company_db->query($query)->row();
	}
	
    function relate ($invoiceID, $docs, $type) {

        foreach( $docs as $i) {
            $q = 'INSERT INTO hiring_invoices_control (type, fk_invoice_id, origin_doc_id)
                   VALUE('.$type.', '.$i.', '.$type.')';
            $this->company_db->query($q);
        }
        
    }
    
    function selectPastInvoices( $contractID ) {
        
        $q = 'SELECT creation_date, pk_id, total, ifnull(unpaid_ammount, total) as unpaid_ammount 
                FROM invoices
                WHERE fk_contract_id = '.$contractID;
        return $this->company_db->query($q)->result();
    }
    
	function selInvoiceItems( $invoice_id )
	{
		
		$query = "SELECT 
                    id.pk_id,
                    id.item_no, 
                    id.qty, 
                    id.description, 
                    id.hire_date_from , 
                    id.rate, 
                    id.hire_date_to, 
                    ifnull(id.discount_perc,0) as discount_perc, 
                    ifnull(id.value,0) as value, 
                    id.item_type,
                    ifnull(id.hours,0) as hours,
                    ifnull(id.days, 0) as days,
                    ifnull(id.weeks,0) as weeks,
                    ifnull(ss.stock_number, hi.fleet_number) as stock_number,
                    id.vat
                from 
                    invoices_details as id 
                    LEFT JOIN sales_stock as ss ON ss.pk_id = id.item_no AND id.item_type = 1
                    LEFT JOIN hire_items as hi ON hi.pk_id = id.item_no AND id.item_type = 2
                where id.fk_invoice_id = ".$invoice_id;
		return $this->company_db->query($query)->result();		
	}
	
	/*function get_invoices_of( $contract_id )
	{
		
		$query = "SELECT pk_id, creation_date, total, unpaid_ammount FROM `cl51-democompa`.invoices WHERE total > 0 and fk_contract_id = ".$contract_id;
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->result();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
		{
			return array();
		}
	}*/
	
	function insInvoiceItem( $param_arr )
	{				
        $this->company_db->trans_start();
       
        $subtotal = $vat = $total = 0.00;
        foreach( $param_arr as $i ) {
            
            array_walk($i, "self::clean_vars");
            
            $query = 'insert into invoices_details (
					fk_invoice_id,
					fk_contract_item, 
					item_no, 
					qty, 
					description, 
					rate, 
					per, 
					discount_perc, 
					value, 
					item_type,
                    hours,
                    days,
                    weeks,
                    hire_date_from,
                    hire_date_to,
                    vat
					)
					values(
						'. $this->company_db->escape_str($i['invoiceID']).',
						'. $this->company_db->escape_str($i['fk_contract_item_id']).',
						'. $this->company_db->escape_str($i['item_no']).',
						'. $this->company_db->escape_str($i['qty']).',
						\''. $this->company_db->escape_str($i['description']).'\',
						'. $this->company_db->escape_str($i['rate']).',
						'. $this->company_db->escape_str($i['per']).',
						'. $this->company_db->escape_str($i['discount_perc']).',
						'. $this->company_db->escape_str($i['value']).',
						'. $this->company_db->escape_str($i['item_type']).',
						'. $this->company_db->escape_str($i['hours']).',
						'. $this->company_db->escape_str($i['days']).',
						'. $this->company_db->escape_str($i['weeks']).',
						\''. $this->company_db->escape_str($i['hire_date_from']).'\',
						\''. $this->company_db->escape_str($i['hire_date_to']).'\',
						'. $this->company_db->escape_str($i['vat']).'
					);					
					';
            $query = str_replace("'NULL'", "NULL", $query);                                
            $query =  $this->company_db->query( $query );
            
            $subtotal += $i['value'];
            $vat      += round((($i['value']*$i['vat'])/100.00),2);
            
        }
        $total = $subtotal + $vat;
        log_message('debug', $total);
        
        $query = 'UPDATE invoices SET subtotal = '.$subtotal.' , vat = '.$vat.', total = '.$total. ' WHERE pk_id = '.$i['invoiceID'];
        $this->company_db->query($query);
        
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
	}
    

	
	function save_invoice_data( $vars_array )
	{
		
		array_walk($vars_array, "self::clean_vars");
		$query = "update invoices set subtotal = ". $this->company_db->escape_str($vars_array["subtotal"]).", vat = ". $this->company_db->escape_str($vars_array["vat"]).", total = ". $this->company_db->escape_str($vars_array["total"]).", unpaid_ammount = ". $this->company_db->escape_str($vars_array["unpaid_ammount"]).", unpaid_cash_invoice = ". $this->company_db->escape_str($vars_array["unpaid_cash_invoice"])." where pk_id = ". $this->company_db->escape_str($vars_array["invoice_id"]);
		$query =  $this->company_db->query($query);
		$query = "select row_count() as 'result'";
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if(!empty($result))
		{
			$result = $query->row();
			if( $result->result > 0 )
				return true;
			else
				return false;
		}else
			return false;
	}
	
	function save_payment( $vars_array )
	{
		
		array_walk($vars_array, "self::clean_vars");
		$query = "insert into invoices_payments (fk_invoice_id, fk_payment_type_id, payment_ammount, date, payment_reference)
				values (
					". $this->company_db->escape_str($vars_array["invoice_id"]).",
					". $this->company_db->escape_str($vars_array["payment_type"]).",
					". $this->company_db->escape_str($vars_array["ammount"]).",
					\"". $this->company_db->escape_str($vars_array["date"])."\",
					\"". $this->company_db->escape_str($vars_array["reference"])."\"
				);";
		$query =  $this->company_db->query($query);
		$query = "select row_count() as 'result'";
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if(!empty($result))
		{
			$result = $query->row();
			if( $query->result > 0)
				return true;
			else
				return false;
		}else
			return false;
	}

    /**
     * 
     * Retrieves an array with all the months between two dates with quantity the 
     * days in every month
     * 
     * @param <datetime sql format> $startDate 
     * @param <datetime sql format> $endDate  
     * 
     * @return <array>
     */
    
    function getMonthlyHiredDays( $startDate, $endDate ) {
        
        $startDate   = DateTime::createFromFormat('Y-m-d H:i:s', $startDate);
        $endDate     = DateTime::createFromFormat('Y-m-d H:i:s', $endDate);
        
        if ( !$startDate || !$endDate ) {
            
            echo 'Invalid arguments';
            return;
        }
        
        if ( $startDate > $endDate) {
            
            echo 'The start date is greater than the end date';
            return;
        }
        
        $hiredDays = array();       
        if ( $startDate->format('n') == $endDate->format('n') && $startDate->format('Y') == $endDate->format('Y') ) {
           
           // Get ddays between days and push to the array
           array_push( $hiredDays,
                        array(
                            'year'  => $startDate->format('Y'),
                            'month' => $startDate->format('n'),
                            'days'  => $startDate->diff($endDate, true)->days
                        )
                    );
           
        }else {
       
            // Loop until the last date and get the days of every month            
            while( true ) {                
                
                if( !isset($startPoint) ) {
                        
                    $startPoint = $startDate;                    
                    $m = $startDate->format('n');
                }else {
                    
                    $startPoint = $endPoint->add(new DateInterval('P1D'));
                    $startPoint = $startPoint->setTime(0,0,0);
                    $m          = $startPoint->format('n');
                }
                
                if (   $m == 11        //30 days
                    || $m == 4
                    || $m == 6
                    || $m == 9 ) {
                                       
                    $monthDays = 30;
                }elseif ( $m == 2 ) { // 28 days
                
                    $monthDays = $startPoint->format('L') == 1 ? 29 : 28;
                    
                }else {               // 31 days                    
                    
                    $monthDays = 31;
                }
               
               $endPoint   = DateTime::createFromFormat('Y-m-d H:i:s', $startPoint->format('Y').'-'.$m.'-'.$monthDays.' 23:59:59');
               
               if ( $endPoint > $endDate ) {
                    
                    $endPoint = $endDate;
                }  
               
               array_push( $hiredDays,
                                    array(
                                        'year'  => $startPoint->format('Y'),
                                        'month' => $startPoint->format('n'),
                                        'days'  => $startPoint->diff($endPoint, true)->days + 1
                                    )
                                );

                if ( $endPoint == $endDate ) {
                    break;
                }
                
                $m = $m == 12 ? 1 : $m + 1;
            }
        }        
        return $hiredDays;
    }
    //getMonthlyHiredDays('2015-05-11 20:44:46',	'2015-06-13 16:55:15');
}