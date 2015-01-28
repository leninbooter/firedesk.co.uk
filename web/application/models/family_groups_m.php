<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Family_groups_m extends CI_model
{
	public function get_groups()
	{
		$this->load->database();
		$query = $this->db->query("select pk_id, name, members from family_groups order by 2");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function insert_group( $name )
	{
		$this->load->database();
		$query = $this->db->query("select pk_id from family_groups where name = '".$this->db->escape_str($name)."';");
		$result = $query->result();
		if( !empty($result) )
		{
			return "exists";
		}
		else{
			$query = $this->db->query("insert into family_groups (name) values('".$this->db->escape_str($name)."')");
			$query = "select last_insert_id() as 'result'";
			$query = $this->db->query($query);
			$result = $query->result();
			if( !empty($result) )
			{
				$result = $query->row();
				mysqli_next_result( $this->db->conn_id );
				return is_numeric($result->result) ? $result->result : false;
			}
		}
		return false;
	}
}