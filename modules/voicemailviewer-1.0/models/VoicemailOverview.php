<?php
class VoicemailOverview extends Bluebox_Record {
  public function setTableDefinition() {
    $this->setTableName('voicemail_overview');
    $this->hasColumn('username', 'string', 255, array('primary' => true));
    $this->hasColumn('domain', 'string', 255, array('primary' => true));
    $this->hasColumn('new', 'integer');
    $this->hasColumn('saved', 'integer');
    $this->hasColumn('new_urgent', 'integer');
    $this->hasColumn('saved_urgent', 'integer');
  }

  public function setUp() {
  }
}
