<?php

/*
 * Invoice_History class file
 */

namespace Moneybird;

/**
 * Invoice_History
 */
class Invoice_History extends Domainmodel_Abstract implements Mapper_Mapable, Storable {
	
	protected $action; 
	protected $createdAt;
	protected $description;
	protected $id; 
	protected $invoiceId;
	protected $updatedAt; 
	protected $userId;
	
	protected $_readonlyAttr = array(
		'createdAt',
		'id', 
		'invoiceId',
		'updatedAt', 
		'userId',
	);

    /**
	 * Inserts history note
	 * @param Service $service
     * @param Invoice $invoice
	 * @return self
	 * @throws NotValidException
     * @todo throw more specific exception on invalid parent
	 */
	public function save(Service $service, Invoice $invoice = null) {
		if (!$this->validate()){
			throw new NotValidException('Unable to validate invoice history');
		}

        if ($invoice === null) {
            throw new Exception('$invoice must be instance of Invoice');
        }

		return $this->reload(
			$service->saveHistory($this, $invoice)
		);
	}

    public function delete(Service $service)
    {
        throw new Exception('Not implemented');
    }
}
