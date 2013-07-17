<?php

/*
 * Invoice subject interface
 */
namespace Moneybird\Invoice;

use Moneybird\Invoice;

/**
 * Invoice subject is an object that has invoices (contact or estimate)
 */
interface Subject
{

    /**
     * Create an invoice
     *
     * @return Invoice
     * @access public
     */
    public function createInvoice();

    /**
     * Get all invoices of subject
     *
     * @return ArrayObject
     * @param Service $service
     * @param string $filter
     * @access public
     */
    public function getInvoices(Service $service, $filter = null);
}

?>
