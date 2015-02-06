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
		$query = $this->db->query("select pk_id, description from discount_groups");
		return !empty($query->result()) ? $query->result() : array();
	}

	public function update_group( $pk_id, $description )
	{
		$this->load->database();
		if($description == "") $description = "NULL";
		$query = "update discount_groups set description = '".$this->db->escape_str($description)."' where pk_id = ".$pk_id;		
		$query = str_replace("'NULL'", "NULL", $query);
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