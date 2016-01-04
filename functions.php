<?php
/**
 * The function getStats() opens the file on the server in reading mode and performs following operations on file:
 * a) counts total number of lines in the file
 * b) counts number of file requests in articles folder
 * c) counts total bandwidth consumed by the file requests
 * d) counts number of 404 errors
 * e) files that produced 404 error gathered into array and all duplicates removed using PHP array_unique() function.
 * @param string $path path provided by glob function
 * @return array
 */
function getStats($path)
{
    $summary = array();

    if (is_file($path) && is_readable($path))
    {
        // open file in reading mode
        $filesToRead = fopen($path, 'r');

        $countTotalRequests = 0; // count the total number of file requests in the month
        $countArticlesRequests = 0; // count the number of file requests from the articles directory
        $bandwidthSum = 0; // count total bandwidth consumed by the file requests over the month
        $count404Errors = 0; // count the number of requests that resulted in 404 status errors
        $fileNames404 = array(); // file names that produced 404 errors gathered into array

        while (!feof($filesToRead))
        {
            $readFileContent = fgets($filesToRead);

            if (!feof($filesToRead))
            {
                $explodeBySpace = explode(' ', $readFileContent);
                $explodeBySlash = explode('/', $explodeBySpace[5]);

                // find articles folder
                if ($explodeBySlash[0] === 'articles')
                {
                    $countArticlesRequests++;
                }

                // check if $explodeBySpace[7] is a number
                if (ctype_digit($explodeBySpace[7]))
                {
                    $bandwidthSum += $explodeBySpace[7];
                }

                // find 404 errors, count totals and add files that produced the error into array
                if ($explodeBySpace[3] === '404')
                {
                    $fileNames404[] = $explodeBySpace[5];
                    $count404Errors++;
                }
                $countTotalRequests++;
            }
        }
        fclose($filesToRead);

        $summary['month'] = $path;
        $summary['totalReq'] = $countTotalRequests;
        $summary['articlesReq'] = $countArticlesRequests;
        $summary['bandwidth'] = $bandwidthSum;
        $summary['total404'] = $count404Errors;

        //PHP function array_unique() to remove duplicate values from the array
        $uniqueFileNames404 = array_unique($fileNames404);

        foreach ($uniqueFileNames404 as $value)
        {
            $summary['unique404'][] = $value;
        }
    }
    else
    {
        $summary['month'] = $path;
        $summary['notReadable'] = 'not readable';
    }
    return $summary;
}

/**
 * This function prints the table with stats returned from getStats() function
 * @param array $datArray
 * @return string $table
 */
function table($datArray)
{
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

    $table = '';
    $table .= '<table border="1" width="100%">';
    $table .= '<tr>';
    foreach ($tableHeaders as $header)
    {
        $table .= '<th>' . $header . '</th>';
    }
    $table .= '</tr>';

    foreach ($datArray as $key => $value)
    {
        if (array_key_exists('notReadable', $value))
        {
            $month = pathinfo($value['month']); // extract log file name from the path
            $table .= '<tr>';
            $table .= '<td>' . ucfirst($month['filename']) . '</td>';
            $table .= '<td colspan="5">' . $value['notReadable'] . '</td>';
            $table .= '</tr>';
        }
        else
        {
            $month = pathinfo($value['month']); // extract log file name from the path
            $table .= '<tr>';
            $table .= '<td>' . ucfirst($month['filename']) . '</td>'; // Capitalise month's first letter
            $table .= '<td>' . $value['totalReq'] . '</td>';
            $table .= '<td>' . $value['articlesReq'] . '</td>';
            $table .= '<td>' . $value['bandwidth'] . '</td>';
            $table .= '<td>' . $value['total404'] . '</td>';

            $table .= '<td>';
            // sub-array with unique 404 requests
            foreach ($value['unique404'] as $item)
            {
                $table .= "$item<br>";
            }
            $table .= '</td>';
            $table .= '</tr>';
        }
    }
    $table .= '</table>';
    return $table;
}
