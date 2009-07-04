<?php

/**
 * Communicates with Moneybird through REST API
 * http://www.moneybird.nl/
 *
 * @author Sjors van der Pluijm <sjors@phpfreakz.nl>
 */

/**
 * Communicates with Moneybird through REST API
 * Main class for sending request to Moneybird
 */
class MoneybirdApi
{
    /**
     * Client name at Moneybird (<clientname>.moneybird.nl)
     *
     * @access protected
     * @var string
     */
    protected $clientname;

    /**
     * Curl connection resource with Moneybird
     *
     * @access protected
     * @var resource
     */
    protected $connection;

    /**
     * Array of error messages
     *
     * @access protected
     * @var array
     */
    protected $errors;

    /**
     * Constructor
     *
     * @param string $clientname first part of Moneybird URL (<clientname>.moneybird.nl)
     * @param string $username username for login
     * @param string $password password for login
     * @access public
     * @throws MoneybirdConnectionErrorException
     */
    public function __construct($clientname=null, $username=null, $password=null)
    {
        // Set defaults
        $this->clientname = $clientname != null ? $clientname : 'pfz';
        $username         = $username   != null ? $username   : 'api';
        $password         = $password   != null ? $password   : 'apiapi';

        $this->errors = array();

        $this->initConnection($username, $password);
    }

    /**
     * Returns an array based on the type:
     * 0 => url-part for request
     * 1 => classname to use
     *
     * @param string $type (contact|invoice)
     * @throws MoneybirdUnknownTypeException
     * @access protected
     * @return array
     */
    protected function typeInfo($type)
    {
        switch ($type)
        {
            case 'contact':
            case 'invoice':
                return array($type.'s', 'Moneybird'.ucfirst($type));
            break;

            default:
                throw new MoneybirdUnknownTypeException('Unknown type: '.$type);
            break;
        }
    }

    /**
     * Connect with API
     *
     * @throws MoneybirdConnectionErrorException
     * @access protected
     */
    protected function initConnection($username, $password)
    {
        if (!$this->connection = curl_init())
        {
            throw new MoneybirdConnectionErrorException('Unable to connect to Moneybird Api');
        }
        else
        {
            $setopt = curl_setopt_array(
                $this->connection,
                array(
                    CURLOPT_USERPWD        => $username.':'.$password,
                    CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTPHEADER     => array(
                        'Content-Type: application/xml',
                        'Accept: application/xml'
                    ),
                )
            );
            if (!$setopt)
            {
                throw new MoneybirdConnectionErrorException('Unable to set cURL options'.PHP_EOL.curl_error($this->connection));
            }
        }
    }

