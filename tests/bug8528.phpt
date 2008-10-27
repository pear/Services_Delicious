--TEST--
Services_Delicious:bug#8528
--SKIPIF--
<?php
require_once 'config.php';
?>
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = new Services_Delicious($username, $password);

$result = $dlc->getRecentPosts();
if (PEAR::isError($result)) {
    echo $result->message;
} else {
    echo 'OK';
}
?>
--EXPECT--
OK
