<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messenger_m extends CI_Model
{		
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->helper('models');
    }
	
	public function del_message( $id )
	{
		$this->load->database();
		
		$this->db->trans_start();		
		$query = "delete from messenger_users_messages where fk_message_id = ".$id;
		$this->db->query($query);
		$query = "delete from messenger_messages where pk_id = ".$id;
		$this->db->query($query);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
	public function ins_message( $vars_array )
	{
		$this->load->database();
		
		array_walk($vars_array, "clean_vars");
		
		$this->db->trans_start();
		$query = "insert into messenger_messages(fk_user_id, fk_destination_user, message, creation_date)
				values(".$this->db->escape_str($vars_array['user_id']).",
				".$this->db->escape_str($vars_array['user_destination_id']).",
				'".$this->db->escape_str($vars_array['message'])."',
				'".$this->db->escape_str($vars_array['creation_date'])."'
				);";
		str_replace("'NULL'", "NULL", $query);
		$this->db->query($query);
		
		$query = "select last_insert_id() as pk_id";
		$query = $this->db->query($query);
		$message_id = $query->row()->pk_id;
		
		if($vars_array['user_destination_id'] != "NULL")
		{
			$query = "insert into messenger_users_messages(fk_message_id, fk_user_id, `read`)
					values(".$message_id.",
					".$this->db->escape_str($vars_array['user_id']).",
					0
					);";
			str_replace("'NULL'", "NULL", $query);
			$this->db->query($query);
		}else{
		$query = "insert into messenger_users_messages(fk_message_id, fk_user_id, `read`)
					select ".$message_id.", pk_id, 0 from users";
			str_replace("'NULL'", "NULL", $query);
			$this->db->query($query);
		}
		$this->db->trans_complete();
		return $this->db->trans_status();		
	}
	
	public function sel_messages_of($user_id)
	{
		$this->load->database();
		$query = "SELECT 
				a.pk_id,
				a.message,
				a.creation_date,
				b.`read` as isread,
				c.name
				FROM messenger_messages as a
				inner join messenger_users_messages as b on a.pk_id = b.fk_message_id
				inner join users as c on c.pk_id = a.fk_user_id
				where c.pk_id = ".$user_id." order by 3 desc";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function upd_message_set_read( $id )
	{
		$this->load->database();
		$query = "update messenger_users_messages set `read` = 1 where fk_message_id = ".$id;
		str_replace("'NULL'", "NULL", $query);
		return $this->db->query($query);
	}
}