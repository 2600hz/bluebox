<?php
/*
 *  $Id: Self.php 1434 2007-05-22 15:57:17Z zYne $
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
 * Doctrine_Relation_Association_Self
 *
 * @package     Doctrine
 * @subpackage  Relation
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 1434 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Relation_Nest extends Doctrine_Relation_Association
{
    /**
     * getRelationDql
     *
     * @param integer $count
     * @return string
     */
    /*public function getRelationDql($count, $context = 'record')
    {
        switch ($context) {
            case 'record':
                $identifierColumnNames = $this->definition['table']->getIdentifierColumnNames();
                $identifier = array_pop($identifierColumnNames);
                $sub    = 'SELECT '.$this->definition['foreign'] 
                        . ' FROM '.$this->definition['refTable']->getTableName()
                        . ' WHERE '.$this->definition['local']
                        . ' = ?';

                $sub2   = 'SELECT '.$this->definition['local']
                        . ' FROM '.$this->definition['refTable']->getTableName()
                        . ' WHERE '.$this->definition['foreign']
                        . ' = ?';

                $dql  = 'FROM ' . $this->definition['table']->getComponentName()
                      . '.' . $this->definition['refTable']->getComponentName()
                      . ' WHERE ' . $this->definition['table']->getComponentName()
                      . '.' . $identifier 
                      . ' IN (' . $sub . ')'
                      . ' || ' . $this->definition['table']->getComponentName() 
                      . '.' . $identifier
                      . ' IN (' . $sub2 . ')';
                break;
            case 'collection':
                $sub  = substr(str_repeat('?, ', $count),0,-2);
                $dql  = 'FROM '.$this->definition['refTable']->getComponentName()
                      . '.' . $this->definition['table']->getComponentName()
                      . ' WHERE '.$this->definition['refTable']->getComponentName()
                      . '.' . $this->definition['local'] . ' IN (' . $sub . ')';
        };

        return $dql;
    }*/

    public function fetchRelatedFor(Doctrine_Record $record)
    {
        $id = $record->getIncremented();

        if (empty($id) || ! $this->definition['table']->getAttribute(Doctrine::ATTR_LOAD_REFERENCES)) {
            return Doctrine_Collection::create($this->getTable());
        } else {
            $q = new Doctrine_RawSql($this->getTable()->getConnection());

            $assocTable = $this->getAssociationFactory()->getTableName();
            $tableName  = $record->getTable()->getTableName();
            $identifierColumnNames = $record->getTable()->getIdentifierColumnNames();
            $identifier = array_pop($identifierColumnNames);

            $sub = 'SELECT ' . $this->getForeignRefColumnName()
                 . ' FROM ' . $assocTable
                 . ' WHERE ' . $this->getLocalRefColumnName()
                 . ' = ?';

            $condition[] = $tableName . '.' . $identifier . ' IN (' . $sub . ')';
            $joinCondition[] = $tableName . '.' . $identifier . ' = ' . $assocTable . '.' . $this->getForeignRefColumnName();

            if ($this->definition['equal']) {
                $sub2   = 'SELECT ' . $this->getLocalRefColumnName()
                        . ' FROM '  . $assocTable
                        . ' WHERE ' . $this->getForeignRefColumnName()
                        . ' = ?';

                $condition[] = $tableName . '.' . $identifier . ' IN (' . $sub2 . ')';
                $joinCondition[] = $tableName . '.' . $identifier . ' = ' . $assocTable . '.' . $this->getLocalRefColumnName();
            }
            $q->select('{'.$tableName.'.*}, {'.$assocTable.'.*}')
              ->from($tableName . ' INNER JOIN ' . $assocTable . ' ON ' . implode(' OR ', $joinCondition))
              ->where(implode(' OR ', $condition))
              ->orderBy($tableName . '.' . $identifier . ' ASC');
            $q->addComponent($tableName,  $this->getClass());

            $path = $this->getClass(). '.' . $this->getAssociationFactory()->getComponentName();
            if ($this->definition['refClassRelationAlias']) {
                $path = $this->getClass(). '.' . $this->definition['refClassRelationAlias'];
            }
            $q->addComponent($assocTable, $path);

            $params = ($this->definition['equal']) ? array($id, $id) : array($id);

            $res = $q->execute($params);

            return $res;
        }
    }
}