    /**
     * Send a request to the API
     *
     * @param string $url request parameters
     * @param string $method (GET|POST|PUT|DELETE)
     * @param iMoneybirdObject $mbObject object to change
     * @return SimpleXMLElement
     * @access protected
     * @throws MoneybirdAuthorizationRequiredException
     * @throws MoneybirdNotAcceptedException
     * @throws MoneybirdUnprocessableEntityException
     * @throws MoneybirdInternalServerErrorException
     * @throws MoneybirdUnknownResponseException
     * @throws MoneybirdItemNotFoundException
     * @throws MoneybirdConnectionErrorException
     * @throws MoneybirdXmlErrorException
     */
    protected function request($url, $method='GET', iMoneybirdObject $mbObject=null)
    {
        $curlopts = array(
            CURLOPT_URL => 'https://'.$this->clientname.'.moneybird.nl/'.$url.'.xml',
        );

        $this->errors = array();

        switch ($method)
        {
            case 'GET':
            default:
                $curlopts[CURLOPT_HTTPGET] = true;
            break;

            case 'POST':
                $curlopts[CURLOPT_POST] = true;
                $curlopts[CURLOPT_POSTFIELDS] = $mbObject->toXML();
            break;

            case 'PUT':
                $xml = $mbObject->toXML();

                $fh  = fopen('php://memory', 'rw');
                fwrite($fh, $xml);
                rewind($fh);

                $curlopts[CURLOPT_PUT]        = true;
                $curlopts[CURLOPT_INFILE]     = $fh;
                $curlopts[CURLOPT_INFILESIZE] = strlen($xml);
            break;

            case 'DELETE':
                $curlopts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            break;
        }

        $setopt = curl_setopt_array($this->connection, $curlopts);
        if (!$setopt)
        {
            throw new MoneybirdConnectionErrorException('Unable to set cURL options'.PHP_EOL.curl_error($this->connection));
        }

        $xmlstring = curl_exec($this->connection);
        $xmlresponse = null;
        if (false === $xmlstring)
        {
            throw new MoneybirdConnectionErrorException('Unable perform request: '.$url.PHP_EOL.curl_error($this->connection));
        }
        elseif (trim($xmlstring) != '')
        {
            $xmlresponse = simplexml_load_string($xmlstring);
        }

        $httpresponse = curl_getinfo($this->connection, CURLINFO_HTTP_CODE);
        switch ($httpresponse)
        {
            case 100: // Continue
            case 200: // OK         Request was successful
            case 201: // Created 	Entity was created successful
            break;

            case 401: // Authorization required     No authorization information provided with request
                $error = new MoneybirdAuthorizationRequiredException('No authorization information provided with request');
            break;

            case 404: // The entity or action is not found in the API
                $error = new MoneybirdItemNotFoundException('The entity or action is not found in the API');
            break;
            
            case 406: // Not accepted               The action you are trying to perform is not available in the API
                $error = new MoneybirdNotAcceptedException('The action you are trying to perform is not available in the API');
            break;
            
            case 422: // Unprocessable entity       Entity was not created because of errors in parameters. Errors are included in XML response.
                $error = new MoneybirdUnprocessableEntityException('Entity was not created or deleted because of errors in parameters. Errors are included in XML response.');
            break;
            
            case 500: // Internal server error      Something went wrong while processing the request. MoneyBird is notified of the error.
                $error = new MoneybirdInternalServerErrorException('Something went wrong while processing the request. MoneyBird is notified of the error.');
            break;

            default:
                $error = new MoneybirdUnknownResponseException('Unknown response from Moneybird: '.$httpresponse);
            break;
        }

        // If $error exists, an exception needs to be thrown
        // Before throwing an exception, parse the errors from the xml
        if (isset($error))
        {
            if ($error instanceof MoneybirdUnprocessableEntityException)
            {
                foreach ($xmlresponse as $message)
                {
                    $this->errors[] = $message;
                }
            }
            throw $error;
        }

        return $xmlresponse;
    }

    /**
     * Get single Moneybird object
     *
     * @param integer $objectID id of object to retreive
     * @param string $type (contact|invoice)
     * @return iMoneybirdObject
     * @access protected
     * @throws MoneybirdUnknownTypeException
     * @throws MoneybirdInvalidIdException
     * @throws MoneybirdItemNotFoundException
     */
    protected function getMbObject($objectID, $type)
    {
        if (!preg_match('/^[0-9]+$/D', $objectID))
        {
            throw new MoneybirdInvalidIdException('Invalid id: '.$objectID);
        }
        list($typegroup, $class) = $this->typeInfo($type);

        $response = $this->request($typegroup.'/'.$objectID);

        $object = new $class;
        $object->fromXML($response);
        return $object;
    }

    /**
     * Get all objects
     *
     * @return array
     * @param string $type (contact|invoice)
     * @param string|iiMoneybirdFilter $filter optional, filter results
     * @access protected
     * @throws MoneybirdInvalidIdException
     */
    protected function getMbObjects($type, $filter=null)
    {
        list($typegroup, $class) = $this->typeInfo($type);

        $request = $typegroup;
        $method  = 'GET';
        if ($filter != null)
        {
            if ($filter instanceof iMoneybirdFilter)
            {
                $request .= '/filter/advanced';
                $method = 'POST';
            }
            else
            {
                $request .= '/filter/'.$filter;
                $filter = null;
            }
        }

        $foundObjects = $this->request(
            $request,
            $method,
            $filter
        );

        $objects = array();
        foreach ($foundObjects as $response)
        {
            $object = new $class;
            $object->fromXML($response);
            $objects[] = $object;
        }
        return $objects;
    }

    /**
     * Save an object
     *
     * @return iMoneybirdObject
     * @access protected
     * @throws MoneybirdInvalidIdException
     * @param iMoneybirdObject $object object to save
     * @param string $type (contact|invoice)
     */
    protected function saveMbObject(iMoneybirdObject $object, $type)
    {
        list($typegroup, $class) = $this->typeInfo($type);

        if ($object->id != null)
        {
            // Update object
            $this->request(
                $typegroup.'/'.$object->id,
                'PUT',
                $object
            );

            return $this->getMbObject($object->id, $type);
        }
        else
        {
            // Insert object
            $response = $this->request(
                $typegroup,
                'POST',
                $object
            );

            $object = new $class;
            $object->fromXML($response);
            return $object;
        }        
    }

