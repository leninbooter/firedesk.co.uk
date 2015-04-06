<?php if(!empty($events)): ?>
<div class="panel-group" id="diary_events_accordion" role="tablist" aria-multiselectable="true">
  <?php $i = 1; ?>
  <?php foreach($events as $e): ?>
	  <div class="panel panel-default <?php echo $user_id != $e->fk_user_id ? "panel-success":""; ?>">
		<div class="panel-heading" role="tab" id="<?php echo "heading_$i"; ?>">
		  <div class="row">
			<div class="col-md-11">
				<h4 class="panel-title">
				<a class="collapsed" data-toggle="collapse" data-parent="#diary_events_accordion" href="#<?php echo "collapse_$i"; ?>" aria-expanded="false" aria-controls="<?php echo "collapse_$i"; ?>">
				  <?php echo $e->title; ?>
				</a>			
			  </h4>
			</div>
			<div class="col-md-1">
				<?php if($e->type=="1"): ?>
					<span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
				<?php endif; ?>
			</div>
		  </div>		  
		  <small><strong>From: </strong><?php echo date('D, d/M/Y \a\t H:i', strtotime( $e->start_datetime)); ?> <strong>To: </strong><?php echo date('D, d/M/Y \a\t H:i', strtotime( $e->end_datetime)); ?></small>
		</div>
		<div id="<?php echo "collapse_$i"; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?php echo "heading_$i"; ?>">
		  <div class="panel-body">
			<?php echo $e->description; ?>
			<?php if(!empty($e->guests)): ?>
				<h5>Guests</h5>
				<ul>
					<?php foreach($e->guests as $g): ?>
						<li><?php echo $g->name; ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if($e->name != "self"): ?>
				<em><?php echo $e->name; ?> invited you.</em>
			<?php else: ?>			
				<button type="button" class="btn btn-danger btn-sm btn-block" onclick="delete_event(this,<?php echo $e->pk_id; ?>)">Delete</button>
			<?php endif; ?>			
		  </div>
		</div>
	  </div>
	  <?php $i++; ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>