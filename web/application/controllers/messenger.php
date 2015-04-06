<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messenger extends CI_Controller 
{
	
	public function are_there_new_messages()
	{
		$this->load->model('messenger_m');
		
		if(empty($this->messenger_m->sel_unread_messages($this->nativesession->get('user')['local_user_id'])))
		{
			$return = array("result"=>"no");
		}
		else
		{
			$return = array("result"=>"yes");
		}
		header('Content-type: application/json');
		echo json_encode($return);
	}
	
	public function inbox()
	{
		$this->load->model('messenger_m');
		
		$data['messages'] = $this->messenger_m->sel_messages_of($this->nativesession->get('user')['local_user_id']);
		
		$this->load->view('messenger_inbox_view', $data);
	}
	
	public function message_delete()
	{
		$this->load->model('messenger_m');
		
		$id = $this->input->get('message', true);
		
		if($this->messenger_m->del_message($id))
			$return = array("result"=>"ok");
		else
			$return = array("result"=>"ko");
		
		echo json_encode($return);
	}
	
	public function message_read()
	{
		$this->load->model('messenger_m');
		
		$id = $this->input->get('message', true);
		
		if($this->messenger_m->upd_message_set_read($id))
			$return = array("result"=>"ok");
		else
			$return = array("result"=>"ko");
		
		echo json_encode($return);
	}
	
	public function save_message()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->load->model('messenger_m');
			
			$user_id = $this->nativesession->get('user')['local_user_id'];
			$user_destination_id = $this->input->post('to', true);;
			$message = $this->input->post('message', true);
			$creation_date = date('Y-m-d H:i:s');
			
			
			if(  !Respect\Validation\Validator::numeric()->validate($user_destination_id) )
			{
				$return = array("result"=>"ko", "error"=>"Plese, select someone to send the message to.");
			}elseif( !Respect\Validation\Validator::string()->notEmpty()->validate($message)) {
				$return = array("result"=>"ko", "error"=>"Please, write a message to send.");
			}else {
				$user_destination_id = $user_destination_id == "-1" ? "null":$user_destination_id;
				$vars_array = compact("user_id", "user_destination_id", "message", "creation_date");
				if($this->messenger_m->ins_message($vars_array))
					$return = array("result"=>"ok");
				else
				{
					$return = array("result"=>"ko", "error"=>"Technical error");
				}
			}
			echo json_encode($return);
		}		
	}	
}