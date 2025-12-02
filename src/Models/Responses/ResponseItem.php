<?php
namespace josemmo\Verifactu\Models\Responses;

use josemmo\Verifactu\Models\Model;
use josemmo\Verifactu\Models\Records\InvoiceIdentifier;
use josemmo\Verifactu\Models\Responses\DuplicatedRecord;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Response for a record submission
 *
 * @field RespuestaLinea
 */
class ResponseItem extends Model {
    /**
     * Indicador de subsanación
     *
     * @field Subsanacion
     */
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    public bool $isCorrection = false;

    /**
     * ID de factura
     *
     * @field IDFactura
     */
    #[Assert\NotBlank]
    #[Assert\Valid]
    public InvoiceIdentifier $invoiceId;

    /**
     * Tipo de registro de operación
     *
     * @field TipoOperacion
     */
    #[Assert\NotBlank]
    public RecordType $recordType;

    /**
     * Rechazo previo por la AEAT.
     *
     * @field RechazoPrevio
     */
    public ?PriorRejectionType $priorRejection;

    /**
     * Sin registro previo en la AEAT
     *
     * @field SinRegistroPrevio
     */
    public bool $withoutPreviousRecord;

    /**
     * Dato adicional de contenido libre para facilitar la identificación de la factura
     *
     * @field RefExterna
     */
    #[Assert\Length(max: 60)]
    public ?string $externalRef;

    /**
     * Estado del envío del registro
     *
     * @field EstadoRegistro
     */
    #[Assert\NotBlank]
    public ItemStatus $status;

    /**
     * Código de error
     *
     * @field CodigoErrorRegistro
     */
    public ?string $errorCode = null;

    /**
     * Descripción del error
     *
     * @field DescripcionErrorRegistro
     */
    public ?string $errorDescription = null;

    /**
     * Información sobre el registro duplicado
     *
     * @field RegistroDuplicado
     */
    public ?DuplicatedRecord $duplicatedRecord = null;
}
