<?php

/*
 * Estimate sync array file
 */
namespace Moneybird\Estimate\Sync;

use Moneybird\ArrayObject as ParentArrayObject;
use Moneybird\Mapper\Mapable;
use Moneybird\SyncArray;

/**
 * Estimate sync array
 */
class ArrayObject extends ParentArrayObject implements Mapable, SyncArray
{

}