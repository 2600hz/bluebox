<?php
/*
 *  $Id: Driver.php 5901 2009-06-22 15:44:45Z jwage $
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
 * Abstract cache driver class
 *
 * @package     Doctrine
 * @subpackage  Cache
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 5901 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
abstract class Doctrine_Cache_Driver implements Doctrine_Cache_Interface
{
    /**
     * @var array $_options      an array of options
     */
    protected $_options = array();

    /**
     * Configure cache driver with an array of options
     *
     * @param array $_options      an array of options
     */
    public function __construct($options = array()) 
    {
        $this->_options = $options;
    }

    /**
     * Set option name and value
     *
     * @param mixed $option     the option name
     * @param mixed $value      option value
     * @return boolean          TRUE on success, FALSE on failure
     */
    public function setOption($option, $value)
    {
        if (isset($this->_options[$option])) {
            $this->_options[$option] = $value;
            return true;
        }
        return false;
    }

    /**
     * Get value of option
     * 
     * @param mixed $option     the option name
     * @return mixed            option value
     */
    public function getOption($option)
    {
        if ( ! isset($this->_options[$option])) {
            return null;
        }

        return $this->_options[$option];
    }

    /**
     * Get the hash key passing its suffix
     *
     * @param string $id  The hash key suffix
     * @return string     Hash key to be used by drivers
     */
    protected function _getKey($id)
    {
        return (isset($this->_options['prefix']) ? $this->_options['prefix'] : '') . $id;
    }
}