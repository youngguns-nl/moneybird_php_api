<?php

/*
 * History class file
 */

namespace Moneybird\Invoice;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;
use Moneybird\Storable;
use Moneybird\Service as ServiceInterface;
use Moneybird\Invoice;
use Moneybird\Exception;
use Moneybird\NotValidException;

/**
 * History
 */
class History extends AbstractModel implements Mapable, Storable {
	
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
	 * @param ServiceInterface $service
     * @param Invoice $invoice
	 * @return self
	 * @throws NotValidException
     * @todo throw more specific exception on invalid parent
	 */
	public function save(ServiceInterface $service, Invoice $invoice = null) {
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

    public function delete(ServiceInterface $service)
    {
        throw new Exception('Not implemented');
    }
}
