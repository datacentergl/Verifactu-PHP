<?php
namespace josemmo\Verifactu\Models\Responses;

/**
 * Rechazo previo por la AEAT
 */
enum PriorRejectionType: string {
    /** No ha habido rechazo previo por la AEAT. */
    case N = 'N';

    /** Ha habido rechazo previo por la AEAT. */
    case S = 'S';

    /** Independientemente de si ha habido o no algún rechazo previo por la AEAT,
     * el registro de facturación no existe en la AEAT  */
    case X = 'X';
}
