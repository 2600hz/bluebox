<?php
defined('SYSPATH') or die('No direct access allowed.');
/**
 * trunk.php - Asterisk Trunk configuration driver
 *
 * Allows for configuration of inbound and outbound trunks
 *
 * @author K Anderson
 * @license MPL
 * @package FreePBX3
 * @subpackage SimpleRoute
 */
class Asterisk_SimpleRouteContext_Driver extends Asterisk_Base_Driver
{
    public static function set($obj)
    {
        $doc = Telephony::getDriver()->doc;
        
        // emergency
        $patterns = simplerouter::getOutboundPattern('emergency', 'asterisk');
        foreach ($patterns as $pattern => $exten) {

            $doc->deleteDialplanExtension($obj->context_id, $pattern);

            if (!$obj->SimpleRoute->emergency) continue;

            $outbound = $obj->SimpleRoute->emergency_prepend;
            $outbound .= $exten;

            $doc->add('NoOp', 1, array('replace' => TRUE));

            $doc->add('Set(CALLERID(name)=' . $obj->SimpleRoute->caller_id_name .')');
            $doc->add('Set(CALLERID(number)=' . $obj->SimpleRoute->caller_id_number .')');

            $doc->add('GotoIf($["${outbound_caller_id_number}" = ""]?DIAL)');

            $doc->add('Set(CALLERID(name)=${outbound_caller_id_name})');
            $doc->add('Set(CALLERID(number)=${outbound_caller_id_number})');

            $doc->add('Dial(SIP/' .$outbound .'@trunk_' . $obj->SimpleRoute->trunk_id . ')', 'DIAL');

            $doc->add('Return');
        }

        // international
        $patterns = simplerouter::getOutboundPattern('international', 'asterisk');
        kohana::log('debug', 'Adding international route with rule ' . $pattern);
        foreach ($patterns as $pattern => $exten) {
            $doc->deleteDialplanExtension($obj->context_id, $pattern);

            if (!$obj->SimpleRoute->international) continue;

            $outbound = $obj->SimpleRoute->international_prepend;
            $outbound .= $exten;

            $doc->add('NoOp', 1, array('replace' => TRUE));

            $doc->add('Set(CALLERID(name)=' . $obj->SimpleRoute->caller_id_name .')');
            $doc->add('Set(CALLERID(number)=' . $obj->SimpleRoute->caller_id_number .')');

            $doc->add('GotoIf($["${outbound_caller_id_number}" = ""]?DIAL)');

            $doc->add('Set(CALLERID(name)=${outbound_caller_id_name})');
            $doc->add('Set(CALLERID(number)=${outbound_caller_id_number})');

            $doc->add('Dial(SIP/' .$outbound .'@trunk_' . $obj->SimpleRoute->trunk_id . ')', 'DIAL');

            $doc->add('Return');
        }

        //domestic dialing plans
        $patterns = simplerouter::getOutboundPattern('local', 'asterisk');
        foreach ($patterns as $pattern => $exten) {

            $doc->deleteDialplanExtension($obj->context_id, $pattern);

            if (!$obj->SimpleRoute->local) continue;

            $outbound = $obj->SimpleRoute->local_prepend;
            $outbound .= $exten;

            $doc->add('NoOp', 1, array('replace' => TRUE));

            $doc->add('Set(CALLERID(name)=' . $obj->SimpleRoute->caller_id_name .')');
            $doc->add('Set(CALLERID(number)=' . $obj->SimpleRoute->caller_id_number .')');

            $doc->add('GotoIf($["${outbound_caller_id_number}" = ""]?DIAL)');

            $doc->add('Set(CALLERID(name)=${outbound_caller_id_name})');
            $doc->add('Set(CALLERID(number)=${outbound_caller_id_number})');

            $doc->add('Dial(SIP/' .$outbound .'@trunk_' . $obj->SimpleRoute->trunk_id . ')', 'DIAL');

            $doc->add('Return');
        }

        if ($patterns = simplerouter::getOutboundPattern('short', 'asterisk')) {
            foreach ($patterns as $pattern => $exten) {

                $doc->deleteDialplanExtension($obj->context_id, $pattern);

                if (!$obj->SimpleRoute->local) continue;

                $outbound = $obj->SimpleRoute->local_prepend;
                $outbound .= $obj->SimpleRoute->area_code;
                $outbound .= $exten;

                $doc->add('NoOp', 1, array('replace' => TRUE));

                $doc->add('Set(CALLERID(name)=' . $obj->SimpleRoute->caller_id_name .')');
                $doc->add('Set(CALLERID(number)=' . $obj->SimpleRoute->caller_id_number .')');

                $doc->add('GotoIf($["${outbound_caller_id_number}" = ""]?DIAL)');

                $doc->add('Set(CALLERID(name)=${outbound_caller_id_name})');
                $doc->add('Set(CALLERID(number)=${outbound_caller_id_number})');

                $doc->add('Dial(SIP/' .$outbound .'@trunk_' . $obj->SimpleRoute->trunk_id . ')', 'DIAL');

                $doc->add('Return');
            }
        }
    }
    
    public static function delete($obj)
    {
        $doc = Telephony::getDriver()->doc;
        
        $base = FreePbx_Record::getBaseTransactionObject();

        if (empty($base->trunk_id)) {
                return FALSE;
        }

        $patterns = simplerouter::getOutboundPattern('emergency', 'asterisk');
        foreach ($patterns as $pattern => $exten) {
            $doc->deleteDialplanExtension($obj->context_id, $pattern);
        }

        $patterns = simplerouter::getOutboundPattern('international', 'asterisk');
        foreach ($patterns as $pattern => $exten) {
            $doc->deleteDialplanExtension($obj->context_id, $pattern);
        }

        $patterns = simplerouter::getOutboundPattern('local', 'asterisk');
        foreach ($patterns as $pattern => $exten) {
            $doc->deleteDialplanExtension($obj->context_id, $pattern);
        }

        $patterns = simplerouter::getOutboundPattern('short', 'asterisk');
        foreach ($patterns as $pattern => $exten) {
            $doc->deleteDialplanExtension($obj->context_id, $pattern);
        }
    }
}
