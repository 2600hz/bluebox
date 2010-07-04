<?php
/*
 *  $Id$
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
 * Doctrine_Ticket_1417_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Ticket_1417_TestCase extends Doctrine_UnitTestCase 
{
    public function testTest()
    {
        $user = new User();
        $user->name = 'jwagejon';
        $this->assertEqual($user->getModified(), array('name' => 'jwagejon'));
        $this->assertEqual($user->getModified(true), array('name' => null));
        $user->save();
        $this->assertEqual($user->getModified(), array());
        $this->assertEqual($user->getModified(true), array());
        $this->assertEqual($user->getLastModified(), array('name' => 'jwagejon', 'type' => 0));
        $this->assertEqual($user->getLastModified(true), array('name' => null, 'type' => null));
        $user->name = 'jon';
        $this->assertEqual($user->getModified(), array('name' => 'jon'));
        $this->assertEqual($user->getModified(true), array('name' => 'jwagejon'));
        $user->save();
        $this->assertEqual($user->getModified(), array());
        $this->assertEqual($user->getModified(true), array());
        $this->assertEqual($user->getLastModified(), array('name' => 'jon'));
        $this->assertEqual($user->getLastModified(true), array('name' => 'jwagejon'));
    }
}