<?php
/**
 * Script to display brand data.
 * * This script takes the brand data provided and displays it in a user-friendly format.
 */

// Sample data (replace with your actual data source, e.g., from a database or API)
$data = [
    "data" => [
        [
            "id" => 1,
            "business_id" => 1,
            "name" => "test",
            "description" => "brand",
            "created_by" => 2,
            "deleted_at" => null,
            "created_at" => "2025-05-20T01:38:34.000000Z",
            "updated_at" => "2025-05-20T01:38:34.000000Z"
        ]
    ]
];

// Function to display data
function displayBrandData($data) {
    if (isset($data['data']) && is_array($data['data'])) {
        echo "<!DOCTYPE html>";
        echo "<html lang='en'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Brand Data</title>";
        // Include some basic styling for better presentation
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }";
        echo "h1 { color: #333; margin-bottom: 20px; }";
        echo "table { width: 100%; border-collapse: collapse; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }";
        echo "th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }";
        echo "th { background-color: #f0f0f0; }";
        echo "tr:hover { background-color: #f5f5f5; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<h1>Brand Data</h1>";
        echo "<table>";
        echo "<thead><tr><th>ID</th><th>Business ID</th><th>Name</th><th>Description</th><th>Created By</th><th>Deleted At</th><th>Created At</th><th>Updated At</th></tr></thead>";
        echo "<tbody>";
        foreach ($data['data'] as $brand) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($brand['id']) . "</td>";
            echo "<td>" . htmlspecialchars($brand['business_id']) . "</td>";
            echo "<td>" . htmlspecialchars($brand['name']) . "</td>";
            echo "<td>" . htmlspecialchars($brand['description']) . "</td>";
            echo "<td>" . htmlspecialchars($brand['created_by']) . "</td>";
            echo "<td>" . htmlspecialchars($brand['deleted_at'] ?? 'NULL') . "</td>"; // Display NULL if null
            echo "<td>" . htmlspecialchars($brand['created_at']) . "</td>";
            echo "<td>" . htmlspecialchars($brand['updated_at']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</body>";
        echo "</html>";
    } else {
        echo "<p>No brand data available.</p>";
    }
}

// Call the function to display the data
displayBrandData($data);
?>
