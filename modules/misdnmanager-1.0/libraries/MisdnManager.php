<?php
/**
 * MisdnManager.php
 *
 * @author reto.haile <reto.haile@selmoni.ch>
 * @license CPAL-1.0
 * @package Bluebox
 * @subpackage MisdnManager
 *
 */
class MisdnManager
{
    public static function addModel($card_vendor_id, $pci_subsys_id, $model, $type, $portcount)
    {
        $cardModel = new MisdnCardModel();
        $cardModel->card_vendor_id = $card_vendor_id;
        $cardModel->pci_subsys_id = $pci_subsys_id;
        $cardModel->model = $model;
        $cardModel->type = $type;
        $cardModel->portcount = $portcount;

        if($cardModel->save())
        {
            return $cardModel->card_model_id;
        } else
        {
            return false;
        }
    }


    public static function addSetting($cardID, $option, $value)
    {
        $cardSetting = new MisdnCardSetting();
        $cardSetting->card_id = $cardID;
        $cardSetting->option = $option;
        $cardSetting->value = $value;

        if($cardSetting->save())
        {
            return $cardSetting->card_setting_id;
        }
        else
        {
            return false;
        }
    }


    public static function addVendor($vendor)
    {
        $cardVendor = new MisdnCardVendor();
        $cardVendor->vendor = $vendor;

        if($cardVendor->save())
        {
            return $cardVendor->card_vendor_id;
        }
        else
        {
            return false;
        }
    }


    public static function addMisdnSetting($node, Array $settings)
    {
        $setting = new MisdnSetting();
        $setting->node = $node;
        $setting->settings = $settings;
        $setting->save();
    }

    public static function addMisdnSettings()
    {
        $setting = new MisdnSetting();
        $setting->id = 0;
        $setting->save();
    }


    /**
     *
     * @return array  A list of vendors
     */
    public static function getVendors()
    {
        $q = Doctrine_Query::create()
        ->select('cv.card_vendor_id, cv.vendor')
        ->from('MisdnCardVendor cv')
        ->orderBy('cv.vendor');

        $results = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
         
        $vendors = array(0 => 'Select');
         
        foreach ($results as $result) {
            $vendors[$result['card_vendor_id']] = $result['vendor'];
        }
         
        return $vendors;
    }


    public static function getVendorByModel($modelId)
    {
        $results = Doctrine_Query::create()
        ->select('cm.card_model_id, cv.card_vendor_id, cv.vendor')
        ->from('MisdnCardModel cm, cm.MisdnCardVendor cv')
        ->where('card_model_id = ?', $modelId)
        ->execute(array(), Doctrine::HYDRATE_ARRAY);
    }


    public static function getModel($pciSubId)
    {
        $model = Doctrine::getTable('MisdnCardModel')->findOneByPci_subsys_id($pciSubId);
        if ($model)
        {
            return $model;
        }

        return false;
    }

    /**
     *
     * @param int     Vendor ID
     * @return array  A list of models supported by vendor
     */
    public static function getModels($card_vendor_id = NULL, $pciSubId = NULL)
    {
        $q = Doctrine_Query::create()
        ->select('cm.card_model_id, cm.pci_subsys_id, cm.model, cv.vendor')
        ->from('MisdnCardModel cm, cm.MisdnCardVendor cv')
        ->orderBy('cm.model');

        if (!is_null($card_vendor_id))
        {
            $q->where('cm.card_vendor_id = ?', $card_vendor_id);
        }

        if (!is_null($pciSubId))
        {
            $pciSubId = preg_replace('/[^0-9a-fA-F\:]*/', '', strtolower($pciSubId));
            if (strlen($pciSubId) == 9)
            {
                $q->where('pci_subsys_id = ?', $pciSubId);
            }
        }

        $results = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

        if($results == NULL)
        {
            return false;
        }

        $models = array(0 => 'Select');

        foreach ($results as $result) {
            $models[$result['card_model_id']] = $result['model'];
        }

        return $models;
    }


    public static function getMisdnSettings($node = NULL)
    {
        $q = Doctrine_Query::create()
        ->select('*')
        ->from('MisdnSetting ds');

        return $q->execute(array(), Doctrine::HYDRATE_ARRAY);

        if (!is_null($node))
        {
            $q->where('ds.node = ?', $node);
        }

        $results = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

        $settings = array();

        foreach ($results as $result)
        {
            $settings[$result['node']] = $result['settings'];
        }

        return $settings;
    }


