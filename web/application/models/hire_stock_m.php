<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hire_stock_m extends CI_Model
{		
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function delete_accesory( $id )
	{
		
		return  $this->company_db->query( "delete from hire_accesory_groups where pk_id = $id or accesory_parent = $id");
		
	}
	
	function delete_group( $id )
	{
		
		return  $this->company_db->query( "delete from hire_items_family_groups where pk_id = $id and qty = 0 or qty is null");		
	}
	
	function delete_component_group( $vars_array )
	{
		
		array_walk($vars_array, "clean_vars");
		$query = "delete from hire_items_bundles_groups where pk_id = ". $this->company_db->escape_str($vars_array['item_id']);

		return  $this->company_db->query($query);
	}
	
	function delete_item_component($vars_array)
	{
		
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items set fk_hire_item_parent = NULL, qty_required_as_component = NULL where pk_id = ". $this->company_db->escape_str($vars_array['item_id']);

		return  $this->company_db->query($query);
	}
	
	function delete_stock( $vars_array )
	{
		
		
		 $this->company_db->trans_start();
		$query = "insert into hire_items_acqs_disp(type, date_time, fk_item_id, qty, cost_value_each, notes, fk_disposal_deducted_from)
					values(2, '". $this->company_db->escape_str($vars_array['date_time'])."', ". $this->company_db->escape_str($vars_array['item_id']).", ". $this->company_db->escape_str($vars_array['qty']).", ". $this->company_db->escape_str($vars_array['cost_value_each']).", '". $this->company_db->escape_str($vars_array['notes'])."', ". $this->company_db->escape_str($vars_array['acquisition_id']).");";
		 $this->company_db->query($query);
		$query = "update hire_items set qty = ifnull(qty,0) - ". $this->company_db->escape_str($vars_array['qty'])." where pk_id = ". $this->company_db->escape_str($vars_array['item_id']);
		 $this->company_db->query($query);
		 $this->company_db->trans_complete();
		return  $this->company_db->trans_status();
	}
	
	function get_groups_all( )
	{
		
		$query =  $this->company_db->query( "SELECT
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
		
	function get_items_all( )
	{
		
		$query =  $this->company_db->query( "SELECT pk_id, fleet_number as 'number', description as 'label', 1 as 'origin' FROM hire_items;");
		return !empty($query->result()) ? $query->result() : array();
	}	
	
	function get_accesories_from_group( $group_id )
	{
		
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
				
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	function ins_hire_item( $vars_array )
	{
		
		
		array_walk($vars_array, "clean_vars");
		
		 $this->company_db->trans_start();
		$query = "insert into hire_items (
					fk_type,
					fleet_number, 
					description,
					fk_family_group, 
					basic_rate,
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
					". $this->company_db->escape_str($vars_array['hire_item_type']).",
					'". $this->company_db->escape_str($vars_array['fleet_number'])."',
					'". $this->company_db->escape_str($vars_array['description'])."',
					". $this->company_db->escape_str($vars_array['group_id']).",
					". $this->company_db->escape_str($vars_array['basic_rate']).",
					'". $this->company_db->escape_str($vars_array['purchase_date'])."',
					". $this->company_db->escape_str($vars_array['cost_price']).",
					'". $this->company_db->escape_str($vars_array['serial_number'])."',
					'". $this->company_db->escape_str($vars_array['power'])."',
					'". $this->company_db->escape_str($vars_array['fuse'])."',
					'". $this->company_db->escape_str($vars_array['flash_test'])."',
					'". $this->company_db->escape_str($vars_array['engine_speed'])."',
					'". $this->company_db->escape_str($vars_array['output_spindle'])."',
					'". $this->company_db->escape_str($vars_array['cable_type'])."',
					'". $this->company_db->escape_str($vars_array['cable_length'])."',
					'". $this->company_db->escape_str($vars_array['ppe_kit'])."',
					'". $this->company_db->escape_str($vars_array['safety_leaflet'])."',
					'". $this->company_db->escape_str($vars_array['test_frequency'])."'
				);";
		$query = str_replace("'NULL'", "NULL", $query);
		if( ! $this->company_db->query($query) )
			return false;
		
		$query =  $this->company_db->query("select last_insert_id() as pk_id;");
		$pk_id = $query->row()->pk_id;
		 $this->company_db->trans_complete();
		return $pk_id;
	}
	
	function insert_group( $vars_array )
	{
		
		$query =  $this->company_db->query("select pk_id from hire_items_family_groups where name = '". $this->company_db->escape_str($vars_array['name'])."';");
		$result = $query->result();
		if( !empty($result) )
		{
			return "exists";
		}
		else{
			array_walk($vars_array, "clean_vars");
			$query ="insert into hire_items_family_groups (name, basic_rate, fk_charging_band, fk_vat_code_id)
						values(
							'". $this->company_db->escape_str($vars_array['name'])."',
							". $this->company_db->escape_str($vars_array['basic_rate']).",
							". $this->company_db->escape_str($vars_array['charging_band_id']).",
							". $this->company_db->escape_str($vars_array['vat_code_id'])."
						)";
						
			$query = str_replace("'NULL'", "NULL", $query);				
			$query =  $this->company_db->query($query);
			$query = "select last_insert_id() as 'result'";
			$query =  $this->company_db->query($query);
			$result = $query->result();
			if( !empty($result) )
			{
				$result = $query->row();
				mysqli_next_result(  $this->company_db->conn_id );
				return is_numeric($result->result) ? $result->result : false;
			}
		}
		return false;
	}
	
	function ins_accesory_to_group( $vars_array )
	{
		
		array_walk($vars_array, "clean_vars");
		$query ="insert into hire_accesory_groups (fk_hire_family_group_id, item_type, fk_item_id, qty)
					values(
						". $this->company_db->escape_str($vars_array['group_id']).",
						". $this->company_db->escape_str($vars_array['item_type']).",
						". $this->company_db->escape_str($vars_array['item_id']).",
						". $this->company_db->escape_str($vars_array['qty'])."
					);";
		
		$query = str_replace("'NULL'", "NULL", $query);
		if( $this->company_db->query($query))
		{
			$query =  $this->company_db->query("select last_insert_id() as 'result';");
			$acc_parent = $query->row()->result;
			
			if($vars_array['qty'] == "NULL")
			{
				$query = "insert into hire_accesory_groups(fk_hire_family_group_id, item_type, fk_item_id, qty, accesory_parent)
							select 
								". $this->company_db->escape_str($vars_array['group_id']).",
								1,
								pk_id,
								0, 
								".$acc_parent."
							from hire_items
							where fk_hire_item_parent = ". $this->company_db->escape_str($vars_array['item_id']);

				if(! $this->company_db->query($query))
					return false;
			}
		}
		return true;
	}

	function ins_initial_qty( $vars_array )
	{
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items set qty = ". $this->company_db->escape_str($vars_array['qty'])." where pk_id = ". $this->company_db->escape_str($vars_array['parent_item']);
		
		return  $this->company_db->query($query);
	}
	
	function ins_component_group( $vars_array )
	{
		
		array_walk($vars_array, "clean_vars");
		$query = "insert into hire_items_bundles_groups(fk_bundle_id, fk_hire_group_id) values (". $this->company_db->escape_str($vars_array['parent_item']).", ". $this->company_db->escape_str($vars_array['item_id'])."); ";
		log_message('debug', $query);
		return  $this->company_db->query($query);
	}
	
	function ins_item_component($vars_array)
	{
		
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items set fk_hire_item_parent = ". $this->company_db->escape_str($vars_array['parent_item']).", qty_required_as_component = ". $this->company_db->escape_str($vars_array['qty'])." where pk_id = ". $this->company_db->escape_str($vars_array['item_id']);

		return  $this->company_db->query($query);
	}
	
	function insert_charging_band( $vars_array )
	{
		
		
		array_walk($vars_array, "clean_vars");
		
		$query =  $this->company_db->query("select pk_id from hire_items_charging_bands where name = '". $this->company_db->escape_str($vars_array['name'])."';");
		$result = $query->result();
		if( !empty($result) )
		{
			return "exists";
		}
		else{
			$query = "insert into hire_items_charging_bands (name, _4hr, _8hr, _1day, _2day, _3day, _4day, _5day, _6day, week, weekend, subsequent_days, days_per_week, thereafter, min_days)
									values('". $this->company_db->escape_str($vars_array["name"])."',
										". $this->company_db->escape_str($vars_array["_4hr_perc"]).",
										". $this->company_db->escape_str($vars_array["_8hr_perc"]).",
										". $this->company_db->escape_str($vars_array["_1day_perc"]).",
										". $this->company_db->escape_str($vars_array["_2day_perc"]).",
										". $this->company_db->escape_str($vars_array["_3day_perc"]).",
										". $this->company_db->escape_str($vars_array["_4day_perc"]).",
										". $this->company_db->escape_str($vars_array["_5day_perc"]).",
										". $this->company_db->escape_str($vars_array["_6day_perc"]).",
										". $this->company_db->escape_str($vars_array["_week_perc"]).",
										". $this->company_db->escape_str($vars_array["_weekend_perc"]).",
										". $this->company_db->escape_str($vars_array["_subsequent_perc"]).",
										". $this->company_db->escape_str($vars_array["days_week"]).",
										". $this->company_db->escape_str($vars_array["thereafter"]).",
										". $this->company_db->escape_str($vars_array["min_days"])."
									)";
			$query = str_replace("'NULL'", "NULL", $query);				
			$query =  $this->company_db->query($query);
			$query = "select last_insert_id() as 'result'";
			$query =  $this->company_db->query($query);
			$result = $query->result();
			if( !empty($result) )
			{
				$result = $query->row();
				mysqli_next_result(  $this->company_db->conn_id );
				return is_numeric($result->result) ? $result->result : false;
			}
		}
		return false;
	}
	
	function insert_stock( $vars_array )
	{
		
		
		 $this->company_db->trans_start();
		$query = "insert into hire_items_acqs_disp(type, date_time, fk_item_id, qty, cost_value_each, notes)
					values(1, '". $this->company_db->escape_str($vars_array['date_time'])."', ". $this->company_db->escape_str($vars_array['item_id']).", ". $this->company_db->escape_str($vars_array['qty']).", ". $this->company_db->escape_str($vars_array['cost_value_each']).", '". $this->company_db->escape_str($vars_array['notes'])."');";
		 $this->company_db->query($query);
		$query = "update hire_items set qty = ifnull(qty,0) + ". $this->company_db->escape_str($vars_array['qty'])." where pk_id = ". $this->company_db->escape_str($vars_array['item_id']);
		 $this->company_db->query($query);
		 $this->company_db->trans_complete();
		return  $this->company_db->trans_status();
	}
	
	function select_elegible_items()
	{
		
		$query = "SELECT pk_id, fleet_number as 'number', description as 'label', 1 as 'origin', fk_type as 'item_type' FROM hire_items WHERE fk_type = 2
					UNION ALL
				SELECT pk_id, stock_number as 'number', description as 'label', 2 as 'origin', NULL as 'item_type' FROM sales_stock;";
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_childrens_items_from_accesory( $parent_id )
	{
		
		$query = "select
					hag.pk_id as 'pk_id',
					hi.fleet_number as 'number',
					hi.description as 'description',
					hag.qty as 'qty'
				from hire_accesory_groups as hag
					inner join hire_items as hi on hi.pk_id = hag.fk_item_id
				where hag.item_type = 1 and hag.accesory_parent = ".$parent_id;
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_charging_bands_all()
	{
		
		$query =  $this->company_db->query("select pk_id, name from hire_items_charging_bands;");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	
	
	function save_item( $vars_array )		
	{		
		
		array_walk($vars_array, "clean_vars");
		
		$query = "call ins_stock_item(
									'". $this->company_db->escape_str($vars_array["stock_number"])."',
									'". $this->company_db->escape_str($vars_array["location"])."',
									'". $this->company_db->escape_str($vars_array["description"])."',
									 ". $this->company_db->escape_str($vars_array["quantity_rec_level"]).",
									 ". $this->company_db->escape_str($vars_array["standard_price"]).",
									 ". $this->company_db->escape_str($vars_array["special_price"]).",
									 ". $this->company_db->escape_str($vars_array["units_of_for_special"]).",
									 ". $this->company_db->escape_str($vars_array["cost_price_a"]).",
									 ". $this->company_db->escape_str($vars_array["cost_price_b"]).",
									 ". $this->company_db->escape_str($vars_array["cost_price_c"]).",
									 ". $this->company_db->escape_str($vars_array["fk_vat_code"]).",
									 ". $this->company_db->escape_str($vars_array["fk_family_group"]).",
									 ". $this->company_db->escape_str($vars_array["fk_discount_group"]).",
									 ". $this->company_db->escape_str($vars_array["fk_supplier_a"]).",
									 ". $this->company_db->escape_str($vars_array["fk_supplier_b"]).",
									 ". $this->company_db->escape_str($vars_array["fk_supplier_c"])."
									);";
		$query = str_replace("'NULL'", "NULL", $query);
		$query =  $this->company_db->query($query);
						
		$result = $query->result();
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
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
	
	function select_acqs_rems( $pk_id )
	{
		
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
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function select_components_from( $pk_id )
	{
		
		
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
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function select_item_details( $pk_id )
	{
		
		$query =  $this->company_db->query( "SELECT 
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
	
	function select_items_multiple()
	{
		
		$query =  $this->company_db->query( "SELECT
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
	
	function select_single_items()
	{
		
		$query =  $this->company_db->query( "SELECT 				
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
	
	function select_all_items()
	{
		
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
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function sel_all_prices()
	{
		$query = "SELECT 
				hifg.name family,
				/*hifg.basic_rate as family_rate,*/
				if(hi.fk_hire_item_parent is null, hi.pk_id, hi.fk_hire_item_parent) as parent,
				hi.pk_id,
				hi.fleet_number, 
				hi.description item_description,
				case hi.fk_type 
					when 1 then ifnull(hi.basic_rate, hifg.basic_rate)
					 when 2 then ifnull(hi.basic_rate, hifg.basic_rate)
					 when 3 then ifnull(hi.basic_rate, 0)
					 when 4 then ifnull(hi.basic_rate, hifg.basic_rate) end as basic_rate,
				ht.description name_type
				FROM hire_items as hi
				inner join hire_items_family_groups as hifg on hi.fk_family_group = hifg.pk_id
				inner join hire_items_type as ht on ht.pk_id = hi.fk_type
				order by 2,3 desc";
				
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function update_accesory_qty($id, $qty)
	{
		
		$query = "update hire_accesory_groups set qty = ".$qty." where pk_id = ".$id;
		return  $this->company_db->query($query);
	}
	
	function update_item_component($vars_array)
	{
		
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items set qty_required_as_component = ". $this->company_db->escape_str($vars_array['qty'])." where pk_id = ". $this->company_db->escape_str($vars_array['item_id']);

		return  $this->company_db->query($query);
	}
	
	function update_group( $vars_array )
	{
		
		
		array_walk($vars_array, "clean_vars");
		$query = "update hire_items_family_groups set name = '". $this->company_db->escape_str($vars_array["name"])."', basic_rate = ". $this->company_db->escape_str($vars_array["basic_rate"]).", fk_vat_code_id = ". $this->company_db->escape_str($vars_array["vat_code_id"]).", fk_charging_band = ". $this->company_db->escape_str($vars_array["charging_band_id"])." where pk_id = ". $this->company_db->escape_str($vars_array["group_id"]);
		$query = str_replace("'NULL'", "NULL", $query);
		return  $this->company_db->query($query);
	}
	
}