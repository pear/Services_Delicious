--TEST--
Services_Delicious:bug#9634 foreach error on empty array
--SKIPIF--
<?php
require_once 'config.php';
?>
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

// Services_Delicious for test
class Services_DeliciousTest extends Services_Delicious
{
    function _sendRequest($subject, $verb, $params = array())
    {
        return array(
            'tag' => '',
            'user' => 'username',
        );
    }
}

$dlc = new Services_DeliciousTest($username, $password);

$result = $dlc->getRecentPosts();
if (PEAR::isError($result)) {
    echo $result->message;
} else {
    echo 'OK';
}
?>
--EXPECT--
OK
