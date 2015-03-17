<h1>Discount groups & descriptions</h1>
<form role="form" id="new_discount_group" method="post" action="<?php echo base_url('index.php/discount_groups/update_groups'); ?>">	
<div class="row">
	<div class="col-md-8">
		<div class="row">
			<div class="col-md-1"><h3>No.</h3></div>
			<div class="col-md-8"><h3>Description</h3></div>
			<div class="col-md-3"></div>
		</div>
		<?php foreach($groups as $row): ?>
			<div class="row">
				<div class="col-md-1"><input type="hidden" id="pk_id" name="pk_id[]" value="<?php echo $row->pk_id;?>"><?php echo $row->pk_id;?>)</div>
				<div class="col-md-5"><div class="form-group"><input type="text" class="form-control" id="description" name="description[]" value="<?php echo $row->description;?>" readonly="true" onclick="$(this).prop('readonly',false); $('input[id=discount_percentage]', $(this).parent().parent().parent()).prop('readonly',false);"></div></div>
				<div class="col-md-3"><div class="form-group form-horizontal"><input type="text" class="form-control" id="discount_percentage" name="discount_percentage[]" value="<?php echo $row->discount_percentage;?>%" readonly="true" onclick="$(this).prop('readonly',false); $('input[id=description]', $(this).parent().parent().parent()).prop('readonly',false);$(this).select()"></div></div>
				<div class="col-md-3"><div class="form-group"><button id="remove_description_btn" name="remove_description_btn[]" class="btn btn-default" type="button">Remove description</button></div></div>
			</div>
		<?php endforeach; ?>	
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-8"><div class="form-group"><button type="submit" class="btn btn-primary  btn-block">Update</button></div><!--<a class="btn btn-default" role="button" href="javascript:history.back();">Go back</a>--></div>
			<div class="col-md-3"></div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Help</h3>				
			</div>
			<div class="panel-body">
				<dl>
				  <dt>Edit:</dt><dd>Click on any field of group description to edit it.</dd>
				</dl>
			</div>
		</div>
	</div>
</div>
	
</form>
