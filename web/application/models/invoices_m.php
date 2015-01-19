<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices_m extends CI_Model
{	
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
}