    /**
     * Delete object
     *
     * @access protected
     * @throws MoneybirdInvalidIdException
     * @param iMoneybirdObject $object object to delete
     * @param string $type (contact|invoice)
     */
    protected function deleteMbObject(iMoneybirdObject $object, $type)
    {
        list($typegroup, $class) = $this->typeInfo($type);
        $this->request($typegroup.'/'.$object->id, 'DELETE');
    }

    /**
     * Get a contact by ID
     *
     * @param integer $contactID
     * @return MoneybirdContact
     * @access public
     * @throws MoneybirdInvalidIdException
     * @throws MoneybirdItemNotFoundException
     */
    public function getContact($contactID)
    {
        return $this->getMbObject($contactID, 'contact');
    }

    /**
     * Get all contacts
     *
     * @return array
     * @access public
     */
    public function getContacts()
    {
        return $this->getMbObjects('contact');
    }

    /**
     * Save contact
     *
     * @return MoneybirdContact
     * @param MoneybirdContact $contact contact to save
     * @access public
     */
    public function saveContact(MoneybirdContact $contact)
    {
        return $this->saveMbObject($contact, 'contact');
    }

    /**
     * Delete contact
     *
     * @param MoneybirdContact $contact contact to delete
     * @access public
     */
    public function deleteContact(MoneybirdContact $contact)
    {
        $this->deleteMbObject($contact, 'contact');
    }

    /**
     * Get invoice
     *
     * @param integer $invoiceID invoice to retreive
     * @return MoneybirdInvoice
     * @access public
     * @throws MoneybirdInvalidIdException
     * @throws MoneybirdItemNotFoundException
     */
    public function getInvoice($invoiceID)
    {
        return $this->getMbObject($invoiceID, 'invoice');
    }

    /**
     * Get all invoices
     *
     * @return array
     * @param string|iMoneybirdFilter $filter optional, filter to apply
     * @access public
     * @throws MoneybirdUnknownFilterException
     */
    public function getInvoices($filter=null)
    {
        $filters = array(
            'all', 'this_month', 'last_month', 'this_quarter', 'last_quarter',
            'this_year', 'draft', 'sent', 'open', 'late', 'paid'
        );

        if ($filter != null && !($filter instanceof iMoneybirdFilter) &&
            !in_array($filter, $filters))
        {
            throw new MoneybirdUnknownFilterException('Unknown filter for invoices: '.
                $filter.'.'.PHP_EOL.'Available filters: '.implode(', ', $filters));
        }

        return $this->getMbObjects('invoice', $filter);
    }

    /**
     * Save invoice
     *
     * @return MoneybirdInvoice
     * @param MoneybirdInvoice $invoice invoice to save
     * @access public
     */
    public function saveInvoice(MoneybirdInvoice $invoice)
    {
        return $this->saveMbObject($invoice, 'invoice');
    }

    /**
     * Delete invoice
     *
     * @param MoneybirdInvoice $invoice invoice to delete
     * @access public
     * @throws Exception
     */
    public function deleteInvoice(MoneybirdInvoice $invoice)
    {
        $this->deleteMbObject($invoice, 'invoice');
    }

    /**
     * Send an invoice
     *
     * @access public
     * @param MoneybirdInvoiceSendInformation $sendinfo information to send invoice
     */
    public function sendInvoice(MoneybirdInvoiceSendInformation $sendinfo)
    {
        $invoice = $sendinfo->getInvoice();
        if ($invoice->id == null)
        {
            // Save invoice first
            $invoice = $this->saveInvoice($invoice);
        }

        // Send
        $this->request(
            'invoices/'.$invoice->id.'/send_invoice',
            'PUT',
            $sendinfo
        );
    }
    
    /**
     * Mark invoice as send
     *
     * @access public
     * @param MoneybirdInvoice $invoice subjected invoice
     */
    public function markInvoiceAsSent(MoneybirdInvoice $invoice)
    {
        return $this->sendInvoice(new MoneybirdInvoiceSendInformation($invoice, 'hand'));
    }

    /**
     * Register invoice payment
     *
     * @access public
     * @param MoneybirdInvoicePayment $payment payment to register
     */
    public function registerInvoicePayment(MoneybirdInvoicePayment $payment)
    {
        $invoice = $payment->getInvoice();
        if ($invoice->id == null)
        {
            // Save invoice first
            $invoice = $this->saveInvoice($invoice);
        }
        $payment->invoice_id = $invoice->id;

        // Send
        $this->request(
            'invoices/'.$invoice->id.'/payments',
            'POST',
            $payment
        );
    }

