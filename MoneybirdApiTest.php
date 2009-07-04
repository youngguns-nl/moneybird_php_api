<?php

require_once('PHPUnit/Framework.php');
include (dirname(__FILE__).'/../application/modules/invoices/models/MoneybirdApi.php');

class MoneybirdApiTest extends PHPUnit_Framework_Testcase
{
    /**
	 * Test create contact
	 *
	public function testCreateContact()
    {
		$mbapi = new MoneybirdApi;

        $contact = new MoneybirdContact;
        $contact->name = 'Testcontact';
        $contact->contact_name = 'Contactpersoon';
        $contact->address1 = 'Adresregel1';
        $contact->address2 = 'Adresregel2';
        $contact->zipcode = '1234AA';
        $contact->city = '\'s-Hertogenbosch';
        $contact->country = 'Nederland';
        $contact->email = 'moneybirdtest@desjors.nl';
        $contact->phone = '0612345678';
        
        $contact = $mbapi->saveContact($contact);

        $this->assertTrue($contact instanceof MoneybirdContact);
        $this->assertGreaterThan($contact->id, 0);
	}

    /**
	 * Test copy of contact
	 *
	public function testCopyContact()
    {
		$mbapi = new MoneybirdApi;

        $contact = $mbapi->getContact(4741);
        $id = $contact->id;
        $contact->id = null;
        $contact->name = $contact->name.'*';

        $contact = $mbapi->saveContact($contact);

        $this->assertTrue($contact instanceof MoneybirdContact);
        $this->assertNotEquals($contact->id, $id);
	}*/

    /**
	 * Test deletion of contact
	 *
	public function testDeleteContact()
    {
		$mbapi = new MoneybirdApi;

        $contact = $mbapi->getContact(4741);
        $mbapi->deleteContact($contact);
	}*/

    /**
	 * Test create invoice
	 *
	public function testCreateInvoice()
    {
		$mbapi = new MoneybirdApi;

        $contact = $mbapi->getContact(4741);

        $invoice = new MoneybirdInvoice;
        $invoice->setContact($contact);

        $lines = array();
        $invoiceLine = new MoneybirdInvoiceLine($invoice);
        $invoiceLine->description = 'Lidmaatschap 2009';
        $invoiceLine->amount = 2;
        $invoiceLine->price = 8.4;
        $invoiceLine->tax = 0.19;
        $lines[] = $invoiceLine;

        $invoiceLine = new MoneybirdInvoiceLine($invoice);
        $invoiceLine->description = 'Toegang PFZcongrez 2009';
        $invoiceLine->amount = 1;
        $invoiceLine->price = 12.14;
        $invoiceLine->tax = 0.19;
        $lines[] = $invoiceLine;

        $invoice->details = $lines;

        $mbapi->saveInvoice($invoice);
	}*/

    /**
	 * Test update invoice
	 *
	public function testUpdateInvoice()
    {
		$mbapi = new MoneybirdApi;

        $invoice = $mbapi->getInvoice(7577);

        $invoiceLines = array();
        foreach ($invoice->details as $lineNo => $invoiceLine)
        {
            $invoiceLines[$lineNo] = $invoiceLine;
            $invoiceLines[$lineNo]->amount = $invoiceLine->amount + 2;
        }
        $invoice->details = $invoiceLines;

        $mbapi->saveInvoice($invoice);
	}*/

    /**
	 * Test send invoice
	 *
	public function testSendInvoice()
    {
		$mbapi = new MoneybirdApi;

        $invoice = $mbapi->getInvoice(7577);
        $sendInfo = new MoneybirdInvoiceSendInformation(
            $invoice,
            'email',
            'sjors@desjors.nl',
            'Et voila, de factuur'
        );

        $mbapi->sendInvoice($sendInfo);
	}*/
    
    /**
	 * Register payment
	 *
	public function testPayment()
    {
		$mbapi = new MoneybirdApi;

        $invoice = $mbapi->getInvoice(7577);
        $payment = new MoneybirdInvoicePayment($invoice);
        $payment->price = 10.94;
        $payment->payment_date = new DateTime();

        $mbapi->registerInvoicePayment($payment);
	}*/

    /**
	 * Test get invoice with advanced filter
	 *
	public function testGetInvoiceAdvanced()
    {
		$mbapi = new MoneybirdApi;

        $filter = new MoneybirdAdvancedInvoiceFilter(
            new DateTime('2009-06-23'),
            new DateTime('2009-06-23'),
            'open'
        );

        $invoices = $mbapi->getInvoices($filter);
        foreach ($invoices as $invoice)
        {
            echo $invoice->invoice_id.PHP_EOL;
        }
	}*/
}