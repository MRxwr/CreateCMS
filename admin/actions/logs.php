<div class="row">
<div class="col-sm-12">
<div class="panel panel-warning card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-light">Logs</h6>
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
<th>Username</th>
<th>Details</th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT l.*, u.username
		FROM `logs` as l
		JOIN `user` as u
		ON l.userId = u.id
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
?>
<tr>
<td><?php echo $row["date"] ?></td>
<td><?php echo $row["username"] ?></td>
<td><?php echo $row["sqlQuery"] ?></td>
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