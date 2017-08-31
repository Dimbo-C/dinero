<?php

namespace App\Http\Controllers\Admins;

use App\Repositories\Admins\GrossIndicatorsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GrossIndicatorsController extends Controller
{
    /**
     * The performance indicators repository instance.
     *
     * @var GrossIndicatorsRepository
     */
    protected $indicators;

    /**
     * Create a new controller instance.
     *
     * @param  GrossIndicatorsRepository  $indicators
     * @return void
     */
    public function __construct(GrossIndicatorsRepository $indicators)
    {
        $this->indicators = $indicators;

        $this->middleware('auth');
//        $this->middleware('dev');
    }

    /**
     * Get the performance indicators for the application.
     *
     * @return Response
     */
    public function all()
    {
        return response()->json([
//            'indicators' => $this->indicators->all(60),
            'last_month' => $this->indicators->lastMonth(),
            'previous_month' => $this->indicators->prevMonth(),
        ]);
    }

    /**
     * Get the revenue amounts for the application.
     *
     * @return array
     */
    public function revenue()
    {
        return [
            'monthlyRecurringRevenue' => $this->indicators->monthlyRecurringRevenue(),
        ];
    }
}
