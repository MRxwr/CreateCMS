<?php
if ( isset($_GET["delete"]) ){
	$table = "project";
	$data = array('status'=>'2');
	$where = "`id` LIKE '".$_GET["delete"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&a=".$_GET["a"]);
}
if ( isset($_GET["done"]) ){
	$table = "project";
	$data = array('status'=>'1');
	$where = "`id` LIKE '".$_GET["done"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&a=".$_GET["a"]);
}
if ( isset($_GET["return"]) ){
	$table = "project";
	$data = array('status'=>'0');
	$where = "`id` LIKE '".$_GET["return"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Details&id=".$_GET["id"]."&a=".$_GET["a"]);
}
?>

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
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT p.*, u.username
		FROM `project` as p
		JOIN `user` as u
		WHERE
		p.userId LIKE '".$_GET['id']."'
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
<td>
<a href="?p=Details<?php echo "&id=".$row["clientId"]."&pid=".$row["id"]."&a=Tasks" ?>" style="margin:3px"><i class="fa fa-table"></i></a>

<a href="?p=Details<?php echo "&id=".$row["clientId"]."&a=".$_GET["a"]."&done=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-check"></i></a>

<a href="?p=Details<?php echo "&id=".$row["clientId"]."&a=".$_GET["a"]."&delete=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-trash"></i></a>

<a href="?p=Details<?php echo "&id=".$row["clientId"]."&a=".$_GET["a"]."&return=" . $row["id"] ?>" style="margin:3px"><i class="fa fa-refresh"></i></a>

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