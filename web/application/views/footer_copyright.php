		<footer class="footer">
			<p>&copy; Firedesk 2014&nbsp;&nbsp;|&nbsp;&nbsp;<strong>User: </strong><?php echo $this->nativesession->get('user')['userfullname']; ?>&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="<?php echo base_url('index.php/utilities/config'); ?>">			
			<button type="button" class="btn btn-default">
				<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Utilities
			</button></a>
			<a href="<?php echo base_url('index.php/sessions/end'); ?>">			
			<button type="button" class="btn btn-default">
				<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Log out
			</button></a>
			</p>			
		</footer>	