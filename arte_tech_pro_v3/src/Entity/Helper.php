<?php

namespace App\Entity;


class Helper
{

    public function convertToGoodDateForDB($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    public function getHoursDifference($startTime, $endTime)
    {
        return $startTime->diff($endTime)->format("%H:%I");
    }

    public function calculateTaskTotalCost($hourlyRate, $totalHours, $transportRate, $transportKM){


        $xAbleHours = date('h',strtotime($totalHours));
        $xAbleMinutes = date('i',strtotime($totalHours))/60;


        return $hourlyRate * ($xAbleHours + $xAbleMinutes) + ($transportRate * $transportKM);
    }
}
