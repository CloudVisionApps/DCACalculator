<?php
/**
 * @author Bozhidar Slaveykov
 * Class DCACalculator
 */

class DCACalculator
{
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
        $periodInvestments = [];

        $totalInvestmentAmount = 0.00;
        $totalAccumulatedAssetQuantity = 0.00;

        $periods = $this->getPeriods();

        $assetStartPeriodPrice = $periods[0]['assetPrice'];
        $assetLastPeriodPrice = end($periods)['assetPrice'];

        foreach ($periods as $currentPeriod) {

            $accumulatedAssetQuantity = ($this->investmentAmount / $currentPeriod['assetPrice']);
            $periodInvestments[] = [
                'date' => $currentPeriod['date'],
                'assetPrice' => $currentPeriod['assetPrice'],
                'accumulatedAssetQuantity' => $accumulatedAssetQuantity,
                'investmentAssetAmount' => $this->investmentAmount
           ];
            $totalAccumulatedAssetQuantity = $totalAccumulatedAssetQuantity + $accumulatedAssetQuantity;

            $totalInvestmentAmount = ($totalInvestmentAmount + $this->investmentAmount);
        }

        if ($assetLastPeriodPrice > $assetStartPeriodPrice) {
            $totalAssetIncreasePercent = abs(1 - ($assetLastPeriodPrice / $assetStartPeriodPrice)) * 100;
        } else {
            $totalAssetIncreasePercent = (($assetLastPeriodPrice / $assetStartPeriodPrice) - 1) * 100;
        }

        $totalProfitAmount = ($totalAccumulatedAssetQuantity * $assetLastPeriodPrice) - $totalInvestmentAmount;
        $totalAccumulatedAssetAmount = ($totalAccumulatedAssetQuantity * $assetLastPeriodPrice);

        if ($totalAccumulatedAssetAmount > $totalInvestmentAmount) {
            $totalProfitPercent = abs(1 - ($totalAccumulatedAssetAmount / $totalInvestmentAmount)) * 100;
        } else {
            $totalProfitPercent = (($totalAccumulatedAssetAmount / $totalInvestmentAmount) - 1) * 100;
        }

        $totalAssetIncreaseAmount = ($assetLastPeriodPrice - $assetStartPeriodPrice);

        return [
            'periodInvestments'=>$periodInvestments,
            'totalInvestmentAmount'=>$totalInvestmentAmount,
            'totalProfitPercent'=>$totalProfitPercent,
            'totalProfitAmount'=>$totalProfitAmount,
            'totalAccumulatedAssetAmount'=>$totalAccumulatedAssetAmount,
            'totalAccumulatedAssetQuantity'=>$totalAccumulatedAssetQuantity,
            'asset'=> [
                'assetStartPeriodPrice'=>$assetStartPeriodPrice,
                'assetLastPeriodPrice'=>$assetLastPeriodPrice,
                'totalAssetIncreasePercent'=>$totalAssetIncreasePercent,
                'totalAssetIncreaseAmount'=>$totalAssetIncreaseAmount
            ]
        ];
    }

    public function getPeriods()
    {
        $periods = [];
        if ($this->investmentCycle == 'daily') {
            foreach ($this->getDatesByMonthAndYear('10', 2021) as $date) {
                $periods[] = [
                    'date' => $date,
                    'assetPrice' => rand(20000,60000)
                ];
            }
        }
        return $periods;
    }

    public function getDatesByMonthAndYear($month, $year) {
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $datesMonth = array();
        for ($i = 1; $i <= $num; $i++) {
            $mktime = mktime(0, 0, 0, $month, $i, $year);
            $date = date("Y-m-d", $mktime);
            $datesMonth[$i] = $date;
        }
        return $datesMonth;
    }
}


$dca = new DCACalculator();
$dca->setInvestmentCycle('daily');
$dca->setInvestmentAmount('20');
$details = $dca->calculate();

print_r($details);
