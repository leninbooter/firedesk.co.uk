<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends CI_Controller 
{

	public function new_existing()
	{
		$this->load->view('new_customer');		
	}

}