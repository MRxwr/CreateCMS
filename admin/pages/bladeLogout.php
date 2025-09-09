<?php
setcookie("cmsCreate", "", time() - 3600, '/');
header('LOCATION: login.php');
?>