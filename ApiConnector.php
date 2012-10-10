<?php

/**
 * Communicates with Moneybird through REST API
 * For more information about the MoneyBird API, please visit 
 * http://www.moneybird.nl/help/api
 * 
 * @author Sjors van der Pluijm <sjors@youngguns.nl>; @sjorsvdp
 */

namespace Moneybird;

/**
 * Service for connecting with Moneybird
 */
class ApiConnector {
	
	/**
	 * Version number of api
	 */
	const API_VERSION = '1.0';
	
	/**
	 * First part of Url to connect to
	 *
	 * @access protected
	 * @var string
	 */
	protected $baseUri;
	
	/**
	 * HttpRequest object
	 *
	 * @access protected
	 * @var Transport
	 */
	protected $transport;
	
	/**
	 * Mapper object
	 * 
	 * @access proteced
	 * @var Mapper
	 */
	protected $mapper;
	
	/**
	 * @var CurrentSession
	 * @access protected
	 */
	protected $currentSession;
	
	/**
	 * Last error messages
	 * @var Array
	 */
	protected $errors = array();
	
	/**
	 * Array of created service objects
	 * @var Array
	 */
	protected $services = array();
	
	/**
	 * Array of available filters
	 * @var Array
	 */
	protected $filters = array(
		'Invoice' => array(
			'all', 'this_month', 'last_month', 'this_quarter', 'last_quarter',
			'this_year', 'last_year', 'draft', 'sent', 'open', 'late', 'paid'
		),
		'Estimate' => array(
			'all', 'active', 'draft', 'sent', 'open', 'late', 'accepted', 
			'rejected', 'billed', 'archived',
			'this_month', 'last_month', 'this_quarter', 'last_quarter',
			'this_year', 'last_year',
		),
		'IncomingInvoice' => array(
			'all', 'this_month', 'last_month', 'this_quarter', 'last_quarter',
			'this_year', 'draft', 'sent', 'open', 'late', 'paid'
		),
		'RecurringTemplate' => array(
			'all', 'inactive', 'weekly', 'monthly', 'quarterly', 'half_yearly', 
			'yearly', 'upcoming',
		),
	);
	
	/**
	 * Array for mapping named id's to objects
	 * @var Array
	 */
	protected $namedId = array(
		'Contact' => array(
			'customer_id',
		),
		'Invoice' => array(
			'invoice_id',
		),
	);
	
	/**
	 * Show debug information
	 * @var bool
	 * @static
	 */
	public static $debug = false;
	
	/**
	 * Autoloader
	 * @static
	 * @param string $classname 
	 */
	static public function autoload($classname) {
		if (strpos($classname, __NAMESPACE__) === 0) {
			$classname = substr($classname, strlen(__NAMESPACE__) + 1);
			if (file_exists(__DIR__.'/'.str_replace('_', '/', $classname).'.php')) {
				require_once __DIR__.'/'.str_replace('_', '/', $classname).'.php';
			}			
		}
	}

	/**
	 * Create a service object
	 * 
	 * @param string $clientName Moneybird client name (<client>.moneybird.nl)
	 * @param Transport $transport
	 * @param Mapper $mapper
	 * @access public
	 * @throws NotLoggedInException
	 */
	public function __construct($clientName, Transport $transport, Mapper $mapper) {
		$this->transport = $transport;
		$this->mapper = $mapper;
		$this->transport->setUserAgent('MoneybirdPhpApi/2.1');
		
		if (!preg_match('/^[a-z0-9_\-]+$/', $clientName)) {
			throw new InvalidConfigException('Invalid companyname/clientname');
		}
		
		$this->baseUri = 'https://' . $clientName . '.moneybird.nl/api/v'.self::API_VERSION;
	}
	
	/**
	 * Login
	 * @return self
	 * @throws NotLoggedInException
	 * @access protected
	 */
	protected function testLogin() {
		static $loginTested = false;
		if (!$loginTested) {
			$loginTested = true;
			try {
				$this->getCurrentSession();
			} catch (NotFoundException $e) {
				throw new NotLoggedInException('Invalid companyname/clientname');
			}
		}
		return $this;
	}
	
