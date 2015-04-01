<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Diary extends CI_Controller 
{
	public function index()
	{
		$this->load->view('header_nav');		
		$this->load->view('diary_index');		
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.3.custom/jquery-ui.js')."\"></script>");
		$this->output->append_output("<script src=\"".base_url('assets/js/diary.js')."\"></script>");					
		$this->output->append_output("<script src=\"".base_url('assets/datepicker/js/bootstrap-datepicker.js')."\"></script>");					
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
}