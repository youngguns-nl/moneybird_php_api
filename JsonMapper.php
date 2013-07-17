<?php

/*
 * JSON Mapper class
 */
namespace Moneybird;

/**
 * Translates JSON into objects and objects into JSON
 */
class JsonMapper implements Mapper
{

    /**
     * Mapper for json element names to class names
     * @var Array
     */
    protected $objectMapper = array();

    /**
     * Create JSON mapper
     */
    public function __construct()
    {
        $this->objectMapper = array(
            'contacts' => __NAMESPACE__ . '\\Contact_Array',
            'contact' => __NAMESPACE__ . '\\Contact',
            'user' => __NAMESPACE__ . '\\CurrentSession',
            'estimates' => __NAMESPACE__ . '\\Estimate_Array',
            'estimate' => __NAMESPACE__ . '\\Estimate',
            'estimate/details' => __NAMESPACE__ . '\\Estimate_Detail_Array',
            'estimate/details/detail' => __NAMESPACE__ . '\\Estimate_Detail',
            'estimate/history' => __NAMESPACE__ . '\\Estimate_History_Array',
            'estimate/history/history' => __NAMESPACE__ . '\\Estimate_History',
            'incoming-invoices' => __NAMESPACE__ . '\\IncomingInvoice_Array',
            'incoming-invoice' => __NAMESPACE__ . '\\IncomingInvoice',
            'incoming-invoice/details' => __NAMESPACE__ . '\\IncomingInvoice_Detail_Array',
            'incoming-invoice/details/detail' => __NAMESPACE__ . '\\IncomingInvoice_Detail',
            'incoming-invoice/payments' => __NAMESPACE__ . '\\IncomingInvoice_Payment_Array',
            'incoming-invoice/payments/payment' => __NAMESPACE__ . '\\IncomingInvoice_Payment',
            'incoming-invoice/history' => __NAMESPACE__ . '\\IncomingInvoice_History_Array',
            'incoming-invoice/history/history' => __NAMESPACE__ . '\\IncomingInvoice_History',
            'invoices' => __NAMESPACE__ . '\\Invoice_Array',
            'invoice' => __NAMESPACE__ . '\\Invoice',
            'invoice/details' => __NAMESPACE__ . '\\Invoice_Detail_Array',
            'invoice/details/detail' => __NAMESPACE__ . '\\Invoice_Detail',
            'invoice/payments' => __NAMESPACE__ . '\\Invoice_Payment_Array',
            'invoice/payments/payment' => __NAMESPACE__ . '\\Invoice_Payment',
            'invoice/history' => __NAMESPACE__ . '\\Invoice_History_Array',
            'invoice/history/history' => __NAMESPACE__ . '\\Invoice_History',
            'invoice-profiles' => __NAMESPACE__ . '\\InvoiceProfile_Array',
            'invoice-profile' => __NAMESPACE__ . '\\InvoiceProfile',
            'products' => __NAMESPACE__ . '\\Product_Array',
            'product' => __NAMESPACE__ . '\\Product',
            'recurring-templates' => __NAMESPACE__ . '\\RecurringTemplate_Array',
            'recurring-template' => __NAMESPACE__ . '\\RecurringTemplate',
            'recurring-template/details' => __NAMESPACE__ . '\\RecurringTemplate_Detail_Array',
            'recurring-template/details/detail' => __NAMESPACE__ . '\\RecurringTemplate_Detail',
            'tax-rates' => __NAMESPACE__ . '\\TaxRate_Array',
            'tax-rate' => __NAMESPACE__ . '\\TaxRate',
            'errors' => __NAMESPACE__ . '\\Error_Array',
            'error' => __NAMESPACE__ . '\\Error',
        );
        uksort($this->objectMapper, array($this, 'sortKeyLength'));
    }

    /**
     * Compare length of $a with length of $b
     * @param string $a
     * @param string $b
     * @return int 
     */
    protected function sortKeyLength($a, $b)
    {
        if (strlen($a) == strlen($b))
            return 0;
        if (strlen($a) > strlen($b))
            return -1;
        return 1;
    }

    /**
     * Returns the content type of mapped objects
     * 
     * @return string
     */
    public function getContentType()
    {
        return 'application/json';
    }

    /**
     * Create object from string
     * @param string $string 
     * @return Mapper_Mapable
     * @access public
     */
    public function mapFromStorage($string)
    {
        return $this->fromJsonString($string);
    }

    /**
     * Map object
     * @access public
     * @param Mapper_Mapable $subject Object to map
     * @return string
     */
    public function mapToStorage(Mapper_Mapable $subject)
    {
        return $this->toJsonString($subject);
    }

    /**
     * Create object from json string
     * @param string $jsonstring 
     * @access public
     */
    public function fromJsonString($jsonstring)
    {
        return $this->fromJson(json_decode($jsonstring, true));
    }

