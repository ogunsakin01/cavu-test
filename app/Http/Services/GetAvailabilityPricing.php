<?php

namespace App\Http\Services;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Carbon;

class GetAvailabilityPricing
{
    public string $start;
    public string $end;
    public int $totalPrice = 0;
    public array $pricingBreakdown = [];

    public function __construct($start, $end){
        $this->start = $start;
        $this->end = $end;
    }

    public function handle(){
        try{
            $this->getDateRangePrice();
            return [
                'message' => 'Availability Pricing',
                'code' => 200,
                'data' => [
                    'start' => $this->start,
                    'end' => $this->end,
                    'total_price' => number_format($this->totalPrice, 2),
                    'pricing_breakdown' => $this->pricingBreakdown
                ]
            ];
        }catch(\Exception $e){
            return [
                'message' => $e->getMessage(),
                'code' => in_array($e->getCode(), [500, 422]) ? $e->getCode() : 400,
                'data' => [
                    'errors' => $e->getTrace()
                ]
            ];
        }
    }

    public function getDateSeason($date)
    {
        $carbonDate = Carbon::parse($date);
        $month = $carbonDate->month;
        $day = $carbonDate->day;

        switch (true) {
            case ($month == 3):
                return ($day >= 20) ? 'spring' : 'winter';
            case ($month > 3 && $month < 6):
                return 'spring';
            case ($month == 6):
                return ($day >= 21) ? 'summer' : 'spring';
            case ($month > 6 && $month < 9):
                return 'summer';
            case ($month == 9):
                return ($day >= 23) ? 'autumn' : 'summer';
            case ($month > 9 && $month < 12):
                return 'autumn';
            case ($month < 3):
                return 'winter';
            case ($month == 12):
                return ($day >= 22) ? 'winter' : 'autumn';
        }
    }

    public function getDateRangePrice(){
        if($this->start == $this->end){
            $period = [$this->start];
        }else{
            $period = new DatePeriod(
                new DateTime($this->start),
                new DateInterval('P1D'),
                new DateTime($this->end)
            );
        }
        foreach($period as $date){
            $season = $this->getDateSeason($date);
            $this->pricingBreakdown[] = [
                'date' => $date,
                'season' => $season,
                'price' => config('app.prices')[$season]
            ];
            $this->totalPrice = $this->totalPrice + config('app.prices')[$season];
        }
    }
}
