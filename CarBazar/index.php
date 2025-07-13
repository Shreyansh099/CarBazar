<?php
// STEP 1: Always start the session and connect to the database first.
session_start();
require_once 'db_connect.php';

// STEP 2: Set any variables needed by the header.
$pageTitle = "CarBazar - Great Deals on Wheels";
$header_content = true; // This tells the header to show the large banner image.

// STEP 3: Now, include the header.
include 'includes/header.php';

// STEP 4: After the header, run page-specific database queries.
// The $conn variable now exists and can be safely used.
$cities_result = $conn->query("SELECT DISTINCT city FROM showrooms WHERE city IS NOT NULL AND city != '' ORDER BY city ASC");
$brands_result = $conn->query("SELECT DISTINCT make FROM cars WHERE make IS NOT NULL AND make != '' ORDER BY make ASC");
$fuel_types_result = $conn->query("SELECT DISTINCT fuel_type FROM cars WHERE fuel_type IS NOT NULL AND fuel_type != '' ORDER BY fuel_type ASC");
?>

<!-- START: This is the page's unique content -->
<div class="container">
    
    <!-- This is the complete, advanced filter bar -->
    <div class="filter-bar">
        <form id="filter-form" onsubmit="event.preventDefault();">
            <div class="filter-group main-search">
                <label for="search-input">Search by Name or Listing ID</label>
                <input type="text" id="search-input" placeholder="e.g., Civic, TY-CM-01...">
            </div>
            <div class="filter-group">
                <label>City</label>
                <select id="city-filter">
                    <option value="">All Cities</option>
                    <?php if ($cities_result) while($row = $cities_result->fetch_assoc()) echo '<option value="'.htmlspecialchars($row['city']).'">'.htmlspecialchars($row['city']).'</option>'; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Brand</label>
                <select id="brand-filter">
                    <option value="">All Brands</option>
                    <?php if ($brands_result) while($row = $brands_result->fetch_assoc()) echo '<option value="'.htmlspecialchars($row['make']).'">'.htmlspecialchars($row['make']).'</option>'; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Fuel Type</label>
                <select id="fuel-filter">
                    <option value="">All Fuel Types</option>
                    <?php if ($fuel_types_result) while($row = $fuel_types_result->fetch_assoc()) echo '<option value="'.htmlspecialchars($row['fuel_type']).'">'.htmlspecialchars($row['fuel_type']).'</option>'; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Max Price (₹)</label>
                <select id="max-price-filter">
                    <option value="">Any</option>
                    <option value="500000">₹5,00,000</option>
                    <option value="1000000">₹10,00,000</option>
                    <option value="2000000">₹20,00,000</option>
                    <option value="5000000">₹50,00,000</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Max KM Driven</label>
                <select id="max-km-filter">
                    <option value="">Any</option>
                    <option value="20000">20,000 km</option>
                    <option value="50000">50,000 km</option>
                    <option value="100000">1,00,000 km</option>
                </select>
            </div>
            <div class="filter-group submit-button-group">
                <button type="submit" class="btn-search">Find Cars</button>
            </div>
        </form>
    </div>
    
    <h2 id="listings-title">Latest Listings</h2>
    <div id="car-listings" class="listings-grid">
        <p>Loading cars...</p>
    </div>

</div>

<!-- The main script for the homepage, which powers the filter bar -->
<script src="js/main.js"></script>
<!-- END: Page-specific content -->

<?php 
// STEP 5: Include the footer at the very end.
include 'includes/footer.php'; 
?>