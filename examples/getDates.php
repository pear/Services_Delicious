<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = &new Services_Delicious($username, $password);

$dates = $dlc->getDates();
echo '<pre>';
print_r($dates);
echo '</pre>';
?>