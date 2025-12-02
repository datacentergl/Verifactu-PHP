<?php
namespace josemmo\Verifactu\Tests\Models\Responses;

use josemmo\Verifactu\Models\Responses\DuplicatedRecord;
use josemmo\Verifactu\Models\Responses\DuplicatedRecordStatusType;
use PHPUnit\Framework\TestCase;

final class DuplicatedRecordTest extends TestCase {
    public function testComparesInstances(): void {
        // Same instance
        $a = new DuplicatedRecord('DuplicatedRequestID', DuplicatedRecordStatusType::AcceptedWithErrors);
        $this->assertTrue($a->equals($a));

        // Same exact values
        $a = new DuplicatedRecord('DuplicatedRequestID', DuplicatedRecordStatusType::Correct);
        $a->errorCode = '2001';
        $a->errorDescription = 'El NIF del bloque Destinatarios no está identificado en el censo de la AEAT.';
        $b = new DuplicatedRecord('DuplicatedRequestID', DuplicatedRecordStatusType::Correct);
        $b->errorCode = '2001';
        $b->errorDescription = 'El NIF del bloque Destinatarios no está identificado en el censo de la AEAT.';
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
    }
}
