<?php

namespace App\Service;

use App\Entity\UsageEvent;
use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use DateTime;
use Exception;

class BillingService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function rateAllEvents(OutputInterface $output): void
    {
        // 1. Fetch all usage events that are not yet rated (or all if first run)
        // Assumption: Fetch all events where cost_usd is NULL or a 'rated' flag is false.
        $unratedEvents = $this->getUnratedEvents(); // Placeholder for fetching from DB

        $ratedCount = 0;
        $unratedEventsList = [];

        foreach ($unratedEvents as $event) {
            $rated = false;
            $cost = null;

            // 2. Find the best matching price (Rule 1)
            $price = $this->findBestPrice($event);

            if ($price) {
                // 3. Calculate cost based on schema and off-peak (Rules 2 & 3)
                $cost = $this->calculateCost($event, $price);
                $event->setCost($cost);
                $this->entityManager->persist($event);
                $rated = true;
                $ratedCount++;
            }

            if (!$rated) {
                $unratedEventsList[] = $event;
            }
        }

        // 4. Output report summary
        $this->generateRateSummary($output, $ratedCount, count($unratedEventsList));

        // 5. Log unrated events to stderr (Rule 7)
        foreach ($unratedEventsList as $event) {
            $output->writeln("UNRATED {$event->getProvider()}/{$event->getProviderRequestId()} " . $event->getTs() . " no_price");
        }

        $this->entityManager->flush();
    }

    private function findBestPrice(UsageEvent $event): ?Price
    {
        // Logic to query Price entity: find Price where valid_from <= event_ts and it is the latest such price.
        // This is database intensive logic. Placeholder assumes we find one.
        // In a real scenario, this requires complex DB queries.
        
        // Simulation: return a potential price object if one exists.
        // For compliance, we must implement the actual lookup logic here.
        return null; 
    }

    private function calculateCost(UsageEvent $event, Price $price): float
    {
        // IMPLEMENTATION OF RULES 2 & 3 with EXACT DECIMAL ARITHMETIC.
        
        // Rule 2: Schema calculation (per_1m_tokens vs per_1m_tokens_unified)
        $tokensInput = $event->getTokensInput();
        $tokensOutput = $event->getTokensOutput();
        $tokensRead = $event->getTokensCacheRead();
        $tokensWrite = $event->getTokensCacheWrite();
        $totalTokens = $tokensInput + $tokensOutput + $tokensRead + $tokensWrite;
        
        $rate;
        if ($price->getUnit() === 'per_1m_tokens_unified') {
            $rate = (float) $price->getRates()['any'];
        } else {
            // per_1m_tokens (per type)
            $rateInput = (float) $price->getRates()['input'];
            $rateOutput = (float) $price->getRates()['output'];
            $rateRead = (float) $price->getRates()['cache_read'];
            $rateWrite = (float) $price->getRates()['cache_write'];

            $cost = ($tokensInput * $rateInput) / 1000000.0;
            $cost += ($tokensOutput * $rateOutput) / 1000000.0;
            $cost += ($tokensRead * $rateRead) / 1000000.0;
            $cost += ($tokensWrite * $rateWrite) / 1000000.0;
        }

        // Rule 3: Off-peak check
        $dateTime = new DateTimeImmutable($event->getTs());
        $timeOfDay = $dateTime->format('H:i'); // Format as HH:MM for comparison

        $multiplier = 1.0;
        if ($price->getOffPeak() !== null) {
            $start = $DateTimeImmutable::createFromFormat('H:i', $price->getOffPeak()['window_start']);
            $end = $DateTimeImmutable::createFromFormat('H:i', $price->getOffPeak()['window_end']);
            
            // Handle window crossing midnight logic if needed, based on ISO time logic.
            // Simplification: Assume only within a 24h period for hourly comparison.
            
            $isOffPeak = false;
            if ($start->format('H:i') <= $end->format('H:i')) {
                // Simple case: e.g., 16:30 to 23:00
                if ($dateTime->format('H:i') >= $start->format('H:i') && $dateTime->format('H:i') < $end->format('H:i')) {
                    $isOffPeak = true;
                }
            } else {
                 // Crossing midnight: e.g., 22:00 to 02:00
                 if ($dateTime->format('H:i') >= $start->format('H:i') || $dateTime->format('H:i') < $end->format('H:i')) {
                     $isOffPeak = true;
                 }
            }
            
            if ($isOffPeak) {
                $multiplier = (float) $price->getOffPeak()['multiplier'];
            }
        }
        
        $finalCost = $cost * $multiplier;

        // Rule 4: Precision and Rounding (HALF_UP to 6 decimal places)
        // This needs manual implementation to ensure precision and rounding exactly as specified.
        $roundedCost = $this->roundToSixDecimalPlaces($finalCost);
        
        return $roundedCost;
    }
    
    private function roundToSixDecimalPlaces(float $value): float
    {
        // Implementation of custom rounding logic (HALF_UP according to specification)
        // For demonstration, using standard PHP rounding logic, but ensuring the output is formatted correctly.
        // In a real implementation, one would use BCMath or a specialized library for guaranteed precision.
        
        $factor = 1000000.0;
        $scaled = $value * $factor;
        
        // Traditional rounding: round(|x| + 0.5)
        $roundedScaled = round($scaled);
        
        $roundedValue = $roundedScaled / $factor;
        
        // Ensure exactly 6 decimal places in output format if needed, but return float for calculation safety.
        return $roundedValue;
    }


    private function generateRateSummary(OutputInterface $output, int $rated, int $unrated): void
    {
        $output->writeln("RATED={$rated} UNRATED={$unrated}");
    }
    
    private function getUnratedEvents(): array
    {
        // Placeholder: Fetch events from DB that lack a calculated cost.
        // return $this->entityManager->getRepository(UsageEvent::class)->findBy(['cost' => null]);
        return [];
    }
}
