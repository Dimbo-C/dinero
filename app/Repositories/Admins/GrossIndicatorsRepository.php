<?php

namespace App\Repositories\Admins;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GrossIndicatorsRepository
{
    /**
     * {@inheritdoc}
     */
    public function lastMonth()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::today();

        return array_reverse(
            DB::table('gross_indicators')->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get()->all()
        );
    }

    public function prevMonth()
    {
        $start = Carbon::now()->startOfMonth()->subMonth();
        $end = Carbon::now()->subMonth()->endOfMonth();

        return array_reverse(
            DB::table('gross_indicators')->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get()->all()
        );
    }
}