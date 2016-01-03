<?php

include 'includes/header.php';

/*
 * glob() function reads and sends paths of all files with 'log' extension to the getStats() function.
 * getStats() function returns all necessary stats to the $monthlyData array.
 */
$monthlyData = array();
foreach (glob('logs/*.log') as $path)
{
    $monthlyData[] = getStats($path);
}

/*
 * Send data and headers to the outputTable() function and print final table with stats
 */
outputTable($monthlyData);

include 'includes/footer.php';
