<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


use Respect\Validation\Validator as v;

class Diary extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('custom_validations');
	}
	
	public function are_there_events_4_today()
	{
		$this->load->model('diary_m');
		
		$from = $this->input->get('dateandtime',true);
		
		if(v::date('Y-m-d H:i.000')->validate($from))
		{
			if(empty($this->diary_m->sel_events_of( $from, $this->nativesession->get('user')['local_user_id'])))
			{
				$return = array("result"=>"no");
			}
			else
			{
				$return = array("result"=>"yes");
			}
		}
		else
		{
			http_response_code(400);
			$return = array("result"=>"ko");
		}
		
		header('Content-type: application/json');
		echo json_encode($return);
	}
	
	public function index()
	{
		$this->load->view('header_nav');		
		$this->load->view('diary_index');		
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.4.custom/jquery-ui.js')."\"></script>");
		$this->output->append_output("<script src=\"".base_url('assets/js/diary.js')."\"></script>");					
		$this->output->append_output("<script src=\"".base_url('assets/datepicker/js/bootstrap-datepicker.js')."\"></script>");					
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function delete_event()
	{
		$event_id = trim($this->input->get('event_id'));
		$user_id = $this->nativesession->get('user')['local_user_id'];
		
		$vars_array = compact("event_id", "user_id");
		$this->load->model('diary_m');
		
		if(!$this->diary_m->del_event($vars_array))
		{
			$return = array("result"=>"ko", "error"=>"Technical error. Try again");
		}
		else
		{
			$return = array("result"=>"ok");
		}
		echo json_encode($return);
	}
	
	public function events_of()
	{
		$date = trim($this->uri->segment(3));
		
		if( !date_validate($date))
		{
			$return = array("result"=>"ko", "error"=>"Bad format");
			echo json_encode($return);
		}
		else
		{
			$this->load->model('diary_m');
			
			$date = DateTime::createFromFormat('d-m-Y', $date)->format('Y-m-d 00:00.000');
			$data['user_id'] =  $this->nativesession->get('user')['local_user_id'];
			$data['events'] = $this->diary_m->sel_events_of($date, $data['user_id']);
			foreach($data['events'] as $key=>$e)
			{
				if($e->type=="1")
				{
					$guests = $this->diary_m->sel_guests_of($e->pk_id);					
					$data['events'][$key]->guests = $guests;
				}
			}
			
			$this->load->view('diary_events', $data);
		}		
	}
	
	public function send_email_invitation($event_id)
	{
		$this->load->model('diary_m');
		$this->load->helper('file');
		
		//$event_id = trim($this->input->get('event_id'));
		
		$data['e'] = $this->diary_m->sel_event($event_id);
		if($data['e']->type=="1")
		{
			$guests = $this->diary_m->sel_guests_of($data['e']->pk_id);					
			$data['e']->guests = $guests;
		
			foreach( $data['e']->guests as $g)
			{
				$html = "";
				//$html = "<style>".read_file('assets/bootstrap/css/bootstrap.min.css')."</style>";
				$html = $html.$this->load->view('diary_event_email_invitacion', $data, true);
				$to = $g->email;
				$vars_array = compact("to", "html");
				//echo $html;
				
				if($this->diary_m->email_invitation($vars_array))
					$return = true; //array("result"=>"ok");
				else
					$return = false; //array("result"=>"ko");

				return $return;
			}
		}
		
		
	}
	
	public function save_event()
	{
		if( isset($_POST['title']) )
		{
			$user_id = $this->nativesession->get('user')['local_user_id'];;
			$title = trim($this->input->post('title', true));
			$description = trim($this->input->post('description', true));
			$from = trim($this->input->post('start_date', true))." ".trim($this->input->post('start_time', true));
			$to = trim($this->input->post('end_date', true))." ".trim($this->input->post('end_time', true));
			if( isset($_POST['guest']) )
			{
				$type = 1;
				$guests = $_POST['guest'];
			}else{
				$type = 0;
			}
			
			if(!v::string()->length(1,512)->validate($title))
			{
				$return = array("result"=>"ko", "error"=>"The title field is empty");
			}
			elseif( !v::string()->length(1,2048)->validate($description) )
			{
				$return = array("result"=>"ko", "error"=>"The descrption field is empty");
			}
			elseif( !datetime_24_validate($from) )
			{
				$return = array("result"=>"ko", "error"=>"The start date is not valid");
			}
			elseif( !datetime_24_validate($to) )
			{
				$return = array("result"=>"ko", "error"=>"The end date is not valid");
			}
			elseif( !v::date('d-m-Y H:i')->max(new DateTime($to), true)->validate($from) )
			{
				$return = array("result"=>"ko", "error"=>"The start date cannot be beyond the end date");
			}
			else
			{
				if($type == 1)
				{
					foreach($guests as $g)
					{
						if( !v::int()->validate($g))
						{
							$return = array("result"=>"ko", "error"=>"Invalid form format. Please, reload the form and try again.");
							echo json_encode($return);
							return;
						}
					}
				}
				else
				{
					$guests = array();
				}
				
				$from = DateTime::createFromFormat('d-m-Y H:i', $from)->format('Y-m-d H:i:00');
				$to = DateTime::createFromFormat('d-m-Y H:i', $to)->format('Y-m-d H:i:00');
				$this->load->model('diary_m');
				$vars_array = compact('user_id', 'from', 'to', 'title', 'description', 'type', 'guests');
				$new_event_id = $this->diary_m->ins_event($vars_array);
				if(is_numeric($new_event_id))
				{
					$return = array("result"=>"ok");
					self::send_email_invitation($new_event_id);
					
				}else
					$return = array("result"=>"ko", "error"=>"Error saving");
			}
			echo json_encode($return);
		}
	}
	
	
}