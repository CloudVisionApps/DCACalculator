<?php


class DCACalculator
{
    public $investments = [];
    public $investmentCycle = 'daily';
    public $investmentAmount = 20;


    public function setInvestmentCycle(string $cycle)
    {
        $this->investmentCycle = $cycle;
    }

    public function setInvestmentAmount(float $amount)
    {
        $this->investmentAmount = $amount;
    }

    public function calculate()
    {
        $totalInvestmentAmount = 0.00;

        $totalProfitPercent = 0;
        $totalProfitAmount = 0.00;

        $totalAssetIncreasePercent = 0;
        $totalAssetIncreaseAmount = 0;

        $periods = $this->getPeriods();

        $assetStartPeriodPrice = $periods[0]['assetPrice'];
        $assetLastPeriodPrice = end($periods)['assetPrice'];

        foreach ($periods as $i=>$currentPeriod) {

            $previousPeriod = false;
            if (isset($periods[$i-1])) {
                $previousPeriod = $periods[$i - 1];
            }

            $periodAssetIncreasePercent = 0;
            $periodAssetIncreaseAmount = 0;

            if ($previousPeriod) {
                $periodAssetIncreaseAmount = ($currentPeriod['assetPrice'] - $previousPeriod['assetPrice']);
                $periodAssetIncreasePercent = ((1 - ($previousPeriod['assetPrice'] / $currentPeriod['assetPrice'])) * 100);
            }

            // Add period asset amount
            if ($periodAssetIncreaseAmount >= 0) {
                $totalAssetIncreaseAmount = ($totalAssetIncreaseAmount + $periodAssetIncreaseAmount);
            } else {
                $totalAssetIncreaseAmount = ($totalAssetIncreaseAmount - abs($periodAssetIncreaseAmount));
            }

            $this->makeInvestment($currentPeriod, $this->investmentAmount);
        }

        foreach ($this->investments as $investment) {
            $totalInvestmentAmount = ($totalInvestmentAmount + $investment['investmentAmount']);
        }


        if ($assetLastPeriodPrice > $assetStartPeriodPrice) {
            $totalAssetIncreasePercent = abs(1 - ($assetLastPeriodPrice / $assetStartPeriodPrice)) * 100;
        } else {
            $totalAssetIncreasePercent = (($assetLastPeriodPrice / $assetStartPeriodPrice) - 1) * 100;
        }

        return [
            'totalInvestmentAmount'=>$totalInvestmentAmount . '$',
            'totalProfitPercent'=>$totalProfitPercent . '%',
            'totalProfitAmount'=>$totalProfitAmount . '$',
            'asset'=> [
                'assetStartPeriodPrice'=>$assetStartPeriodPrice . '$',
                'assetLastPeriodPrice'=>$assetLastPeriodPrice . '$',
                'totalAssetIncreasePercent'=>$totalAssetIncreasePercent . '%',
                'totalAssetIncreaseAmount'=>$totalAssetIncreaseAmount . '$'
            ]
        ];
    }

    public function makeInvestment($period, $amount)
    {
        $period['investmentAmount'] = $amount;

        $this->investments[] = $period;
    }

    public function getPeriods()
    {
        $periods = [];
        if ($this->investmentCycle == 'daily') {
            $periods[] = [
                'date'=>1,
                'assetPrice'=> 100
            ];
            $periods[] = [
                'date'=>2,
                'assetPrice'=> 300
            ];
            $periods[] = [
                'date'=>3,
                'assetPrice'=> 500
            ];
            $periods[] = [
                'date'=>4,
                'assetPrice'=> 200
            ];
        }
        return $periods;
    }
}


$dca = new DCACalculator();
$dca->setInvestmentCycle('daily');
$dca->setInvestmentAmount('20');
$details = $dca->calculate();

print_r($details);
