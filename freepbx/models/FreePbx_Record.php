<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 * K Anderson
 *
 */

/**
 * FreePbx_Record - Extends doctrine's record class
 *
 * Always use this class instead of Doctrine_Record when creating a model.
 * Provides a significant number of integrations to validation and the event system.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */
abstract class FreePbx_Record extends Doctrine_Record {

    /**
     * @var string $_dirtyNumbers                 number types to regenerate each time this record type is saved (if any)
     */
    public $_dirtyNumbers = NULL;

    /**
     * @var array this is an array of defaul validation error messages based on the type of failure, it only applies to freepbx_record
     */
    public static $defaultErrors = array(
        'required' => 'Field is required.',
        'default' => 'Field contains an error.'
    );

    /**
     * @var array this is an array of validation error messages used expected to be overridden in freepbx models
     */
    public static $errors = array();

    /**
     * Any time we save a record and it's associations, various events may need access to the base record that save()
     * was called on (and it's associations). We make that available here, as Doctrine does not provide a simple way to
     * see the base record via getInvoker() once you are already into the associations
     *
     * @var FreePbx_Record
     */
    protected static $baseSaveObject = NULL;

    /**
     * Forcibly mark a field as dirty.
     *
     * Use this method to forcibly set a field of type object as modified, to force Doctrine to save it.
     *
     * @param string $fieldName  FieldName (must be of type object!)
     */
    public function markModified($fieldName)
    {
        $this->_modified[] = $fieldName;
        $this->_oldValues[$fieldName] = $this->_data[$fieldName]; // Just in case someone goes a-lookin

        // Ensure the record is marked as dirty now
        switch ($this->_state) {
            case Doctrine_Record::STATE_CLEAN:
            case Doctrine_Record::STATE_PROXY:
                $this->_state = Doctrine_Record::STATE_DIRTY;
                break;
            case Doctrine_Record::STATE_TCLEAN:
                $this->_state = Doctrine_Record::STATE_TDIRTY;
                break;
        }
    }

    public function save() {
        Kohana::log('debug', 'Saving record ' . get_class($this));

        // If column aggregation is used on this model we will have $class_type defined. Ensure it's set model is loaded
        if (!empty($this->class_type)) {
            kohana::log('debug', 'Initialized model ' . $this->class_type . ' for save operation.');
            Doctrine::initializeModels($this->class_type);
        }

        // Look for Kohana errors, and if they exist, throw FreePbx_Validation_Exception
        if ((count(FreePbx_Controller::$validation->field_names()) > 0) and (FreePbx_Controller::$validation->validate() == FALSE)) {
            Kohana::log('debug', 'Kohana validation failed while saving a record.', 1);
            throw new FreePbx_Validation_Exception('Kohana validation failed.', 1);   // re-throw
        }

        try {
            // Store only the first record in this save event, making it available for any events that may need to know who started the save
            if (!self::getBaseTransactionObject()) {
                self::setBaseSaveObject($this);

                $invalid = $this->checkValidation();
                if(!empty($invalid)) {
                    throw new Doctrine_Validator_Exception($invalid);
                }
            }
            
            parent::save();

            // See if the base object requires phone number re-generation
            // And the Doctrine hacks get even dirtier... [sigh]
            if ($this->_dirtyNumbers) {
                $id_column = $this->_dirtyNumbers['id'];
                $class_type = $this->_dirtyNumbers['type'];

                $results = Doctrine_Query::create()
                    ->select('number_id')
                    ->from('Number')
                    ->where('foreign_id = ?', $this->$id_column)
                    ->andWhere('class_type = ?', $class_type)
                    ->execute();

                foreach ($results as $result) {
                    Telephony::set($result);
                }

                Telephony::save();

                Telephony::commit();
            }

            // Done with any events that may use this
            if (self::getBaseTransactionObject() == $this) {
                self::setBaseSaveObject(NULL);
            }
            
            return TRUE;
        } catch (Doctrine_Validator_Exception $e) {
            Kohana::log('error', 'Doctrine_Validator_Exception on record ' .get_class($this) . ': ' . $e->getMessage());
            self::normalizeErrors($e);
            return FALSE;
        } catch (Doctrine_Transaction_Exception $e) {
            Kohana::log('error', 'Doctrine_Transaction_Exception on record ' .get_class($this) . ': ' . $e->getMessage());
            self::setBaseSaveObject(NULL);
            throw new Doctrine_Transaction_Exception($e->getMessage());
            return FALSE;
        } catch (Doctrine_Connection_Exception $e) {
            Kohana::log('error', 'Doctrine_Connection_Exception on record ' .get_class($this) . ': ' . $e->getMessage());
            self::setBaseSaveObject(NULL);
            throw new Doctrine_Connection_Exception($e->getMessage());
            return FALSE;
        } catch (Exception $e) {
            Kohana::log('error', 'Unhandled ' . get_class($e) . ' on record ' .get_class($this) . ': ' . $e->getMessage());
            self::setBaseSaveObject(NULL);
            message::set($e->getMessage());
            throw new Exception($e->getMessage());
            return FALSE;
        }
    }

