<?php

/*
 * Interface for objects that can be requested as PDF
 */
namespace Moneybird;

/**
 * PdfDocument
 */
interface PdfDocument
{

    /**
     * Get the raw PDF content
     * @param Service $service
     * @return string
     */
    public function getPdf(Service $service);
}
