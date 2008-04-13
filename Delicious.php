<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Stephan Schmidt <schst@php-tools.net>                       |
// +----------------------------------------------------------------------+
//
//    $Id$

/**
 * uses PEAR error management
 */
require_once 'PEAR.php';

/**
 * uses XML_Serializer to read result
 */
require_once 'XML/Unserializer.php';

/**
 * uses HTTP to send the request
 */
require_once 'HTTP/Request.php';

/**
 * Services_Delicious
 *
 * Client for the REST-based webservice at http://del.ico.us
 *
 * del.icio.us is a site for social bookmarking, that means that you bookmark
 * your favourite sites, assign them to one or more topics (tags) and other users
 * are able to browse through the bookmarks.
 *
 * Services_Delicious allows you to
 * - get
 * - add
 * - delete
 * your bookmarks from PHP.
 *
 * @author      Stephan Schmidt <schst@phap-tools.net>
 * @author      Tatsuya Tsuruoka <ttsuruoka@php.net>
 * @package     Services_Delicious
 * @version     0.5
 */
class Services_Delicious
{
   /**
    * URI of the REST API
    *
    * @access  private
    * @var     string
    */
    var $_apiUrl = 'https://api.del.icio.us/v1';
    
   /**
    * Username
    *
    * @access  private
    * @var     string
    */
    var $_user   = null;

   /**
    * password
    *
    * @access  private
    * @var     string
    */
    var $_passwd = null;
    
   /**
    * XML_Unserializer, used to parse the XML
    *
    * @access  private
    * @var     object XML_Unserializer
    */
    var $_us = null;
    
   /**
    * Last accessed time
    *
    * @access  private
    * @var     integer
    */
    var $_last_time = null;

   /**
    * Create a new client
    *
    * @access  public
    * @param   string      username
    * @param   string      password
    */
    function Services_Delicious($user, $passwd)
    {
        $this->_user   = $user;
        $this->_passwd = $passwd;
    }
    
   /**
    * Get all tags
    *
    * This will return an associative array containing tags
    * in the keys and their occurences in the values:
    * <code>
    * Array
    * (
    *    [pear] => 1
    *    [php] => 2
    *)
    *</code>
    *
    * @access  public
    * @return  array
    */
    function getTags()
    {
        $result = $this->_sendRequest('tags', 'get');
        if (PEAR::isError($result)) {
            return $result;
        }
        $tags = array();
        if (is_array($result['tag'])) {
            foreach ($result['tag'] as $tmp) {
                $tags[$tmp['tag']] = $tmp['count'];
            }
        }
        return $tags;
    }


   /**
    * Retrieve all of a user's bundles
    *
    * This will return an associative array containing bundles
    * in the keys and their tags in the values:
    * <code>
    * Array
    * (
    *    [music] => "ipod mp3 music"
    * )
    * </code>
    *
    * @access   public
    * @return   array
    */
    function getTagsBundles()
    {
        $result = $this->_sendRequest('tags/bundles', 'all');
        if (PEAR::isError($result)) {
            return $result;
        }

        $bundles = array();
        if (!empty($result['bundle']) && is_array($result['bundle'])) {
            if (isset($result['bundle']['name'])) {
                $bundles[$result['bundle']['name']] = $result['bundle']['tags'];
            } else {
                foreach ($result['bundle'] as $bundle) {
                    $bundles[$bundle['name']] = $bundle['tags'];
                }
            }
        }

        return $bundles;
    }

   /**
    * Assign a set of tags to a single bundle
    *
    * @access   public
    * @param    string          bundle name
    * @param    string|array    list of tags
    * @return   boolean
    */
    function setTagsBundle($bundle, $tags)
    {
        if (is_array($tags)) {
            $tags = implode(" ", $tags);
        }

        $params = array(
                        'bundle'    => $bundle,
                        'tags'      => $tags
                    );

        $result = $this->_sendRequest('tags/bundles', 'set', $params);

        return $this->_resultToBoolean($result);
    }

   /**
    * Delete a bundle
    *
    * @acces    public
    * @param    string      bundle name
    * @returnn  boolean
    */
    function deleteTagsBundle($bundle)
    {
        $params = array(
                        'bundle' => $bundle
                    );

        $result = $this->_sendRequest('tags/bundles', 'delete', $params);

        return $this->_resultToBoolean($result);
    }
    
   /**
    * Rename a tag
    *
    * @access   public
    * @param    string      old name
    * @param    string      new name
    * @return   boolean
    */
    function renameTag($old, $new)
    {
        $params = array(
                        'old' => $old,
                        'new' => $new
                    );
        $result = $this->_sendRequest('tags', 'rename', $params);
        
        return $this->_resultToBoolean($result);
    }

   /**
    * Get all dates on which posts have been added.
    *
    * This will return an associative array containing dates
    * in the keys and their occurences in the values:
    * <code>
    * Array
    * (
    *    [2004-11-01] => 1
    *    [2004-11-02] => 2
    *)
    *</code>
    *
    * @access  public
    * @return  array
    */
    function getDates()
    {
        $result = $this->_sendRequest('posts', 'dates');
        if (PEAR::isError($result)) {
            return $result;
        }
        $dates = array();
        if (is_array($result['date'])) {
            foreach ($result['date'] as $tmp) {
                $dates[$tmp['date']] = $tmp['count'];
            }
        }
        return $dates;
    }
    