    /**
     * Return the last errors
     *
     * @access public
     * @return array
     */
    public function getErrorMessages()
    {
        $errors = $this->errors;
        $this->errors = array();
        return $errors;
    }
}

/**
 * Exceptions for Moneybird
 * 
 */
class MoneybirdException extends Exception
{
}

/**
 * Exception Authorization required (No authorization information provided with request)
 * 
 */
class MoneybirdAuthorizationRequiredException extends MoneybirdException
{
}

/**
 * Exception Not accepted (The action you are trying to perform is not available in the API)
 * 
 */
class MoneybirdNotAcceptedException extends MoneybirdException
{
}

/**
 * Exception Unprocessable entity (Entity was not created because of errors in parameters. Errors are included in XML response.)
 * 
 */
class MoneybirdUnprocessableEntityException extends MoneybirdException
{
}

/**
 * Exception Internal server error (Something went wrong while processing the request. MoneyBird is notified of the error.)
 * 
 */
class MoneybirdInternalServerErrorException extends MoneybirdException
{
}

/**
 * Exception The entity or action is not found in the API
 * 
 */
class MoneybirdItemNotFoundException extends MoneybirdException
{
}

/**
 * Exception Unkown reponse
 * 
 */
class MoneybirdUnknownResponseException extends MoneybirdException
{
}

/**
 * Exception Unkown filter
 * 
 */
class MoneybirdUnknownFilterException extends MoneybirdException
{
}

/**
 * Exception Unknown send method
 * 
 */
class MoneybirdUnknownSendMethodException extends MoneybirdException
{
}

/**
 * Exception Invalid id
 * 
 */
class MoneybirdInvalidIdException extends MoneybirdException
{
}

/**
 * Getting or setting an unknown property
 * 
 */
class MoneybirdUnknownPropertyException extends MoneybirdException
{
}

/**
 * Exception Connection error (probably cURL to blame)
 *
 */
class MoneybirdConnectionErrorException extends MoneybirdException
{
}

/**
 * Exception Unknown state of invoice
 *
 */
class MoneybirdUnknownInvoiceStateException extends MoneybirdException
{
}

/**
 * Interface for MoneybirdObject
 *
 */
interface iMoneybirdObject
{
    /**
     * Fill with XML
     *
     * @access public
     * @param SimpleXMLElement $xml
     */
    public function fromXML(SimpleXMLElement $xml);

    /**
     * Convert to XML string
     *
     * @access public
     * @return string
     */
    public function toXML();
}

/**
 * Interface for MoneybirdInvoiceDetail
 * 
 */
interface iMoneybirdInvoiceDetail extends iMoneybirdObject
{
    /**
     * Constructor
     *
     * @access public
     * @param MoneybirdInvoice $invoice
     */
    public function __construct(MoneybirdInvoice $invoice);

    /**
     * Get the invoice
     *
     * @access public
     * @return MoneybirdInvoice
     */
    public function getInvoice();
}


/**
 * Interface for MoneybirdFilter
 *
 */
interface iMoneybirdFilter extends iMoneybirdObject
{
}

/**
 * Object in Moneybird
 *
 * @abstract
 */
abstract class MoneybirdObject implements iMoneybirdObject
{
    /**
     * Properties
     *
     * @access protected
     * @var array
     */
    protected $properties;

    /**
     * Set property
     *
     * @access public
     * @param string $property
     * @param mixed $value
     * @throws Exception
     */
    public function __set($property, $value)
    {
        $this->properties[$property] = $value;
    }

    /**
     * Get property
     *
     * @access public
     * @param string $property
     * @return mixed
     * @throws Exception
     */
    public function __get($property)
    {
        if (!isset($this->properties[$property]))
        {
            return null;
        }
        return $this->properties[$property];
    }

    /**
     * Load object from XML
     *
     * @access public
     * @param SimpleXMLElement $xml XML to load
     * @param array $arrayHandlers Mapping for converting array elements to objects
     */
    public function fromXML(SimpleXMLElement $xml, Array $arrayHandlers = null)
    {
        foreach ($xml as $key => $value)
        {
            $attributes = $value->attributes();
            $type = isset($attributes['type']) ? $attributes['type'] : 'string';

            $key = str_replace('-', '_', $key);

            if ($type == 'array')
            {
                $this->properties[$key] = array();
                if (isset($arrayHandlers[$key]))
                {
                    foreach ($value as $subxml)
                    {
                        $object = new $arrayHandlers[$key]($this);
                        $object->fromXML($subxml);
                        $this->properties[$key][] = $object;
                    }
                }
            }
            elseif ($type == 'integer')
            {
                $this->properties[$key] = intval($value);
            }
            elseif ($type == 'float')
            {
                $this->properties[$key] = floatval($value);
            }
            elseif ($type == 'datetime' || $type == 'date')
            {
                $this->properties[$key] = new DateTime(strval($value));
            }
            else
            {
                $this->properties[$key] = strval($value);
            }
        }
    }

