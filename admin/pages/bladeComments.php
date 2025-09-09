<?php
if ( isset($_POST["taskId"]) ){
	$table = "comments";
	$_POST["date"] = $date;
	insertDB($table,$_POST);
	header("Location: ?p=Comments&id=".$_POST["taskId"]);
}
?>
<form method="post" action="">
<div class="row">
<div class="col-md-12">
<div class="panel panel-default border-panel card-view pa-0">
<div class="panel-wrapper collapse in">
<div class="panel-body pa-0">
<div class="recent-chat-box-wrap">
<div class="recent-chat-wrap">
	<div class="panel-heading ma-0 pt-15">
		<div class="goto-back txt-dark" style="font-weight:700">	
			<?php
			$sql = "SELECT *
					FROM `task`
					WHERE
					`id` LIKE '".$_GET["id"]."'
					";
			$result = $dbconnect->query($sql);
			$row = $result->fetch_assoc();
			echo $row["task"];
			?>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="panel-wrapper collapse in">
		<div class="panel-body pa-0">
			<div class="chat-content">
				<div class="" style="position: relative; overflow-y:hidden; width: auto; height: 483px;">
				
				<ul class="chatapp-chat-nicescroll-bar pt-20" style="overflow: hidden; width: auto; height: 483px;">
				<?php
				$sql = "SELECT c.*, u.username as User, e.username as EmpUser
						FROM `comments` as c
						LEFT JOIN `user` as u
						ON c.userId = u.id
						LEFT JOIN `employee` as e
						ON c.empId = e.id
						WHERE
						c.taskId LIKE '".$_GET["id"]."'
						ORDER BY c.id ASC
						";
				$result = $dbconnect->query($sql);
				while ( $row = $result->fetch_assoc() ){
					if ( $userId != $row["userId"] && $userId != $row["empId"] ){
				?>
					<li class="friend">
						<div class="friend-msg-wrap">
							<img class="user-img img-circle block pull-left" src="../img/user.png" alt="user">
							<div class="msg pull-left">
								<p><?php if ( !empty($row["User"]) ){ echo $row["User"] . ": " . $row["send-msg"];}else{ echo $row["EmpUser"] . ": " . $row["send-msg"]; } ?></p>
								<div class="msg-per-detail text-right">
									<span class="msg-time txt-grey"><?php echo substr(str_replace("20","",$row["date"]),0,14) ?></span>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>	
					</li>
				<?php
					}else{
					?>				
					<li class="self mb-10">
						<div class="self-msg-wrap">
							<div class="msg block pull-right"><?php echo $row["send-msg"] ?>
								<div class="msg-per-detail text-right">
									<span class="msg-time txt-grey"><?php echo substr(str_replace("20","",$row["date"]),0,14) ?></span>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>	
					</li>
				<?php
					}
				}
				?>
				</ul>
				
				<div class="slimScrollBar" style="background: rgb(135, 135, 135); width: 4px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 0px; z-index: 99; right: 1px; height: 483px;"></div>
				
				<div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
				
				</div>
			</div>
			<div class="input-group">
			<table style="width:100%">
			<tr>
			<td style="width:70%">
				<input type="text" name="send-msg" class="input-msg-send form-control" placeholder="Type something">
				<input type="hidden" name="taskId" value="<?php echo $_GET["id"] ?>">
				<input type="hidden" name="userId" value="<?php if($userType == 0 ) { echo $userId; }else{ echo "0"; } ?>">
				<input type="hidden" name="empId" value="<?php if($userType == 1 ) { echo $userId; }else{ echo "0"; } ?>">
				<input type="hidden" name="type" value="<?php echo $userType ?>">
			</td>
			<td>
				<input type="submit" class="form-control" value="Send" style="height:60px">
			</td>
			</tr>
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
</div>
</form>