<?php

/*
 * Interface for objects that keep track of dirty attributes
 */
namespace Moneybird;

/**
 * DirtyAware
 */
interface DirtyAware
{

    /**
     * Returns true if the object contains any dirty attributes
     * @return bool
     * @access public
     */
    public function isDirty();

    /**
     * Returns an array representation of this object's dirty attributes
     * @return array
     * @access public
     */
    public function getDirtyAttributes();
}