	/**
	 * Number of requests left (max 350/h)
	 * @return int
	 */
	public function requestsLeft() {
		$this->testLogin();
		return $this->transport->requestsLeft();
	}
	
	/**
	 * Send request to Moneybird
	 * 
	 * @access protected
	 * @return string
	 * @param string $url
	 * @param string $method (GET|POST|PUT|DELETE)
	 * @param string $data
	 */
	protected function request($url, $method, $data = null) {
		$this->testLogin();
		try {
			$response = $this->transport->send(
				$url,
				$method,
				$data,
				array(
					'Content-Type: '.$this->mapper->getContentType(),
				)				
			);
		} catch (HttpClient_HttpStatusException $e) {
			$message = $e->getMessage();
			if ($e->getCode() == 403 || $e->getCode() == 422) {
				$this->errors = $this->mapper->mapFromStorage($this->transport->getLastResponse());
				if ($this->errors instanceof Error_Array && count($this->errors) > 0) {
					$message .= PHP_EOL . 'Errors:' . PHP_EOL . $this->errors;
				}
			}
			
			if (self::$debug) {
				printf(
					'Url: %s'.PHP_EOL.'Method: %s'.PHP_EOL.'Data: %s',
					$url,
					$method,
					$data
				);
			}
			
			switch ($e->getCode()) {
				case 401:
					throw new NotLoggedInException($message, 0, $e);
				break;
				case 403:
					throw new ForbiddenException($message, 0, $e);
				break;
				case 404:
					throw new NotFoundException($message, 0, $e);
				break;
				case 406:
				case 422:
					throw new NotValidException($message, 0, $e);
				break;
				default:
					$message = 'Unknown error';
				// no break
				case 500:
				case 501:
					throw new ServerErrorException($message, 0, $e);
				break;
			}
		}

		return $response;
	}
	
