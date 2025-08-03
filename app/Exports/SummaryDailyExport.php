<?php

namespace App\Exports;

use App\Exports\DailyActivityExport;
use App\Exports\HistoryRepairsExport;
use App\Exports\SummaryRepairTowerExport;
use App\Exports\SummaryRepairUnitExport;
use App\Exports\SummaryMaintenanceTowerExport;
use App\Exports\SummaryMaintenanceUnitExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SummaryDailyExport implements WithMultipleSheets
{
    protected $dailyActivity;
    protected $repairTower;
    protected $repairUnit;
    protected $maintenanceTower;
    protected $maintenanceUnit;
    protected $date;

    public function __construct($dailyActivity, $repairTower, $repairUnit, $maintenanceTower, $maintenanceUnit, $date)
    {

        $this->dailyActivity = $dailyActivity;
        $this->repairTower = $repairTower;
        $this->repairUnit = $repairUnit;
        $this->maintenanceTower = $maintenanceTower;
        $this->maintenanceUnit = $maintenanceUnit;
        $this->date = $date;
    }

    public function sheets(): array
    {
        return [
            new DailyActivityExport($this->dailyActivity, $this->date),
            new SummaryRepairTowerExport($this->repairTower),
            new SummaryRepairUnitExport($this->repairUnit),
            new SummaryMaintenanceTowerExport($this->maintenanceTower, $this->date),
            new SummaryMaintenanceUnitExport($this->maintenanceUnit, $this->date),
        ];
    }
}
