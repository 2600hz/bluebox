<?php
class JC2 extends Doctrine_Record {
    public function setTableDefinition() {
        $this->hasColumn('c1_id', 'integer');
        $this->hasColumn('c2_id', 'integer');
    }
}

