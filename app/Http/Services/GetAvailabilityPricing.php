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

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function handle()
    {
        try {
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
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'code' => in_array($e->getCode(), [500, 422]) ? $e->getCode() : 400,
                'data' => [
                    'errors' => $e->getTrace()
                ]
            ];
        }
    }

    public function getDateRangePrice()
    {
        $period = new DatePeriod(
            new DateTime($this->start),
            new DateInterval('P1D'),
            (new DateTime($this->end))->modify('+1 day')
        );
        foreach ($period as $date) {
            $season = $this->getDateSeason($date);
            $seasonName = $season['season'];
            $weekEndOrWeekDay = in_array($season['day'], [0, 6]) ? 'weekend' : 'weekday';
            $price = config('app.prices')[$seasonName][$weekEndOrWeekDay];
            $this->pricingBreakdown[] = [
                'date' => $date,
                'season' => $season['season'],
                'price' => $price,
                'day_of_week' => $weekEndOrWeekDay
            ];
            $this->totalPrice = $this->totalPrice + $price;
        }
    }

    public function getDateSeason($date)
    {
        $carbonDate = Carbon::parse($date);
        $month = $carbonDate->month;
        $day = $carbonDate->day;
        $dayOfWeek = $carbonDate->dayOfWeek;

        switch (true) {
            case ($month == 3):
                $season = ($day >= 20) ? 'spring' : 'winter';
                break;
            case ($month > 3 && $month < 6):
                $season = 'spring';
                break;
            case ($month == 6):
                $season = ($day >= 21) ? 'summer' : 'spring';
                break;
            case ($month > 6 && $month < 9):
                $season = 'summer';
                break;
            case ($month == 9):
                $season = ($day >= 23) ? 'autumn' : 'summer';
                break;
            case ($month > 9 && $month < 12):
                $season = 'autumn';
                break;
            case ($month < 3):
                $season = 'winter';
                break;
            case ($month == 12):
                $season = ($day >= 22) ? 'winter' : 'autumn';
        }

        return [
            'season' => $season,
            'day' => $dayOfWeek
        ];
    }
}
