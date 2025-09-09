<?php
if ( isset($_POST["ref"]) ){
	if( is_uploaded_file($_FILES['file']['tmp_name']) ){
		@$ext = end((explode(".", $_FILES['file']['name'])));
		$directory = "logos/";
		$originalfile = $directory . md5(date("d-m-y").time().rand(111111,999999))."." . $ext;
		move_uploaded_file($_FILES["file"]["tmp_name"], $originalfile);
		$_POST["file"] = str_replace("logos/",'',$originalfile);
	}else{
		$_POST["file"] = "";
	}
	$table = "invoice";
	insertDB($table,$_POST);
	$_POST["table"] = "invoice";
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=Invoices");
}
if ( isset($_GET["delete"]) ){
	$table = "invoice";
	$data = array('status'=>'1');
	$where = "`id` LIKE '".$_GET["delete"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=Invoices");
}
if ( isset($_GET["cost"]) ){
	$table = "invoice";
	$data = array('type'=>'2');
	$where = "`id` LIKE '".$_GET["cost"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=Invoices");
}
if ( isset($_GET["paid"]) ){
	$table = "invoice";
	$data = array('type'=>'1');
	$where = "`id` LIKE '".$_GET["paid"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=Invoices");
}
if ( isset($_GET["pending"]) ){
	$table = "invoice";
	$data = array('type'=>'0');
	$where = "`id` LIKE '".$_GET["pending"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=Invoices");
}
?>
<!-- /Title -->

<div class="row">

<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Add New Invoice</h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="row">
<div class="col-sm-12 col-xs-12">
<div class="form-wrap">

<form action="" method="post" enctype="multipart/form-data">

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputuname_1">ref</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-barcode"></i></div>
<input type="text" class="form-control" id="exampleInputuname_1" placeholder="ref" name="ref">
</div>
</div>
</div>

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputuname_1">Details</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-comment"></i></div>
<input type="text" class="form-control" id="exampleInputuname_1" placeholder="invoice details" name="details">
</div>
</div>
</div>	

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputEmail_1">Type</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-circle-o"></i></div>
<select class="form-control" name="type">
	<option value='0'>Pending</option>
	<option value='1'>Paid</option>
	<option value='2'>Cost</option>
</select>
</div>
</div>
</div>	

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputpwd_1">Total</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-money"></i></div>
<input type="text" class="form-control" id="exampleInputpwd_1" placeholder="Enter total" name="total">
</div>
</div>
</div>	

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputpwd_1">Upload file</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-file"></i></div>
<input type="file" name="file" class="form-control" >
</div>
</div>
</div>

<div class="col-md-12">
<button type="submit" class="btn btn-success mr-10">Submit</button>
<input type="hidden" name="by" value="<?php echo $userId ?>">
<input type="hidden" name="date" value="<?php echo $date ?>">
<input type="hidden" name="projectId" value="<?php echo $_GET["pid"] ?>">
</div>

</form>

</div>
</div>
</div>
</div>
</div>
</div>
</div>

</div>

<!-- Row -->

<div class="row">
<div class="col-sm-12">
<div class="panel panel-primary card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-light">Invoices</h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="table-wrap">
<div class="">

<table id="myTable1" class="table table-hover display  pb-30" >
<thead>
<tr>
<th>Date</th>
<th>By</th>
<th>Ref</th>
<th>Details</th>
<th>Type</th>
<th>Total</th>
<th>File</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT i.*, u.username, p.title
		FROM `invoice` as i
		JOIN `user` as u
		ON i.by = u.id
		JOIN `project` as p
		ON i.projectId = p.id
		WHERE
		i.status LIKE '0'
		AND
		i.projectId LIKE '".$_GET["pid"]."'
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
	if($row["type"] == '0'){
		$type = "Pending";
	}elseif($row["type"] == '1'){
		$type = "Paid";
	}else{
		$type = "Cost";
	}
?>
<tr>
<td><?php echo substr($row["date"], 0, 10); ?></td>
<td><?php echo $row["username"] ?></td>
<td><?php echo $row["ref"] ?></td>
<td><?php echo $row["details"] ?></td>
<td><?php echo $type ?></td>
<td><?php echo $row["total"] ?> KD</td>
<td><a href="logos/<?php echo $row["file"] ?>" target="_blank">Download</a></td>
<td>

<a href="?<?php echo $_SERVER["QUERY_STRING"] . "&paid=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-money"></i></a>

<a href="?<?php echo $_SERVER["QUERY_STRING"] . "&cost=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-user"></i></a>

<a href="?<?php echo $_SERVER["QUERY_STRING"] . "&pending=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-mail-reply"></i></a>

</td>
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

<script>

<?php
if ( isset($_GET["paid"]) || isset($_GET["cost"]) || isset($_GET["pending"]) ){
	?>
	window.location.replace("?p=Details&id="+'<?php echo $_GET["id"] . "&pid=" . $_GET["pid"] ?>'+"&a=Invoices");
	<?php
}
?>

</script>