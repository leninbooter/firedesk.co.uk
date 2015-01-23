<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices_m extends CI_Model
{	
	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}
	
	public function email_invoice( $email, $file_name )
	{	
		$this->load->library('email');

		$this->email->from('info@firedesk.com', 'Firedesk');
		$this->email->to($email);		
		$this->email->subject('Firedesk Invoice');
		$this->email->message('');
		$this->email->attach($file_name);
		echo $this->email->print_debugger();		
		return $this->email->send();
	}

	public function generate_invoice( $contract_id )
	{
		$this->load->database();
		$query = "insert into invoices (creation_date, fk_contract_id) values (\"".date('Y-m-d H:i:s')."\", ".$this->db->escape_str($contract_id).")";		
		$query = $this->db->query($query);
		$query = "select last_insert_id() as 'result'";
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			log_message('debug', $result->result);
			return is_numeric($result->result) ? $result->result : false;
		}else
			return false;
	}
	
	public function get_invoice_details( $invoice_id )
	{
		$this->load->database();
		$query = "SELECT i.fk_contract_id, i.creation_date, i.subtotal, i.vat, i.total, p.payment_ammount, p.payment_reference, t.name
					from invoices as i
							left join invoices_payments as p on i.pk_id = p.fk_invoice_id
							left join payment_types as t on p.fk_payment_type_id = t.pk_id
					where i.pk_id = ".$invoice_id;
		$query = $this->db->query($query);
		$result = $query->result();
		if(!empty($query))
		{
			$result = $query->row();
			return $result;
		}else
			return array();
	}
	
	public function get_all_invoice_items( $invoice_id )
	{
		$this->load->database();
		$query = "select item_no, qty, description, entries_no, rate, regularity, discount_perc, value, item_type from invoices_details where fk_invoice_id = ".$invoice_id;
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->result();
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
		{
			return array();
		}
	}
	
	public function get_invoices_of( $contract_id )
	{
		$this->load->database();
		$query = "SELECT pk_id, creation_date, total, unpaid_ammount FROM `cl51-democompa`.invoices WHERE total > 0 and fk_contract_id = ".$contract_id;
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->result();
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
		{
			return array();
		}
	}
	
	public function save_invoice_item( $vars_array )
	{
		$this->load->database();
		array_walk($vars_array, "self::clean_vars");
		$query = "insert into invoices_details (
					fk_invoice_id,
					fk_contract_item, 
					item_no, 
					qty, 
					description, 
					entries_no, 
					rate, 
					regularity, 
					discount_perc, 
					value, 
					item_type
					)
					values(
						".$this->db->escape_str($vars_array["invoice_id"]).",
						".$this->db->escape_str($vars_array["pk_id"]).",
						".$this->db->escape_str($vars_array["item_no"]).",
						".$this->db->escape_str($vars_array["qty"]).",
						\"".$this->db->escape_str($vars_array["description"])."\",
						".$this->db->escape_str($vars_array["entries_no"]).",
						".$this->db->escape_str($vars_array["rate_per"]).",
						".$this->db->escape_str($vars_array["regularity"]).",
						".$this->db->escape_str($vars_array["discount_perc"]).",
						".$this->db->escape_str($vars_array["value"]).",
						".$this->db->escape_str($vars_array["item_type"])."
					);					
					";		
		$query = $this->db->query( $query );
		$query = "select row_count() as 'result'";
		$query = $this->db->query($query);
		$result = $query->result();
		if (!empty($result) )
		{			
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			if( is_numeric($result->result) && $result->result > 0 )
			{
				$query = "update contract_items set invoiced = 1 where pk_id = ".$this->db->escape_str($vars_array["pk_id"]);
				$query = $this->db->query($query);
				$query = "select row_count() as 'result'";
				$query = $this->db->query($query);
				$result = $query->row();
				if( $result->result > 0)				
					return true;
				else
					return false;
			}
			else
				return false;
		}else
			return false;
	}
	
	public function save_invoice_data( $vars_array )
	{
		$this->load->database();
		array_walk($vars_array, "self::clean_vars");
		$query = "update invoices set subtotal = ".$this->db->escape_str($vars_array["subtotal"]).", vat = ".$this->db->escape_str($vars_array["vat"]).", total = ".$this->db->escape_str($vars_array["total"]).", unpaid_ammount = ".$this->db->escape_str($vars_array["unpaid_ammount"]).", unpaid_cash_invoice = ".$this->db->escape_str($vars_array["unpaid_cash_invoice"])." where pk_id = ".$this->db->escape_str($vars_array["invoice_id"]);
		$query = $this->db->query($query);
		$query = "select row_count() as 'result'";
		$query = $this->db->query($query);
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
	
	public function save_payment( $vars_array )
	{
		$this->load->database();
		array_walk($vars_array, "self::clean_vars");
		$query = "insert into invoices_payments (fk_invoice_id, fk_payment_type_id, payment_ammount, date, payment_reference)
				values (
					".$this->db->escape_str($vars_array["invoice_id"]).",
					".$this->db->escape_str($vars_array["payment_type"]).",
					".$this->db->escape_str($vars_array["ammount"]).",
					\"".$this->db->escape_str($vars_array["date"])."\",
					\"".$this->db->escape_str($vars_array["reference"])."\"
				);";
		$query = $this->db->query($query);
		$query = "select row_count() as 'result'";
		$query = $this->db->query($query);
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
}