<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * EndpointManager.php - This class provides support for the provisioner
 *
 * @author Michael Phillips
 * @author Karl Anderson
 * @license MPL
 * @package Bluebox
 */
class EndpointManager
{
    /**
     * This function will return a list of drivers that service the $mac
     * passed.  If no $mac exists then all endpoint vendors are listed.
     *
     * @param string[optional] The mac address to filter the vendor list by
     * @param array[optional] The default value key pairs to contain in the return
     * @param bool[optional] If true and no match is found for the OUI given, return all vendors
     * @return array A list of vendors starting with key 0 value 'Select'
     */
    public static function listVendors($mac = NULL, array $vendors = array(
        0 => 'Select'
    ) , $returnAllOnFail = true)
    {
        // Build a query to find all the vendors and desctriptions that support this mac
        $q = Doctrine_Query::create()->select('ev.endpoint_vendor_id, ev.vendor, ev.description')->from('EndpointVendor ev')->orderBy('ev.vendor');
        // If a $mac was passed as a parameter add the where clause for this OUI
        if (!is_null($mac)) {
            $mac = preg_replace('/[^0-9a-fA-F]*/', '', strtoupper($mac));
            $oui = substr($mac, 0, 6);
            $q->where('oui = ?', $oui);
        }
        // Execute the query
        $results = $q->execute(array() , Doctrine::HYDRATE_ARRAY);
        // If the user wants all vendors when there is no OUI match do that now
        if (empty($results) && !empty($returnAllOnFail)) {
            $listAllVendors = self::listVendors(NULL, $vendors, false);
            return $listAllVendors;
        }
        // Append the query into an array
        foreach($results as $result) {
            $vendors[$result['endpoint_vendor_id']] = $result['vendor'] . " (" . $result['description'] . ")";
        }
        return $vendors;
    }
    /**
     * This function will return the vendor object for the provided
     * vendor id
     *
     * @param int a vendor id to seek
     * @return mixed a vendor object or false if none is found
     */
    public static function getVendor($endpoint_vendor_id = NULL)
    {
        if (empty($endpoint_vendor_id)) return FALSE;
        $vendor = Doctrine::getTable('EndpointVendor')->findOneByEndpointVendorId($endpoint_vendor_id);
        if (!empty($vendor)) {
            return $vendor;
        } else {
            return FALSE;
        }
    }
    /**
     * This function will return just the vendor name (as it appears in the
     * dropdown) for a given vendor id
     *
     * @param int a vendor id to seek
     * @param string the default return if the vendor can not be found
     * @return string either the vendor name from the db or UNKNOWN if none is found
     */
    public static function getVendorName($endpoint_vendor_id = NULL, $failReturn = 'UNKNOWN')
    {
        if ($vendor = self::getVendor($endpoint_vendor_id)) {
            return $vendor['vendor'] . " (" . $vendor['description'] . ")";
        }
        return $failReturn;
    }
    /**
     * This function will return an array of models support by the vendor
     *
     * @param int[optional] the endpoint vendor id to get the list of models for
     * @param array[optional] The default value key pairs to contain in the return
     * @param bool[optional] If true and no match is found for the vendor id, return all models
     * @return array a list of models supported by the vendor
     */
    public static function listModels($endpoint_vendor_id = NULL, array $models = array(
        0 => 'Select'
    ) , $returnAllOnFail = true)
    {
        // Build a query to find the all the model ids and descriptions for this vendor
        $q = Doctrine_Query::create()->select('em.endpoint_model_id, em.description')->from('EndpointModel em')->orderBy('em.description');
        if (!is_null($endpoint_vendor_id)) {
            $q->where('em.endpoint_vendor_id = ?', array(
                $endpoint_vendor_id
            ));
        }
        // Execute the query
        $results = $q->execute(array() , Doctrine::HYDRATE_ARRAY);
        // If the user wants all models when there is no vendor id match do that now
        if (empty($results) && !empty($returnAllOnFail)) {
            $listAllModels = self::listModels(NULL, $models, false);
            return $listAllModels;
        }
        // Build the results into an array
        foreach($results as $result) {
            $models[$result['endpoint_model_id']] = $result['description'];
        }
        return $models;
    }
    /**
     * This function will return the model object for the provided
     * model id
     *
     * @param int a model id to seek
     * @return mixed a model object or false if none is found
     */
    public static function getModel($endpoint_model_id = NULL)
    {
        if (empty($endpoint_model_id)) return FALSE;
        $model = Doctrine::getTable('EndpointModel')->findOneByEndpointModelId($endpoint_model_id);
        if (!empty($model)) {
            return $model;
        } else {
            return FALSE;
        }
    }
    /**
     * This function will return just the model name (as it appears in the
     * dropdown) for a given model id
     *
     * @param int a model id to seek
     * @param string the default return if the vendor can not be found
     * @return string either the model name from the db or UNKNOWN if none is found
     */
    public static function getModelName($endpoint_model_id = NULL, $failReturn = 'UNKNOWN')
    {
        if ($model = self::getModel($endpoint_model_id)) {
            return $model['description'];
        }
        return $failReturn;
    }
    /**
     * To get a driver we need to know for which model id since there could
     * be several matches against the OUI
     *
     * @param int the model id for which a driver object should be created
     * @param string the mac address of the new endpoint
     * @param array key value pairs to pass to the driver constructor
     * @return object this function will return an object or die tring
     */
    public static function createEndpoint($mac, $endpoint_model_id, $options = array())
    {
        // Create a query to get the driver and oui based on the model_id
        $q = Doctrine_Query::create()->select('em.endpoint_model_id, ev.driver, ev.oui')->from('EndpointModel em, em.EndpointVendor ev')->where('em.endpoint_model_id = ?', array(
            $endpoint_model_id
        ));
        // Execute the query
        $result = $q->execute(array() , Doctrine::HYDRATE_ARRAY);
        // If we somehow got more than one driver back
        if (count($result) > 1) {
            throw new Exception("Driver selection is ambiguous");
        }
        if (empty($endpoint_model_id)){
            throw new Exception("Please provide a valid model");
        }
        // Check if we retrieved a driver name
        if (!empty($result[0]['EndpointVendor']['driver'])) {
            $driverName = $result[0]['EndpointVendor']['driver'];
            $supportedOUI = strtoupper($result[0]['EndpointVendor']['oui']);
            // Clean up the mac address
            $mac = preg_replace('/[^0-9a-fA-F]*/', '', strtoupper($mac));
            // If we dont have valid mac throw an error
            if (strlen($mac) != 12) {
                throw new Exception("Please provide a valid MAC address");
            }
            // If the driver we found does not support this oui throw and error
            $oui = substr($mac, 0, 6);
            if ($oui != $supportedOUI) {
                throw new Exception("The selected driver does not support the provided MAC");
            }
            // Make sure the class we are about to init exists
            if (!class_exists($driverName)) {
                throw new Exception("The selected driver is not enabled");
            }
            // init the new driver
            return new $driverName($mac, $options);
        } else {
            // Oops our query did not return a driver class name
            throw new Exception("Failed to find driver");
        }
    }
    /**
     * To get a driver we need to know for which model id since there could
     * be several matches against the OUI
     *mphill: question, do you know how to force doctrine to follow all relationships? It is doing this lazy loading and the data is not in the array until it is requested.....
     * @param int the model id for which a driver object should be created
     * @return object this function will return an object or die tring
     */
    public static function getDriver($mac = NULL)
    {
        $endpoint = Doctrine::getTable('Endpoint')->findOneByMac($mac);
        if (!empty($endpoint)) {
            $driver = self::createEndpoint($endpoint->mac, $endpoint->endpoint_model_id, $endpoint->options);
            $driver->import($endpoint);
            return $driver;
        } else {
            throw new Exception("Failed to find Endpoint with MAC " . $mac);
        }
    }
    /**
     * To get a driver we need to know for which model id since there could
     * be several matches against the OUI
     *
     * @param int the model id for which a driver object should be created
     * @return object this function will return an object or die tring
     */
    public static function getDriverById($endpoint_id = NULL)
    {
        $endpoint = Doctrine::getTable('Endpoint')->findOneByEndpointId($endpoint_id);
        if (!empty($endpoint)) {
            $driver = self::createEndpoint($endpoint->mac, $endpoint->endpoint_model_id, $endpoint->options);
            $driver->import($endpoint);
            return $driver;
        } else {
            throw new Exception("Failed to find Endpoint with ID " . $endpoint_id);
        }
    }
    /**
     * To get a driver we need to know for which model id since there could
     * be several matches against the OUI
     *
     * @param int the model id for which a driver object should be created
     * @return object this function will return an object or die tring
     */
    public static function getDriverByDeviceId($device_id = NULL)
    {
        $endpointLine = Doctrine::getTable('EndpointLine')->findOneByDeviceId($device_id);
        if (!empty($endpointLine)) {
            $endpoint = $endpointLine->Endpoint;
            $driver = self::createEndpoint($endpoint->mac, $endpoint->endpoint_model_id, $endpoint->options);
            $driver->import($endpoint);
            return $driver;            
        } else {
            throw new Exception("Failed to find Endpoint with ID " . $endpoint_id);
        }
    }
    /**
     * This function adds a vendor to the EnpointVendor table, and is used to
     * install a endpoint driver
     *
     * @param string the name of the vendor
     * @param string the oui this driver supports
     * @param string the class name of the driver
     * @param string a breif description of what this driver supports
     * @return mixed this will return a id if it saves properly otherwise false
     */
    public static function addVendor($vendor, $oui, $driver, $description = '')
    {
        $ev = Doctrine::getTable('EndpointVendor')->findOneByVendor($vendor);
        if (!empty($ev->endpoint_vendor_id)) {
            return $ev->endpoint_vendor_id;
        } else {
            $ev = new EndpointVendor();
            $ev->vendor = $vendor;
            $ev->oui = strtolower($oui);
            $ev->driver = $driver;
            $ev->description = $description;
            if ($ev->save()) {
                return $ev->endpoint_vendor_id;
            } else {
                return false;
            }            
        }
    }
    /**
     * This function adds a model to the EnpointModel table, and is used to
     * install a endpoint driver
     *
     * @param int the id of the vendor that this model belongs tor
     * @param string a breif description of what this driver supports
     * @return mixed this will return a id if it saves properly otherwise false
     */
    public static function addModel($endpoint_vendor_id, $line_count, $description)
    {
        $em = Doctrine::getTable('EndpointModel')->findOneByDescription($description);
        if (!empty($em->endpoint_model_id)) {
            return $em->endpoint_model_id;
        } else {
            $em = new EndpointModel();
            $em->endpoint_vendor_id = $endpoint_vendor_id;
            $em->line_count = $line_count;
            $em->description = $description;
            if ($em->save()) {
                return $em->endpoint_model_id;
            } else {
                return false;
            }
        }


    }
    /**
     * This function removes a driver
     *
     * @param string the class name of the driver to remove
     * @return mixed void
     */
    public static function deleteDriver($driver)
    {
        $vendor = Doctrine::getTable('EndpointVendor')->findOneByDriver($driver);
        if ($vendor) {
            $vendor->delete();
        }
    }
}
