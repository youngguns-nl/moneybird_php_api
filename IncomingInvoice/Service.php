<?php

/*
 * IncomingInvoice service class
 */

namespace Moneybird;

/**
 * IncomingInvoice service
 */
class IncomingInvoice_Service implements Service {
	
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
		return $this->connector->getSyncList('IncomingInvoice');
	}
	
	/**
	 * Get invoice by id
	 * @param int $id
	 * @return IncomingInvoice
	 */
	public function getById($id) {
		return $this->connector->getById('IncomingInvoice', $id);
	}
	
	/**
	 * Get invoices by id (max 100)
	 * @param Array $ids
	 * @return IncomingInvoice_Array
	 */
	public function getByIds(Array $ids) {
		return $this->connector->getByIds('IncomingInvoice', $ids);
	}
	
	/**
	 * Get all invoices
	 * 
	 * @param string|integer $filter Filter name or id (advanced filters)
	 * @param IncomingInvoice_Subject $parent
	 * @return IncomingInvoice_Array
	 * @throws InvalidFilterException 
	 */
	public function getAll($filter = null, IncomingInvoice_Subject $parent = null) {
		return $this->connector->getAll('IncomingInvoice', $filter, $parent);
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
	 * @param IncomingInvoice_Payment $payment
	 * @return IncomingInvoice
	 */
	public function registerPayment(IncomingInvoice &$invoice, IncomingInvoice_Payment $payment) {
		return $this->connector->registerPayment($invoice, $payment);
	}
}