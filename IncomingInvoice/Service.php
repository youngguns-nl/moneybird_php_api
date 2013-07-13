<?php

/*
 * IncomingInvoice service class
 */

namespace Moneybird\IncomingInvoice;

use Moneybird\Service as ServiceInterface;
use Moneybird\ApiConnector;
use Moneybird\InvalidFilterException;
use Moneybird\IncomingInvoice;

/**
 * IncomingInvoice service
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
	 * @return IncomingInvoice_Array
	 */
	public function getSyncList() {
		return $this->connector->getSyncList(__NAMESPACE__);
	}
	
	/**
	 * Get invoice by id
	 * @param int $id
	 * @return IncomingInvoice
	 */
	public function getById($id) {
		return $this->connector->getById(__NAMESPACE__, $id);
	}
	
	/**
	 * Get invoices by id (max 100)
	 * @param Array $ids
	 * @return ArrayObject
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
	 * Updates or inserts an invoice
	 * @param IncomingInvoice $invoice
	 * @return IncomingInvoice
	 */
	public function save(IncomingInvoice $invoice) {
		return $this->connector->save($invoice);
	}
	
	/**
	 * Deletes an invoice
	 * @param IncomingInvoice $invoice
	 * @return self
	 */
	public function delete(IncomingInvoice $invoice) {
		$this->connector->delete($invoice);
		return $this;
	}
	
	/**
	 * Register a payment for the invoice
	 * @param IncomingInvoice $invoice
	 * @param Payment $payment
	 * @return IncomingInvoice
	 */
	public function registerPayment(IncomingInvoice &$invoice, Payment $payment) {
		return $this->connector->registerPayment($invoice, $payment);
	}
}