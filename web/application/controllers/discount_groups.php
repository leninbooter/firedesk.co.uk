<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discount_groups extends CI_Controller
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

	public function groups()
	{
		$this->load->model('discount_groups_m');
		$data['groups'] = $this->discount_groups_m->get_groups();
		$this->load->view('header_nav');
		$this->load->view('discount_groups', $data);
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/discounts_groups.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function update_groups()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('discount_groups_m');
		$this->load->view('header_nav');
		
		$error = false;
		if(!empty($_POST) and isset($_POST['description']))
		{
			for($i = 0; $i < count($_POST['description']); $i++)
			{
				$pk_id = trim($this->security->xss_clean($_POST['pk_id'][$i]));
				$description = trim($this->security->xss_clean($_POST['description'][$i]));
				$discount_percentage = trim($this->security->xss_clean($_POST['discount_percentage'][$i]));
				$discount_percentage = str_replace("%", "", $discount_percentage);
				
				if( is_numeric($discount_percentage) )
				{				
					if( !$this->discount_groups_m->update_group($pk_id, $description, $discount_percentage) )
					{
						$this->output->append_output("
												<div class=\"row\">
													<div class=\"col-md-12\">&nbsp;</div>
												</div>
												<div class=\"row\">
													<div class=\"col-md-12\"><div class=\"alert alert-danger\" role=\"alert\">There was a problem updating the records; please, try again.</div></div>
												</div>");
					$error = true;
					break;
					}
				}else
					{
					$error = true;
					$this->output->append_output("
																	<div class=\"row\">
																		<div class=\"col-md-12\">&nbsp;</div>
																	</div>
																	<div class=\"row\">
																		<div class=\"col-md-12\"><div class=\"alert alert-danger\" role=\"alert\">Data sent has invalid format; please, try again with valid information.</div></div>
																	</div>");					
				}
		
			}
			if(!$error)
			$this->output->append_output("
									<div class=\"row\">
										<div class=\"col-md-12\">&nbsp;</div>
									</div>
									<div class=\"row\">
										<div class=\"col-md-12\"><div class=\"alert alert-success\" role=\"alert\">Records updated successfully.</div></div>
									</div>");
		}		
		$data['groups'] = $this->discount_groups_m->get_groups();
		$this->load->view('discount_groups', $data);
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/discounts_groups.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
}