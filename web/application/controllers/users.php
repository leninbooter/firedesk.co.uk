<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller 
{
	public function get_user_data($global_user_id)
	{
		
	}

	public function get_all_users_json()
	{
		$this->load->model('users_m');
		
		header('Content-type: application/json');
		echo json_encode($this->users_m->select_users_all());
		
	}

}