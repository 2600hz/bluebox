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
 * Doctrine_Ticket_1134_TestCase
 *
 * @package     Doctrine
 * @author      Jeff Hansen <jhansen@hivalley.com>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Ticket_1134_TestCase extends Doctrine_UnitTestCase 
{
    public function prepareTables()
    {
        $this->tables[] = 'Ticket_1134_User';
        parent::prepareTables();
    }


    public function prepareData()
    {
        $user = new Ticket_1134_User();
		$user->is_pimp = TRUE;
        $user->save();
    }


    public function testAfterOriginalSave()
    {
        $user = Doctrine_Query::create()->from('Ticket_1134_User u')->fetchOne();
        $this->assertEqual($user->is_pimp, TRUE);
    
    }

    public function testAfterModification()
    {
        $user = Doctrine_Query::create()->from('Ticket_1134_User u')->fetchOne();
		$user->is_pimp = "1";
		$this->assertEqual($user->getModified(), FALSE);    
    }	
	
}

class Ticket_1134_User extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('is_pimp', 'boolean', TRUE);
    }

    public function setUp()
    {
    }
}
