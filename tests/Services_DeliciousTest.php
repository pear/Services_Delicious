<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Services/Delicious.php';
require_once 'HTTP/Request2.php';
require_once 'HTTP/Request2/Adapter/Mock.php';

class Services_DeliciousTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->mock = new HTTP_Request2_Adapter_Mock();

        $this->request = new HTTP_Request2();
        $this->request->setAdapter($this->mock);

        $this->sd = new Services_Delicious('', '', $this->request);
    }

    public function aResponse($path) {
        $response = new HTTP_Request2_Response('HTTP/1.1 200 OK');
        $response->appendBody(file_get_contents($path));

        return $response;
    }

    public function testShouldGetRecentPosts() {
        $this->mock->addResponse($this->aResponse(dirname(__FILE__) . '/getRecentPosts.xml'));

        $result = $this->sd->getRecentPosts();

        $this->assertFalse(PEAR::isError($result));


        $this->markTestIncomplete(print_r($result, true));
    }


    public function testShouldGetPosts() {
        $this->mock->addResponse($this->aResponse(dirname(__FILE__) . '/getPosts.xml'));

        $result = $this->sd->getPosts();

        $this->assertFalse(PEAR::isError($result));

        $this->markTestIncomplete(print_r($result, true));
    }


    public function testShouldAddPost() {
        $this->mock->addResponse($this->aResponse(dirname(__FILE__) . '/addPost.xml'));

        $params = array(
            'url' => 'http://www.php.net',
            'description' => 'php',
            'shared' => 'no',
        );

        $result = $this->sd->addPost($params);

        $this->assertFalse(PEAR::isError($result));

        $this->markTestIncomplete(print_r($result, true));
    }

    public function testShouldGetTagsBundles() {
        $this->mock->addResponse($this->aResponse(dirname(__FILE__) . '/getTagsBundles.xml'));

        $result = $this->sd->getTagsBundles();

        $this->assertFalse(PEAR::isError($result));

        $this->markTestIncomplete(print_r($result, true));
    }
}