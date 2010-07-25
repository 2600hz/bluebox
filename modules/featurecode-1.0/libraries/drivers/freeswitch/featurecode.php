<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_FeatureCode_Driver extends FreeSwitch_Base_Driver {
  public static function set($obj) {
  }

  public static function delete($obj) {
  }

  public static function dialplan($obj) {
    foreach ( self::$dialplanSections as $section ) {
      self::createExtensionSection($obj, $section);
    }
  }

  protected static function createExtensionSection($obj, $section) {
    kohana::log('debug', 'createExtensionSection ' . $section . ': FC_id(' . $obj->feature_code_id . ')');

    if ( empty($obj->registry[$section]) ) {
      kohana::log('debug', 'createExtensionSection: No section found. ');
      return;
    }

    $xml = FreeSWITCH::createExtension('featurecode_' . $obj->feature_code_id, $section);
    $xml->replaceWithXml($obj->registry[$section]);
  }
}
