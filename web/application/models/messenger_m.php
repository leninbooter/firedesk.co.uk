<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messenger_m extends CI_Model
{		
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function del_message( $id )
	{
		
		
		 $this->company_db->trans_start();		
		$query = "delete from messenger_users_messages where fk_message_id = ".$id;
		 $this->company_db->query($query);
		$query = "delete from messenger_messages where pk_id = ".$id;
		 $this->company_db->query($query);
		 $this->company_db->trans_complete();
		return  $this->company_db->trans_status();
	}
	
	function ins_message( $vars_array )
	{
		
		
		array_walk($vars_array, "clean_vars");
		
		 $this->company_db->trans_start();
		$query = "insert into messenger_messages(fk_user_id, fk_destination_user, message, creation_date)
				values(". $this->company_db->escape_str($vars_array['user_id']).",
				". $this->company_db->escape_str($vars_array['user_destination_id']).",
				'". $this->company_db->escape_str($vars_array['message'])."',
				'". $this->company_db->escape_str($vars_array['creation_date'])."'
				);";
		str_replace("'NULL'", "NULL", $query);
		 $this->company_db->query($query);
		
		$query = "select last_insert_id() as pk_id";
		$query =  $this->company_db->query($query);
		$message_id = $query->row()->pk_id;
		
		if($vars_array['user_destination_id'] != "NULL")
		{
			$query = "insert into messenger_users_messages(fk_message_id, fk_user_id, `read`)
					values(".$message_id.",
					". $this->company_db->escape_str($vars_array['user_id']).",
					0
					);";
			str_replace("'NULL'", "NULL", $query);
			 $this->company_db->query($query);
		}else{
		$query = "insert into messenger_users_messages(fk_message_id, fk_user_id, `read`)
					select ".$message_id.", pk_id, 0 from users";
			str_replace("'NULL'", "NULL", $query);
			 $this->company_db->query($query);
		}
		 $this->company_db->trans_complete();
		return  $this->company_db->trans_status();		
	}
	
	function sel_messages_of($user_id)
	{
		
		$query = "SELECT 
				a.pk_id,
				a.message,
				a.creation_date,
				b.`read` as isread,
				c.name
				FROM messenger_messages as a
				inner join messenger_users_messages as b on a.pk_id = b.fk_message_id
				inner join users as c on c.pk_id = a.fk_user_id
				where a.fk_destination_user = ".$user_id." order by 3 desc";
					log_message('debug', $query);
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function sel_unread_messages($user_id)
	{
		$query = "select mm.pk_id from messenger_messages as mm
					inner join messenger_users_messages as mum on mum.fk_message_id = mm.pk_id 
					and mum.`read` = 0 
					and mm.fk_destination_user = $user_id
					limit 1;";
					log_message('debug', $query);
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->row() : array();
	}
	
	function upd_message_set_read( $id )
	{
		
		$query = "update messenger_users_messages set `read` = 1 where fk_message_id = ".$id;
		str_replace("'NULL'", "NULL", $query);
		return  $this->company_db->query($query);
	}
}