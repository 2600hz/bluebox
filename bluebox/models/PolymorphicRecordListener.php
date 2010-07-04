<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */

class PolymorphicRecordListener extends Doctrine_Record_Listener
{
    protected $parent = NULL;

    public function  __construct($parent) {
        $this->parent = $parent;
    }

    public function postValidate($event) {
        $invoker =& $event->getInvoker();

        $conn = Doctrine_Manager::connection();
        $invalid = $conn->transaction->getInvalid();

        if(!empty($invalid)) {
            kohana::log('debug', 'Initializing foreign_id');
            if (!is_int($invoker['foreign_id'])) {
                $invoker['foreign_id'] = 0;
            }
        } else {
            kohana::log('debug', 'Leaving foreign_id alone');
        }
    }
}