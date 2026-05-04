<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ParkingCalculator;

class ParkingCalculatorTest extends TestCase
{
    public function test_calculate_fee_within_first_hour_and_grace_period()
    {
        // duration = 70 mins, base = 5000, hour = 3000, grace = 15
        $fee = ParkingCalculator::calculateFromMinutes(70, 5000, 3000, 8, 10000, 15);
        
        // 70 <= (60 + 15), so it should only charge base price
        $this->assertEquals(5000, $fee);
    }

    public function test_calculate_fee_after_grace_period()
    {
        // duration = 76 mins, base = 5000, hour = 3000, grace = 15
        $fee = ParkingCalculator::calculateFromMinutes(76, 5000, 3000, 8, 10000, 15);
        
        // 76 > 75, billable = 61 mins. ceil(61/60) = 2 hours.
        // Cost: base (5000) + 1 extra hour (3000) = 8000
        $this->assertEquals(8000, $fee);
    }

    public function test_calculate_fee_with_penalty_hours()
    {
        // duration = 10 hours (600 mins), base = 5000, hour = 3000, maxHours = 8, penalty = 10000
        $fee = ParkingCalculator::calculateFromMinutes(600, 5000, 3000, 8, 10000, 15);
        
        // Normal 8 hours = 5000 + (7 * 3000) = 26000
        // Penalty 2 hours = 2 * 10000 = 20000
        // Total = 46000
        $this->assertEquals(46000, $fee);
    }
}
