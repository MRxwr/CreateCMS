<?php
if ( isset($_POST["userId"]) ){
	if( is_uploaded_file($_FILES['file']['tmp_name']) ){
		@$ext = end((explode(".", $_FILES['file']['name'])));
		$directory = "logos/";
		$originalfile = $directory . md5(date("d-m-y").time().rand(111111,999999))."." . $ext;
		move_uploaded_file($_FILES["file"]["tmp_name"], $originalfile);
		$_POST["file"] = str_replace("logos/",'',$originalfile);
	}else{
		$_POST["file"] = "";
	}
	$table = "client";
	insertDB($table,$_POST);
	//header("Location: ?p=Leads");
}
if ( isset($_GET["delete"]) ){
	$table = "client";
	$data = array('type'=>'1');
	$where = "`id` LIKE '".$_GET["delete"]."'";
	updateDB($table,$data,$where);
	//header("Location: ?p=Leads");
}
if ( isset($_GET["customer"]) ){
	$table = "client";
	$data = array('type'=>'2');
	$where = "`id` LIKE '".$_GET["customer"]."'";
	updateDB($table,$data,$where);
	//header("Location: ?p=Leads");
}
if ( isset($_GET["return"]) ){
	$table = "client";
	$data = array('type'=>'0');
	$where = "`id` LIKE '".$_GET["return"]."'";
	updateDB($table,$data,$where);
	//header("Location: ?p=Leads");
}
?>
		<div class="row">
		
			<div class="col-md-12">
			<div class="panel panel-default card-view">
				<div class="panel-heading">
					<div class="pull-left">
						<h6 class="panel-title txt-dark">Add New Lead</h6>
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
			<label class="control-label mb-10" for="exampleInputuname_1">Name</label>
			<div class="input-group">
				<div class="input-group-addon"><i class="icon-user"></i></div>
				<input type="text" class="form-control" id="exampleInputuname_1" placeholder="Full Name" name="name">
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="form-group">
			<label class="control-label mb-10" for="exampleInputuname_1">Company</label>
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-institution"></i></div>
				<input type="text" class="form-control" id="exampleInputuname_1" placeholder="Company Name" name="company">
			</div>
		</div>
	</div>	
	
	<div class="col-md-4">
		<div class="form-group">
			<label class="control-label mb-10" for="exampleInputEmail_1">Email address</label>
			<div class="input-group">
				<div class="input-group-addon"><i class="icon-envelope-open"></i></div>
				<input type="email" class="form-control" id="exampleInputEmail_1" placeholder="Enter email" name="email">
			</div>
		</div>
	</div>	
	
	<div class="col-md-4">
		<div class="form-group">
			<label class="control-label mb-10" for="exampleInputpwd_1">Mobile</label>
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-phone"></i></div>
				<input type="text" class="form-control" id="exampleInputpwd_1" placeholder="Enter Mobile" name="phone">
			</div>
		</div>
	</div>	
	
	<div class="col-md-4">
		<div class="form-group">
			<label class="control-label mb-10" for="exampleInputpwd_1">Upload File</label>
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-file"></i></div>
				<input type="file" name="logo" class="form-control" >
			</div>
		</div>
	</div>
	
	<div class="col-md-12">
		<button type="submit" class="btn btn-success mr-10">Submit</button>
		<input type="hidden" name="userId" value="<?php echo $userId ?>">
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
				<!-- /Title -->
				
				<!-- Row -->
		<?php
		$typeOfLead 	= array('0','1');
		$arrayOfTitles 	= array('List Of Leads','Lost Leads');
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
				c.type LIKE '".$typeOfLead[$i]."'
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
				
				<a href="?p=Leads&customer=<?php echo $row["id"] ?>" style="margin:3px"><i class="fa fa-user"></i></a>

				<a href="?p=Leads&<?php echo $action[$i] . $row["id"] ?>" style="margin:3px"><i class="<?php echo $icon[$i] ?>"></i></a>
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
	window.location.replace("?page=leads");
	<?php
}
?>
</script>
<!-- /Main Content -->