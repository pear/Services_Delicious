<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = &new Services_Delicious($username, $password);

$result = $dlc->addPost('http://www.php-tools.net', 'PHP Application Tools', null, 'php');
if (PEAR::isError($result)) {
	die($result->getMessage());
} else {
    echo "Success";
}
?>