    /**
     * Convert to XML string
     *
     * @access public
     * @param array $arrayHandlers Mapping for internal naming of properties to XML tags
     * @param string $elmKeyOpen Open tag
     * @param string $elmKeyClose Close tag
     * @param array $skipProperties Skip the properties with these names
     * @return string
     */
    public function toXML(Array $arrayHandlers = null, $elmKeyOpen = null, $elmKeyClose = null, array $skipProperties = array())
    {
        if ($elmKeyOpen == null)
        {
            $elmKeyOpen = '<'.strtolower(substr(get_class($this), 9)).'>';
        }
        if ($elmKeyClose == null)
        {
            $elmKeyClose = '</'.strtolower(substr(get_class($this), 9)).'>';
        }

        $skipProperties = array_merge($skipProperties, array(
            'updated_at', 'created_at',
        ));

        $xml = $elmKeyOpen.PHP_EOL;
        foreach ($this->properties as $key => $value)
        {
            if (in_array($key, $skipProperties))
            {
                continue;
            }

            $key = str_replace('_', '-', $key);

            $keyOpen  = '<'.$key.'>';
            $keyClose = '</'.$key.'>';
            
            if (is_object($value) && $value instanceof DateTime)
            {
                $value = $value->format('c');
            }
            if (is_array($value))
            {
                if (isset($arrayHandlers[$key]))
                {
                    $keyOpen  = '<'.$arrayHandlers[$key].' type="array">'.PHP_EOL;
                    $keyClose = '</'.$arrayHandlers[$key].'>';
                    $newvalue = '';
                    foreach ($value as $object)
                    {
                        $newvalue .= $object->toXML();
                    }
                    $value = $newvalue;
                }
                else
                {
                    foreach ($value as $arrayValue)
                    {
                        $xml .= '   '.$keyOpen.$arrayValue.$keyClose.PHP_EOL;
                    }
                    continue;
                }
            }
            $xml .= '   '.$keyOpen.$value.$keyClose.PHP_EOL;
        }
        $xml .= $elmKeyClose.PHP_EOL;

        return $xml;
    }
}

/**
 * Contact in Moneybird
 *
 */
class MoneybirdContact extends MoneybirdObject
{
}

/**
 * Invoice in Moneybird
 * 
 */
class MoneybirdInvoice extends MoneybirdObject
{
    /**
     * Load object from XML
     *
     * @access public
     * @param SimpleXMLElement $xml
     */
    public function fromXML(SimpleXMLElement $xml)
    {
        parent::fromXML($xml, array(
            'history'  => 'MoneybirdInvoiceHistoryItem',
            'payments' => 'MoneybirdInvoicePayment',
            'details'  => 'MoneybirdInvoiceLine',
        ));
    }

    /**
     * Convert object to XML
     *
     * @access public
     * @return string
     */
    public function toXML()
    {
        return parent::toXML(
            array(
                'details' => 'details_attributes',
            ),
            null,
            null,
            array('history', 'payments')
        );
    }

    /**
     * Copy info from contact to invoice
     *
     * @access public
     * @param MoneybirdContact $contact
     */
    public function setContact(MoneybirdContact $contact)
    {
        $properties = array(
            'name', 'contact_name', 'address1', 'address2', 'zipcode', 'city',
            'country', 'customer_id',
        );

        $this->contact_id = $contact->id;
        foreach ($properties as $property)
        {
            $this->$property = $contact->$property;
        }
    }
}

/**
 * InvoiceDetail in Moneybird
 *
 */
class MoneybirdInvoiceDetail extends MoneybirdObject implements iMoneybirdInvoiceDetail
{
    /**
     * Invoice
     * 
     * @access private
     * @var MoneybirdInvoice
     */
    private $invoice;

    /**
     * Constructor
     *
     * @access public
     * @param MoneybirdInvoice $invoice
     */
    public function __construct(MoneybirdInvoice $invoice)
    {
        $this->invoice = $invoice;
        $this->properties['invoice_id'] = $invoice->id;
    }

