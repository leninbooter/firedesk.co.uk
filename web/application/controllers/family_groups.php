<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Family_groups extends CI_Controller
{

	public function alpha_dash_space( $valor )
	{
		if( preg_match('/^[A-Za-z0-9\-\_\.\,\s]{5,50}$/', $valor) == 1 )
		{
			return true;
		}else
		{
			$this->form_validation->set_message('alpha_dash_space', "The %s field can only contain letters, numbers, dashes, underscores, dots, commas, white spaces and its length from 5 to 50 characteres.");
			return false;
		}
	}

	public function list_suppliers_addresses()
	{
		$this->load->model('suppliers_m');
		$data['suppliers'] = $this->suppliers_m->get_suppliers_addresses();
		$this->load->view('header_nav');
		$this->load->view('list_suppliers_addresses', $data);
		$this->load->view('footer_common');
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}

	public function groups()
	{
		$this->load->model('family_groups_m');
		$data['groups'] = $this->family_groups_m->get_groups();
		$this->load->view('header_nav');
		$this->load->view('family_groups', $data);
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/suppliers.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}

	public function save_group()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('family_groups_m');

		$this->form_validation->set_rules('name', 'Family Name', 'callback_alpha_dash_space');

		if ( !$this->form_validation->run() )
		{
			$data['groups'] = $this->family_groups_m->get_groups();
			if( $this->input->post('name') )
				$data['name'] = $this->input->post('name');
			$this->load->view('header_nav');
			if( $this->input->post('name') )
				$this->output->append_output("
										<div class=\"row\">
											<div class=\"col-md-12\">&nbsp;</div>
										</div>
										<div class=\"row\">
											<div class=\"col-md-12\"><div class=\"alert alert-danger\" role=\"alert\">".validation_errors()."</div></div>
										</div>");
			$this->load->view('family_groups', $data);
			$this->load->view('footer_common');
			$this->load->view('footer_copyright');
			$this->load->view('footer');
		}
		else
		{
			$name = trim($this->input->post('name'), true);
			$result = $this->family_groups_m->insert_group( $name );
			$data['groups'] = $this->family_groups_m->get_groups();
			$this->load->view('header_nav');
			switch( $result )
			{
				case false:
					$this->output->append_output("
											<div class=\"row\">
												<div class=\"col-md-12\">&nbsp;</div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-12\"><div class=\"alert alert-danger\" role=\"alert\">There was a problem saving the group; please, try again.</div></div>
											</div>");
				break;

				case "exists":
					$this->output->append_output("
											<div class=\"row\">
												<div class=\"col-md-12\">&nbsp;</div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-12\"><div class=\"alert alert-warning\" role=\"alert\">The family name exists; please, try with a different one.</div></div>
											</div>");
				break;

				default:
					$this->output->append_output("
											<div class=\"row\">
												<div class=\"col-md-12\">&nbsp;</div>
											</div>
											<div class=\"row\">
												<div class=\"col-md-12\"><div class=\"alert alert-success\" role=\"alert\">The group was inserted successfully.</div></div>
											</div>");
				break;
			}
			$this->load->view('family_groups', $data);
			$this->load->view('footer_common');
			$this->load->view('footer_copyright');
			$this->load->view('footer');
		}
	}
}