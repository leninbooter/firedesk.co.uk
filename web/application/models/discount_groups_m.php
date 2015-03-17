<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Discount_groups_m extends CI_model
{
	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}

	public function get_groups()
	{
		$this->load->database();
		$query = $this->db->query("select pk_id, description, discount_percentage from discount_groups");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function update_group( $pk_id, $description, $discount_percentage )
	{
		$this->load->database();
		if($description == "") $description = "NULL";
		$query = "update discount_groups set description = '".$this->db->escape_str($description)."', discount_percentage = ".$discount_percentage." where pk_id = ".$pk_id;		
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		$query = $this->db->query("select row_count() as 'result'");
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			$value = is_numeric($result->result) ? $result->result : false;
			return is_numeric($result->result) ? true : false;
		}else
			return false;
	}
}