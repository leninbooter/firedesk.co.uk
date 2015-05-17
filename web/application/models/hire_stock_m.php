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
		
		$query =  $this->company_db->query( "SELECT pk_id, fleet_number as 'number', description as 'label' FROM hire_items WHERE fk_hire_item_parent is null;");
		return !empty($query->result()) ? $query->result() : array();
	}	    
	
	/**
    *
    * Retrieves an object array with the accesories related to a hire items group
    *
    * @param rowID of the group
    *
    * @return Object array with every row by accesories
    *
    */
    function get_accesories_from_group( $group_id, $salePrices = false, $bundleID = false )
	{	
        if ( $bundleID != false) {
            
            $query = "SELECT fk_hire_group_id 
                            FROM hire_items_bundles_groups
                            WHERE fk_bundle_id = {$bundleID}";
                            
            $bundleComponents = $this->company_db->query($query)->result_array();
            
            if ( !empty($bundleComponents)) {
                
                $groupsIDin = "(";        
                foreach( $bundleComponents as $i ) {
                
                    $groupsIDin .= $i['fk_hire_group_id'].",";
                    
                }
                
                $groupsIDin[strlen($groupsIDin)-1] = ")";
            }else {
               
                $groupsIDin = "(-1)";
            }
            
            $queryLastPartWhere = " and hag.fk_hire_family_group_id in {$groupsIDin} ";
        }else {
            
            $queryLastPartWhere = " and hag.fk_hire_family_group_id = {$group_id} ";
        }
	
        //hag.item_type as 'item_type',
        $queryHireItems = "SELECT 
                                
                                2 as 'item_type',
                                hag.fk_item_id as 'pk_id',
                                hi.fleet_number as 'number',
                                hi.description as 'description',
                                hag.qty as 'required_qty',
                                hi.qty as 'balance_qty'";
        
        $querySalesItems = "SELECT
                                1 as 'item_type',
                                hag.fk_item_id as 'pk_id',
                                ss.stock_number as 'number',
                                ss.description as 'description',
                                hag.qty as 'required_qty',
                                ss.quantity_balance as 'balance_qty'";
    
        if( $salePrices ) {
             $queryHireItems .= ", CASE  WHEN hi.basic_rate is null THEN hifg.basic_rate 
                                                WHEN hi.basic_rate is not null THEN hi.basic_rate END as rate";
            $querySalesItems .= ", '' as rate";
        }
        
		$query = $queryHireItems.
                " FROM hire_accesory_groups AS hag
					INNER JOIN hire_items AS hi ON hi.pk_id = hag.fk_item_id
                    INNER JOIN hire_items_family_groups as hifg ON hifg.pk_id = hag.fk_hire_family_group_id
				WHERE hag.item_type = 1 AND hag.accesory_parent is null {$queryLastPartWhere}
				UNION ALL ".
				$querySalesItems.
				" FROM hire_accesory_groups as hag
					inner join sales_stock as ss on ss.pk_id = hag.fk_item_id
				WHERE hag.item_type = 2 and hag.accesory_parent is null {$queryLastPartWhere}";
				
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
					test_frequency,
					qty					)
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
					'". $this->company_db->escape_str($vars_array['test_frequency'])."',
					". $this->company_db->escape_str($vars_array['qty'])."
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
    
    function isMultipleTypeEmpty( $itemID ) {
        
        $query  = "SELECT pk_id FROM hire_items WHERE fk_hire_item_parent = {$itemID} LIMIT 1
                   UNION ALL
                   SELECT pk_id FROM hire_items_bundles_groups WHERE fk_bundle_id = {$itemID} LIMIT 1";
        $r1      = $this->company_db->query($query);
        
        
        if ( empty($r1->result())  ) {
            
            return true;
        }else {
            
            return false;
        }
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
	
	
	
	/*function save_item( $vars_array )		
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
	}*/
	
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
	
	function select_activity($from, $to, $item_id)
	{	
		log_message('debug', "AQUI DB");
		$from = explode("/", $from);
		$to = explode("/", $to);
		
		$query = "SELECT year, month, hired_days FROM hire_items_activity WHERE fk_hire_item_id = $item_id and ";
		
		if($from[1] == $to[1])
		{
			$query = $query."(year = $from[1] and month between $from[0] and $to[0])";
		}
		else
		{
			$query = $query . "((year = $from[1] and month between $from[0] and 12) or (year = $to[1] and month between 1 and $to[0]))";
		}
		
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();		
	}
    
    function selChargingBand ($itemID) {
        
        $query = 'SELECT
                        _4hr, _8hr, _1day, _2day, _3day, _4day, _5day, _6day,
                        week, weekend, subsequent_days, ifnull(days_per_week,7) as days_week, thereafter, 
                        min_days
                  FROM hire_items_charging_bands as hicb
                    INNER JOIN hire_items_family_groups as hifg ON hifg.fk_charging_band = hicb.pk_id
                    INNER JOIN hire_items as hi ON hi.fk_family_group = hifg.pk_id AND hi.pk_id = '.$itemID;
        return $this->company_db->query($query)->row_array();
    }
    
    function selectItemComponentsForContract( $pk_id ) {
        
        $querySelect = "SELECT
                            a.pk_id,
                            a.fleet_number,
                            a.description as label,
                            ifnull(a.qty_required_as_component,0) as qty_required,
                            ifnull(a.qty,0) as qty_stock";
        
        $itemDetails = $this->select_item_details($pk_id);
        
        if ($itemDetails->fk_type == 4) {
                $query = "SELECT fk_hire_group_id 
                            FROM hire_items_bundles_groups
                            WHERE fk_bundle_id = {$pk_id}";
                            
                $bundleComponents = $this->company_db->query($query)->result_array();
                
                if ( !empty($bundleComponents) ) {
                    $groupsIDin = "(";                
                    foreach( $bundleComponents as $i  ) {
                        $groupsIDin .= $i['fk_hire_group_id'].",";
                        
                    }                                
                    $groupsIDin[strlen($groupsIDin)-1] = ")";
                }else {
                    
                    $groupsIDin = "(-1)";
                }
                $queryFrom =  " FROM hire_items as a
                             INNER JOIN hire_items_family_groups as hifg ON hifg.pk_id = a.fk_family_group
                          WHERE a.fk_family_group IN {$groupsIDin}
                            AND fk_type = 1";
                
        }else {
            
            $queryFrom = " FROM hire_items as a
                                INNER JOIN hire_items_family_groups as hifg ON hifg.pk_id = a.fk_family_group
                             WHERE fk_hire_item_parent = {$pk_id}";
        }
        
         if($itemDetails->basic_rate > 0) {
           
            // If the kit or bundle has a basic_rate > 0
            // we use that basic rate as the sales price and the components items
            // must show a price of 0
            $querySelect .= ", 0 as rate ";

        }else {

            $querySelect .= ", CASE WHEN a.basic_rate is null THEN hifg.basic_rate 
                                        WHEN a.basic_rate is not null THEN a.basic_rate END as rate ";
        }
        
        log_message('debug', $querySelect.$queryFrom);
		$query =  $this->company_db->query($querySelect.$queryFrom);
		return !empty($query->result()) ? $query->result() : array();
    }
    
    /**
    *
    * Query the components of a bundle or kit item
    *
    * @param    string  $pk_id      parent  hire item ID,
    *           boolean $salePrices indicates whether to retrieve prices for sales
    *                               or the base rate of the elements.
    *
    * @return An object array with the components
    *
    */
    
	function select_components_from( $pk_id ) {   
        
        $itemDetails = $this->select_item_details($pk_id);
        
        if ( $itemDetails->fk_type == 3) {
            
            $query = "SELECT
                        a.pk_id,
                        a.fleet_number,
                        a.description as label,
                        ifnull(a.qty_required_as_component,0) as qty_required,
                        ifnull(a.qty,0) as qty_stock
                    FROM hire_items as a
                        INNER JOIN hire_items_family_groups as hifg ON hifg.pk_id = a.fk_family_group
                    WHERE fk_hire_item_parent = {$pk_id}";
                                 
        }elseif ( $itemDetails->fk_type == 4 ) {
            
            $query = "SELECT 
                        a.pk_id,
                        '' as fleet_number,
                        hifg.name as label,
                        '' as qty_required,
                        '' as qty_stock
                    FROM hire_items_bundles_groups as a
                            INNER JOIN hire_items_family_groups as hifg on hifg.pk_id = a.fk_hire_group_id
                    WHERE fk_bundle_id ={$pk_id}";
            
        }else {
            
            return array();
        }
        log_message('debug', $query);
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
                                    hi.fk_type,
									hit.description as 'item_type',
                                    hi.basic_rate
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
    
    /**
    *
    * Retrieve from the db the single items that belongs to a specified group
    *
    * @param Group ID
    *
    * @return An array object with the rows found
    *
    */
    function selectSingleItemsFromGroup( $groupID ) {
        
        $query = $this->company_db->query("SELECT
                                            hi.pk_id, 
                                            hi.fleet_number,
                                            hi.description
                                           FROM hire_items as hi 
                                           INNER JOIN hire_accesory_groups AS hag 
                                            ON hag.fk_hire_family_group_id = hi.pk_id
                                           WHERE 
                                            hi.fk_type = 1 
                                            AND hag.fk_hire_family_group_id = $groupID");
        return !empty($query->result()) ? $query->result() : array();
    }
	
	function select_all_items()
	{
		
		$query = "SELECT 
                    hi.pk_id,
                    hi.fleet_number,
                    hi.description , 
                    ifnull(hi.qty, 0) as qty, 
                    hi.fk_family_group as 'family_group_id',
                    hifg.name as 'family_group',
                    hit.description as 'type', 
                    CASE WHEN hi.basic_rate is null AND hi.fk_type < 3  THEN hifg.basic_rate 
                         WHEN hi.basic_rate is not null AND hi.fk_type < 3 THEN hi.basic_rate 
                         WHEN hi.basic_rate is not null AND hi.fk_type >= 3 THEN hi.basic_rate
                         WHEN hi.basic_rate = 0.00 OR hi.basic_rate is null THEN 0.00  END as rate
				 FROM
					hire_items as hi
					inner join hire_items_family_groups as hifg on hifg.pk_id = hi.fk_family_group
					inner join hire_items_type as hit on hit.pk_id = hi.fk_type
				 /*WHERE fk_hire_item_parent is null;*/";
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
					 when 3 then ifnull(hi.basic_rate, 0.00)
					 when 4 then ifnull(hi.basic_rate, 0.00) end as basic_rate,
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