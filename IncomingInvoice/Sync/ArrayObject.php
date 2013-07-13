<?php

/*
 * IncomingInvoice sync array file
 */

namespace Moneybird\IncomingInvoice\Sync;

use Moneybird\ArrayObject as ParentArrayObject;
use Moneybird\Mapper\Mapable;
use Moneybird\SyncArray;

/**
 * IncomingInvoice sync array
 */
class ArrayObject extends ParentArrayObject implements Mapable, SyncArray {
}