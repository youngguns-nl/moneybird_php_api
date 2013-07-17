<?php

/*
 * Detail class file
 */
namespace Moneybird\Invoice;

use Moneybird\Detail\AbstractDetail;

/**
 * Detail
 */
class Detail extends AbstractDetail
{

    protected $invoiceId;

    /**
     * Copy the invoice detail
     * @param array $filter
     * @return self
     */
    public function copy(array $filter = array())
    {
        return parent::copy(array(
                'invoiceId',
            ));
    }
}
