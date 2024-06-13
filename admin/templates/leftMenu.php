
<!-- Left Sidebar Menu -->
<div class="fixed-sidebar-left">
	<ul class="nav navbar-nav side-nav nicescroll-bar">
		<li class="navigation-header">
			<span>Create Co CMS</span> 
			<i class="zmdi zmdi-more"></i>
		</li>
<?php
if ( $userType  == 0 ){
?>
		<li>
			<a class="active" href="?page=home" >
				<div class="pull-left">
					<i class="zmdi zmdi-landscape mr-20"></i>
					<span class="right-nav-text">Dashboard</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="" href="?page=leads" >
				<div class="pull-left">
					<i class="fa fa-users mr-20"></i>
					<span class="right-nav-text">Leads</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="" href="?page=customers" >
				<div class="pull-left">
					<i class="fa fa-user mr-20"></i>
					<span class="right-nav-text">Customers</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="" href="?page=employees" >
				<div class="pull-left">
					<i class="fa fa-user-md mr-20"></i>
					<span class="right-nav-text">employees</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="" href="?page=users" >
				<div class="pull-left">
					<i class="fa fa-user-plus mr-20"></i>
					<span class="right-nav-text">Users</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="" href="?page=details&action=logs" >
				<div class="pull-left">
					<i class="fa fa-clock-o mr-20"></i>
					<span class="right-nav-text">History</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="" href="?page=settings" >
				<div class="pull-left">
					<i class="fa fa-gears mr-20"></i>
					<span class="right-nav-text">Settings</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
		</li>
<?php
}
?>
	</ul>
</div>
<!-- /Left Sidebar Menu -->
