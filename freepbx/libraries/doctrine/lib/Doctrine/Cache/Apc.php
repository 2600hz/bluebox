<?php
/*
 *  $Id: Apc.php 5798 2009-06-02 15:10:46Z piccoloprincipe $
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
 * APC Cache Driver
 *
 * @package     Doctrine
 * @subpackage  Cache
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 5798 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Cache_Apc extends Doctrine_Cache_Driver
{
    /**
     * constructor
     * 
     * @param array $options    associative array of cache driver options
     */
    public function __construct($options = array())
    {      
        if ( ! extension_loaded('apc')) {
            throw new Doctrine_Cache_Exception('The apc extension must be loaded for using this backend !');
        }
        parent::__construct($options);
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else).
     * 
     * @param string $id cache id
     * @param boolean $testCacheValidity        if set to false, the cache validity won't be tested
     * @return mixed The stored variable on success. FALSE on failure.
     */
    public function fetch($id, $testCacheValidity = true) 
    {
        $results = apc_fetch($this->_getKey($id));
        $results = (array) $results;
        return $results[0];
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function contains($id) 
    {
        return apc_fetch($this->_getKey($id)) === false ? false : true;
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
        return (bool) apc_store($this->_getKey($id), $data, $lifeTime);
    }

    /**
     * Remove a cache record
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function delete($id) 
    {
        return apc_delete($this->_getKey($id));
    }
}