    public static function deleteDriver($driver)
    {

    }


    public static function saveConfig($configID = 0)
    {
        $misdnSettings = Doctrine::getTable('MisdnSetting')->findOneById($configID);

        $cards = Doctrine::getTable('MisdnCard')->findAll();

        $XML = new DOMDocument('1.0');
        $XML->formatOutput = true;

        $root = $XML->appendChild($XML->createElement('mISDNconf'));

        $hfcmulti = $XML->createElement('module', 'hfcmulti');
        $hfcmulti->setAttribute('poll', $misdnSettings->hfcmulti_poll);
        $hfcmulti->setAttribute('debug', $misdnSettings->hfcmulti_debug);
        $hfcmulti->setAttribute('pcm', $misdnSettings->hfcmulti_pcm);
        $root->appendChild($hfcmulti);

        $misdndsp = $XML->createElement('module', 'mISDN_dsp');
        $misdndsp->setAttribute('poll', $misdnSettings->dsp_poll);
        $misdndsp->setAttribute('debug', $misdnSettings->dsp_debug);
        $misdndsp->setAttribute('dtmfthreshold', $misdnSettings->dsp_dtmfthreshold);
        $root->appendChild($misdndsp);

        $devnode = $XML->createElement('devnode', 'mISDN');
        $devnode->setAttribute('user', $misdnSettings->devnode_user);
        $devnode->setAttribute('group', $misdnSettings->devnode_group);
        $devnode->setAttribute('mode', $misdnSettings->devnode_mode);
        $root->appendChild($devnode);

        foreach ($cards as $card)
        {
            $settings = array();
            if (count($card->Settings) > 0)
            {
                foreach ($card->Settings as $setting)
                {
                    $settings[$setting->option] = $setting->value;
                }
            }

            $cardElem = $XML->createElement('card');
            $cardElem->setAttribute('type', $card->MisdnCardModel->type);
            if(!empty($settings['ulaw']))
            {
                //                echo "DEBUG: set ulaw attribute<br/>\n";
                $cardElem->setAttribute('ulaw', $settings['ulaw']);
            }
            if(!empty($settings['dtmf']))
            {
                //                echo "DEBUG: set dtmf attribute<br/>\n";
                $cardElem->setAttribute('dtmf', $settings['dtmf']);
            }
            if(!empty($settings['pcm_slave']))
            {
                //                echo "DEBUG: set pcm_slave attribute<br/>\n";
                $cardElem->setAttribute('pcm_slave', $settings['pcm_slave']);
            }
            if(!empty($settings['ignore_pcm_frameclock']))
            {
                //                echo "DEBUG: set ignore_pcm_frameclock attribute<br/>\n";
                $cardElem->setAttribute('ignore_pcm_frameclock', $settings['ignore_pcm_frameclock']);
            }
            if(!empty($settings['rxclock']))
            {
                //                echo "DEBUG: set rxclock attribute<br/>\n";
                $cardElem->setAttribute('rxclock', $settings['rxclock']);
            }
            if(!empty($settings['crystalclock']))
            {
                //                echo "DEBUG: set crystalclock attribute<br/>\n";
                $cardElem->setAttribute('crystalclock', $settings['crystalclock']);
            }
            if(!empty($settings['watchdog']))
            {
                //                echo "DEBUG: set watchdog attribute<br/>\n";
                $cardElem->setAttribute('watchdog', $settings['watchdog']);
            }
            for ($i = 1; $i <= $card->MisdnCardModel->portcount; $i++)
            {
                foreach($card->Ports as $port)
                {
                    $portSettings = array();
                    foreach($port->Settings as $setting)
                    {
                        $portSettings[$setting->option] = $setting->value;
                    }
                    $xmlPort = $XML->createElement('port', $port->number);
                    $xmlPort->setAttribute('mode', $settings['mode']);
                    if(strlen($settings['link']) > 0)
                    {
                        $xmlPort->setAttribute('link', $settings['link']);
                    }
                    if ($settings['masterclock_port'] == $port->number)
                    {
                        $xmlPort->setAttribute('master-clock', 'yes');
                    }
                    $cardElem->appendChild($xmlPort);
                }
            }

            $root->appendChild($cardElem);
        }

        $res = @fopen($misdnSettings['misdn_conf_file'], 'w');

        if($res)
        {
            fclose($res);

            if($XML->save($misdnSettings['misdn_conf_file']))
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }
}