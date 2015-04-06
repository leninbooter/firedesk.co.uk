<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Diary_m extends CI_Model
{		
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function del_event( $vars_array )
	{
		
		array_walk($vars_array, "clean_vars");
		
		$this->company_db->trans_start();
		$query = "delete from diary_events_users where fk_event_id = ".$vars_array['event_id'];
		$query = str_replace("'NULL'", "NULL", $query);
		$this->company_db->query($query);
		$query = "delete from diary_events where pk_id = ".$vars_array['event_id']." and fk_user_id = ".$vars_array['user_id'];
		$query = str_replace("'NULL'", "NULL", $query);
		$this->company_db->query($query);
		$this->company_db->trans_complete();		
		return  $this->company_db->trans_status();
	}
	
	function email_invitation( $vars_array )
	{
		$this->load->library('email');
		
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('info@firedesk.com', 'Firedesk');
		$this->email->to($vars_array['to']);		
		$this->email->subject('Firedesk event invitation');
		$this->email->message($vars_array['html']);
		return $this->email->send();
	}
	
	function ins_event( $vars_array )
	{
		
		
		array_walk($vars_array, "clean_vars");
		
		 $this->company_db->trans_start();
		$query = "insert into diary_events(fk_user_id, start_datetime, end_datetime, title, description, type)
				values(".$vars_array['user_id'].", 
						'".$vars_array['from']."', 
						'".$vars_array['to']."', 
						'".$vars_array['title']."', 
						'".$vars_array['description']."',
						".$vars_array['type'].");";
		$query = str_replace("'NULL'", "NULL", $query);
		 $this->company_db->query($query);
		
		$query = "select last_insert_id() as new_event_id;";
		$query = str_replace("'NULL'", "NULL", $query);
		$query =  $this->company_db->query($query);
		$new_event_id = $query->row()->new_event_id;
		
		if($vars_array['type']==1)
		{
			foreach($vars_array['guests'] as $g)
			{
				if( $g == "-1")
				{
					$query = "insert into diary_events_users(fk_event_id, fk_user_id) select $new_event_id, pk_id from users;";
				}
				else
				{
					$query = "insert into diary_events_users(fk_event_id, fk_user_id) values($new_event_id, $g);";
				
				}
				$query = str_replace("'NULL'", "NULL", $query);
				 $this->company_db->query($query);
			}
		}
		
		 $this->company_db->trans_complete();		
		if( $this->company_db->trans_status())
		{
			return $new_event_id;
		}else
			return false;
	}
	
	function sel_event($event_id)
	{
		
		
		$query = "select dv.*,
					u.name as inviter
					from diary_events as dv 
					inner join users as u on u.pk_id = dv.fk_user_id
					where dv.pk_id = $event_id";
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->row() : array();
	}
	
	function sel_events_of($date, $user_id)
	{	
		$enddate = DateTime::createFromFormat('Y-m-d H:i.000', $date)->format('Y-m-d 23:59.999');
		
		$query = "select dv.*, 'self' as name, u.email from diary_events as dv left join users as u on u.pk_id = dv.fk_user_id where start_datetime >= '".$date."' and start_datetime <= '".$enddate."' and fk_user_id = $user_id
				union all
				select dv.*, u.name, u.email from diary_events as dv
				left join diary_events_users as deu on deu.fk_event_id = dv.pk_id
				left join users as u on u.pk_id = dv.fk_user_id
				where start_datetime >= '$date' and start_datetime <= '$enddate' 
				and dv.fk_user_id != $user_id
				and deu.fk_user_id = $user_id";
				log_message('debug', $query);
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function sel_guests_of($event_id)
	{
		
		
		$query = "SELECT u.name, u.email FROM diary_events_users as e inner join users as u on u.pk_id = e.fk_user_id where e.fk_event_id = ".$event_id	;
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
}