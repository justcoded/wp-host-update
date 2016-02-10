<section id="running">
	<div class="page-header">
		<h2>Doing Magic! Please be patient...</h2>
	</div>


	<div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="<?php echo $total_rows;?>" style="width: 0%">
			<span class="sr-only">0% Complete (success)</span>
		</div>
	</div>
	
	<br><br>
	
	<div class="panel panel-default">
		<div class="panel-heading">A bit more info while you waiting:</div>
		<div id="progress-log" class="panel-body">
			<?php /*
			<div class="row">
				<div class="col-md-1 text-right"><span class="text-success glyphicon glyphicon-ok"></span></div>
				<div class="col-md-11">Completed with table <span class="text-warning">wp_abc</span>.</div>
			</div>
			<div class="row">
				<div class="col-md-1 text-right"></div>
				<div class="col-md-11">Processing table <span class="text-warning">wp_abc</span>...</div>
			</div>
			 * 
			 */ ?>
		</div>
	</div>
</section>
