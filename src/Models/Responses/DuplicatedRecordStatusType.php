<?php
namespace josemmo\Verifactu\Models\Responses;

/**
 * Estado del registro duplicado
 */
enum DuplicatedRecordStatusType: string {
    /** Correcta */
    case Correct = 'Correcta';

    /** Aceptada con errores */
    case AcceptedWithErrors = 'AceptadaConErrores';

    /** Anulada */
    case Cancelled = 'Anulada';
}
