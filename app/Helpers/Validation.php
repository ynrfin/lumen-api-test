<?php

namespace App\Helpers;

class Validation
{
    static function isTimestampIsoValid($timestamp)
    {
        if (preg_match('/^'.
            '(\d{4})-(\d{2})-(\d{2})T'. // YYYY-MM-DDT ex: 2014-01-01T
            '(\d{2}):(\d{2}):(\d{2})'.  // HH-MM-SS  ex: 17:00:00
            '(Z|((-|\+)\d{2}:\d{2}))'.  // Z or +01:00 or -01:00
            '$/', $timestamp, $parts) == true)
        {
            try {
                new \DateTime($timestamp);
                return true;
            }
            catch ( \Exception $e)
            {
                return false;
            }
        } else {
            return false;
        }
    }
}

?>
