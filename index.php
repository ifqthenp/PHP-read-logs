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
 * Array of table headers (content of <th> tags)
 */
$tableHeaders = array(
    'Month',
    'Total file requests',
    'Articles files requests',
    'Total bandwidth KB',
    'Total 404 requests',
    'Unique 404 requests'
);

/*
 * Send data and headers to the outputTable() function and print final table with stats
 */
outputTable($monthlyData, $tableHeaders);

include 'includes/footer.php';
