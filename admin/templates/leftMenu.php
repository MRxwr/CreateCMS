<!-- Left Sidebar Menu -->
<div class="fixed-sidebar-left">
	<ul class="nav navbar-nav side-nav nicescroll-bar">
		<li class="navigation-header">
			<span>Create CMS</span> 
			<i class="zmdi zmdi-more"></i>
		</li>
<?php
if ( $userType == 0 ){
	$currentPage = isset($_GET['p']) ? $_GET['p'] : 'Home';
?>
		<li>
			<a class="<?php echo ($currentPage == 'Home') ? 'active' : ''; ?>" href="?p=Home" >
				<div class="pull-left">
					<i class="zmdi zmdi-landscape mr-20"></i>
					<span class="right-nav-text">Dashboard</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="<?php echo ($currentPage == 'Leads') ? 'active' : ''; ?>" href="?p=Leads" >
				<div class="pull-left">
					<i class="fa fa-users mr-20"></i>
					<span class="right-nav-text">Leads</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="<?php echo ($currentPage == 'Customers') ? 'active' : ''; ?>" href="?p=Customers" >
				<div class="pull-left">
					<i class="fa fa-user mr-20"></i>
					<span class="right-nav-text">Customers</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>

			<a class="<?php echo ($currentPage == 'Projects') ? 'active' : ''; ?>" href="?p=Projects" >
				<div class="pull-left">
					<i class="fa fa-folder-open mr-20"></i>
					<span class="right-nav-text">Projects</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>

			<a class="<?php echo ($currentPage == 'Tasks') ? 'active' : ''; ?>" href="?p=Tasks" >
				<div class="pull-left">
					<i class="fa fa-tasks mr-20"></i>
					<span class="right-nav-text">Tasks</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="<?php echo ($currentPage == 'Employees') ? 'active' : ''; ?>" href="?p=Employees" >
				<div class="pull-left">
					<i class="fa fa-user-md mr-20"></i>
					<span class="right-nav-text">Employees</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="<?php echo ($currentPage == 'Users') ? 'active' : ''; ?>" href="?p=Users" >
				<div class="pull-left">
					<i class="fa fa-user-plus mr-20"></i>
					<span class="right-nav-text">Users</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="<?php echo ($currentPage == 'Details') ? 'active' : ''; ?>" href="?p=Details&a=Logs" >
				<div class="pull-left">
					<i class="fa fa-clock-o mr-20"></i>
					<span class="right-nav-text">History</span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
			
			<a class="<?php echo ($currentPage == 'Settings') ? 'active' : ''; ?>" href="?p=Settings" >
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
