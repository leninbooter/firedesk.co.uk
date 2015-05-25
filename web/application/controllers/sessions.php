<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Respect\Validation\Validator as v;

class Sessions extends CI_Controller 
{
	
	public function __construct()
	{
		parent::__construct();
        // $this->load->library('Nativesession');
	}
	
	public function end()
	{
		$this->nativesession->destroy();
		header('location: '.base_url('index.php/login'));
	}
	
	public function start()
	{	
		//$this->output->enable_profiler(true);
		
		$this->benchmark->mark('posts_variables_start');
		$username = trim($this->input->post('email', true));	
		$password = trim($this->input->post('password', true));	
		$this->benchmark->mark('posts_variables_end');
		$this->benchmark->mark('validation_start');
		
		if(!v::email()->validate($username))
		{
			$return = array("result"=>"ko", "error"=>"The username has not the email address format.");
		}
		elseif(!v::notEmpty()->validate($password))
		{
			$return = array("result"=>"ko", "error"=>"The password field cannot be empty.");
		}
		else
		{
			$this->benchmark->mark('validation_end');
			
			$this->benchmark->mark('sessions_m_start');
			
			$this->load->model('sessions_m');
		
			$this->benchmark->mark('sessions_m_end');
			
			$this->benchmark->mark('sel_user_data_start');
			
			$userdata = $this->sessions_m->sel_user_data($username);			
			
			$this->benchmark->mark('sel_user_data_end');
			
			if(!empty($userdata))
			{				
				$this->benchmark->mark('verify_pwd_start');
				
				if(password_verify($password, $userdata->password)) {
                    
					$this->benchmark->mark('verify_pwd_end');
					
					$this->benchmark->mark('nativession_start');
					
					$this->load->library('nativesession');					
				
					$this->benchmark->mark('nativession_end');				
					
					$this->benchmark->mark('session_start');								
				
					$user = array(
					   'global_user_id'     => $userdata->pk_id,
					   'username'           => $userdata->username,
					   'logged_in'          => TRUE,
					   'company_id'         => $userdata->company_id,
					   'company_name'       => $userdata->company_name,
					   'companyLogo'        => $userdata->logo,
					   'profile_id'         => $userdata->profile_id,
					   'profile_name'       => $userdata->profile_name,
					   'db'                 => $userdata->db,
					   'db_user'            => $userdata->db_user,
					   'db_pwd'             => $userdata->db_pwd,
					   'db_host'            => $userdata->db_host,
					   'db_warehouse_host'  => $userdata->db_warehouse_host,
					   'db_warehouse_name'  => $userdata->db_warehouse_name,
					   'db_warehouse_user'  => $userdata->db_warehouse_user,
					   'branch_id'          => $userdata->pk_id,
					   'branch_name'        => $userdata->branch_name,
					   'db_warehouse_pwd'   => $userdata->db_warehouse_pwd
				   );
					$this->nativesession->set('user', $user);
					
					$this->benchmark->mark('private_data_start');								
					if( $userdata->profile_id == 2 )
					{
						$this->load->model('users_m');
						
						$userdata_private = $this->users_m->sel_user_data($userdata->pk_id);
						
						$userdata_private_array = array(
							'local_user_id' => isset($userdata_private->pk_id) ? $userdata_private->pk_id : NULL,
							'userfullname' => isset($userdata_private->name) ? $userdata_private->name : NULL,
						   'email' => isset($userdata_private->email) ? $userdata_private->email : NULL,
						   'profile'=> isset($userdata_private->fk_profile_id) ? $userdata_private->fk_profile_id : NULL
						);						
						$this->nativesession->set('user', array_merge($user, $userdata_private_array));					
					}
					$this->benchmark->mark('private_data_end');								
					
					$return = array("result"=>"ok");
					$this->benchmark->mark('session_end');
				}
				else
				{
					$return = array("result"=>"ko", "error"=>"Incorrect password!");
				}
				
			}
			else
			{
				$return = array("result"=>"ko", "error"=>"The username you entered is not recognized.");
			}
		}		
		$this->benchmark->mark('json_encode_start');
		echo json_encode($return);
		$this->benchmark->mark('json_encode_end');
	//$this->output->enable_profiler(FALSE);		
	}

}