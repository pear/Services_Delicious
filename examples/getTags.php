<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = &new Services_Delicious($username, $password);

$tags = $dlc->getTags();
echo '<pre>';
print_r($tags);
echo '</pre>';
?>