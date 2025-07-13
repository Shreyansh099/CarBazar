<?php
// STEP 1: Define page-specific variables and connect to the database.
// The session is started inside db_connect.php
require_once 'db_connect.php'; 
$pageTitle = "Sell Your Car - CarBazar";

// STEP 2: Include the header, which will use the $pageTitle.
include 'includes/header.php';

// STEP 3: Fetch data needed for this specific page.
$cities_result = $conn->query("SELECT DISTINCT city FROM showrooms ORDER BY city ASC");
?>

<!-- START: This is the unique HTML content for this page -->
<div class="container" style="max-width: 900px; margin: auto; padding: 2rem 0;">
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <h2>Sell Your Car with CarBazar</h2>
        <p style="font-size: 1.1rem; color: #555; max-width: 700px; margin: auto;">
            We offer a hassle-free process to get your car listed on our platform. To ensure quality and provide the best experience for buyers, our team handles the listing process. Get started by contacting your nearest showroom.
        </p>
    </div>

    <div class="locator-section" style="padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center;">
        <h3>Find Your Nearest Showroom to Begin</h3>
        <p style="color: #666;">Select a city to see the contact details for our local branches.</p>
        
        <select id="city-selector" style="font-size: 1.2rem; padding: 0.75rem 1.25rem; margin-top: 1rem; min-width: 300px; border-radius: 5px; border: 1px solid #ccc;">
            <option value="">-- Show All Locations --</option>
            <?php 
            if ($cities_result && $cities_result->num_rows > 0) {
                while($city = $cities_result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($city['city']) . '">' . htmlspecialchars($city['city']) . '</option>';
                }
            }
            ?>
        </select>

        <div id="showroom-results" style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; text-align: left;">
            <!-- Showroom contact details will be loaded here by JavaScript -->
        </div>
    </div>
</div>
<!-- END: Unique page content -->

<!-- This self-contained JavaScript block powers this page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const citySelector = document.getElementById('city-selector');
    const resultsContainer = document.getElementById('showroom-results');

    // This function fetches showroom data from the API
    function fetchShowrooms(city = '') {
        resultsContainer.innerHTML = '<p>Loading locations...</p>';
        // The API sends back an array of showrooms. If no city is specified, it sends all of them.
        fetch(`api/get_showrooms.php?city=${encodeURIComponent(city)}`)
            .then(response => response.json())
            .then(showrooms => {
                resultsContainer.innerHTML = ''; // Clear the "Loading..." message
                if (showrooms.length === 0) {
                    resultsContainer.innerHTML = '<p>No showrooms found matching your selection.</p>';
                    return;
                }
                
                showrooms.forEach(showroom => {
                    // This creates a text-only card for each showroom
                    const card = `
                        <div style="background: #f8f9fa; padding: 1.5rem; border: 1px solid #dee2e6; border-radius: 5px;">
                            <h4 style="margin-top:0; color:#0056b3;">CarBazar ${showroom.city}</h4>
                            <p style="margin:0.5rem 0;"><strong>Address:</strong> ${showroom.address}</p>
                            <p style="margin:0.5rem 0;"><strong>Phone:</strong> ${showroom.phone}</p>
                            <p style="margin:0.5rem 0;"><strong>Manager:</strong> ${showroom.manager_name || 'N/A'}</p>
                        </div>
                    `;
                    resultsContainer.innerHTML += card;
                });
            })
            .catch(error => {
                console.error('Error fetching showrooms:', error);
                resultsContainer.innerHTML = '<p class="message error">Could not load showroom information.</p>';
            });
    }

    // Add an event listener to the dropdown menu
    citySelector.addEventListener('change', function() {
        fetchShowrooms(this.value);
    });

    // Immediately fetch all showrooms when the page first loads
    fetchShowrooms(); 
});
</script>

<?php
// Include the footer at the very end
include 'includes/footer.php'; 
?>