<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_FeatureCode_Driver extends FreeSwitch_Base_Driver {
  public static function set($obj) {
  }

  public static function delete($obj) {
  }

  public static function network($obj) {
    self::createExtensionSection($obj, 'network');
  }

  public static function conditioning() {
    self::createExtensionSection($obj, 'conditioning');
  }

  public static function preroute() {
    self::createExtensionSection($obj, 'preroute');
  }

  public static function postroute() {
    self::createExtensionSection($obj, 'postroute');
  }

  public static function preanswer() {
    self::createExtensionSection($obj, 'preanswer');
  }

  public static function postanswer() {
    self::createExtensionSection($obj, 'postanswer');
  }

  public static function main() {
    self::createExtensionSection($obj, 'main');
  }

  public static function prenumber() {
    self::createExtensionSection($obj, 'prenumber');
  }

  public static function postnumber() {
    self::createExtensionSection($obj, 'postnumber');
  }

  public static function catchall() {
    self::createExtensionSection($obj, 'catchall');
  }

  public static function postexecute() {
    self::createExtensionSection($obj, 'postexecute');
  }

  protected static function createExtensionSection($obj, $section) {
    kohana::log('debug', 'createExtensionSection ' . $section . ': FC_id(' . $obj->feature_code_id . ')');

    if ( empty($obj->registry[$section]) ) {
      kohana::log('debug', 'createExtensionSection: No section found. ');
      return;
    }

    $xml = FreeSWITCH::createExtension('featurecode_' . $obj->feature_code_id);
    $xml->replaceWithXml($obj->registry[$section]);
  }
}
