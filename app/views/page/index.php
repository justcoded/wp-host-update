<?php
/* @var $tables array */
/* @var $default_search_replace array */

include VIEWS_PATH . '/layouts/header.php';
?>


<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
	<h1>Moving the WordPress site?</h1>
	<p>While moving your WordPress site to another domain or folder you probably get a problem with image paths and different hard-coded URLs inside the serialized objects.</p>
	<p>The solution is here, just enter correct replace strings below and enjoy your working site!</p>
	
	<div class="alert alert-warning" role="alert">
		<strong>Warning!</strong> Please do not forget to backup your database before going next!
	</div>
</div>



<section id="replace-form">
	<form class="form">

		<!-- find replace block -->
		<div class="page-header">
			<h2>Replace options</h2>
		</div>

		<div class="row form-header">
			<div class="col-md-1"></div>
			<div class="col-md-4">
				<h4>Find</h4>
			</div>
			<div class="col-md-1"></div>
			<div class="col-md-4">
				<h4>Replace</h4>
			</div>
			<div class="col-md-1"></div>
		</div>
		<fieldset id="find-replace-rows">
			<?php foreach ($default_search_replace as $params) : ?>
			<div class="row">
				<div class="col-md-1 text-right"><span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span></div>
				<div class="col-md-4">
					<div class="form-group"><input type="text" class="form-control" name="find[]" 
												   placeholder="<?php echo html_encode($params[0]); ?>" 
												   value="<?php echo html_encode($params[1]); ?>"></div>
				</div>
				<div class="col-md-1 text-center"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></div>
				<div class="col-md-4">
					<div class="form-group"><input type="text" class="form-control" name="replace[]" 
												   placeholder="<?php echo html_encode($params[2]); ?>" 
												   value="<?php echo html_encode($params[3]); ?>"></div>
				</div>
				<div class="col-md-1"><a href="#" class="text-danger" title="Delete"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a></div>
			</div>
			<?php endforeach; ?>
		</fieldset>
		
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-4">
				<button id="find-replace-add-row" class="btn">Add Row</button>
			</div>
		</div>
		
		<!-- Advanced options -->
		<div class="page-header">
			<h2>Advanced options</h2>
		</div>
		
		<div class="form-group">
			<div class="radio">
				<label>
					<input type="radio" name="tables" value="all" checked>
					Replace all tables with prefix "<?php echo $this->wpdb->prefix;?>" <span class="text-danger">TODO: use prefix from wp-config and filter tables list with this prefix</span>
				</label>
			  </div>
			<div class="radio">
				<label>
					<input type="radio" name="tables" value="custom">
					Replace only selected tables below
				</label>
			</div>
		</div>
		<div class="form-group hidden" id="custom-tables">
			<label>Tables to search/replace</label>
			<select multiple class="form-control">
				<?php echo html_options($tables); ?>
			</select>
		</div>
		
		<!-- Advanced options -->
		<div class="page-header">
			<h2>That's it!</h2>
		</div>
		<button class="btn btn-primary">Do the Magic!</button>
		
	</form>
</section>


<?php include  VIEWS_PATH . '/layouts/footer.php'; ?>