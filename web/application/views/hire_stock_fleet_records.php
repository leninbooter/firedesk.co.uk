<div class="row">
	<div class="col-md-6"><h1>Fleet Records</h1></div>
	<div class="col-md-6">
	<?php if(isset($editing)): ?>
		<?php if($editing): ?>
			<div class="alert alert-warning" role="alert"><b>Editing <?php echo $item[0]->description; ?>.</b><p>Unless you change the number of the item, all changes made will overwrite existing data.</p></div>
		<?php endif; ?>
	<?php endif; ?>
			
	</div>
</div>
<div class="row">
	<div class="col-md-9">
		<div class="row">
			<div class="col-md-12">
				<div id="gridbox" style="width:100%;height:350px; box-sizing:content-box !important"></div>
				<br>
			</div>
		</div>
				
	</div>
	<div class="col-md-3">
		<div class="row">
			<div class="col-md-12">
				<a href="<?php echo base_url('index.php/hire_stock/new_item'); ?>" class="btn btn-default btn-block btn-sm" role="button">New item</a>
				<a href="" id="print_prices_list_pdf_btn" data-toggle="modal" data-target="#multiporpuses_modal" class="btn btn-default btn-block btn-sm" role="button">Print price list</a>
			</div>
		</div>
		<div class="row">	
			<div class="col-md-12">
			<br>
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Help</h3>				
					</div>
					<div class="panel-body">
						<dl>
						  <dt>Stock Number</dt><dd>This field can be any combinationof letters andnumbersupto 15 characters.</dd>
						  <br/>				  
						</dl>
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>

<div class="modal fade" id="items_menu_modal" tabindex="-1" role="dialog" aria-labelledby="items_menu_modal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">		
			<div class="modal-body">
				<div class="list-group">
					<a href="javascript:go_to(1);" class="list-group-item">Activity</a>
					<a href="javascript:go_to('add_remove');"" class="list-group-item">Addition / Disposal</a>
					<a href="javascript:go_to(3);"" class="list-group-item">Edit</a>
					<a href="javascript:go_to(4);"" class="list-group-item">History</a>
				</div>								
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="components_modal" tabindex="-1" role="dialog" aria-labelledby="components_modal" aria-hidden="true">
	<div class="modal-dialog">
		<form  role="form" id="components_form">
		<input type="hidden" id="parent_item" name="parent_item" value=""/>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Components & quantities required</h4>
			</div>
			<div class="modal-body">
			<table id="items_table" class="table table-condensed">
				<thead>
					<tr>
						<th style="width:10%">Fleet number</th>
						<th style="width:50%">Description</th>
						<th style="width:20%">Total stock</th>
						<th style="width:10%">Required</th>
						<th style="width:10%"></th>
					</tr>
				</thead>
				<tbody>
				
				</tbody>
			</table>
				<div class="form-group">
				<label>Search an item to add it <span style="font-size:150%">&darr;</span></label>
				<input type="text" id="search_hire_item" class="form-control input-sm" />
				</div>
				<button type="submit" class="btn btn-primary btn-block">Save components</button>
			</div>
		</div>
		</form>
	</div>
</div>