   /**
    * Get posts
    *
    * @access   public
    * @param    string|array    one or more tags
    * @param    string          date
    * @return   array
    */
    function getPosts($tags = array(), $date = null)
    {
        $params = array();
        if (!empty($tags)) {
            $params['tag'] = $tags;
        }
        if (!empty($date)) {
            $params['dt'] = $date;
        }
        
        $result = $this->_sendRequest('posts', 'get', $params);
        if (PEAR::isError($result)) {
            return $result;
        }

        $posts  = array();
        if (!empty($result['post']) && is_array($result['post'])) {
            foreach ($result['post'] as $post) {
                $post['tag'] = explode(' ', $post['tag']);
                $posts[] = $post;
            }
        }
        return $posts;
    }
    
   /**
    * Get recent posts
    *
    * @access   public
    * @param    string|array    one or more tags
    * @param    integer         maximum amount
    * @return   array
    */
    function getRecentPosts($tags = array(), $max = 15)
    {
        $params = array('count' => $max);
        if (!empty($tags)) {
            $params['tag'] = $tags;
        }
        
        $result = $this->_sendRequest('posts', 'recent', $params);
        if (PEAR::isError($result)) {
            return $result;
        }

        $posts  = array();
        if (!empty($result['post']) && is_array($result['post'])) {
            foreach ($result['post'] as $post) {
                $post['tag'] = explode(' ', $post['tag']);
                $posts[] = $post;
            }
        }
        
        return $posts;
    }

   /**
    * Get all posts
    *
    * @access   public
    * @param    string|array    one or more tags
    * @param    string          date
    * @return   array
    */
    function getAllPosts()
    {
        $result = $this->_sendRequest('posts', 'all');
        if (PEAR::isError($result)) {
            return $result;
        }

        $posts  = array();
        if (!empty($result['post']) && is_array($result['post'])) {
            foreach ($result['post'] as $post) {
                $post['tag'] = explode(' ', $post['tag']);
                $posts[] = $post;
            }
        }
        
        return $posts;
    }

   /**
    * Add a post
    *
    * @access   public
    * @param    string|array    url or all data for the post
    * @param    string          description
    * @param    string          extended description
    * @param    string          tags for the items
    * @param    string          datestamp
    * @param    string          make the item private if "$shared = 'no'"
    * @return   boolean
    */
    function addPost($url, $description = null, $extended = null, $tags = null, $date = null, $shared = null)
    {
        if (is_array($url)) {
            $params = $url;
            if (!isset($params['dt'])) {
                $params['dt'] = strftime('%Y-%m-%dT%h:%i:%sZ', time());
            }
        } else {
            if (is_null($date)) {
                $date = strftime('%Y-%m-%dT%h:%i:%sZ', time());
            } else {
                $tmp = strtotime($date);
                if ($tmp) {
                    $date = strftime('%Y-%m-%dT%h:%i:%sZ', $date);
                }
            }
            $params = array(
                             'url'         => $url,
                             'description' => $description,
                             'extended'    => $extended,
                             'tags'        => $tags,
                             'dt'          => $date,
                             'shared'      => $shared
                            );
        }
        
        $result = $this->_sendRequest('posts', 'add', $params);
        
        return $this->_resultToBoolean($result);
    }

   /**
    * Delete a post
    *
    * @access   public
    * @param    string|array    url or all data for the post
    * @param    string          description
    * @param    string          extended description
    * @param    
    * @return   boolean
    */
    function deletePost($url)
    {
        $params = array(
                         'url' => $url
                       );

        $result = $this->_sendRequest('posts', 'delete', $params);
        
        return $this->_resultToBoolean($result);
    }
    
   /**
    * Returns the last update time for the user
    *
    * @access   public
    * @return   string
    */
    function getLastUpdate()
    {
        $result = $this->_sendRequest('posts', 'update');      

        return $result['time'];
    }

   /**
    * Auxiliary method to send a request
    *
    * @access   private
    * @param    string      what to fetch
    * @param    string      action
    * @param    array       parameters
    * @return   array|PEAR_Error
    */
    function _sendRequest($subject, $verb, $params = array())
    {
        list($usec, $sec) = explode(' ', microtime());
        $current_time = $sec . sprintf('%03d', (integer)($usec * 1000));
        $last_time = $this->_last_time;
        $this->_last_time = $current_time;
        if ($current_time - $last_time < 1000) {
            return PEAR::raiseError('Wait 1 second between queries');
        }
        
        $url = sprintf('%s/%s/%s?', $this->_apiUrl, $subject, $verb);
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
            $url = $url . '&' . $key . '=' . urlencode($value);
        }
        
        $request = &new HTTP_Request($url);
        $request->setBasicAuth($this->_user, $this->_passwd);
        $request->addHeader('User-Agent', 'PEAR::Services_Delicious');

        $request->sendRequest();
        if ($request->getResponseCode() !== 200) {
            return PEAR::raiseError('Invalid Response Code', $request->getResponseCode());
        }
        
        $xml = $request->getResponseBody();
        
        if (!is_object($this->_us)) {
            $this->_us = &new XML_Unserializer();
            $this->_us->setOption('parseAttributes', true);
            $this->_us->setOption('forceEnum', array(
                                                       'tag',
                                                       'post',
                                                       'date'
                                                    )
                                 );
        }
        
        $result = $this->_us->unserialize($xml);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $this->_us->getUnserializedData();
    }

   /**
    * convert a result from del.icio.us to a boolean
    * value or PEAR_Error
    *
    * @access   private
    * @param    mixed
    * @return   boolean
    */
    function _resultToBoolean($result)
    {
        if (PEAR::isError($result)) {
            return $result;
        }
        if ($result == 'done' || $result == 'ok') {
            return true;
        }
        
        if ($result['code'] == 'done') {
            return true;
        }
        if (is_string($result)) {
            $error = $result;
        } else {
            $error = $result['code'];
        }
        return PEAR::raiseError('Error from del.icio.us: '. $error);
    }
}
?>
