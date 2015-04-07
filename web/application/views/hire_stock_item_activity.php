<?php if(isset($custom_css)): ?>
			<?php foreach($custom_css as $file): ?>
				<link href="<?php echo base_url($file); ?>" rel="stylesheet">		
			<?php endforeach; ?>
		<?php endif; ?>
		
<div class="modal-body">		
	<input type="hidden" id="hire_item_id" name="hire_item_id" value="<?php echo $hire_item_id; ?>">
	<input type="hidden" id="from" name="from" value="<?php echo $from; ?>">
	<input type="hidden" id="to" name="to" value="<?php echo $to; ?>">
	<h2 class="text-center">Days on hire for <?php echo $item_name; ?></h2>
	<h3 class="text-center">From: 
	<select id="select_month_from" style="-webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding: 2px 3px 2px 2px;
    border: none;">
		<?php for($i=1; $i<=12; $i++): ?>
			<option value="<?php echo $i; ?>" <?php echo $i == $from[0] ? "selected" : ""; ?>><?php echo DateTime::createFromFormat('m',explode("/",$i)[0])->format('M'); ?></option>
		<?php endfor; ?>
	</select>
	/
	<select id="select_year_from" style="-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		padding: 2px 3px 2px 2px;
		border: none;">
		<?php for($i=2014; $i<=2024; $i++): ?>
			<option value="<?php echo $i; ?>" <?php echo $i == $from[1] ? "selected" : ""; ?>><?php echo $i; ?></option>
		<?php endfor; ?>	
		</select>
	<?php //echo DateTime::createFromFormat('m',explode("/",$from)[0])->format('')."/ ".explode("/",$from)[1]; ?> To: 
		<select id="select_month_to" style="-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		padding: 2px 3px 2px 2px;
		border: none;">
		<?php for($i=1; $i<=12; $i++): ?>
			<option value="<?php echo $i; ?>" <?php echo $i == $to[0] ? "selected" : ""; ?>><?php echo DateTime::createFromFormat('m',explode("/",$i)[0])->format('M'); ?></option>
		<?php endfor; ?>	
		</select>
		/
		<select id="select_year_to" style="-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		padding: 2px 3px 2px 2px;
		border: none;">
		<?php for($i=2014; $i<=2024; $i++): ?>
			<option value="<?php echo $i; ?>" <?php echo $i == $to[1] ? "selected" : ""; ?>><?php echo $i; ?></option>
		<?php endfor; ?>	
		</select>
	<?php // echo DateTime::createFromFormat('m',explode("/",$to)[0])->format('')."/ ".explode("/",$to)[1]; ?></h3>
	<div id="chartDiv" style="width:100%;height:200px"></div>
</div>