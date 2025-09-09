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
	$menuItems = [
		['p' => 'Home','icon' => 'zmdi zmdi-landscape','text' => 'Dashboard'],
		['p' => 'Leads','icon' => 'fa fa-users','text' => 'Leads'],
		['p' => 'Customers','icon' => 'fa fa-user','text' => 'Customers'],
		['p' => 'Employees','icon' => 'fa fa-user-md','text' => 'employees'],
		['p' => 'Users','icon' => 'fa fa-user-plus','text' => 'Users'],
		['p' => 'Details','icon' => 'fa fa-clock-o','text' => 'History','extra' => '&a=Logs'],
		['p' => 'Settings','icon' => 'fa fa-gears','text' => 'Settings']
	];
	foreach($menuItems as $item){
		$isActive = ($currentPage == $item['p']) ? 'active' : '';
		$href = '?p=' . $item['p'];
		if (isset($item['extra'])) $href .= $item['extra'];
		?>
		<li>
			<a class="<?php echo $isActive; ?>" href="<?php echo $href; ?>" >
				<div class="pull-left">
					<i class="<?php echo $item['icon']; ?> mr-20"></i>
					<span class="right-nav-text"><?php echo $item['text']; ?></span>
				</div>
				<div class="pull-right"></div>
				<div class="clearfix"></div>
			</a>
		</li>
		<?php
	}
}
