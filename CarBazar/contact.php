<?php
// STEP 1: Connect to DB and set page-specific variables
require_once 'db_connect.php'; // The session is started inside this file
$pageTitle = "Contact Us & Our Locations";

// STEP 2: Capture page-specific CSS
ob_start();
?>
<style>
    .page-container { max-width: 900px; margin: auto; }
    .locator-section { 
        padding: 2rem; 
        background: #fff; 
        border-radius: 8px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        text-align: center; 
    }
    #city-selector { 
        font-size: 1.2rem; 
        padding: 0.75rem 1.25rem; 
        margin-top: 1rem; 
        min-width: 300px;
        border-radius: 5px; 
        border: 1px solid #ccc;
    }
    #showroom-results { 
        margin-top: 2rem; 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
        gap: 1.5rem; 
        text-align: left; 
    }
    .showroom-card { 
        background: #f8f9fa; 
        padding: 1.5rem; 
        border: 1px solid #dee2e6; 
        border-radius: 5px; 
    }
    .showroom-card h4 { 
        margin-top: 0; 
        color: #0056b3; 
        font-size: 1.2rem; 
    }
    .showroom-card p { 
        margin: 0.5rem 0; 
        line-height: 1.6; 
    }
</style>
<?php
$extra_styles = ob_get_clean();

// STEP 3: Include the header
include 'includes/header.php';

// STEP 4: Fetch data needed for this page
$cities_result = $conn->query("SELECT DISTINCT city FROM showrooms ORDER BY city ASC");
?>

<!-- START: Page-specific content -->
<div class="container page-container">
    <h2 style="text-align: center;">Find a Showroom Near You</h2>

    <div class="locator-section">
        <p>Select a city to filter our locations, or browse all showrooms below.</p>
        
        <select id="city-selector">
            <option value="">-- Show All Cities --</option>
            <?php if ($cities_result && $cities_result->num_rows > 0): ?>
                <?php while($city = $cities_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($city['city']); ?>">
                        <?php echo htmlspecialchars($city['city']); ?>
                    </option>
                <?php endwhile; ?>
            <?php else: ?>
                <option value="" disabled>No locations available</option>
            <?php endif; ?>
        </select>

        <div id="showroom-results">
            <!-- Showroom contact details will be loaded here by JavaScript -->
        </div>
    </div>
</div>
<!-- END: Page-specific content -->


<!-- This self-contained JavaScript block powers this page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const citySelector = document.getElementById('city-selector');
    const resultsContainer = document.getElementById('showroom-results');

    // This function fetches showroom data from our API
    function fetchShowrooms(city = '') {
        resultsContainer.innerHTML = '<p>Loading locations...</p>';
        
        // The api/get_showrooms.php will return all showrooms if the city parameter is empty
        fetch(`api/get_showrooms.php?city=${encodeURIComponent(city)}`)
            .then(response => response.json())
            .then(showrooms => {
                resultsContainer.innerHTML = ''; // Clear the "Loading..." message
                if (showrooms.length === 0) {
                    resultsContainer.innerHTML = '<p>No showrooms found matching your selection.</p>';
                    return;
                }
                
                showrooms.forEach(showroom => {
                    const card = `
                        <div class="showroom-card">
                            <h4>CarBazar ${showroom.city}</h4>
                            <p><strong>Address:</strong> ${showroom.address}</p>
                            <p><strong>Phone:</strong> ${showroom.phone}</p>
                            <p><strong>Manager:</strong> ${showroom.manager_name || 'N/A'}</p>
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

    // Add an event listener to the dropdown menu to fetch data on change
    citySelector.addEventListener('change', function() {
        fetchShowrooms(this.value);
    });

    // Immediately fetch all showrooms when the page first loads
    fetchShowrooms(); 
});
</script>


<?php
// STEP 5: Include the footer at the very end
include 'includes/footer.php'; 
?>