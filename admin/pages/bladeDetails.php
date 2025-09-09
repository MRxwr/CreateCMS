<?php

?>

<div class="right-sidebar-backdrop"></div>

<!-- Main Content -->
<div class="page-wrapper">
	<div class="container-fluid pt-25">
		<!-- Row -->
		
		<?php
		if ( isset($_GET["a"]) && ( $_GET["a"] != 'Employees' && $_GET["a"] != 'Users' && $_GET["a"] != 'Logs' ) ){
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
		if( isset($_GET["a"]) && searchFile("actions","blade{$_GET["a"]}.php") ){
			require_once("actions/".searchFile("actions","blade{$_GET["a"]}.php"));
		}else{
			require_once("actions/bladeProjects.php");
		}
		?>
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