<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = &new Services_Delicious($username, $password);

$result = $dlc->deletePost('http://www.php-tools.net');
if (PEAR::isError($result)) {
	die($result->getMessage());
} else {
    echo "Success";
}
?>