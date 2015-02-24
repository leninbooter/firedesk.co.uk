<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/common.css'); ?>">				
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/sticky-footer-navbar'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/justified-nav.css'); ?>">
		<link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
		<link href="<?php echo base_url('assets/datepicker/css/datepicker.css'); ?>" rel="stylesheet">
		<link href="<?php echo base_url('assets/css/firedesk.css'); ?>" rel="stylesheet">
		<link href="<?php echo base_url('assets/jquery-ui-1.11.3.custom/themes/themes/flick/jquery-ui.css'); ?>" rel="stylesheet">		
		 
		<title>FireDesk</title>
	</head>
	<body>
	<div class="container">
		<div class="masthead">
			<h3 class="text-muted">Firedesk <img src="<?php echo base_url('assets/images/shell_logo.jpg');?>" style="width:3%"/></h3>		
			<div role="navigation">
			  <ul class="nav nav-justified">
				<li class="dropdown"><a href="#" class="dropdown-toggle" aria-expanded="false" aria-expanded="false" data-toggle="dropdown">Notes & Contracts</a>
				<ul class="dropdown-menu" role="menu">
						<li><a href="<?php echo base_url('index.php/contracts/new_contract'); ?>">New contract</a></li>
						<li><a href="#">Cash sale</a></li>
						<li><a href="<?php echo base_url('index.php/contracts/list_all_contracts'); ?>">Existing contract</a></li>
						<li><a href="<?php echo base_url('index.php/contracts/list_live_contracts'); ?>">List:&nbsp;Live contracts</a></li>
						<li><a href="#" data-toggle="modal" data-target="#balance_orders_submenu">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Balance orders</a></li>
						<li><a href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;All</a></li>
						<li><a href="#">Find item</a></li>
						<li><a href="#">Off hire notes</a></li>						
						<li><a href="#">Turnover</a></li>
						<li><a href="#">Availability / Price</a></li>
						<li><a href="#">Cross Hire Menu</a></li>
					</ul>
				</li>
				<li class="dropdown"><a href="#" class="dropdown-toggle" aria-expanded="false" aria-expanded="false" data-toggle="dropdown">Quotations<br/>&nbsp;</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">New quotation</a></li>
						<li><a href="#">Existing quotations</a></li>
					</ul>
				</li>
				<li class="dropdown"><a href="#" class="dropdown-toggle" aria-expanded="false" aria-expanded="false" data-toggle="dropdown">Customer Details</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?php echo base_url('index.php/customers/new_existing'); ?>">New/existing customer details</a></li>
						<li><a href="<?php echo base_url('index.php/customers/list_names_address'); ?>">List customer names/addresses</a></li>
					</ul>
				</li>
				<li class="dropdown"><a href="#" class="dropdown-toggle" aria-expanded="false" aria-expanded="false" data-toggle="dropdown">Hire Fleet</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">Fleet records</a></li>
						<li><a href="#">Group changes</a></li>
						<li><a href="#">Discount groups</a></li>
						<li><a href="#">Safety Tests</a></li>
						<li><a href="#">Nett rates</a></li>
						<li><a href="#">Global</a></li>
						<li><a href="#">Fleet status</a></li>
						<li><a href="#">Price List</a></li>
						<li><a href="#">Reports</a></li>
					</ul>
				</li>
				<li class="dropdown"><a href="#" class="dropdown-toggle" aria-expanded="false" aria-expanded="false" data-toggle="dropdown">Cross Hire</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">New/existing order</a></li>
						<li><a href="#">Display existing orders</a></li>
						<li><a href="#">Items on hire to customers</a></li>
					</ul>
				</li>
				<li><a href="#" class="dropdown-toggle" aria-expanded="false" aria-expanded="false" data-toggle="dropdown">Sales Stock</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?php echo base_url('index.php/sales_stock/new_existing'); ?>">Stock Item</a></li>
						<li><a href="<?php echo base_url('index.php/family_groups/groups'); ?>">Group Changes/order</a></li>
						<li><a href="<?php echo base_url('index.php/discount_groups/groups'); ?>">Discount groups</a></li>
						<!--<li><a href="#">Add stock quantities</a></li>
						<li><a href="#">Global</a></li>
						<li><a href="#">Sold since</a></li>
						<li><a href="#">Price & stock enquiry</a></li>
						<li><a href="#">Price list</a></li>-->
						<li><a href="<?php echo base_url('index.php/sales_stock/report_stock_levels'); ?>">Reports</a></li>
					</ul>
				</li>
				<li><a href="#" class="dropdown-toggle" aria-expanded="false" aria-expanded="false" data-toggle="dropdown">Purchases & Suppliers</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?php echo base_url('index.php/suppliers/new_existing'); ?>">New/Existing supplier details</a></li>
						<li><a class="dropdown-toggle" href="<?php echo base_url('index.php/purchases_orders/new_order'); ?>">Purchase orders</a></li>
						<li><a href="<?php echo base_url('index.php/suppliers/list_suppliers_addresses'); ?>">List supplier names/addresses</a></li>
					</ul>
				</li>
				<li><a href="#">Servicing<br/>&nbsp;</a></li>
				<li><a href="#">Ledgers & Cashbook</a></li>
				<li><a href="#">Diary Messages</a></li>
				<li><a href="#">Reports<br/>&nbsp;</a></li>
			  </ul>
			</div>
		  </div>		