    /**
     * Create object from json
     * @param 
     * @access public
     */
    public function fromJson(Array $jsonArray)
    {
        $classname = $this->jsonKeyToClassname(key($jsonArray));
        $jsonArray = current($jsonArray);
        if (!is_null($classname)) {
            $return = new $classname();
            if ($return instanceof ArrayObject) {
                foreach ($jsonArray as $child) {
                    $return->append($this->fromJson($child));
                }
            } else {
                $objectData = array();
                foreach ($jsonArray as $key => $child) {
                    $key = $this->keyToProperty($key);
                    if (isset($objectData[$key])) {
                        if (!is_array($objectData[$key])) {
                            $objectData[$key] = array($objectData[$key]);
                        }
                        $objectData[$key][] = $this->fromJson(array($key => $child));
                    } else {
                        $objectData[$key] = $this->fromJson(array($key => $child));
                    }
                }
                $return = new $classname($objectData);
            }
        } else {
            $return = $jsonArray;
        }

        return $return;
    }

    /**
     * Map key to classname
     * @param string $jsonKey
     * @return string
     * @access protected
     */
    protected function jsonKeyToClassname($jsonKey)
    {
        if (isset($this->objectMapper[$jsonKey])) {
            return $this->objectMapper[$jsonKey];
        }
        return null;
    }

    /**
     * Write key as property
     * @param string $key
     * @return string
     * @access protected
     */
    protected function keyToProperty($key)
    {
        return preg_replace_callback('/(-)(\w)/', array($this, 'keyToPropertyCallback'), $key);
    }

    /**
     * Callback to rewrite key to property
     * @param Array $match
     * @return string
     * @access protected
     */
    protected function keyToPropertyCallback($match)
    {
        return strtoupper($match[2]);
    }

    /**
     * Write property as xml key
     * @param string $key
     * @return string
     * @access protected
     */
    protected function propertyToKey($key)
    {
        return strtolower(preg_replace_callback('/([a-z])([A-Z])/', array($this, 'propertyToKeyCallback'), $key));
    }

    /**
     * Callback to rewrite property to key
     * @param Array $match
     * @return string
     * @access protected
     */
    protected function propertyToKeyCallback($match)
    {
        return $match[1] . '-' . strtolower($match[2]);
    }

    /**
     * Maps object to Json element-name
     * @access protected
     * @param Mapper_Mapable $subject Object to map to Json
     * @return string
     */
    protected function mapObjectToElementName(Mapper_Mapable $subject)
    {
        // Pick a default value based on the subject class
        $name = substr(get_class($subject), strlen(__NAMESPACE__) + 1);

        // See if the objectMapper array contains the (super)class
        foreach ($this->objectMapper as $key => $class) {
            if ($subject instanceof $class) {
                $name = $key;
                break;
            }
        }

        // Map the name to a proper key
        $name = $this->propertyToKey($name);

        // Get rid of the unnecessary type specs (i.e. invoice_detail => detail)
        $simplified = array(
            'payment', 'history', 'detail',
        );
        foreach ($simplified as $simplify) {
            $name = preg_replace('/^[a-z\-\/]+[_\/](' . preg_quote($simplify) . ')/', '\\1', $name);
        }

        if ($subject instanceof SyncObject) {
            $name = 'ids';
        }

        $pos = ($subject instanceof SyncArray) ? strpos($name, '_') : strrpos($name, '_');
        if ($pos !== false) {
            $name = substr($name, 0, $pos);
        }
        if ($subject instanceof ArrayObject && substr($name, -1) != 's') {
            $name .= 's';
        }

        // Exceptions
        $name = str_replace(
            array(
            'historys',
            'details',
            'incoming-invoice',
            ), array(
            'history',
            'details_attributes',
            'incoming_invoice',
            ), $name
        );

        return lcfirst($name);
    }

    /**
     * Test if $str ends with $end
     * @param string $str
     * @param string $end
     * @return bool
     * @access protected
     */
    protected function strEndsWith($str, $end)
    {
        return substr_compare($str, $end, -strlen($end), strlen($end)) === 0;
    }

    /**
     * Convert to Json
     *
     * @access public
     * @param Mapper_Mapable $subject Object to map to Json
     * @return SimpleXMLElement
     */
    public function toJson(Mapper_Mapable $subject)
    {
        $jsonArrayValue = array();
        if ($subject instanceof DeleteBySaving && $subject->isDeleted()) {
//			$jsonArrayValue->addChild('_destroy', '1');
        }

        foreach ($subject->toArray() as $property => $value) {
            $key = $this->propertyToXmlkey($property);
            if (is_null($value)) {
                $jsonArrayValue[$key] = null;
            } elseif (is_bool($value)) {
                $jsonArrayValue[$key] = $value === true ? 'true' : 'false';
            } elseif (is_array($value)) {
                $jsonArrayValue[$key] = $value;
            } elseif (!is_object($value) && !is_array($value)) {
                $jsonArrayValue[$key] = $value;
            } elseif (is_object($value) && $value instanceof Mapper_Mapable) {
                $jsonArrayValue[$key] = $this->toXml($value);
            } elseif (is_object($value) && $value instanceof \DateTime) {
                $jsonArrayValue[$key] = $value->format('c');
            } else {
                throw new Mapper_Exception('Invalid value for key ' . $key);
            }
        }

        $key = $this->mapObjectToElementName($subject);
        return array($key => $jsonArrayValue);
    }

    /**
     * Convert to json string
     *
     * @access public
     * @param Mapper_Mapable $subject Object to map to json
     * @return string
     */
    public function toJsonString(Mapper_Mapable $subject)
    {
        return json_encode($this->toJson($subject));
    }
}