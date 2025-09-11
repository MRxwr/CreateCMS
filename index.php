<?php 
require_once('admin/includes/config.php');
require_once('admin/includes/functions.php');
require_once('includes/auth.php');

// Require authentication for all pages
requireAuth();

require_once('templates/header.php');

// Show welcome message for new users
if (isset($_GET['welcome']) && $_GET['welcome'] == '1') {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i>
            <strong>Welcome!</strong> Your account has been created successfully. Enjoy using our platform!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

if( isset($_GET["v"]) && searchFile("views","blade{$_GET["v"]}.php") ){
	require_once("views/".searchFile("views","blade{$_GET["v"]}.php"));
}else{
	require_once("views/bladeHome.php");
}

require_once('templates/footer.php');
?>