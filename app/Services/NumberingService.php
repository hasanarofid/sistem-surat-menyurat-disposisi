<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NumberingService
{
    /**
     * Generate the next letter number.
     * Type: 'masuk' or 'keluar'
     */
    public function generateNextNumber($type = 'keluar')
    {
        $prefixKey = "nomor_surat_{$type}_prefix";
        $counterKey = "nomor_surat_{$type}_counter";

        $prefix = DB::table('settings')->where('key', $prefixKey)->value('value') ?? ($type === 'keluar' ? 'SK-BLU' : 'SM-BLU');
        $counter = (int) DB::table('settings')->where('key', $counterKey)->value('value') ?? 0;
        
        $nextCounter = $counter + 1;
        $year = date('Y');
        $monthRoman = $this->getRomanMonth(date('n'));

        // Format: 001/SK-BLU/IV/2026
        $number = sprintf("%03d/%s/%s/%s", $nextCounter, $prefix, $monthRoman, $year);

        return [
            'number' => $number,
            'counter' => $nextCounter
        ];
    }

    /**
     * Increment the counter in settings.
     */
    public function incrementCounter($type = 'keluar')
    {
        $counterKey = "nomor_surat_{$type}_counter";
        
        DB::table('settings')
            ->where('key', $counterKey)
            ->increment('value');
    }

    private function getRomanMonth($month)
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$month] ?? 'I';
    }
}
