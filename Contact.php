<?php

/*
 * Contact class
 */
namespace Moneybird;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;
use Moneybird\Contact\Note\ArrayObject as NoteArray;
use Moneybird\Invoice\Subject as InvoiceSubject;
use Moneybird\Invoice\Service as InvoiceService;
use Moneybird\Invoice\ArrayObject as InvoiceArray;
use Moneybird\Estimate\Subject as EstimateSubject;
use Moneybird\Estimate\Service as EstimateService;
use Moneybird\Estimate\ArrayObject as EstimateArray;
use Moneybird\IncomingInvoice\Subject as IncomingInvoiceSubject;
use Moneybird\IncomingInvoice\Service as IncomingInvoiceService;
use Moneybird\IncomingInvoice\ArrayObject as IncomingInvoiceArray;
use Moneybird\RecurringTemplate\Subject as RecurringTemplateSubject;
use Moneybird\RecurringTemplate\Service as RecurringTemplateService;
use Moneybird\RecurringTemplate\ArrayObject as RecurringTemplateArray;

/**
 * Contact
 */
class Contact extends
AbstractModel implements
Mapable, Storable, InvoiceSubject, RecurringTemplateSubject, EstimateSubject, IncomingInvoiceSubject
{

    protected $address1;
    protected $address2;
    protected $attention;
    protected $bankAccount;
    protected $chamberOfCommerce;
    protected $city;
    protected $companyName;
    protected $contactHash;
    protected $contactName;
    protected $country;
    protected $createdAt;
    protected $customerId;
    protected $email;
    protected $firstname;
    protected $id;
    protected $lastname;
    protected $name;
    protected $phone;
    protected $revision;
    protected $sendMethod;
    protected $taxNumber;
    protected $updatedAt;
    protected $zipcode;
    protected $notes;
    protected $_readonlyAttr = array(
        'contactHash',
        'contactName',
        'createdAt',
        'id',
        'name',
        'revision',
        'updatedAt'
    );
    protected $_requiredAttr = array(
        //'customerId',
        array('companyName', 'firstname', 'lastname',),
    );

    /**
     * Set Id
     * @param int $value
     * @throws InvalidIdException
     */
    protected function setIdAttr($value)
    {
        if (!is_null($value) && !preg_match('/^[0-9]+$/D', $value)) {
            throw new InvalidIdException('Invalid id: ' . $value);
        }

        $this->id = $value;
    }

    /**
     * Set notes
     * @param NoteArray $value
     * @param bool $isDirty new value is dirty, defaults to true
     */
    protected function setNotesAttr(NoteArray $value = null, $isDirty = true)
    {
        if (!is_null($value)) {
            $this->notes = $value;
            $this->setDirtyState($isDirty, 'notes');
        }
    }

    /**
     * Initialize vars
     */
    protected function _initVars()
    {
        $this->notes = new NoteArray();
        return parent::_initVars();
    }

    /**
     * Deletes a contact
     * @param Service $service
     */
    public function delete(Service $service)
    {
        $service->delete($this);
    }

    /**
     * Updates or inserts a contact
     * @param Service $service
     * @return self
     * @throws NotValidException
     */
    public function save(Service $service)
    {
        if (!$this->validate()) {
            throw new NotValidException('Unable to validate contact');
        }

        return $this->reload(
                $service->save($this)
        );
    }

    /**
     * Copy the contact
     * @param array $filter
     * @return self
     */
    public function copy(array $filter = array())
    {
        return parent::copy(array(
                'customerId',
            ));
    }

    /**
     * Create an invoice for this contact
     * @param array $data
     * @return Invoice
     */
    public function createInvoice(array $data = array())
    {
        return new Invoice($data, $this, true);
    }

    /**
     * Get all invoices of this contact
     *
     * @return InvoiceArray
     * @param InvoiceService $service
     * @param string $filter
     * @access public
     */
    public function getInvoices(InvoiceService $service, $filter = null)
    {
        return $service->getAll($filter, $this);
    }

    /**
     * Create a recurring template for this contact
     * @param array $data
     * @return RecurringTemplate
     */
    public function createRecurringTemplate(array $data = array())
    {
        return new RecurringTemplate($data, $this, true);
    }

    /**
     * Get all recurring templates of this contact
     *
     * @return RecurringTemplateArray
     * @param RecurringTemplateService $service
     * @param string $filter
     * @access public
     */
    public function getRecurringTemplates(RecurringTemplateService $service, $filter = null)
    {
        return $service->getAll($filter, $this);
    }

    /**
     * Create an estimate for this contact
     * @param array $data
     * @return Estimate
     */
    public function createEstimate(array $data = array())
    {
        return new Estimate($data, $this, true);
    }

    /**
     * Get all estimates of this contact
     *
     * @return EstimateArray
     * @param EstimateService $service
     * @param string $filter
     * @access public
     */
    public function getEstimates(EstimateService $service, $filter = null)
    {
        return $service->getAll($filter, $this);
    }

    /**
     * Create an invoice for this contact
     * @param array $data
     * @return IncomingInvoice
     */
    public function createIncomingInvoice(array $data = array())
    {
        return new IncomingInvoice($data, $this, true);
    }

    /**
     * Get all invoices of this contact
     *
     * @return IncomingInvoiceArray
     * @param IncomingInvoiceService $service
     * @param string $filter
     * @access public
     */
    public function getIncomingInvoices(IncomingInvoiceService $service, $filter = null)
    {
        return $service->getAll($filter, $this);
    }
}
