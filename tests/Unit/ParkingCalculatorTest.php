<?php

namespace Tests\Unit;

use App\Services\ParkingCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ParkingCalculatorTest extends TestCase
{
    public function test_menghitung_tarif_lintas_hari_tanpa_denda(): void
    {
        $masuk = Carbon::parse('2026-04-29 22:30:00');
        $keluar = Carbon::parse('2026-04-30 02:10:00');

        $durasiMenit = $masuk->diffInMinutes($keluar); // 220 menit -> 4 jam (ceil)

        $total = ParkingCalculator::calculateFromMinutes(
            durationMinutes: $durasiMenit,
            basePrice: 2000,
            hourlyRate: 3000,
            maxHours: 8,
            penaltyRate: 5000
        );

        // 4 jam = 2000 + (3 x 3000) = 11000
        $this->assertSame(11000, $total);
    }

    public function test_menghitung_tarif_nginap_kena_denda_setelah_batas_jam(): void
    {
        $masuk = Carbon::parse('2026-04-29 20:15:00');
        $keluar = Carbon::parse('2026-04-30 07:05:00');

        $durasiMenit = $masuk->diffInMinutes($keluar); // 650 menit -> 11 jam (ceil)

        $total = ParkingCalculator::calculateFromMinutes(
            durationMinutes: $durasiMenit,
            basePrice: 2000,
            hourlyRate: 3000,
            maxHours: 8,
            penaltyRate: 5000
        );

        // Normal 8 jam: 2000 + (7 x 3000) = 23000
        // Penalti 3 jam: 3 x 5000 = 15000
        // Total: 38000
        $this->assertSame(38000, $total);
    }
}
