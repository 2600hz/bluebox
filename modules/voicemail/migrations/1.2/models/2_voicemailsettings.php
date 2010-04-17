<?php defined('SYSPATH') or die('No direct access allowed.');

class Voicemail extends Doctrine_Migration_Base
{
    public function up() {
        $this->addColumn('voicemail', 'new_email_address', 'string', '255', array ());
    }

    public function down() {
        $this->removeColumn('voicemail', 'new_email_address');
    }
}