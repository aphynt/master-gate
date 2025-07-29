<?php

namespace App\Exports;

use App\Exports\DailyActivityExport;
use App\Exports\HistoryRepairsExport;
use App\Exports\SummaryMaintenanceTowerExport;
use App\Exports\SummaryMaintenanceUnitExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SummaryDailyExport implements WithMultipleSheets
{
    protected $dailyActivity;
    protected $historyRepair;
    protected $maintenanceTower;
    protected $maintenanceUnit;
    protected $date;

    public function __construct($dailyActivity, $historyRepair, $maintenanceTower, $maintenanceUnit, $date)
    {
        $this->dailyActivity = $dailyActivity;
        $this->historyRepair = $historyRepair;
        $this->maintenanceTower = $maintenanceTower;
        $this->maintenanceUnit = $maintenanceUnit;
        $this->date = $date;
    }

    public function sheets(): array
    {
        return [
            new DailyActivityExport($this->dailyActivity, $this->date),
            new HistoryRepairsExport($this->historyRepair),
            new SummaryMaintenanceTowerExport($this->maintenanceTower),
            // new SummaryMaintenanceUnitExport($this->maintenanceUnit),
        ];
    }
}
