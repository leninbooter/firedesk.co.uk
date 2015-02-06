<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utilities_m extends CI_Model
{	
	public function save_holiday($month, $day, $isholiday)
	{
		$this->load->database();
		$query = "UPDATE holidays SET isholiday = ".$isholiday." WHERE month = ".$month." AND monthday = ".$day;
		log_message('debug', $query);
		$query = $this->db->query($query);
		return true;
	}
	
	public function get_holidays($month)
	{
		$this->load->database();
		$query = "select monthday from holidays where isholiday = 1 and month = ".$month;
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			return $result;
		}else
		{
			return array();
		}
	}
}