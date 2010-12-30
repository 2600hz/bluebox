<?php defined('SYSPATH') or die('No direct access allowed.');

class Regenerate_Plugin extends Bluebox_Plugin
{
    public function add_to_account()
    {
        $this->grid->addAction('regenerate/account/', 'Rebuild', array(
                'arguments' => 'account_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
    }

    public function add_to_number()
    {
        $this->grid->addAction('regenerate/number/', 'Rebuild', array(
                'arguments' => 'number_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
    }

    public function add_to_context()
    {
        $this->grid->addAction('regenerate/context/', 'Rebuild', array(
                'arguments' => 'context_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
    }
}