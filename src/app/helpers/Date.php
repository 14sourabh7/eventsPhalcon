<?php
namespace App\Helper;

class Date
{
    public function currentDate()
    {
        return date('d-m-y');
    }

    public function currentTime()
    {
        return date('H:i:s');
    }
}