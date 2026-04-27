<?php

namespace App\Services;

/**
 * Utility class for parking fee calculation.
 *
 * Rules implemented:
 * - There is a base price for the first hour.
 * - There is an hourly rate for following hours.
 * - A maximum number of hours (maxHours) is defined for normal rates.
 * - Any hours beyond maxHours are charged using penaltyRate per hour.
 * - Duration is rounded up to full hours.
 *
 * Usage example:
 *   $total = ParkingCalculator::calculateFromMinutes(
 *       durationMinutes: 125,
 *       basePrice: 2000,
 *       hourlyRate: 3000,
 *       maxHours: 8,
 *       penaltyRate: 5000
 *   );
 */
class ParkingCalculator
{
    /**
     * Calculate total parking fee from duration in minutes.
     *
     * @param int $durationMinutes Duration in minutes (will be rounded up to hours)
     * @param int|float $basePrice Price for the first hour
     * @param int|float $hourlyRate Price per hour for subsequent hours (applies up to maxHours)
     * @param int $maxHours Maximum hours for normal hourly rate (includes the first hour)
     * @param int|float $penaltyRate Price per hour for hours beyond maxHours
     * @return int|float Total fee (same numeric type as inputs)
     */
    public static function calculateFromMinutes(int $durationMinutes, $basePrice, $hourlyRate, int $maxHours, $penaltyRate)
    {
        if ($durationMinutes <= 0) {
            return 0 + 0 * $basePrice; // preserve numeric type
        }

        // round up to whole hours
        $hours = (int) ceil($durationMinutes / 60);

        // if only within first hour
        if ($hours <= 1) {
            return $basePrice;
        }

        // normal hours charged under normal rates (including first hour)
        $normalHours = max(1, min($hours, max(1, $maxHours)));

        // additional normal hours beyond the first hour
        $normalExtraHours = max(0, $normalHours - 1);

        $normalCost = $basePrice + ($normalExtraHours * $hourlyRate);

        // penalty hours beyond maxHours
        $penaltyHours = $hours > $maxHours ? ($hours - $maxHours) : 0;
        $penaltyCost  = $penaltyHours * $penaltyRate;

        return $normalCost + $penaltyCost;
    }

    /**
     * Convenience wrapper when you already have duration in hours (can be fractional).
     * Will ceil the hours before calculation.
     *
     * @param float|int $durationHours
     */
    public static function calculateFromHours($durationHours, $basePrice, $hourlyRate, int $maxHours, $penaltyRate)
    {
        $minutes = (int) ceil($durationHours * 60);
        return self::calculateFromMinutes($minutes, $basePrice, $hourlyRate, $maxHours, $penaltyRate);
    }
}
