<?php
if ( isset($_POST["task"]) ){
	if( is_uploaded_file($_FILES['file']['tmp_name']) ){
		@$ext = end((explode(".", $_FILES['file']['name'])));
		$directory = "logos/";
		$originalfile = $directory . md5(date("d-m-y").time().rand(111111,999999))."." . $ext;
		move_uploaded_file($_FILES["file"]["tmp_name"], $originalfile);
		$_POST["file"] = str_replace("logos/",'',$originalfile);
	}else{
		$_POST["file"] = "";
	}
	$table = "task";
	insertDB($table,$_POST);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=Tasks");
}
if ( isset($_GET["doing"]) ){
	$table = "task";
	$data = array('status'=>'1','finished'=>'','doing'=>$date);
	$where = "`id` LIKE '".$_GET["doing"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=".$_GET["a"]);
}
if ( isset($_GET["done"]) ){
	$table = "task";
	$data = array('status'=>'2','finished'=>$date);
	$where = "`id` LIKE '".$_GET["done"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=".$_GET["a"]);
}
if ( isset($_GET["return"]) ){
	$table = "task";
	$data = array('status'=>'0','finished'=>'','doing'=>'');
	$where = "`id` LIKE '".$_GET["return"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=".$_GET["a"]);
}
if ( isset($_GET["delete"]) ){
	$table = "task";
	$data = array('status'=>'2');
	$where = "`id` LIKE '".$_GET["delete"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=".$_GET["a"]);
}
?>
<!-- /Title -->

<div class="row">

<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Add New Task</h6>
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
<label class="control-label mb-10" for="exampleInputuname_1">Details</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-list-alt"></i></div>
<input type="text" class="form-control" id="exampleInputuname_1" placeholder="Details" name="task">
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
<label class="control-label mb-10" for="exampleInputuname_1">Assign To</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-user"></i></div>
<select class="form-control" name="to" >
	<?php
	$sql = "SELECT *
			FROM `employee`
			WHERE
			`status` LIKE '0'
			";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() ){
	?>
	<option value="<?php echo $row["id"] ?>"><?php echo $row["name"] ?></option>
	<?php
	}
	?>
</select>
</div>
</div>
</div>	

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10" for="exampleInputuname_1">Upload</label>
<div class="input-group">
<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
<input type="file" class="form-control" id="exampleInputuname_1" placeholder="Details" name="file">
</div>
</div>
</div>	

<div class="col-md-12">
<button type="submit" class="btn btn-success mr-10">Submit</button>
<input type="hidden" name="date" value="<?php echo $date ?>">
<input type="hidden" name="projectId" value="<?php echo $_GET["pid"] ?>">
<input type="hidden" name="by" value="<?php echo $userId ?>">
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
$arrayOfTitles 	= array('To-do','Doing','finished');
$myTable 		= array('myTable1','myTable2','myTable3');
$panel 			= array('panel-default','panel-warning','panel-success');
$textColor 		= array('txt-dark','txt-light','txt-dark');

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
<th>Initiated</th>
<th>Expected</th>
<?php if ( $status[$i] == 1 || $status[$i] == 2) { echo '<th>Doing</th>';} ?>
<?php if ( $status[$i] == 2  ){ echo '<th>Finished</th>';} ?>
<th>Task</th>
<th>Assign To</th>
<th>Assign By</th>
<th>Files</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT t.*, u.username, e.name
		FROM `task` as t
		JOIN `user` as u
		ON u.id = t.by
		JOIN `employee` as e
		ON e.id = t.to
		WHERE
		t.projectId LIKE '".$_GET['pid']."'
		AND
		t.status LIKE '".$status[$i]."'
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
?>
<tr>
<td><?php echo substr($row["date"], 0, 10); ?></td>
<td><?php echo substr($row["expected"], 0, 10); ?></td>
<?php if ( $status[$i] == 1 || $status[$i] == 2 ) {echo '<td>'.substr($row["doing"], 0, 10).'</td>';} ?>
<?php if ( $status[$i] == 2 ) {echo '<td>'.substr($row["finished"], 0, 10).'</td>';} ?>
<td><?php echo $row["task"] ?></td>
<td><?php echo $row["name"] ?></td>
<td><?php echo $row["username"] ?></td>
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
<?php if ( $status[$i] == '0' ){  ?>
<a href="?p=Details<?php echo "&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=".$_GET["a"]."&doing=" . $row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Doing"><i class="fa fa-circle-o-notch"></i></a>
<?php }if ( $status[$i] != '2' ){  ?>
<a href="?p=Details<?php echo "&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=".$_GET["a"]."&done=" . $row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Done"><i class="fa fa-check"></i></a>
<?php }if ( $status[$i] != '0' ){  ?>
<a href="?p=Details<?php echo "&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=".$_GET["a"]."&return=" . $row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Pending"><i class="fa fa-refresh"></i></a>
<?php } ?>

<a href="?p=Comments<?php echo "&id=".$row["id"]."&pid=".$_GET["pid"] ?>" style="margin:3px" data-toggle="tooltip" title="Comment"><i class="fa fa-comments"></i></a>

<a href="?p=Details<?php echo "&id=".$_GET["id"]."&pid=".$_GET["pid"]."&a=".$_GET["a"]."&delete=" . $row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>

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