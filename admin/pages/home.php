<div class="right-sidebar-backdrop"></div>

<!-- Main Content -->
<div class="page-wrapper">
	<div class="container-fluid pt-25">
		<!-- Row -->
		
		<div class="row">
		
			<div class="col-md-12">
				
<div class="row">

<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
<div class="panel panel-default card-view pa-0">
<div class="panel-wrapper collapse in">
<div class="panel-body pa-0">
<div class="sm-data-box">
<div class="container-fluid">
<div class="row">
	<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
		<span class="txt-dark block counter"><span class="counter-anim"><?php echo getTotals("client","type LIKE '0'") ?></span></span>
		<span class="weight-500 uppercase-font block font-13">Leads</span>
	</div>
	<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
		<i class="icon-user data-right-rep-icon txt-light-grey"></i>
	</div>
</div>	
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
<div class="panel panel-default card-view pa-0">
<div class="panel-wrapper collapse in">
<div class="panel-body pa-0">
<div class="sm-data-box">
<div class="container-fluid">
<div class="row">
	<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
		<span class="txt-dark block counter"><span class="counter-anim"><?php echo getTotals("client","type LIKE '2'") ?></span></span>
		<span class="weight-500 uppercase-font block">Customers</span>
	</div>
	<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
		<i class="icon-user-following data-right-rep-icon txt-light-grey"></i>
	</div>
</div>	
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
<div class="panel panel-default card-view pa-0 <!--bg-green-->">
<div class="panel-wrapper collapse in">
<div class="panel-body pa-0">
<div class="sm-data-box">
<div class="container-fluid">
<div class="row">
	<div class="col-xs-6 text-center pl-0 pr-0 txt-dark data-wrap-left">
		<span class="block counter"><span class="counter-anim"><?php echo getTotals("project","status LIKE '0'") ?></span></span>
		<span class="weight-500 uppercase-font block">Active Projects</span>
	</div>
	<div class="col-xs-6 text-center  pl-0 pr-0 txt-light-grey data-wrap-right">
		<i class="icon-layers data-right-rep-icon"></i>
	</div>
</div>	
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
<div class="panel panel-default card-view pa-0">
<div class="panel-wrapper collapse in">
<div class="panel-body pa-0">
<div class="sm-data-box">
<div class="container-fluid">
<div class="row">
	<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
		<span class="txt-dark block counter"><span class="counter-anim"><?php echo getTotals("task","status LIKE '0'") ?></span></span>
		<span class="weight-500 uppercase-font block">Pending Tasks</span>
	</div>
	<div class="col-xs-6 text-center  pl-0 pr-0 txt-light-grey data-wrap-right">
		<i class="fa fa-table data-right-rep-icon"></i>
	</div>
</div>	
</div>
</div>
</div>
</div>
</div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
	<div class="pull-left">
		<h6 class="panel-title txt-dark">Recent Projects</h6>
	</div>
	<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
	<div class="panel-body row pa-0">
			<div class="table-wrap sm-data-box-2">
			<div class="table-responsive">
			  <table class="table table-striped mb-0">
				<thead>
				  <tr>
					<th>Project</th>
					<th>Submitted</th>
					<th>Action</th>
				  </tr>
				</thead>
				<tbody>
<?php
$sql = "SELECT * FROM `project` WHERE `status` LIKE '0' ORDER BY `id` DESC LIMIT 5";
$result = $dbconnect->query($sql);
while($row = $result->fetch_Assoc() ){
?>			
				   <tr>
					<td><?php echo $row["title"] ?></td>
					<td><?php echo substr($row["date"],0,11) ?></td>
					<td><a href="?page=details&id=<?php echo $row["clientId"] ?>">Go</a></td>
				  </tr>
<?php
}
?>
				</tbody>
			  </table>
			</div>
		</div>
	</div>	
</div>
</div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="panel panel-warning card-view">
<div class="panel-heading">
	<div class="pull-left">
		<h6 class="panel-title txt-dark">Attention Projects</h6>
	</div>
	<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
	<div class="panel-body row pa-0">
			<div class="table-wrap sm-data-box-2">
			<div class="table-responsive">
			  <table class="table table-striped mb-0">
				<thead>
				  <tr>
					<th>Project</th>
					<th>Expected</th>
					<th>Action</th>
				  </tr>
				</thead>
				<tbody>
<?php
$sql = "SELECT * FROM `project` WHERE `status` LIKE '0' ORDER BY `expected` ASC LIMIT 5";
$result = $dbconnect->query($sql);
while($row = $result->fetch_Assoc() ){
?>			
				   <tr>
					<td><?php echo $row["title"] ?></td>
					<td><?php echo substr($row["expected"],0,10) ?></td>
					<td><a href="?page=details&id=<?php echo $row["clientId"] ?>">Go</a></td>
				  </tr>
<?php
}
?>
				</tbody>
			  </table>
			</div>
		</div>
	</div>	
</div>
</div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
	<div class="pull-left">
		<h6 class="panel-title txt-dark">Recent Tasks</h6>
	</div>
	<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
	<div class="panel-body row pa-0">
			<div class="table-wrap sm-data-box-2">
			<div class="table-responsive">
			  <table class="table table-striped mb-0">
				<thead>
				  <tr>
					<th>Task</th>
					<th>Submitted</th>
					<th>Action</th>
				  </tr>
				</thead>
				<tbody>
<?php
$sql = "SELECT t.*, p.clientId
		FROM `task` as t
		JOIN `project` as p
		ON p.id = t.projectId
		WHERE
		t.status LIKE '0'
		ORDER BY `date` DESC
		LIMIT 5";
$result = $dbconnect->query($sql);
while($row = $result->fetch_Assoc() ){
?>			
				   <tr>
					<td><?php echo $row["task"] ?></td>
					<td><?php echo substr($row["date"],0,11) ?></td>
					<td><a href="?page=details&action=tasks&pid=<?php echo $row["projectId"] ?>">Go</a></td>
				  </tr>
<?php
}
?>
				</tbody>
			  </table>
			</div>
		</div>
	</div>	
</div>
</div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="panel panel-warning card-view">
<div class="panel-heading">
	<div class="pull-left">
		<h6 class="panel-title txt-dark">Attention Tasks</h6>
	</div>
	<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
	<div class="panel-body row pa-0">
			<div class="table-wrap sm-data-box-2">
			<div class="table-responsive">
			  <table class="table table-striped mb-0">
				<thead>
				  <tr>
					<th>Task</th>
					<th>Expected</th>
					<th>Action</th>
				  </tr>
				</thead>
				<tbody>
<?php
$sql = "SELECT t.*, p.clientId
		FROM `task` as t
		JOIN `project` as p
		ON p.id = t.projectId
		WHERE
		t.status LIKE '0'
		ORDER BY `expected` ASC
		LIMIT 5
		";
$result = $dbconnect->query($sql);
while($row = $result->fetch_Assoc() ){
?>			
				   <tr>
					<td><?php echo $row["task"] ?></td>
					<td><?php echo substr($row["expected"],0,10) ?></td>
					<td><a href="?page=details&action=tasks&pid=<?php echo $row["projectId"] ?>">Go</a></td>
				  </tr>
<?php
}
?>
				</tbody>
			  </table>
			</div>
		</div>
	</div>	
</div>
</div>
</div>

</div>				

			</div>
			
		</div>
		
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
<!-- /Main Content -->