	/**
	 * Save object
	 * 
	 * @param Storable $model
	 * @return Storable
	 */
	public function save(Storable $model) {
		$response = $this->request(
			$this->buildUrl($model),
			$model->getId() > 0 ? 'PUT' : 'POST',
			$this->mapper->mapToStorage($model)
		);
		
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Delete object
	 * @param Storable $model
	 * @return self
	 * @throws ForbiddenException
	 */
	public function delete(Storable $model) {
		$types = array('Invoice', 'Estimate', 'RecurringTemplate', 'IncomingInvoice');
		foreach ($types as $type) {
			$interface = $type . '_Subject';
			$method = 'get' . $type . 's';
			if (($model instanceof $interface) && count($model->$method($this->getService($type))) > 0) {
				throw new ForbiddenException('Unable to delete ' . $type . '_Subject with ' . strtolower($type) . 's');
			}
		}
		$this->request(
			$this->buildUrl($model),
			'DELETE'
		);
		
		return $this;
	}
	
	/**
	 * Send invoice or estimate
	 * 
	 * @param Sendable $model
	 * @param Envelope_Abstract $envelope
	 * @return Sendable
	 */
	public function send(Sendable $model, Envelope_Abstract $envelope) {
		$classname = $this->getType($model);

		if (intval($model->getId()) == 0) {
			$model->save($this->getService($classname));
		}

		$response = $this->request(
			$this->buildUrl($model, null, '/send_' . strtolower($classname)), 
			'PUT', 
			$this->mapper->mapToStorage($envelope)
		);
		
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Register payment
	 *
	 * @access public
	 * @param Payable $invoice invoice to register payment for
	 * @param Payment_Abstract $payment payment to register
	 * @return Payable
	 */
	public function registerPayment(Payable $invoice, Payment_Abstract $payment) {
		$classname = $this->getType($invoice);
		
		if (intval($invoice->getId()) == 0) {
			$invoice->save($this->getService($classname));
		}
		$idProperty = strtolower($classname).'Id';
		$payment->setData(array(
			$idProperty => $invoice->getId()
		));
		
		$response = $this->request(
			$this->buildUrl($invoice, null, '/payments'), 
			'POST', 
			$this->mapper->mapToStorage($payment)
		);
		
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Send reminder
	 * 
	 * @param Invoice $invoice
	 * @param Invoice_Envelope $envelope
	 * @return ApiConnector 
	 */
	public function remind(Invoice $invoice, Invoice_Envelope $envelope) {
		$envelope->setData(array(
			'invoiceId' => $invoice->getId()
		));

		$response = $this->request(
			$this->buildUrl($invoice, null, '/send_reminder'), 
			'PUT', 
			$this->mapper->mapToStorage($envelope)
		);
		
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Get raw PDF content
	 * 
	 * @return string
	 * @param PdfDocument $model
	 * @param Domainmodel_Abstract $parent
	 */
	public function getPdf(PdfDocument $model, Domainmodel_Abstract $parent = null) {
		return $this->request($this->buildUrl($model, $parent, null, 'pdf'), 'GET');
	}
	
	/**
	 * Build the url for the request
	 * 
	 * @param Domainmodel_Abstract $subject
	 * @param Domainmodel_Abstract $parent
	 * @param string $appendUrl Filter url
	 * @param string $docType (pdf|xml|json)
	 * @return string
	 */
	protected function buildUrl(Domainmodel_Abstract $subject, Domainmodel_Abstract $parent = null, $appendUrl = null, $docType = null) {
		if (is_null($docType)) {
			$docType = substr($this->mapper->getContentType(), strpos($this->mapper->getContentType(), '/') + 1);
		}
		
		$url = $this->baseUri;
		if (!is_null($parent) && intval($parent->getId()) > 0) {
			$url .= '/'.$this->mapTypeName($parent).'/'.$parent->getId();
		}
		$url .= '/'.$this->mapTypeName($subject);
		if (intval($subject->getId()) > 0) {
			$url .= '/'.$subject->getId();
		}
		if (!is_null($appendUrl)) {
			$url .= $appendUrl;
		}
		return $url.'.'.$docType;
	}
	
	/**
	 * Return the last errors
	 *
	 * @access public
	 * @return Error_Array
	 */
	public function getErrors() {
		$errors = $this->errors;
		$this->errors = new Error_Array;
		return $errors;
	}
	
	/**
	 * Maps object to an url-part
	 *
	 * @access protected
	 * @param Domainmodel_Abstract $model
	 * @return string
	 */
	protected function mapTypeName(Domainmodel_Abstract $model) {
		if ($model instanceof CurrentSession) {
			$mapped = 'current_session';
		} else {
			$classname = $this->getType($model);
			$mapped = lcfirst($classname);
			if (false !== ($pos = strpos($mapped, '_'))) {
				$mapped = substr($mapped, 0, $pos);
			}
			$mapped = strtolower(
				preg_replace_callback('/([A-Z])/', array($this, 'classnameToUrlpartCallback'), $mapped)
			).'s';
		}
		return $mapped;
	}
	
	/**
	 * Callback to rewrite classname to url-part
	 * 
	 * @param Array $match
	 * @return string
	 * @access protected
	 */
	protected function classnameToUrlpartCallback($match) {
		return '_'.strtolower($match[1]);
	}
	
	/**
	 * Get current session
	 * 
	 * @return CurrentSession
	 */
	public function getCurrentSession() {
		if (is_null($this->currentSession)) {
			$this->currentSession = $this->mapper->mapFromStorage(
				$this->request($this->buildUrl(new CurrentSession()), 'GET')
			);
		}
		if (!($this->currentSession instanceof CurrentSession)) {
			$this->currentSession = null;
			throw new NotLoggedInException('Authorization required');
		}
		return $this->currentSession;
	}
	
	/**
	 * Get sync status
	 * 
	 * @param string $classname
	 * @return ArrayObject
	 */
	public function getSyncList($classname) {
		$classname = __NAMESPACE__.'\\'.$classname.'_Sync';
		$response = $this->request($this->buildUrl(new $classname(), null, '/sync_list_ids'), 'GET');
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Get objects by ids (array)
	 * 
	 * @param string $classname
	 * @param Array $ids
	 * @return ArrayObject 
	 */
	public function getByIds($classname, Array $ids) {
		$classname = __NAMESPACE__.'\\'.$classname.'_Sync';
		$classnameArray = $classname.'_Array';
		$objects = new $classnameArray();
		$objects->append(new $classname(array('id' => $ids)));

		$response = $this->request(
			$this->buildUrl(new $classname(), null, '/sync_fetch_ids'),
			'POST', 
			$this->mapper->mapToStorage($objects)
		);
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Get object by id
	 * 
	 * @param string $classname
	 * @param int $id
	 * @return Domainmodel_Abstract 
	 */
	public function getById($classname, $id) {
		if (!preg_match('/^[0-9]+$/D', $id)) {
			throw new InvalidIdException('Invalid id: ' . $id);
		}
		$classname = __NAMESPACE__.'\\'.$classname;
		$response = $this->request($this->buildUrl(new $classname(array('id' => $id))), 'GET');
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Get all objects
	 * 
	 * @param string $classname
	 * @param string|integer $filter Filter name or id (advanced filters)
	 * @param Domainmodel_Abstract $parent
	 * @return ArrayObject
	 * @throws InvalidFilterException 
	 */
	public function getAll($classname, $filter = null, Domainmodel_Abstract $parent = null) {
		$filterUrl = '';
		if (!is_null($filter)) {
			if (
				isset($this->filters[$classname]) &&
				in_array($filter, $this->filters[$classname])) {
				
				$filterUrl = '/filter/' . $filter;
			} elseif (
				isset($this->filters[$classname]) &&
				preg_match('/^[0-9]+$/D', $filter)) {
				
				$filterUrl = '/filter/' . $filter;
			} else {
				$message = 'Unknown filter "' . $filter . '" for ' . $classname;
				if (isset($this->filters[$classname])) {
					$message .= '; available filters: ' . implode(', ', $this->filters[$classname]);
				}
				throw new InvalidFilterException($message);
			}
		}
		
		$classname = __NAMESPACE__.'\\'.$classname;
		$response = $this->request($this->buildUrl(new $classname(), $parent, $filterUrl), 'GET');
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Get an object by it's named id (customer_id, invoice_id)
	 * 
	 * @param string $classname
	 * @param string $name
	 * @param string $id
	 * @return Domainmodel_Abstract
	 * @throws InvalidNamedIdExeption
	 * @throws InvalidIdException 
	 */
	public function getByNamedId($classname, $name, $id) {
		if (!isset($this->namedId[$classname]) || !in_array($name, $this->namedId[$classname])) {
			throw new InvalidNamedIdExeption('NamedId '.$name.' is not a valid type');
		}
		if (!preg_match('/^[a-zA-Z0-9\- _]+$/D', $id)) {
			throw new InvalidIdException('Invalid id: ' . $id);
		}
		$classname = __NAMESPACE__.'\\'.$classname;
		$response = $this->request($this->buildUrl(new $classname(), null, '/'.$name.'/'.$id), 'GET');
		return $this->mapper->mapFromStorage($response);
	}
	
	/**
	 * Build a service object for contacts, invoices, etc
	 * @param string $type
	 * @return Object
	 */
	public function getService($type) {
		if (!isset($this->services[$type])) {
			if (!file_exists(__DIR__.'/'.$type.'/Service.php')) {
				throw new InvalidServiceTypeException('No service for type '.$type);
			}
			$classname = __NAMESPACE__.'\\'.$type.'_Service';
			$this->services[$type] = new $classname($this);
		}
		return $this->services[$type];
	}
	
	/**
	 * Determine the type of $model
	 * @param Domainmodel_Abstract $model
	 * @return string
	 */
	protected function getType(Domainmodel_Abstract $model) {
		$types = array(
			'Contact',
			'Invoice',
			'Estimate',
			'IncomingInvoice',
			'RecurringTemplate',
			'CurrentSession',
			'TaxRate',
			'Product',
		);
		foreach ($types as $type) {
			$classname = __NAMESPACE__ . '\\' . $type;
			if ($model instanceof $classname) {
				return $type;
			}
		}
		return substr(get_class($model), strlen(__NAMESPACE__) + 1);
	}
}