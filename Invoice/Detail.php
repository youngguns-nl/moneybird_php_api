<?php

/*
 * Detail class file
 */
namespace Moneybird\Invoice;

use Moneybird\Detail\AbstractDetail;
use Moneybird\Product;

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

    /**
     * Extract will take an array and try to automatically map the array values
     * to properties in this object
     *
     * @param Array $values
     * @param Array $filter
     * @param bool $isDirty new data is dirty, defaults to true
     * @access protected
     */
    protected function extract(Array $values, $filter = array(), $isDirty = true)
    {
        $product = &$values['product'];
        if (isset($product) && $product instanceof Product) {
            $filter[] = 'product';            
            $values = array_merge($values, array(
                'description' => $product->description,
                'ledgerAccountId' => $product->ledgerAccountId,
                'price' => $product->price,
                'taxRateId' => $product->taxRateId,
            ));
        }
        return parent::extract($values, $filter, $isDirty);
    }
}
