<?php
require_once 'config.php';
require_once 'Services/Delicious.php';

$dlc = &new Services_Delicious($username, $password);

$posts = $dlc->getPosts('pear');
echo '<pre>';
print_r($posts);
echo '</pre>';

$posts = $dlc->getRecentPosts();
echo '<pre>';
print_r($posts);
echo '</pre>';
?>