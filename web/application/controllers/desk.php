<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Desk extends CI_Controller 
{

	public function index()
	{
		$this->load->view('header_nav');
		$this->load->view('desk');
		$this->load->view('footer_copyright');
		$this->load->view('footer_common');
		$this->load->view('footer');
	}

}