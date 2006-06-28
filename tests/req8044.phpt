--TEST--
Services_Delicious:Req#8044
--SKIPIF--
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = new Services_Delicious($username, $password);

$params = array(
    'url' => 'http://www.php.net',
    'description' => 'php',
    'shared' => 'no',
);
$result = $dlc->addPost($params);
if (PEAR::isError($result)) {
    echo $result->message;
} else {
    echo 'OK';
}
?>
--EXPECT--
OK
