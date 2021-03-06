<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sessions_m extends CI_Model
{		
	var $fd_db;

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		$this->fd_db = $this->load->database('default', true);
	}
	
	function sel_user_data($username)
	{
		$query = 'select 
                    cr.pk_id, 
                    cr.username,
                    cr.password,
                    p.pk_id as profile_id,
                    p.name as profile_name,
                    co.pk_id as company_id,
                    co.name as company_name,
                    b.db,
                    b.db_user,
                    b.db_pwd,
                    b.db_host,
                    b.db_warehouse_host,
                    b.db_warehouse_name,
                    b.db_warehouse_user,
                    b.db_warehouse_pwd,
                    b.pk_id as branch_id,
                    b.branch_name,
                    co.logo,
                    b.city,
                    countr.name as country
				from credentials as cr
                    inner join profiles as p on p.pk_id = cr.fk_profile_id				
                    inner join branches as b on b.pk_id = cr.fk_branch_id
                    inner join companies as co on co.pk_id = b.fk_company_id
                    inner join countries as countr ON countr.pk_id = b.fk_country_id
				where cr.username = \''.$username.'\'';
				log_message('debug', $query);
		$query =$this->fd_db->query($query);
		return !empty($query->result()) ? $query->row() : array();
	}
}