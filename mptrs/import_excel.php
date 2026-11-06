<?php
require 'config/config.php';
require 'app/core/Database.php';
require 'app/core/Auth.php';

// Security check - ensure only admin can run this
if (!Auth::check() || Auth::role() !== 'admin') {
    die("Access Denied. Please log in as an Admin first.");
}

$db = Database::getInstance();
$file = 'pietersburg_plan.csv';

if (!file_exists($file)) {
    die("Error: Please save the Excel file as '{$file}' and place it in the mptrs root folder.");
}

$handle = fopen($file, "r");
if ($handle === FALSE) die("Cannot open file.");

// 1. Ensure the Facility Exists
$stmt = $db->prepare("SELECT id FROM facilities WHERE name = 'Pietersburg Hospital'");
$stmt->execute();
$facility = $stmt->fetch();

if (!$facility) {
    $db->query("INSERT INTO facilities (code, name, type) VALUES ('P-HOSP', 'Pietersburg Hospital', 'Hospital')");
    $facilityId = $db->lastInsertId();
} else {
    $facilityId = $facility['id'];
}

// 2. Ensure the Building Exists (We'll use 'General Buildings' as the catch-all)
$stmt = $db->prepare("SELECT id FROM buildings WHERE name = 'General Buildings' AND facility_id = ?");
$stmt->execute([$facilityId]);
$building = $stmt->fetch();

if (!$building) {
    $db->prepare("INSERT INTO buildings (facility_id, code, name, type) VALUES (?, 'GEN-BLD', 'General Buildings', 'Administration Block')")
       ->execute([$facilityId]);
    $buildingId = $db->lastInsertId();
} else {
    $buildingId = $building['id'];
}

// Ensure Asset Category Exists (Use 'Civil' as default)
$stmt = $db->prepare("SELECT id FROM asset_categories WHERE name = 'Civil'");
$stmt->execute();
$categoryId = $stmt->fetchColumn();

$rowCount = 0;
$importedAssets = 0;
$importedWOs = 0;

// 3. Loop through the CSV
while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
    $rowCount++;
    
    // Skip the first 3 header rows
    if ($rowCount <= 3) continue;

    // Map columns based on the Excel structure
    // [0]=Item, [1]=Category, [2]=Type (Asset Name), [3]=Currently Available
    // [4]=Condition, [5]=Required Action
    // [8]=Y1 Cost, [10]=Y2 Cost, [12]=Y3 Cost, [14]=Y4 Cost, [16]=Y5 Cost
    
    $categoryGroup = trim($data[1] ?? '');
    $assetName = trim($data[2] ?? '');
    
    // Skip empty rows or summary rows
    if (empty($assetName) || strpos($assetName, 'Yearly Maintenance Estimate') !== false) continue;

    $condition = trim($data[4] ?? '');
    $action = trim($data[5] ?? '');

    // Combine Category and Type for a better Asset Name
    $fullAssetName = $categoryGroup ? "{$categoryGroup} - {$assetName}" : $assetName;
    
    // Generate a unique asset code
    $assetCode = 'PH-' . strtoupper(substr(md5($fullAssetName), 0, 6));

    // Check if asset already exists to prevent duplicates
    $stmt = $db->prepare("SELECT id FROM assets WHERE asset_code = ?");
    $stmt->execute([$assetCode]);
    $existingAsset = $stmt->fetch();

    if ($existingAsset) {
        $assetId = $existingAsset['id'];
    } else {
        // Determine Criticality based on condition text
        $criticality = 'Medium';
        if (stripos($condition, 'very poor') !== false || stripos($action, 'Urgent') !== false) {
            $criticality = 'Critical';
        } elseif (stripos($condition, 'poor') !== false) {
            $criticality = 'High';
        }

        // Insert Asset
        $db->prepare("INSERT INTO assets (asset_code, name, category_id, facility_id, building_id, condition_status, criticality, notes) 
                      VALUES (?, ?, ?, ?, ?, 'Fair', ?, ?)")
           ->execute([$assetCode, $fullAssetName, $categoryId, $facilityId, $buildingId, $criticality, $condition]);
        
        $assetId = $db->lastInsertId();
        $importedAssets++;
    }

    // 4. Process 5-Year Costs into Work Orders
    // The Excel has columns for Year 1 to Year 5 costs.
    $yearsData = [
        1 => $data[8] ?? null,
        2 => $data[10] ?? null,
        3 => $data[12] ?? null,
        4 => $data[14] ?? null,
        5 => $data[16] ?? null,
    ];

    foreach ($yearsData as $year => $costString) {
        // Clean the cost string (remove commas, equal signs, text)
        $cost = (float) preg_replace('/[^0-9.]/', '', $costString);
        
        if ($cost > 0) {
            // Check if a WO exists for this asset and year to avoid duplicates
            $woTitle = "Master Plan Maintenance - Year {$year}";
            $stmt = $db->prepare("SELECT id FROM work_orders WHERE asset_id = ? AND title = ?");
            $stmt->execute([$assetId, $woTitle]);
            
            if (!$stmt->fetch()) {
                // Create Work Order
                $woNum = 'MP-' . date('Y') . '-' . rand(1000, 9999);
                $dueDate = date('Y-m-d', strtotime("+{$year} year"));
                
                $db->prepare("INSERT INTO work_orders (wo_number, asset_id, title, description, status, due_date, created_by, priority) 
                              VALUES (?, ?, ?, ?, 'Created', ?, 1, 'Medium')")
                   ->execute([$woNum, $assetId, $woTitle, $action, $dueDate, Auth::id()]);
                
                $woId = $db->lastInsertId();
                $importedWOs++;

                // Create the Cost Record
                $db->prepare("INSERT INTO costs (work_order_id, cost_type, description, amount, incurred_date, recorded_by) 
                              VALUES (?, 'Other', 'Estimated Budget from Master Plan', ?, ?, 1)")
                   ->execute([$woId, $cost, $dueDate]);
            }
        }
    }
}

fclose($handle);
echo "<h2>Import Complete!</h2>";
echo "<ul>";
echo "<li>Imported Assets: <strong>{$importedAssets}</strong></li>";
echo "<li>Imported Work Orders (from 5-Year Budgets): <strong>{$importedWOs}</strong></li>";
echo "</ul>";
echo "<a href='public/index.php?route=assets' class='btn btn-primary'>View Imported Assets</a>";