    public function delete() {
        Kohana::log('debug', 'Delete record ' . get_class($this));

        // If column aggregation is used on this model we will have $class_type defined. Ensure it's set model is loaded
        if (isset($this->class_type)) {
            kohana::log('debug', 'Initialized model ' . $this->class_type . ' for delete operation.');
            Doctrine::initializeModels($this->class_type);
        }

        /**
         * Commented out by Karl, we set this manually now.
         */
        //if (!self::getBaseTransactionObject())
        //    self::setBaseSaveObject($this);
            
        try {
            // Before we run the parrent we need to unlink our number
            // this is because the parent will lock the record and if we
            // are proxied then we will not be able to get the necessary fields
            // populated (ya that was a bitch to figure out)
            if (get_parent_class($this) == 'FreePbx_Record') {
                $class_type = get_class($this) . 'Number'; //transform to class name
            } else {
                $class_type = get_parent_class($this) . 'Number'; //transform to original parent's class name
            }
            $identifier = $this->identifier();
            $q = Doctrine_Query::create()
                ->from('Number n')
                ->where('class_type = ?', $class_type)
                ->andWhere('foreign_id = ?', reset($identifier));
            $numbers = $q->execute();

            foreach ($numbers as $number) {
                kohana::log('error', 'Found number ' . $number->class_type . ' ' . $number->foreign_id);
                $number->class_type = NULL;
                $number->foreign_id = 0;
            }

            parent::delete();

            // Ok now if we got this far go ahead an unlink the number, we didnt
            // unlink above because we where not sure if we are in a transaction
            // and if we are not we want to delete to succeed before we unlink :)
            if (!empty($numbers)) {
                foreach ($numbers as $number) {
                    kohana::log('debug', 'Deleted record had a number attached, unlinking number ' . $number->number_id);
                    $number->save();
                }
            }

            /**
             * Commented out by Karl, we need this to persist because delete are
             * executed individually... so we clear this manually now.
             *
             * NOTE: this means we can not delete in Doctrine collections or things
             * will not work properly!!
             */
            // Done with any events that may use this
            //if (self::getBaseTransactionObject() == $this) {
            //    self::setBaseSaveObject(NULL);
            //}

            return TRUE;
        } catch (Doctrine_Validator_Exception $e) {
            Kohana::log('error', 'Doctrine_Validator_Exception: ' . $e->getMessage());
            self::normalizeErrors($e);
            return FALSE;
        } catch (Doctrine_Transaction_Exception $e) {
            Kohana::log('error', 'Doctrine_Transaction_Exception: ' . $e->getMessage());
            self::setBaseSaveObject(NULL);
            throw new Doctrine_Transaction_Exception($e->getMessage());
            return FALSE;
        } catch (Doctrine_Connection_Exception $e) {
            Kohana::log('error', 'Doctrine_Connection_Exception (' .get_class($this) . '): ' . $e->getMessage());
            self::setBaseSaveObject(NULL);
            throw new Doctrine_Connection_Exception($e->getMessage());
            return FALSE;
        } catch (Exception $e) {
            Kohana::log('error', 'Unhandled ' . get_class($e) . ': ' . $e->getMessage());
            self::setBaseSaveObject(NULL);
            throw new Exception($e->getMessage());
            return FALSE;
        }
    }

    /**
     *
     * @param FreePbx_Record $baseSaveObject
     * @return boolean TRUE if successfully set, otherwise false. Should only be false if you pass an invalid object in
     */
    public static function setBaseSaveObject($baseSaveObject) {
        if ((!$baseSaveObject instanceof FreePbx_Record) and !empty($baseSaveObject)) {
            Kohana::log('alert', 'Attempt to set base object to invalid value!');
            return FALSE;
        }

        if (is_object($baseSaveObject)) {
            Kohana::log('debug', 'Setting base object to ' . get_class($baseSaveObject));
        } else {
            Kohana::log('debug', 'Done with base object');
        }

        self::$baseSaveObject = $baseSaveObject;

        return TRUE;
    }

    /**
     *
     * @return FreePbx_Record Should return an instance of FreePbx_Record or Doctrine_Record containing the root most model node
     */
    public static function getBaseTransactionObject() {
        return self::$baseSaveObject;
    }

