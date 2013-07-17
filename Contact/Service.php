<?php

/*
 * Contact service class
 */
namespace Moneybird\Contact;

use Moneybird\Service as ServiceInterface;
use Moneybird\ApiConnector;
use Moneybird\Contact\ArrayObject as ContactArray;
use Moneybird\Contact;

/**
 * Contact service
 */
class Service implements ServiceInterface
{

    /**
     * ApiConnector object
     * @var ApiConnector
     */
    protected $connector;

    /**
     * Constructor
     * @param ApiConnector $connector 
     */
    public function __construct(ApiConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * Get contacts sync status
     * @return ContactArray
     */
    public function getSyncList()
    {
        return $this->connector->getSyncList(__NAMESPACE__);
    }

    /**
     * Get contact by id
     * @param int $id
     * @return Contact
     */
    public function getById($id)
    {
        return $this->connector->getById(__NAMESPACE__, $id);
    }

    /**
     * Get contacts by id (max 100)
     * @param Array $ids
     * @return ContactArray
     */
    public function getByIds($ids)
    {
        return $this->connector->getByIds(__NAMESPACE__, $ids);
    }

    /**
     * Get all contacts
     * 
     * @return ContactArray
     */
    public function getAll()
    {
        return $this->connector->getAll(__NAMESPACE__);
    }

    /**
     * Get contact by customer id
     * @param string $customerId
     * @return Contact
     */
    public function getByCustomerId($customerId)
    {
        return $this->connector->getByNamedId(__NAMESPACE__, 'customer_id', $customerId);
    }

    /**
     * Updates or inserts a contact
     * @param Contact $contact
     * @return Contact
     */
    public function save(Contact $contact)
    {
        return $this->connector->save($contact);
    }

    /**
     * Deletes a contact
     * @param Contact $contact
     * @return self
     */
    public function delete(Contact $contact)
    {
        $this->connector->delete($contact);
        return $this;
    }
}