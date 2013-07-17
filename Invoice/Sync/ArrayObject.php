<?php

/*
 * Invoice sync array file
 */
namespace Moneybird\Invoice\Sync;

use Moneybird\ArrayObject as ParentArrayObject;
use Moneybird\Mapper\Mapable;
use Moneybird\SyncArray;

/**
 * Invoice sync array
 */
class ArrayObject extends ParentArrayObject implements Mapable, SyncArray
{

}