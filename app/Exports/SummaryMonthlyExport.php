<?php

namespace App\Exports;

use App\Exports\DailyActivityExport;
use App\Exports\HistoryRepairsExport;
use App\Exports\SummaryRepairTowerExport;
use App\Exports\SummaryRepairUnitExport;
use App\Exports\SummaryMaintenanceTowerExport;
use App\Exports\SummaryMaintenanceUnitExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SummaryMonthlyExport implements WithMultipleSheets
{
    protected $towerMonthlyActivity;
    protected $unitMonthlyActivity;
    protected $nonMtStart;
    protected $nonMtEnd;

    public function __construct($towerMonthlyActivity, $unitMonthlyActivity, $nonMtStart, $nonMtEnd)
    {

        $this->towerMonthlyActivity = $towerMonthlyActivity;
        $this->unitMonthlyActivity = $unitMonthlyActivity;
        $this->nonMtStart = $nonMtStart;
        $this->nonMtEnd = $nonMtEnd;
    }

    public function sheets(): array
    {
        return [
            new SummaryMonthlyTowerExport($this->towerMonthlyActivity, $this->nonMtStart),
            new SummaryMonthlyUnitExport($this->unitMonthlyActivity, $this->nonMtStart),
        ];
    }
}
