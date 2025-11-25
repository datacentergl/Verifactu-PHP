<?php
namespace josemmo\Verifactu\Services;

use josemmo\Verifactu\Models\ComputerSystem;
use josemmo\Verifactu\Models\Records\CancellationRecord;
use josemmo\Verifactu\Models\Records\FiscalIdentifier;
use josemmo\Verifactu\Models\Records\RegistrationRecord;
use UXML\UXML;

/**
 * Class to communicate with the AEAT web service endpoint for VERI*FACTU
 */
class InvoiceSchemaVerifier {
    public const NS_SOAPENV = 'http://schemas.xmlsoap.org/soap/envelope/';
    public const NS_SUM = 'https://www2.agenciatributaria.gob.es/static_files/common/internet/dep/aplicaciones/es/aeat/tike/cont/ws/SuministroLR.xsd';
    public const NS_SUM1 = 'https://www2.agenciatributaria.gob.es/static_files/common/internet/dep/aplicaciones/es/aeat/tike/cont/ws/SuministroInformacion.xsd';

    private readonly ComputerSystem $system;
    private readonly FiscalIdentifier $taxpayer;
    private ?FiscalIdentifier $representative = null;
    private string $schemaSum = self::NS_SUM;

    /**
     * Class constructor
     *
     * @param ComputerSystem   $system     Computer system details
     * @param FiscalIdentifier $taxpayer   Taxpayer details (party that issues the invoices)
     */
    public function __construct(
        ComputerSystem $system,
        FiscalIdentifier $taxpayer
    ) {
        $this->system = $system;
        $this->taxpayer = $taxpayer;
    }

    /**
     * Set representative
     *
     * NOTE: Requires the represented fiscal entity to fill the "GENERALLEY58" form at AEAT.
     *
     * @param FiscalIdentifier|null $representative Representative details (party that sends the invoices)
     *
     * @return $this This instance
     */
    public function setRepresentative(?FiscalIdentifier $representative): static {
        $this->representative = $representative;
        return $this;
    }

    /**
     * Overwrites NS SUM to allow use of local files
     *
     * @param string $schemaSum XSD file path
     *
     * @return $this This instance
     */
    public function setSchemaSum(string $schemaSum): static {
        $this->schemaSum = $schemaSum;
        return $this;
    }

    /**
     * Verify invoicing records
     *
     * @param (RegistrationRecord|CancellationRecord)[] $records Invoicing records
     */
    public function verify(array $records): bool {
        // Build initial request
        $xml = UXML::newInstance('soapenv:Envelope', null, [
            'xmlns:soapenv' => self::NS_SOAPENV,
            'xmlns:sum' => self::NS_SUM,
            'xmlns:sum1' => self::NS_SUM1
        ]);
        $xml->add('soapenv:Header');
        $baseElement = $xml->add('soapenv:Body')->add('sum:RegFactuSistemaFacturacion');

        // Add header
        $cabeceraElement = $baseElement->add('sum:Cabecera');
        $obligadoEmisionElement = $cabeceraElement->add('sum1:ObligadoEmision');
        $obligadoEmisionElement->add('sum1:NombreRazon', $this->taxpayer->name);
        $obligadoEmisionElement->add('sum1:NIF', $this->taxpayer->nif);
        if ($this->representative !== null) {
            $representanteElement = $cabeceraElement->add('sum1:Representante');
            $representanteElement->add('sum1:NombreRazon', $this->representative->name);
            $representanteElement->add('sum1:NIF', $this->representative->nif);
        }

        // Add registration records
        foreach ($records as $record) {
            $record->export($baseElement->add('sum:RegistroFactura'), $this->system);
        }

        $xmlString = $xml->asXML();

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        if (!$dom->loadXML($xmlString)) {
            throw new \RuntimeException('Generated XML is not well-formed.');
        }

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('sum', self::NS_SUM);

        $node = $xpath->query('//sum:RegFactuSistemaFacturacion')->item(0);
        $payloadDoc = new \DOMDocument('1.0', 'UTF-8');
        $payloadDoc->appendChild($payloadDoc->importNode($node, true));

        libxml_use_internal_errors(true);
        $ok = $payloadDoc->schemaValidate($this->schemaSum);

        if (!$ok) {
            $errors = libxml_get_errors();
            libxml_clear_errors();

            $messages = [];
            foreach ($errors as $error) {
                $messages[] = sprintf(
                    '[Line %d, Col %d] %s',
                    $error->line,
                    $error->column,
                    trim($error->message)
                );
            }

            throw new \RuntimeException(
                "XML does not conform to schema:\n" . implode("\n", $messages)
            );
        }

        return true;
    }
}
