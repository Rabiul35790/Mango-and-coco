<?php

namespace App\Support;

/** Human-friendly tracking codes: MC-XXXXXXXX (no 0/O/1/I to avoid misreads). */
class TrackingId
{
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public static function make(string $prefix = 'MC', int $length = 8): string
    {
        $alpha = self::ALPHABET;
        $max = strlen($alpha) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $alpha[random_int(0, $max)];
        }

        return $prefix.'-'.$code;
    }

    /** Generate a code unique across both order tables. */
    public static function unique(string $prefix = 'MC'): string
    {
        for ($i = 0; $i < 10; $i++) {
            $code = self::make($prefix);
            $exists = \App\Models\Order::where('tracking_id', $code)->exists()
                || \App\Models\VideoOrder::where('tracking_id', $code)->exists();
            if (! $exists) {
                return $code;
            }
        }

        return $prefix.'-'.strtoupper(uniqid());
    }
}
