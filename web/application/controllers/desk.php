<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Desk extends CI_Controller 
{

	public function index()	{
        
        $this->load->library('nativesession');	
        
        $data = array(
                        'company_logo' => base_url('assets/images/logos/'.$this->nativesession->get('user')['companyLogo']),
                        'company_name' => $this->nativesession->get('user')['company_name'],
                        'branch_name'  => $this->nativesession->get('user')['branch_name'],
                        'branch_city'  => $this->nativesession->get('user')['branch_city'],
                        'branch_country'  => $this->nativesession->get('user')['branch_country']
                    );
        
		$this->load->view('header_nav');
		$this->load->view('desk', $data);
		$this->load->view('footer_copyright');
		$this->load->view('footer_common');
		$this->load->view('footer');
	}

}