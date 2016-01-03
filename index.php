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
 * Send data from $monthlyData array to table() function
 */
$printTable = table($monthlyData);

/*
 * Print final table
 */
echo $printTable;

include 'includes/footer.php';
