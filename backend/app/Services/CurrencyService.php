<?php

namespace App\Services;

class CurrencyService
{
    public function convert(float $amount, string $to, ?string $from = null): float
    {
        $rates = config('currency.rates');
        $from = $from ?? config('currency.default');
        if (!isset($rates[$from]) || !isset($rates[$to])) {
            throw new \InvalidArgumentException('Unsupported currency');
        }
        $base = $amount / $rates[$from];
        return $base * $rates[$to];
    }
}

