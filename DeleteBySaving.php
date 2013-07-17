<?php

/*
 * Interface for objects that are deleted by saving them
 */
namespace Moneybird;

/**
 * DeleteBySaving
 */
interface DeleteBySaving
{

    /**
     * Mark deleted
     */
    public function setDeleted();

    /**
     * Get delete status
     * @return bool
     */
    public function isDeleted();
}
