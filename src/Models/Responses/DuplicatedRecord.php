<?php
namespace josemmo\Verifactu\Models\Responses;

use josemmo\Verifactu\Models\Model;
use josemmo\Verifactu\Models\Responses\DuplicatedRecordStatusType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Identificador de registro duplicado
 */
class DuplicatedRecord extends Model {
    /**
     * Class constructor
     *
     * @param string                    $requestId     Duplicated record request id
     * @param DuplicatedRecordStatusType $status        Duplicated record status
     */
    public function __construct(
        string $requestId,
        DuplicatedRecordStatusType $status
    ) {
        $this->requestId = $requestId;
        $this->status = $status;
    }

    /**
     * IdPeticion asociado a la factura registrada previamente en el sistema
     *
     * @field RegistroDuplicado/IdPeticionRegistroDuplicado
     */
    #[Assert\NotBlank]
    public string $requestId;

    /**
     * Estado del registro duplicado almacenado en el sistema
     *
     * @field RegistroDuplicado/EstadoRegistroDuplicado
     */
    #[Assert\NotBlank]
    public DuplicatedRecordStatusType $status;

    /**
     * Código del error de registro duplicado almacenado en el sistema
     *
     * @field RegistroDuplicado/CodigoErrorRegistro
     */
    public ?string $errorCode = null;

    /**
     * Descripción detallada del error de registro duplicado almacenado en el sistema
     *
     * @field RegistroDuplicado/DescripcionErrorRegistro
     */
    public ?string $errorDescription = null;

    /**
     * Compare instance against another duplicated record instance
     *
     * @param DuplicatedRecord $other Other duplicated record instance
     *
     * @return boolean Whether instances are equal
     */
    public function equals(DuplicatedRecord $other): bool {
        return $this->requestId === $other->requestId
            && $this->status === $other->status
            && $this->errorCode === $other->errorCode
            && $this->errorDescription === $other->errorDescription;
    }
}
