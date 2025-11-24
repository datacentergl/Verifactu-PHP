<?php
namespace josemmo\Verifactu\Models\Records;

/**
 * Emitida por tercero o destinatario
 */
enum IssuedByThirdPartyOrRecipient: string {
    /**
     * Destinatario
     */
    case Recipient = 'D';

    /**
     * Tercero
     */
    case ThirdParty = 'T';
}
