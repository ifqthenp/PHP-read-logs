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

    if (!is_file($path) && !is_readable($path))
    {
        echo '<p>' . 'Error. Not a file or not readable' . '</p>';
    }
    else
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
        return $summary;
    }
}

/**
 * This function prints the table with stats returned from getStats() function
 * @param $datArray
 * @param $headers
 */
function outputTable($datArray, $headers)
{
    echo '<table border="1" width="100%">';
    echo '<tr>';
    foreach ($headers as $header)
    {
        echo '<th>' . $header . '</th>';
    }
    echo '</tr>';

    foreach ($datArray as $key => $value)
    {
        $month = pathinfo($value['month']); // extract log file name from the path
        echo '<tr>';
        echo '<td>' . ucfirst($month['filename']) . '</td>'; // Capitalise month's first letter
        echo '<td>' . $value['totalReq'] . '</td>';
        echo '<td>' . $value['articlesReq'] . '</td>';
        echo '<td>' . $value['bandwidth'] . '</td>';
        echo '<td>' . $value['total404'] . '</td>';

        echo '<td>';
        // echo sub-array with unique 404 requests
        foreach ($value['unique404'] as $item)
        {
            echo "$item<br>";
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
