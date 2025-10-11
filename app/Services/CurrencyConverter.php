<?php

namespace App\Services;

class CurrencyConverter
{
    protected $rates = [
        'TND' => 1.0,
        'USD' => 0.32,
        'SAR' => 1.2,
    ];

    public function convert($amount, $from, $to)
    {
        if (!isset($this->rates[$from]) || !isset($this->rates[$to])) {
            throw new \Exception('Unsupported currency');
        }
        $base = $amount / $this->rates[$from];
        return round($base * $this->rates[$to], 2);
    }
}
