<?php

namespace App\Services;

class ParkingCalculator
{
    /**
     * Calculate total parking fee from duration in raw minutes.
     * Ceiling is applied internally here after considering grace period.
     *
     * @param int $durationMinutes Duration in raw minutes
     * @param int|float $basePrice Price for the first hour
     * @param int|float $hourlyRate Price per hour for subsequent hours
     * @param int $maxHours Maximum hours for normal hourly rate
     * @param int|float $penaltyRate Price per hour for hours beyond maxHours
     * @param int $gracePeriod Minutes allowed before charging next hour
     * @return int|float Total fee
     */
    public static function calculateFromMinutes(
        int $durationMinutes, 
        $basePrice, 
        $hourlyRate, 
        int $maxHours, 
        $penaltyRate,
        int $gracePeriod = 15 // Default 15 mins grace period
    ) {
        if ($durationMinutes <= 0) {
            return 0 + 0 * $basePrice; 
        }

        // If parking duration is under 1 hour + grace period, charge only base price
        if ($durationMinutes <= (60 + $gracePeriod)) {
            return $basePrice;
        }

        // Deduct grace period for next hour calculation
        $billableMinutes = $durationMinutes - $gracePeriod;

        // Apply ceiling ONLY here internally
        $hours = (int) ceil($billableMinutes / 60);

        // Calculate normal cost
        $normalHours = max(1, min($hours, max(1, $maxHours)));
        $normalExtraHours = max(0, $normalHours - 1);
        $normalCost = $basePrice + ($normalExtraHours * $hourlyRate);

        // Calculate penalty cost
        $penaltyHours = $hours > $maxHours ? ($hours - $maxHours) : 0;
        $penaltyCost  = $penaltyHours * $penaltyRate;

        return $normalCost + $penaltyCost;
    }

    /**
     * Deprecated: Should use raw minutes from timestamps.
     */
    public static function calculateFromHours($durationHours, $basePrice, $hourlyRate, int $maxHours, $penaltyRate)
    {
        $minutes = (int) ceil($durationHours * 60);
        return self::calculateFromMinutes($minutes, $basePrice, $hourlyRate, $maxHours, $penaltyRate);
    }
}