    /**
     * Get the invoice
     *
     * @access public
     * @return MoneybirdInvoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}

/**
 * InvoicePayment in Moneybird
 *
 */
class MoneybirdInvoicePayment extends MoneybirdInvoiceDetail
{
    /**
     * Convert to XML string
     *
     * @access public
     * @return string
     */
    public function toXML()
    {
        return parent::toXML(
            null,
            '<invoice_payment>',
            '</invoice_payment>',
            array('invoice_id')
        );
    }
}

/**
 * InvoiceLine in Moneybird
 *
 */
class MoneybirdInvoiceLine extends MoneybirdInvoiceDetail
{
    /**
     * Convert to XML string
     *
     * @access public
     * @return string
     */
    public function toXML()
    {
        return parent::toXML(
            null,
            '<detail type="InvoiceDetail">',
            '</detail>',
            array('total_price_excl_tax', 'total_price_incl_tax',)
        );
    }
}

/**
 * InvoiceHistoryItem in Moneybird
 *
 */
class MoneybirdInvoiceHistoryItem extends MoneybirdInvoiceDetail
{
}

/**
 * InvoiceSendInformation for Moneybird
 *
 */
class MoneybirdInvoiceSendInformation extends MoneybirdInvoiceDetail
{
    /**
     * Constructor
     *
     * @access public
     * @param MoneybirdInvoice $invoice
     * @param string $sendMethod (email|post|hand)
     * @param string $email Address to send to
     * @param string $message Message in mail body
     * @throws MoneybirdUnknownSendMethodException
     */
    public function __construct(MoneybirdInvoice $invoice, $sendMethod='email', $email=null, $message=null)
    {
        parent::__construct($invoice);

        if (!in_array($sendMethod, array('hand', 'email', 'post')))
        {
            throw new MoneybirdUnknownSendMethodException('Unknown send method: '.$sendMethod);
        }
        $this->properties['sendMethod'] = $sendMethod;

        if ($sendMethod == 'email')
        {
            $this->properties['email']   = $email;
            $this->properties['message'] = $message;
        }
    }

    /**
     * Convert to XML string
     *
     * @access public
     * @return string
     */
    public function toXML()
    {
        $xml  = '<invoice>'.PHP_EOL;
        $xml .= '   <send-method>'.$this->properties['sendMethod'].'</send-method>'.PHP_EOL;
        if ($this->properties['email'] != null)
        {
            $xml .= '   <email>'.$this->properties['email'].'</email>'.PHP_EOL;
        }
        if ($this->properties['message'] != null)
        {
            $xml .= '   <invoice-email>'.$this->properties['message'].'</invoice-email>'.PHP_EOL;
        }
        $xml .= '</invoice>'.PHP_EOL;

        return $xml;
    }

    /**
     * Load object from XML
     *
     * @access public
     * @param SimpleXMLElement $xml
     */
    public function fromXML(SimpleXMLElement $xml)
    {
        throw new Exception(__CLASS__.' cannot be loaded from XML');
    }
}

/**
 * Advanced filter for finding invoices
 *
 */
class MoneybirdAdvancedInvoiceFilter extends MoneybirdObject implements iMoneybirdFilter
{
    /**
     * Constructor
     *
     * @access public
     * @param DateTime $fromDate Start date
     * @param DateTime $fromDate End date
     * @param string|array $states States to search (draft|open|late|paid)
     * @throws MoneybirdUnknownInvoiceStateException
     */
    public function __construct(DateTime $fromDate, DateTime $toDate, $states)
    {
        $statesAvailable = array('draft', 'open', 'late', 'paid');

        $this->properties['from_date'] = $fromDate;
        $this->properties['to_date']   = $toDate;

        $this->properties['states'] = array();
        foreach ((array) $states as $state)
        {
            if (!in_array($state, $statesAvailable))
            {
                throw new MoneybirdUnknownInvoiceStateException('Unknown state for invoices: '.$state);
            }
            $this->properties['states'][] = $state;
        }
    }

    /**
     * Load object from XML
     *
     * @access public
     * @param SimpleXMLElement $xml
     */
    public function fromXML(SimpleXMLElement $xml)
    {
        throw new Exception(__CLASS__.' cannot be loaded from XML');
    }

    /**
     * Convert to XML string
     *
     * @access public
     * @return string
     */
    public function toXML()
    {
        return parent::toXML(
            null,
            '<filter>',
            '</filter>'
        );
    }
}
?>