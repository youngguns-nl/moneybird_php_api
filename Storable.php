<?php

/*
 * Interface for Storable objects
 */
namespace Moneybird;

/**
 * Storable
 */
interface Storable
{

    /**
     * Deletes an object
     * @param Service $service
     */
    public function delete(Service $service);

    /**
     * Updates or inserts an object
     * @param Service $service
     * @return self
     * @throws NotValidException
     */
    public function save(Service $service);
}
