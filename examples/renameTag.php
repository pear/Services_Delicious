<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = &new Services_Delicious($username, $password);

$result = $dlc->renametag(null, null);
if (PEAR::isError($result)) {
	die($result->getMessage());
} else {
    echo "Success";
}
?>