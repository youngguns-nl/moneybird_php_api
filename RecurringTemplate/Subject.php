<?php

/*
 * RecurringTemplate subject interface
 */
namespace Moneybird\RecurringTemplate;

use Moneybird\RecurringTemplate;

/**
 * RecurringTemplate subject is an object that has invoices (contact or estimate)
 */
interface Subject
{

    /**
     * Create a recurring template
     *
     * @return RecurringTemplate
     * @access public
     */
    public function createRecurringTemplate();

    /**
     * Get all recurring templates of subject
     *
     * @return ArrayObject
     * @param Service $service
     * @param string $filter
     * @access public
     */
    public function getRecurringTemplates(Service $service, $filter = null);
}

?>
