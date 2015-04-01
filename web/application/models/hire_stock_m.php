<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hire_stock_m extends CI_Model
{		
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->helper('models');
    }
	
	
	
	public function delete_accesory( $id )
	{
		$this->load->database();
		return $this->db->query( "delete from hire_accesory_groups where pk_id = $id or accesory_parent = $id");
		
	}
	
	public function delete_group( $id )
	{
		$this->load->database();
		return $this->db->query( "delete from hire_items_family_groups where pk_id = $id and qty = 0 or qty is null");		
	}
	
	public function delete_component_group( $vars_array )
	{
		$this->load->database();
		array_walk($vars_array, "clean_vars");
		$query = "delete from hire_items_bundles_groups where pk_id = ".$this->db->escape_str($vars_array['item_id']);

		return $this->db->query($query);
	}
	
	public function delete_item_component($vars_array)
	{
		$this->load->database();
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items set fk_hire_item_parent = NULL, qty_required_as_component = NULL where pk_id = ".$this->db->escape_str($vars_array['item_id']);

		return $this->db->query($query);
	}
	
	public function delete_stock( $vars_array )
	{
		$this->load->database();
		
		$this->db->trans_start();
		$query = "insert into hire_items_acqs_disp(type, date_time, fk_item_id, qty, cost_value_each, notes, fk_disposal_deducted_from)
					values(2, '".$this->db->escape_str($vars_array['date_time'])."', ".$this->db->escape_str($vars_array['item_id']).", ".$this->db->escape_str($vars_array['qty']).", ".$this->db->escape_str($vars_array['cost_value_each']).", '".$this->db->escape_str($vars_array['notes'])."', ".$this->db->escape_str($vars_array['acquisition_id']).");";
		$this->db->query($query);
		$query = "update hire_items set qty = ifnull(qty,0) - ".$this->db->escape_str($vars_array['qty'])." where pk_id = ".$this->db->escape_str($vars_array['item_id']);
		$this->db->query($query);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
	public function get_groups_all( )
	{
		$this->load->database();
		$query = $this->db->query( "SELECT
									hifg.pk_id,
									hifg.name,
									ifnull(qty,0) as 'total',
									hifg.basic_rate,
									hicb.name as 'calc_code',
									concat(v.description,' (',v.percentage,'%)') as vat
									FROM hire_items_family_groups as hifg
										inner join vats as v on v.pk_id = hifg.fk_vat_code_id
										inner join hire_items_charging_bands as hicb  on hicb.pk_id = hifg.fk_charging_band
									");
		return !empty($query->result()) ? $query->result() : array();
	}
		
	public function get_items_all( )
	{
		$this->load->database();
		$query = $this->db->query( "SELECT pk_id, fleet_number as 'number', description as 'label', 1 as 'origin' FROM hire_items;");
		return !empty($query->result()) ? $query->result() : array();
	}	
	
	public function get_accesories_from_group( $group_id )
	{
		$this->load->database();
		$query = "select 
					hag.pk_id as 'pk_id',
					hi.fleet_number as 'number',
					hi.description as 'description',
					hag.qty as 'qty'
				from hire_accesory_groups as hag
					inner join hire_items as hi on hi.pk_id = hag.fk_item_id
				where hag.item_type = 1 and hag.accesory_parent is null and hag.fk_hire_family_group_id = ".$group_id."
				union all
				select
					hag.pk_id as 'pk_id',
					ss.stock_number as 'number',
					ss.description as 'description',
					hag.qty as 'qty'
				from hire_accesory_groups as hag
					inner join sales_stock as ss on ss.pk_id = hag.fk_item_id
				where hag.item_type = 2 and hag.accesory_parent is null and hag.fk_hire_family_group_id = ".$group_id;
				
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	public function ins_hire_item( $vars_array )
	{
		$this->load->database();
		
		array_walk($vars_array, "clean_vars");
		
		$this->db->trans_start();
		$query = "insert into hire_items (
					fk_type,
					fleet_number, 
					description,
					fk_family_group, 
					purchase_date, 
					cost, 
					serial_number, 
					power, 
					fuse, 
					flash_test, 
					engine_speed, 
					output_spindle, 
					cable_type, 
					cable_length, 
					PPE_kit, 
					safety_leaflet, 
					test_frequency )
				values (
					".$this->db->escape_str($vars_array['hire_item_type']).",
					'".$this->db->escape_str($vars_array['fleet_number'])."',
					'".$this->db->escape_str($vars_array['description'])."',
					".$this->db->escape_str($vars_array['group_id']).",
					'".$this->db->escape_str($vars_array['purchase_date'])."',
					".$this->db->escape_str($vars_array['basic_rate']).",
					'".$this->db->escape_str($vars_array['serial_number'])."',
					'".$this->db->escape_str($vars_array['power'])."',
					'".$this->db->escape_str($vars_array['fuse'])."',
					'".$this->db->escape_str($vars_array['flash_test'])."',
					'".$this->db->escape_str($vars_array['engine_speed'])."',
					'".$this->db->escape_str($vars_array['output_spindle'])."',
					'".$this->db->escape_str($vars_array['cable_type'])."',
					'".$this->db->escape_str($vars_array['cable_length'])."',
					'".$this->db->escape_str($vars_array['ppe_kit'])."',
					'".$this->db->escape_str($vars_array['safety_leaflet'])."',
					'".$this->db->escape_str($vars_array['test_frequency'])."'
				);";
		$query = str_replace("'NULL'", "NULL", $query);
		if( !$this->db->query($query) )
			return false;
		
		$query = $this->db->query("select last_insert_id() as pk_id;");
		$pk_id = $query->row()->pk_id;
		$this->db->trans_complete();
		return $pk_id;
	}
	
	public function insert_group( $vars_array )
	{
		$this->load->database();
		$query = $this->db->query("select pk_id from hire_items_family_groups where name = '".$this->db->escape_str($vars_array['name'])."';");
		$result = $query->result();
		if( !empty($result) )
		{
			return "exists";
		}
		else{
			array_walk($vars_array, "clean_vars");
			$query ="insert into hire_items_family_groups (name, basic_rate, fk_charging_band, fk_vat_code_id)
						values(
							'".$this->db->escape_str($vars_array['name'])."',
							".$this->db->escape_str($vars_array['basic_rate']).",
							".$this->db->escape_str($vars_array['charging_band_id']).",
							".$this->db->escape_str($vars_array['vat_code_id'])."
						)";
						
			$query = str_replace("'NULL'", "NULL", $query);				
			$query = $this->db->query($query);
			$query = "select last_insert_id() as 'result'";
			$query = $this->db->query($query);
			$result = $query->result();
			if( !empty($result) )
			{
				$result = $query->row();
				mysqli_next_result( $this->db->conn_id );
				return is_numeric($result->result) ? $result->result : false;
			}
		}
		return false;
	}
	
	public function ins_accesory_to_group( $vars_array )
	{
		$this->load->database();
		array_walk($vars_array, "clean_vars");
		$query ="insert into hire_accesory_groups (fk_hire_family_group_id, item_type, fk_item_id, qty)
					values(
						".$this->db->escape_str($vars_array['group_id']).",
						".$this->db->escape_str($vars_array['item_type']).",
						".$this->db->escape_str($vars_array['item_id']).",
						".$this->db->escape_str($vars_array['qty'])."
					);";
		
		$query = str_replace("'NULL'", "NULL", $query);
		if($this->db->query($query))
		{
			$query = $this->db->query("select last_insert_id() as 'result';");
			$acc_parent = $query->row()->result;
			
			if($vars_array['qty'] == "NULL")
			{
				$query = "insert into hire_accesory_groups(fk_hire_family_group_id, item_type, fk_item_id, qty, accesory_parent)
							select 
								".$this->db->escape_str($vars_array['group_id']).",
								1,
								pk_id,
								0, 
								".$acc_parent."
							from hire_items
							where fk_hire_item_parent = ".$this->db->escape_str($vars_array['item_id']);

				if(!$this->db->query($query))
					return false;
			}
		}
		return true;
	}

	public function ins_initial_qty( $vars_array )
	{
		$this->load->database();
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items set qty = ".$this->db->escape_str($vars_array['qty'])." where pk_id = ".$this->db->escape_str($vars_array['parent_item']);
		
		return $this->db->query($query);
	}
	
	public function ins_component_group( $vars_array )
	{
		$this->load->database();
		array_walk($vars_array, "clean_vars");
		$query = "insert into hire_items_bundles_groups(fk_bundle_id, fk_hire_group_id) values (".$this->db->escape_str($vars_array['parent_item']).", ".$this->db->escape_str($vars_array['item_id'])."); ";
		log_message('debug', $query);
		return $this->db->query($query);
	}
	
	public function ins_item_component($vars_array)
	{
		$this->load->database();
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items set fk_hire_item_parent = ".$this->db->escape_str($vars_array['parent_item']).", qty_required_as_component = ".$this->db->escape_str($vars_array['qty'])." where pk_id = ".$this->db->escape_str($vars_array['item_id']);

		return $this->db->query($query);
	}
	
	public function insert_charging_band( $vars_array )
	{
		$this->load->database();
		
		array_walk($vars_array, "clean_vars");
		
		$query = $this->db->query("select pk_id from hire_items_charging_bands where name = '".$this->db->escape_str($vars_array['name'])."';");
		$result = $query->result();
		if( !empty($result) )
		{
			return "exists";
		}
		else{
			$query = "insert into hire_items_charging_bands (name, _4hr, _8hr, _1day, _2day, _3day, _4day, _5day, _6day, week, weekend, subsequent_days, days_per_week, thereafter, min_days)
									values('".$this->db->escape_str($vars_array["name"])."',
										".$this->db->escape_str($vars_array["_4hr_perc"]).",
										".$this->db->escape_str($vars_array["_8hr_perc"]).",
										".$this->db->escape_str($vars_array["_1day_perc"]).",
										".$this->db->escape_str($vars_array["_2day_perc"]).",
										".$this->db->escape_str($vars_array["_3day_perc"]).",
										".$this->db->escape_str($vars_array["_4day_perc"]).",
										".$this->db->escape_str($vars_array["_5day_perc"]).",
										".$this->db->escape_str($vars_array["_6day_perc"]).",
										".$this->db->escape_str($vars_array["_week_perc"]).",
										".$this->db->escape_str($vars_array["_weekend_perc"]).",
										".$this->db->escape_str($vars_array["_subsequent_perc"]).",
										".$this->db->escape_str($vars_array["days_week"]).",
										".$this->db->escape_str($vars_array["thereafter"]).",
										".$this->db->escape_str($vars_array["min_days"])."
									)";
			$query = str_replace("'NULL'", "NULL", $query);				
			$query = $this->db->query($query);
			$query = "select last_insert_id() as 'result'";
			$query = $this->db->query($query);
			$result = $query->result();
			if( !empty($result) )
			{
				$result = $query->row();
				mysqli_next_result( $this->db->conn_id );
				return is_numeric($result->result) ? $result->result : false;
			}
		}
		return false;
	}
	
	public function insert_stock( $vars_array )
	{
		$this->load->database();
		
		$this->db->trans_start();
		$query = "insert into hire_items_acqs_disp(type, date_time, fk_item_id, qty, cost_value_each, notes)
					values(1, '".$this->db->escape_str($vars_array['date_time'])."', ".$this->db->escape_str($vars_array['item_id']).", ".$this->db->escape_str($vars_array['qty']).", ".$this->db->escape_str($vars_array['cost_value_each']).", '".$this->db->escape_str($vars_array['notes'])."');";
		$this->db->query($query);
		$query = "update hire_items set qty = ifnull(qty,0) + ".$this->db->escape_str($vars_array['qty'])." where pk_id = ".$this->db->escape_str($vars_array['item_id']);
		$this->db->query($query);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
	public function select_elegible_items()
	{
		$this->load->database();
		$query = "SELECT pk_id, fleet_number as 'number', description as 'label', 1 as 'origin', fk_type as 'item_type' FROM hire_items WHERE fk_type = 2
					UNION ALL
				SELECT pk_id, stock_number as 'number', description as 'label', 2 as 'origin', NULL as 'item_type' FROM sales_stock;";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_childrens_items_from_accesory( $parent_id )
	{
		$this->load->database();
		$query = "select
					hag.pk_id as 'pk_id',
					hi.fleet_number as 'number',
					hi.description as 'description',
					hag.qty as 'qty'
				from hire_accesory_groups as hag
					inner join hire_items as hi on hi.pk_id = hag.fk_item_id
				where hag.item_type = 1 and hag.accesory_parent = ".$parent_id;
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_charging_bands_all()
	{
		$this->load->database();
		$query = $this->db->query("select pk_id, name from hire_items_charging_bands;");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	
	
	public function save_item( $vars_array )		
	{		
		$this->load->database();
		array_walk($vars_array, "clean_vars");
		
		$query = "call ins_stock_item(
									'".$this->db->escape_str($vars_array["stock_number"])."',
									'".$this->db->escape_str($vars_array["location"])."',
									'".$this->db->escape_str($vars_array["description"])."',
									 ".$this->db->escape_str($vars_array["quantity_rec_level"]).",
									 ".$this->db->escape_str($vars_array["standard_price"]).",
									 ".$this->db->escape_str($vars_array["special_price"]).",
									 ".$this->db->escape_str($vars_array["units_of_for_special"]).",
									 ".$this->db->escape_str($vars_array["cost_price_a"]).",
									 ".$this->db->escape_str($vars_array["cost_price_b"]).",
									 ".$this->db->escape_str($vars_array["cost_price_c"]).",
									 ".$this->db->escape_str($vars_array["fk_vat_code"]).",
									 ".$this->db->escape_str($vars_array["fk_family_group"]).",
									 ".$this->db->escape_str($vars_array["fk_discount_group"]).",
									 ".$this->db->escape_str($vars_array["fk_supplier_a"]).",
									 ".$this->db->escape_str($vars_array["fk_supplier_b"]).",
									 ".$this->db->escape_str($vars_array["fk_supplier_c"])."
									);";
		$query = str_replace("'NULL'", "NULL", $query);
		$query = $this->db->query($query);
						
		$result = $query->result();
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			if($result->type == "inserted" )
			{	
				$return['type'] = $result->type;
				$return['pk_id'] = $result->pk_id;
				return $return;
			}elseif($result->type == "updated")
			{
				$return['type'] = $result->type;
				$return['pk_id'] = $result->pk_id;
				return $return;
			}
		}
		return false;
	}
	
	public function select_acqs_rems( $pk_id )
	{
		$this->load->database();
		$query = "select a.pk_id, 
					a.type, 
					a.date_time, 
					a.qty, 
                    ifnull(sum(b.qty),'') as disposed,
					a.cost_value_each, 
					a.notes, 
					a.fk_disposal_deducted_from 
				from hire_items_acqs_disp as a
                left join hire_items_acqs_disp as b on b.fk_disposal_deducted_from = a.pk_id
                where a.fk_item_id = ".$pk_id."
                group by a.pk_id ";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function select_components_from( $pk_id )
	{
		$this->load->database();
		
		$query = "SELECT
					a.pk_id,
					a.fleet_number,
					a.description as label,
					ifnull(a.qty_required_as_component,0) as qty_required,
					ifnull(a.qty,0) as qty_stock
				 FROM hire_items as a
				 WHERE fk_hire_item_parent = ".$pk_id."
				 UNION ALL
				 SELECT 
					a.pk_id,
					'' as fleet_number,
					b.name as label,
					'' as qty_required,
					'' as qty_stock
				FROM hire_items_bundles_groups as a
				inner join hire_items_family_groups as b on b.pk_id = a.fk_hire_group_id
				where fk_bundle_id = ".$pk_id;
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function select_item_details( $pk_id )
	{
		$this->load->database();
		$query = $this->db->query( "SELECT 
									hi.pk_id as pk_id, 
									hi.fleet_number,
									hi.description,
									hi.qty_required_as_component,
									hi.purchase_date, 
									hi.cost, 
									hi.deprec_type, 
									hi.deprec_value, 
									hi.serial_number, 
									hi.power, 
									hi.fuse, 
									hi.flash_test, 
									hi.engine_speed, 
									hi.output_spindle, 
									hi.cable_type, 
									hi.cable_length, 
									hi.PPE_kit, 
									hi.safety_leaflet, 
									hi.test_frequency, 
									hi.last_test_date, 
									hi.repairs, 
									hi.average_days_hire, 
									hi.fk_hire_item_parent,
									ifnull(hi.qty,0) as qty,
									hit.description as 'item_type'
									 FROM 
										hire_items as hi
										inner join hire_items_type as hit on hit.pk_id = hi.fk_type
									WHERE hi.pk_id =".$pk_id);
		return !empty($query->result()) ? $query->row() : array();
	}
	
	public function select_items_multiple()
	{
		$this->load->database();
		$query = $this->db->query( "SELECT
					pk_id,
					fleet_number, 
					description as label, 
					ifnull(qty,0) as qty,
					fk_family_group, 
					purchase_date, 
					cost, 
					serial_number, 
					power, 
					fuse, 
					flash_test, 
					engine_speed, 
					output_spindle, 
					cable_type, 
					cable_length, 
					PPE_kit, 
					safety_leaflet, 
					test_frequency  FROM hire_items WHERE fk_type = 2 and fk_hire_item_parent is null;");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function select_single_items()
	{
		$this->load->database();
		$query = $this->db->query( "SELECT 				
					pk_id,
					fleet_number, 
					description as label, 
					ifnull(qty, 0) as qty, 
					fk_family_group, 
					purchase_date, 
					cost, 
					serial_number, 
					power, 
					fuse, 
					flash_test, 
					engine_speed, 
					output_spindle, 
					cable_type, 
					cable_length, 
					PPE_kit, 
					safety_leaflet, 
					test_frequency FROM hire_items WHERE fk_type = 1  and fk_hire_item_parent is null;");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function select_all_items()
	{
		$this->load->database();
		$query = "select 
				hi.pk_id,
				 hi.fleet_number,
				 hi.description , 
				 ifnull(hi.qty, 0) as qty, 
				 hifg.name as 'family_group',
				 hit.description as 'type'
				 from
					hire_items as hi
					inner join hire_items_family_groups as hifg on hifg.pk_id = hi.fk_family_group
					inner join hire_items_type as hit on hit.pk_id = hi.fk_type";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function update_accesory_qty($id, $qty)
	{
		$this->load->database();
		$query = "update hire_accesory_groups set qty = ".$qty." where pk_id = ".$id;
		return $this->db->query($query);
	}
	
	public function update_item_component($vars_array)
	{
		$this->load->database();
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items set qty_required_as_component = ".$this->db->escape_str($vars_array['qty'])." where pk_id = ".$this->db->escape_str($vars_array['item_id']);

		return $this->db->query($query);
	}
	
	public function update_group( $vars_array )
	{
		$this->load->database();
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items_family_groups set name = '".$this->db->escape_str($vars_array["name"])."', basic_rate = ".$this->db->escape_str($vars_array["basic_rate"]).", fk_vat_code_id = ".$this->db->escape_str($vars_array["vat_code_id"]).", fk_charging_band = ".$this->db->escape_str($vars_array["charging_band_id"])." where pk_id = ".$this->db->escape_str($vars_array["group_id"]);
		$query = str_replace("'NULL'", "NULL", $query);
		return $this->db->query($query);
	}
	
}