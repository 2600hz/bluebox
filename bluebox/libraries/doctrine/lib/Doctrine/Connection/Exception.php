<?php
/*
 *  $Id: Exception.php 5798 2009-06-02 15:10:46Z piccoloprincipe $
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
 * Doctrine_Exception
 *
 * @package     Doctrine
 * @subpackage  Connection
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 5798 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Connection_Exception extends Doctrine_Exception
{
    /**
     * @var array $errorMessages        an array containing messages for portable error codes
     */
    static protected $errorMessages = array(
                Doctrine::ERR                    => 'unknown error',
                Doctrine::ERR_ALREADY_EXISTS     => 'already exists',
                Doctrine::ERR_CANNOT_CREATE      => 'can not create',
                Doctrine::ERR_CANNOT_ALTER       => 'can not alter',
                Doctrine::ERR_CANNOT_REPLACE     => 'can not replace',
                Doctrine::ERR_CANNOT_DELETE      => 'can not delete',
                Doctrine::ERR_CANNOT_DROP        => 'can not drop',
                Doctrine::ERR_CONSTRAINT         => 'constraint violation',
                Doctrine::ERR_CONSTRAINT_NOT_NULL=> 'null value violates not-null constraint',
                Doctrine::ERR_DIVZERO            => 'division by zero',
                Doctrine::ERR_INVALID            => 'invalid',
                Doctrine::ERR_INVALID_DATE       => 'invalid date or time',
                Doctrine::ERR_INVALID_NUMBER     => 'invalid number',
                Doctrine::ERR_MISMATCH           => 'mismatch',
                Doctrine::ERR_NODBSELECTED       => 'no database selected',
                Doctrine::ERR_NOSUCHFIELD        => 'no such field',
                Doctrine::ERR_NOSUCHTABLE        => 'no such table',
                Doctrine::ERR_NOT_CAPABLE        => 'Doctrine backend not capable',
                Doctrine::ERR_NOT_FOUND          => 'not found',
                Doctrine::ERR_NOT_LOCKED         => 'not locked',
                Doctrine::ERR_SYNTAX             => 'syntax error',
                Doctrine::ERR_UNSUPPORTED        => 'not supported',
                Doctrine::ERR_VALUE_COUNT_ON_ROW => 'value count on row',
                Doctrine::ERR_INVALID_DSN        => 'invalid DSN',
                Doctrine::ERR_CONNECT_FAILED     => 'connect failed',
                Doctrine::ERR_NEED_MORE_DATA     => 'insufficient data supplied',
                Doctrine::ERR_EXTENSION_NOT_FOUND=> 'extension not found',
                Doctrine::ERR_NOSUCHDB           => 'no such database',
                Doctrine::ERR_ACCESS_VIOLATION   => 'insufficient permissions',
                Doctrine::ERR_LOADMODULE         => 'error while including on demand module',
                Doctrine::ERR_TRUNCATED          => 'truncated',
                Doctrine::ERR_DEADLOCK           => 'deadlock detected',
                );

    /**
     * @see Doctrine::ERR_* constants
     * @since 1.0
     * @var integer $portableCode           portable error code
     */
    protected $portableCode;

    /**
     * getPortableCode
     * returns portable error code
     *
     * @return integer      portable error code
     */
    public function getPortableCode()
    {
        return $this->portableCode;
    }

    /**
     * getPortableMessage
     * returns portable error message
     *
     * @return string       portable error message
     */
    public function getPortableMessage()
    {
        return self::errorMessage($this->portableCode);
    }

    /**
     * Return a textual error message for a Doctrine error code
     *
     * @param   int|array   integer error code,
     *                           null to get the current error code-message map,
     *                           or an array with a new error code-message map
     *
     * @return  string  error message, or false if the error code was
     *                  not recognized
     */
    public function errorMessage($value = null)
    {
        return isset(self::$errorMessages[$value]) ?
           self::$errorMessages[$value] : self::$errorMessages[Doctrine::ERR];
    }

    /**
     * This method checks if native error code/message can be
     * converted into a portable code and then adds this
     * portable error code to $portableCode field
     *
     * @param array $errorInfo      error info array
     * @since 1.0
     * @return boolean              whether or not the error info processing was successfull
     *                              (the process is successfull if portable error code was found)
     */
    public function processErrorInfo(array $errorInfo)
    { }
}