<!DOCTYPE html>
<html>
<head>
    <title>Report - <?= date('F Y', mktime(0,0,0,$month,1,$year)) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()">Print / Save as PDF</button>
    </div>
    <h2>MPTRS Monthly Maintenance Report</h2>
    <h4>Period: <?= date('F Y', mktime(0,0,0,$month,1,$year)) ?></h4>
    
    <!-- Add the same tables from views/reports/index.php here -->
    <p>Report generated on <?= date('d M Y H:i') ?> by <?= Auth::user()['full_name'] ?></p>
</body>
</html>