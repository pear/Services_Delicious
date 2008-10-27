--TEST--
Services_Delicious:Req#7293
--SKIPIF--
<?php
require_once 'config.php';
?>
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = new Services_Delicious($username, $password);

// no wait
$result = $dlc->getPosts();
$result = $dlc->getPosts();
if (PEAR::isError($result)) {
    echo $result->message;
} else {
    echo 'OK';
}
echo "\n";

sleep(1);

// wait 1s
$result = $dlc->getPosts();
sleep(1);
$result = $dlc->getPosts();
if (PEAR::isError($result)) {
    echo $result->message;
} else {
    echo 'OK';
}

?>
--EXPECT--
Wait 1 second between queries
OK
