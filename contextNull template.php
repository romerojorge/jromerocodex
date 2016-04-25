<?php
/**
 * Created by PhpStorm.
 * User: jorgeromero
 * Date: 4/25/16
 * Time: 1:20 PM
 */

// place under the if $context['status] = 'NA'

if ((strpos($human, 'burn') !== false) and (strpos($human, '500')) !== false) {
        $context['status'] = "500calwork";


}

if ($context['status'] == "500calwokr"){

    $context ['status'] = "NA";
    return "Tennis (520 calories)\n
Running (600 calories)\n
Bicycling (600 calories)\n
Football (600 calories)\n
Basketball (600 calories)\n
Soccer (600 calories)\n
all 1 HOUR ";


}