    public static function normalizeErrors($e)
    {
        // Destroy the pointer to our base record.
        self::setBaseSaveObject(NULL);

        // Attempt to get a list of invalid records
        if (method_exists($e, 'getInvalidRecords')) {
            $records = $e->getInvalidRecords();
        } else {
            Kohana::log('error', 'Unable to collect invalid records on exception!');
            return FALSE;
        }

        // foreach invalid record normalize the errors
        foreach ($records as $record) {
            $doctrineErrors = $record->getErrorStack();

            foreach($doctrineErrors as $fieldName => $errorCodes) {
                foreach($errorCodes as $type)
                {
                    if ((get_parent_class($record) == 'FreePbx_Record') or (get_parent_class($record) == 'Doctrine_Record'))
                        $parent = strtolower(get_class($record));
                    else
                        $parent = strtolower(get_parent_class($record));

                    // set the error key of this issue
                    $errorKey = strtolower($parent) . '[' . $fieldName . ']';

                    // Normalize error type codes with Kohana's validator error codes
                    if ($type == 'notblank') {
                        $type = 'required';
                    }

                    // setup to find the mapping of type to message in a record
                    $model = strtolower(get_class($record));
                    $modelErrors = get_class_vars($model);
                    $modelErrors = !empty($modelErrors['errors']) ? $modelErrors['errors'] : array();

                    // if this record has a parent then we are going to use both to preform the mappings
                    if ($parent != $model) {
                        
                        $parentErrors = get_class_vars($parent);
                        $parentErrors = !empty($parentErrors['errors']) ? $parentErrors['errors'] : array();
                        
                        $modelErrors = array_merge($parentErrors, $modelErrors);
                    }

                    // See if the model has an explicit error message for this validation failure on this input
                    if (!empty($modelErrors[$fieldName][$type])) {
                        FreePbx_Controller::$validation->add_error($errorKey, $modelErrors[$fieldName][$type]);
                        continue;
                    }
                    
                    // see if there is default error for this input
                    Kohana::log('alert', 'Using default error message for ' . $fieldName . ' after failing ' . $type);
                    if (!empty($modelErrors[$fieldName]['default'])) {
                        FreePbx_Controller::$validation->add_error($errorKey, $modelErrors[$fieldName]['default']);
                        continue;
                    }

                    // See if there is a default error message for this type of validation failure
                    if (!empty(self::$defaultErrors[$type])) {
                        FreePbx_Controller::$validation->add_error($errorKey, self::$defaultErrors[$type]);
                        continue;
                    }
                    
                    // Now we are just getting desprate, see if there is just a default message at all!!
                    if (!empty(self::$defaultErrors['default'])) {
                        FreePbx_Controller::$validation->add_error($errorKey, self::$defaultErrors['default']);
                        continue;
                    }
                    
                    // Give up, we gave it a good run....
                    FreePbx_Controller::$validation->add_error($errorKey, $type);
                }
            }
        }
        throw new FreePbx_Validation_Exception('Doctrine validation failed.', 2); //re-throw
    }

    public function checkValidation($deep = true, $hooks = true)
    {
        $invalidRecords = array();
        
        if ( ! $this->_table->getAttribute(Doctrine::ATTR_VALIDATE)) {
            return true;
        }

        if ($this->_state == self::STATE_LOCKED || $this->_state == self::STATE_TLOCKED) {
            return true;
        }

        if ($hooks) {
            $this->invokeSaveHooks('pre', 'save');
            $this->invokeSaveHooks('pre', $this->exists() ? 'update' : 'insert');
        }

        // Clear the stack from any previous errors.
        $this->getErrorStack()->clear();

        // Run validation process
        $event = new Doctrine_Event($this, Doctrine_Event::RECORD_VALIDATE);
        $this->preValidate($event);
        $this->getTable()->getRecordListener()->preValidate($event);

        if ( ! $event->skipOperation) {

            $validator = new Doctrine_Validator();
            $validator->validateRecord($this);
            $this->validate();
            if ($this->_state == self::STATE_TDIRTY || $this->_state == self::STATE_TCLEAN) {
                $this->validateOnInsert();
            } else {
                $this->validateOnUpdate();
            }
        }

        $this->getTable()->getRecordListener()->postValidate($event);
        $this->postValidate($event);

        $valid = $this->getErrorStack()->count() == 0 ? true : false;

        if (!$valid) {
            $invalidRecords[] = $this;
        }

        if ($deep) {
            $stateBeforeLock = $this->_state;
            $this->_state = $this->exists() ? self::STATE_LOCKED : self::STATE_TLOCKED;

            foreach ($this->_references as $reference) {
                if ($reference instanceof Doctrine_Record) {
                    if (!method_exists($reference, 'checkValidation')) continue;
                    $valid = $reference->checkValidation($deep);
                    if (is_array($valid) && !empty($valid)) {
                        $invalidRecords = array_merge($valid, $invalidRecords);
                    }
                } else if ($reference instanceof Doctrine_Collection) {
                    foreach ($reference as $record) {
                        if (!method_exists($record, 'checkValidation')) continue;
                        $valid = $record->checkValidation($deep);
                        if (is_array($valid) && !empty($valid)) {
                            $invalidRecords = array_merge($valid, $invalidRecords);
                        }
                    }
                }
            }
            $this->_state = $stateBeforeLock;
        }

        return $invalidRecords;
    }
}
