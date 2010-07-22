<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Support
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Support_Controller extends Bluebox_Controller
{
    public function request_help()
    {
        if ($this->submitted(NULL, array('submitString' => 'send')))
        {
            message::set('Request was submitted, you will be contacted shortly.', 'success');
            
            $this->returnQtipAjaxForm(NULL, TRUE);
        }
    }
}
