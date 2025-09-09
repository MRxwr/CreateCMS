<?php
if ( isset($_GET["doing"]) ){
	$table = "task";
	$data = array('status'=>'1','finished'=>'','doing'=>$date);
	$where = "`id` LIKE '".$_GET["doing"]."'";
	$msg = selectTask($_GET["doing"],"DOING");
	updateDB($table,$data,$where);
	notifyMe($_GET["doing"],$msg);
}
if ( isset($_GET["done"]) ){
	$table = "task";
	$data = array('status'=>'2','finished'=>$date);
	$where = "`id` LIKE '".$_GET["done"]."'";
	updateDB($table,$data,$where);
}
if ( isset($_GET["return"]) ){
	$table = "task";
	$data = array('status'=>'0','finished'=>'','doing'=>'');
	$where = "`id` LIKE '".$_GET["return"]."'";
	updateDB($table,$data,$where);
}
if ( isset($_GET["delete"]) ){
	$table = "task";
	$data = array('status'=>'2');
	$where = "`id` LIKE '".$_GET["delete"]."'";
	updateDB($table,$data,$where);
}

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
<th>Project</th>
<th>Task</th>
<th>Assign To</th>
<th>Assign By</th>
<th>Files</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
if ($userType == 1 ){
	$sql = "SELECT t.*, u.username, e.name, p.title, p.id as pid
			FROM `task` as t
			JOIN `user` as u
			ON u.id = t.by
			JOIN `employee` as e
			ON e.id = t.to
			JOIN `project` as p
			ON p.id = t.projectId
			WHERE
			t.status LIKE '{$status[$i]}'
			AND
			t.to LIKE '{$userId}'
			";
}else{
	$sql = "SELECT t.*, u.username, e.name, p.title, p.id as pid
			FROM `task` as t
			JOIN `user` as u
			ON u.id = t.by
			JOIN `employee` as e
			ON e.id = t.to
			JOIN `project` as p
			ON p.id = t.projectId
			WHERE
			t.status LIKE '{$status[$i]}'
			";
}
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
?>
<tr>
<td><?php echo substr($row["date"], 0, 10); ?></td>
<td><?php echo substr($row["expected"], 0, 10); ?></td>
<?php if ( $status[$i] == 1 || $status[$i] == 2 ) {echo '<td>'.substr($row["doing"], 0, 10).'</td>';} ?>
<?php if ( $status[$i] == 2 ) {echo '<td>'.substr($row["finished"], 0, 10).'</td>';} ?>
<td><?php echo $row["title"] ?></td>
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
<a href="?page=details<?php echo "&id=".$_GET["id"]."&pid=".$row["pid"]."&action=".$_GET["action"]."&doing=" . $row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Doing"><i class="fa fa-circle-o-notch"></i></a>
<?php }if ( $status[$i] != '2' ){  ?>
<a href="?page=details<?php echo "&id=".$_GET["id"]."&pid=".$row["pid"]."&action=".$_GET["action"]."&done=" . $row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Done"><i class="fa fa-check"></i></a>
<?php }if ( $status[$i] != '0' ){  ?>
<a href="?page=details<?php echo "&id=".$_GET["id"]."&pid=".$row["pid"]."&action=".$_GET["action"]."&return=" . $row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Pending"><i class="fa fa-refresh"></i></a>
<?php } ?>

<a href="?page=comments<?php echo "&id=".$row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Comment"><i class="fa fa-comments"></i></a>

<a href="?page=details<?php echo "&id=".$_GET["id"]."&pid=".$row["pid"]."&action=".$_GET["action"]."&delete=" . $row["id"] ?>" style="margin:3px" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>

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