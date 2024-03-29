<?php
require '../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include '../../conn.php';
date_default_timezone_set('Asia/Manila');
// Get the current date in the Philippines timezone in the format "Y-m-d"
$currentDate = date('Y-m-d');
$formattedDate = date("F d, Y", strtotime($currentDate));


    $title = 'Daily';
    $inventory_query = "SELECT * FROM inventory WHERE item_status = 0 ORDER BY item_desc ASC";
    $log_reports_query = "SELECT * FROM log_reports
                        LEFT JOIN inventory ON inventory.item_id = log_reports.report_item_id
                        LEFT JOIN users ON users.user_id = log_reports.report_user_id
                        WHERE DATE(date_time) = '$currentDate'";

    $i = 1;
    $inventory_result = mysqli_query($connection, $inventory_query);
    $log_reports_result = mysqli_query($connection, $log_reports_query);

$html = '

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Romantic Baboy | Generate Reports</title>
    <!--Icon-->
    <link rel="icon" type="image/x-icon" href="../../assets/rombab-logo.png">
    <style>
        img {
            width: 100%;
            margin: 0px;
        }
        body {
            font-family: Arial, sans-serif;
            margin-top: 130px;
            margin-bottom: 100px;
        }
        h2 {
            text-align: center;
            margin-top: 0px;
            margin-bottom: 0px;
        }
        p {
            text-align: center;
            margin: 0px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #4444;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h5 {
            margin-top: 50;
            margin-bottom: 50;
            text-align: center;
        }
        h4 {
            margin: 0;
            font-weight: 400;
        }
        .center {
            text-align: center;
        }
        .invisible {
            border: none;
            background: none;
        }

        @page {
            margin-top: 10px; /* Adjust as needed */
            margin-bottom: 10px; /* Adjust as needed */
        }

        .header-letter,
        .footer-letter {
            position: fixed;
            left: 0;
            right: 0;
        }

        .header-letter {
            top: 0;
        }

        .footer-letter {
            bottom: 0;
        }

        img {
            width: 100%;
            margin: 0px;
        }
    </style>
    <div class="header-letter">
        <img src="romantic-baboy-header.png" alt="Example Image" class="img-fluid">
        </div>
        <div class="footer-letter">
        <img src="romantic-baboy-footer.png" alt="Example Image" class="img-fluid">
    </div>
<body>
    <h2>Romantic Baboy '.$title.' Report</h2>
    <p style="margin-bottom: 10px;">Generated on: ' . date('F j, Y | g:i A') . '</p>
    
    <table>
        <thead>
            <tr><th colspan="5">'.$title.' Inventory Report</th></tr>
            <tr>
                <th>No</th>
                <th>Item</th>
                <th>Description</th>
                <th>OUM</th>
                <th>Available Stocks</th>
            </tr>
        </thead>
        <tbody>';
        if(mysqli_num_rows($inventory_result) > 0) {
            while ($row_inventory = mysqli_fetch_array($inventory_result)) {
            $html .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.$row_inventory['item_name'].'</td>
                    <td>'.$row_inventory['item_desc'].'</td>
                    <td>'.$row_inventory['unit_of_measure'].'</td>
                    <td>'.$row_inventory['stock'].'</td>
                </tr>';
                $i++;
            }
        }
        else {
            $html .='<tr>
                <td class="text-center" colspan="5">No record found!</td>
            </tr>';
            }
        $html .= '</tbody>
    </table>
    
    <table>
        <thead>
            <tr><th colspan="5">'.$title.' Inventory Report</th></tr>
            <tr>
                <th>No</th>
                <th>Item</th>
                <th>User</th>
                <th>Qty</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>';
        $i = 1;
        if (mysqli_num_rows($log_reports_result) > 0) {
            while ($row_logs = mysqli_fetch_array($log_reports_result)) {
                $html .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.$row_logs['item_name'].'</td>
                    <td>'.$row_logs['name'].'</td>';
        
                // Check user role and adjust the sign accordingly
                if ($row_logs['action'] == 0) {
                    $html .= '<td>- '.$row_logs['report_qty'].''.$row_logs['unit_of_measure'].'</td>';
                } else {
                    $html .= '<td>+ '.$row_logs['report_qty'].''.$row_logs['unit_of_measure'].'</td>';
                }
        
                $html .= '<td>'.$row_logs['date_time'].'</td>
                </tr>';
                $i++;
            }
        }        
        else {
        $html .='<tr>
            <td class="text-center" colspan="5">No record found!</td>
        </tr>';
        }
        $html .= '</tbody>
        <tr><th class="invisible" colspan="5"><h5>--- NOTHING FOLLOWS ---</h5></tr>
    </table>';

$options = new Options();
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
// Set the paper size to A4 and orientation to portrait
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$uniqueId = uniqid();
$NameModified = strtolower(str_replace(' ', '', $formattedDate));
// Generate the file name with the current time, unique identifier, and equipment name
$fileName = 'daily_inventory_report_' . $NameModified . '_' . $uniqueId . '.pdf';

// Save the PDF to a directory in your file system
$directoryPath = '../daily_reports/';
$filePath = $directoryPath . $fileName;
file_put_contents($filePath, $dompdf->output());

// Output the PDF to the browser
$dompdf->stream($fileName, ["Attachment" => false]);

// Insert the file information into the daily_reports table
$insertQuery = "INSERT INTO daily_reports (report_file, report_time, as_archived) VALUES ('$fileName', NOW(), '0')";
    if (mysqli_query($connection, $insertQuery)) {
        echo "File information saved to the database.";
    } else {
        echo "Error: " . mysqli_error($connection);
    }
?>
