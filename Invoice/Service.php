<?php

/*
 * Invoice service class
 */

namespace Moneybird\Invoice;

use Moneybird\Invoice;
use Moneybird\ApiConnector;
use Moneybird\Service as ServiceInterface;
use Moneybird\InvalidRequestException;
use Moneybird\InvalidFilterException;

/**
 * Invoice service
 */
class Service implements ServiceInterface {
	
	/**
	 * ApiConnector object
	 * @var ApiConnector
	 */
	protected $connector;
	
	/**
	 * Constructor
	 * @param ApiConnector $connector 
	 */
	public function __construct(ApiConnector $connector) {
		$this->connector = $connector;
	}
	
	/**
	 * Get invoices sync status
	 * @return Invoice_Array
	 */
	public function getSyncList() {
		return $this->connector->getSyncList(__NAMESPACE__);
	}
	
	/**
	 * Get invoice by id
	 * @param int $id
	 * @return Invoice
	 */
	public function getById($id) {
		return $this->connector->getById(__NAMESPACE__, $id);
	}
	
	/**
	 * Get invoices by id (max 100)
	 * @param Array $ids
	 * @return Invoice_Array
	 */
	public function getByIds(Array $ids) {
		return $this->connector->getByIds(__NAMESPACE__, $ids);
	}
	
	/**
	 * Get all invoices
	 * 
	 * @param string|integer $filter Filter name or id (advanced filters)
	 * @param Subject $parent
	 * @return ArrayObject
	 * @throws InvalidFilterException 
	 */
	public function getAll($filter = null, Subject $parent = null) {
		return $this->connector->getAll(__NAMESPACE__, $filter, $parent);
	}	

	/**
	 * Get invoice by invoice number
	 * @param string $invoiceId
	 * @return Invoice
	 */
	public function getByInvoiceId($invoiceId) {
		return $this->connector->getByNamedId(__NAMESPACE__, 'invoice_id', $invoiceId);
	}
	
	/**
	 * Updates or inserts an invoice
	 * @param Invoice $invoice
	 * @return Invoice
	 */
	public function save(Invoice $invoice) {
		return $this->connector->save($invoice);
	}
	
	/**
	 * Deletes an invoice
	 * @param Invoice $invoice
	 * @return self
	 */
	public function delete(Invoice $invoice) {
		$this->connector->delete($invoice);
		return $this;
	}
	
	/**
	 * Send the invoice. Returns the updated invoice
	 * @param Invoice $invoice
	 * @param string $method Send method (email|hand|post); default: email
	 * @param type $email Address to send to; default: contact e-mail
	 * @param type $message
	 * @return Invoice 
	 */
	public function send(Invoice $invoice, $method = 'email', $email = null, $message = null) {
		return $this->connector->send($invoice, $this->buildEnvelope($method, $email, $message));
	}
	
	/**
	 * Send a reminder. Returns the updated invoice
	 * @param Invoice $invoice
	 * @param string $method Send method (email|hand|post); default: email
	 * @param type $email Address to send to; default: contact e-mail
	 * @param type $message
	 * @return Invoice 
	 */
	public function remind(Invoice $invoice, $method = 'email', $email = null, $message = null) {
		return $this->connector->remind($invoice, $this->buildEnvelope($method, $email, $message));
	}
	
	/**
	 * Build an envelope to send the invoice or reminder with
	 * @param string $method Send method (email|hand|post); default: email
	 * @param type $email Address to send to; default: contact e-mail
	 * @param type $message
	 * @return Envelope
	 * @access protected
	 */
	protected function buildEnvelope($method = 'email', $email = null, $message = null) {
		return new Envelope(
			array(
				'sendMethod' => $method,
				'email' => $email,
				'invoiceEmail' => $message,
			)
		);
	}
	
	/**
	 * Register a payment for the invoice
	 * @param Invoice $invoice
	 * @param Payment $payment
	 * @return Invoice
	 */
	public function registerPayment(Invoice &$invoice, Payment $payment) {
		return $this->connector->registerPayment($invoice, $payment);
	}
	
	/**
	 * Get the raw PDF content
	 * @param Invoice $invoice
	 * @return string
	 */
	public function getPdf(Invoice $invoice) {
		return $this->connector->getPdf($invoice);
	}
	
	/**
	 * Get the invoice of which a state change notification has been received
	 *
	 * When the invoice state changes in Moneybird, your application can be notified
	 * Use this method to validate the request and retreive the invoice
	 *
	 * @return Invoice
	 * @access public
	 * @throws InvalidRequestException
	 */
	public function getByPushMessage() {
		if (!isset($_POST['invoice_id'], $_POST['state'])) {
			throw new InvalidRequestException('Required fields not found');
		}
		return $this->getById($_POST['invoice_id']);
	}

    /**
	 * Inserts history note
     * @param History $history
	 * @param Invoice $invoice
	 * @return History
	 */
	public function saveHistory(History $history, Invoice $invoice) {
		return $this->connector->save($history, $invoice);
	}
}