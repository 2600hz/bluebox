<?php

class FeatureCode_Plugin extends Bluebox_Plugin {
  public function selector() {
    $subview = new View('featurecode/selector');
    $subview->section = 'featurecode';

    // Add our view to the main application
    $featureCodes = Doctrine_Query::create()
      ->select('feature_code_id, name')
      ->from('FeatureCode')
      ->orderBy('name')
      ->execute(array(), Doctrine::HYDRATE_ARRAY);

    if (empty($featureCodes)) {
      return FALSE;
    }

    $subview->featureCodes = array();
    foreach ($featureCodes as $featureCode) {
      $subview->featureCodes[$featureCode['feature_code_id']] = $featureCode['name'];
    }

    $this->views[] = $subview;

  }
}