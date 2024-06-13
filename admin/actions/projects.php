<?php
if ( isset($_POST["title"]) ){
	if( is_uploaded_file($_FILES['file']['tmp_name']) ){
		@$ext = end((explode(".", $_FILES['file']['name'])));
		$directory = "logos/";
		$originalfile = $directory . md5(date("d-m-y").time().rand(111111,999999))."." . $ext;
		move_uploaded_file($_FILES["file"]["tmp_name"], $originalfile);
		$_POST["file"] = str_replace("logos/",'',$originalfile);
	}else{
		$_POST["file"] = "";
	}
	$table = "project";
	$_POST["finished"] = '';
	insertDB($table,$_POST);
}
if ( isset($_GET["delete"]) ){
	$table = "project";
	$data = array('status'=>'2', 'finished'=>'');
	$where = "`id` LIKE '".$_GET["delete"]."'";
	updateDB($table,$data,$where);
}
if ( isset($_GET["done"]) ){
	$table = "project";
	$data = array('status'=>'1', 'finished'=>$date);
	$where = "`id` LIKE '".$_GET["done"]."'";
	updateDB($table,$data,$where);
}
if ( isset($_GET["return"]) ){
	$table = "project";
	$data = array('status'=>'0', 'finished'=>'');
	$where = "`id` LIKE '".$_GET["return"]."'";
	updateDB($table,$data,$where);
}
?>
<!-- /Title -->

<div class="row">

<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Add New Project</h6>
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
<label class="control-label mb-10" for="exampleInputuname_1">Title</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-header"></i></div>
<input type="text" class="form-control" id="exampleInputuname_1" placeholder="title" name="title">
</div>
</div>
</div>

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputuname_1">Details</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-list-alt"></i></div>
<input type="text" class="form-control" id="exampleInputuname_1" placeholder="Details" name="details">
</div>
</div>
</div>	

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputuname_1">Expected Date</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
<input type="date" class="form-control" id="exampleInputuname_1" placeholder="Details" name="expected">
</div>
</div>
</div>	

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputuname_1">Price</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-dollar"></i></div>
<input type="text" class="form-control" id="exampleInputuname_1" placeholder="price" name="price">
</div>
</div>
</div>	

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputuname_1">Upload</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-dollar"></i></div>
<input type="file" class="form-control" id="exampleInputuname_1" placeholder="" name="file">
</div>
</div>
</div>

<div class="col-md-12">
<button type="submit" class="btn btn-success mr-10">Submit</button>
<input type="hidden" name="date" value="<?php echo $date ?>">
<input type="hidden" name="userId" value="<?php echo $userId ?>">
<input type="hidden" name="clientId" value="<?php echo $_GET["id"] ?>">
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

<?php
$status 		= array('0','1','2');
$arrayOfTitles 	= array('Current Projects','Finished Projects','Lost Projects');
$myTable 		= array('myTable1','myTable2','myTable3');
$panel 			= array('panel-default','panel-success','panel-danger');
$textColor 		= array('txt-dark','txt-light','txt-light');

for($i = 0; $i < 3 ; $i++ ){
?>

<div class="row">
<div class="col-sm-12">
<div class="panel <?php echo $panel[$i] ?> card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title <?php echo $textColor[$i] ?>"><?php echo $arrayOfTitles[$i] ?></h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="table-wrap">
<div class="">

<table id="<?php echo $myTable[$i] ?>" class="table table-hover display  pb-30" >
<thead>
<tr>
<th>Date</th>
<th>Title</th>
<th>Details</th>
<th>User</th>
<th>Expected</th>
<?php 
if ($status[$i] == '1'){
?>
<th>Finished</th>
<?php
}
?>
<th>Files</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT p.*, u.username
		FROM `project` as p
		JOIN `user` as u
		ON p.userId = u.id
		WHERE
		p.clientId LIKE '".$_GET['id']."'
		AND
		p.status LIKE '".$status[$i]."'
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
?>
<tr>
<td><?php echo substr($row["date"], 0, 10); ?></td>
<td><?php echo $row["title"] ?></td>
<td><?php echo $row["details"] ?></td>
<td><?php echo $row["username"] ?></td>
<td><?php echo substr($row["expected"],0,10) ?></td>
<?php 
if ($status[$i] == '1'){
?>
<td><?php echo substr($row["finished"],0,10) ?></td>
<?php
}
?>
<td>
<?php
if ( !empty($row["file"]) ){
	echo '<a target="_blank" href="logos/'.$row["file"].'">Download</a>';
}else{
	echo "";
}
?>
</td>
<td>
<a href="?page=details<?php echo "&id=".$_GET["id"]."&pid=".$row["id"]."&action=tasks" ?>" style="margin:3px"><i class="fa fa-table"></i></a>

<a href="?page=details<?php echo "&id=".$_GET["id"]."&action=invoices&pid=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-credit-card-alt"></i></a>
<?php if ( $status[$i] != '1' ){  ?>
<a href="?page=details<?php echo "&id=".$_GET["id"]."&action=".$_GET["action"]."&done=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-check"></i></a>
<?php }if ( $status[$i] != '0' ){  ?>
<a href="?page=details<?php echo "&id=".$_GET["id"]."&action=".$_GET["action"]."&return=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-refresh"></i></a>
<?php }if ( $status[$i] != '2' ){  ?>
<a href="?page=details<?php echo "&id=".$_GET["id"]."&action=".$_GET["action"]."&delete=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-trash"></i></a>
<?php } ?>
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
<?php
}
?>	