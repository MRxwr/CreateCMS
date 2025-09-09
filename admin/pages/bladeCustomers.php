<?php
if ( isset($_GET["delete"]) ){
	$table = "client";
	$data = array('type'=>'3');
	$where = "`id` LIKE '".$_GET["delete"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Customers");
}
if ( isset($_GET["return"]) ){
	$table = "client";
	$data = array('type'=>'2');
	$where = "`id` LIKE '".$_GET["return"]."'";
	updateDB($table,$data,$where);
	header("Location: ?p=Customers");
}
?>
		<?php
		$typeOfCustomer = array('2','3');
		$arrayOfTitles 	= array('List Of Customer','Lost Customers');
		$myTable 		= array('myTable1','myTable2');
		$panel 			= array('panel-default','panel-danger');
		$textColor 		= array('txt-dark','txt-light');
		$icon 			= array('fa fa-trash-o','fa fa-refresh');
		$action			= array('delete=','return=');
		for ($i = 0 ; $i < 2 ; $i++){
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
				<th>User</th>
				<th>Name</th>
				<th>Company</th>
				<th>Email</th>
				<th>Mobile</th>
				<th>File</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$sql = "SELECT c.*, u.username
				FROM `client` as c
				JOIN `user` as u
				ON c.userId = u.id
				WHERE
				c.type LIKE '".$typeOfCustomer[$i]."'
				";
		$result = $dbconnect->query($sql);
		while ( $row = $result->fetch_assoc() ){
		?>
			<tr>
				<td><?php echo substr($row["date"], 0, 10); ?></td>
				<td><?php echo $row["username"] ?></td>
				<td><?php echo $row["name"] ?></td>
				<td><?php echo $row["company"] ?></td>
				<td><a href="mailto:<?php echo $row["email"] ?>">Email</a></td>
				<td><a href="tel:<?php echo $row["phone"] ?>">call</a></td>
				<td><a href="logos/<?php echo $row["image"] ?>">Download</a></td>
				<td>
				<a href="?p=Details&id=<?php echo $row["id"] ?>" style="margin:3px"><i class="fa fa-table"></i></a>
				<a href="?p=Customers&<?php echo $action[$i] . $row["id"] ?>" style="margin:3px"><i class="<?php echo $icon[$i] ?>"></i></a>
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
<script>
<?php
if ( isset($image) || isset($_GET["customer"]) || isset($_GET["delete"]) || isset($_GET["return"]) ){
	?>
	window.location.replace("?page=customers");
	<?php
}
?>
</script>
<!-- /Main Content -->