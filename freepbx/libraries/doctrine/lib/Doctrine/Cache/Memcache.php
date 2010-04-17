<?php
/*
 *  $Id: Memcache.php 5798 2009-06-02 15:10:46Z piccoloprincipe $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Memcache cache driver
 *
 * @package     Doctrine
 * @subpackage  Cache
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 5798 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Cache_Memcache extends Doctrine_Cache_Driver
{
    /**
     * @var Memcache $_memcache     memcache object
     */
    protected $_memcache = null;

    /**
     * constructor
     * 
     * @param array $options        associative array of cache driver options
     */
    public function __construct($options = array())
    {      
        if ( ! extension_loaded('memcache')) {
            throw new Doctrine_Cache_Exception('In order to use Memcache driver, the memcache extension must be loaded.');
        }
        parent::__construct($options);

        if (isset($options['servers'])) {
            $value= $options['servers'];
            if (isset($value['host'])) {
                // in this case, $value seems to be a simple associative array (one server only)
                $value = array(0 => $value); // let's transform it into a classical array of associative arrays
            }
            $this->setOption('servers', $value);
        }
        
        $this->_memcache = new Memcache;

        foreach ($this->_options['servers'] as $server) {
            if ( ! array_key_exists('persistent', $server)) {
                $server['persistent'] = true;
            }
            if ( ! array_key_exists('port', $server)) {
                $server['port'] = 11211;
            }
            $this->_memcache->addServer($server['host'], $server['port'], $server['persistent']);
        }
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     * 
     * @param string $id cache id
     * @param boolean $testCacheValidity        if set to false, the cache validity won't be tested
     * @return mixed The stored variable on success. FALSE on failure.
     */
    public function fetch($id, $testCacheValidity = true) 
    {
        return $this->_memcache->get($this->_getKey($id));
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function contains($id) 
    {
        return (bool) $this->_memcache->get($this->_getKey($id));
    }

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always saved as a string
     *
     * @param string $data      data to cache
     * @param string $id        cache id
     * @param int $lifeTime     if != false, set a specific lifetime for this cache record (null => infinite lifeTime)
     * @return boolean true if no problem
     */
    public function save($id, $data, $lifeTime = false)
    {
        if ($this->_options['compression']) {
            $flag = MEMCACHE_COMPRESSED;
        } else {
            $flag = 0;
        }

        $result = $this->_memcache->set($this->_getKey($id), $data, $flag, $lifeTime);
    }

    /**
     * Remove a cache record
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function delete($id) 
    {
        return $this->_memcache->delete($this->_getKey($id));
    }
}