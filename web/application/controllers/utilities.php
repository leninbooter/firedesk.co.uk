<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utilities extends CI_Controller 
{
	public function config()
	{
		$this->load->model('utilities_m');
		$months = array(
					1=>"jan",2=>"feb",3=>"mar",4=>"apr",5=>"may",6=>"jun",7=>"jul",8=>"aug",9=>"sep",10=>"oct",11=>"nov",12=>"dec"
					);
		$data[''] = '';
		for($i=1; $i<=12; $i++)
		{
			$holidays = $this->utilities_m->get_holidays($i);
			$data[$months[$i]."_dates"] = '';
			$data[$months[$i]."_days"] = '';
			foreach($holidays as $d)
			{
				$data[$months[$i]."_dates"] .= $i ."/".$d->monthday."/".date("Y") . ",";
				$data[$months[$i]."_days"] .= $d->monthday . ",";
			}
		}
		$this->load->view('header_nav');
		$this->load->view('utilities_form', $data);
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/utilities.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function save_holiday_schema()
	{		
		$this->load->model('utilities_m');
		
		for($i=1; $i<=12; $i++)
		{		
			switch($i)
			{
				case 1:
					$month = explode(",",$this->input->post('jan',true));
					$top = 31;
				break;
				case 2:
					$month = explode(",",$this->input->post('feb',true));
					$top = 28;
				break;
				case 3:
					$month = explode(",",$this->input->post('mar',true));
					$top = 31;
				break;
				case 4:
					$month = explode(",",$this->input->post('apr',true));
					$top = 30;
				break;
				case 5:
					$month = explode(",",$this->input->post('may',true));
					$top = 31;
				break;
				case 6:
					$month = explode(",",$this->input->post('jun',true));
					$top = 30;
				break;
				case 7:
					$month = explode(",",$this->input->post('jul',true));
					$top = 31;
				break;
				case 8:
					$month = explode(",",$this->input->post('aug',true));
					$top = 33;
				break;
				case 9:
					$month = explode(",",$this->input->post('sep',true));
					$top = 30;
				break;
				case 10:
					$month = explode(",",$this->input->post('oct',true));
					$top = 31;
				break;
				case 11:
					$month = explode(",",$this->input->post('nov',true));
					$top = 30;
				break;
				case 12:
					$month = explode(",",$this->input->post('dec',true));
					$top = 31;
				break;				
			}
			echo "\n";
			for($j=1; $j<=$top; $j++)
			{
				if( in_array($j, $month) )
				{
					$this->utilities_m->save_holiday($i,$j, 1);
				}else
				{
					$this->utilities_m->save_holiday($i,$j, 0);
				}
			}
		}
		$this->load->model('utilities_m');
		$months = array(
					1=>"jan",2=>"feb",3=>"mar",4=>"apr",5=>"may",6=>"jun",7=>"jul",8=>"aug",9=>"sep",10=>"oct",11=>"nov",12=>"dec"
					);
		for($i=1; $i<=12; $i++)
		{
			$holidays = $this->utilities_m->get_holidays($i);
			$data[$months[$i]."_dates"] = '';
			$data[$months[$i]."_days"] = '';
			foreach($holidays as $d)
			{
				$data[$months[$i]."_dates"] .= $i ."/".$d->monthday."/".date("Y") . ",";
				$data[$months[$i]."_days"] .= $d->monthday . ",";
			}
		}
		$this->load->view('header_nav');
		$this->load->view('utilities_form', $data);
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/js/utilities.js')."\"></script>");
		$this->load->view('footer_copyright');
		$this->load->view('footer');		
	}
	
}