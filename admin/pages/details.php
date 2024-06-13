<?php

?>

<div class="right-sidebar-backdrop"></div>

<!-- Main Content -->
<div class="page-wrapper">
	<div class="container-fluid pt-25">
		<!-- Row -->
		
		<?php
		if ( isset($_GET["action"]) && ( $_GET["action"] != 'employees' && $_GET["action"] != 'users' && $_GET["action"] != 'logs' ) ){
		?>
		<div class="row">
		<div class="col-md-12">
		<div class="panel panel-default card-view">
			<div class="panel-heading">
				<div class="pull-left">
					<h6 class="panel-title txt-dark">Actions</h6>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="panel-wrapper collapse in">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12 col-xs-12">
						
						<a href="?page=details&id=<?php echo $_GET["id"] ?>&action=projects"><button class="btn btn-default btn-anim"><i class="fa fa-pencil"></i><span class="btn-text">Project</span></button></a>
						
						<!--<a href="?page=details&id=<?php echo $_GET["id"] ?>&action=tasks"><button class="btn btn-success btn-anim"><i class="fa fa-pencil"></i><span class="btn-text">Task</span></button></a>

						<a href="?page=details&id=<?php echo $_GET["id"] ?>&action=invoices"><button class="btn btn-primary btn-anim"><i class="fa fa-pencil"></i><span class="btn-text">Invoice</span></button></a>

						<a href="?page=details&id=<?php echo $_GET["id"] ?>&action=logs"><button class="btn btn-warning btn-anim"><i class="fa fa-pencil"></i><span class="btn-text">Logs</span></button></a> -->					
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
		</div>
		
		<?php 
		}
		if ( isset($_GET["action"]) && $_GET["action"] == 'tasks' ){
			require_once('actions/tasks.php');
		}elseif( isset($_GET["action"]) && $_GET["action"] == 'projects'  ){
			require_once('actions/projects.php');
		}elseif( isset($_GET["action"]) && $_GET["action"] == 'invoices'  ){
			require_once('actions/invoices.php');
		}elseif( isset($_GET["action"]) && $_GET["action"] == 'logs'  ){
			require_once('actions/logs.php');
		}elseif( isset($_GET["action"]) && $_GET["action"] == 'employees'  ){
			require_once('actions/employees.php');
		}elseif( isset($_GET["action"]) && $_GET["action"] == 'users'  ){
			require_once('actions/users.php');
		}else{
			$_GET["action"] = "projects";
			require_once('actions/projects.php');
		}
		
		?>
		
		<!-- /Row -->
	</div>

<!-- Footer -->
	<footer class="footer container-fluid pl-30 pr-30">
		<div class="row">
			<div class="col-sm-12">
				<p>2021 &copy; Create Co. CMS</p>
			</div>
		</div>
	</footer>
	<!-- /Footer -->
	
</div>

<script>
/*
<?php
if ( isset($image) || isset($_GET["customer"]) || isset($_GET["delete"]) || isset($_GET["return"]) ){
	?>
	window.location.replace("?page=details&id="+<?php echo $_GET["id"] ?>);
	<?php
}
?>
*/
</script>
<!-- /Main Content -->