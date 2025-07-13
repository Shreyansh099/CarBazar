<?php
// STEP 1: Connect to DB and get data needed for the page title.
// This must happen before the header is included.
require_once 'db_connect.php'; // The session is started inside this file.

$showroom_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// If the ID is invalid, redirect back to the main showrooms page.
if ($showroom_id <= 0) { 
    header("Location: showrooms.php"); 
    exit; 
}

// Fetch the showroom's name for the title.
$stmt = $conn->prepare("SELECT city, listing_id FROM showrooms WHERE id = ?");
$stmt->bind_param("i", $showroom_id); 
$stmt->execute();
$showroom = $stmt->get_result()->fetch_assoc();

// If no showroom with that ID exists, redirect.
if (!$showroom) { 
    header("Location: showrooms.php"); 
    exit; 
}

// STEP 2: Set the page title variable for the header.
$pageTitle = "Cars at " . htmlspecialchars($showroom['city']) . " (" . htmlspecialchars($showroom['listing_id']) . ") Showroom";

// STEP 3: Include the header.
include 'includes/header.php';
?>

<!-- START: Page-specific content -->
<div class="container">
    <h2 id="page-main-title"><?php echo htmlspecialchars($pageTitle); ?></h2>
    <div id="car-listings" class="listings-grid">
        <p>Loading cars for this location...</p>
    </div>
</div>
<!-- END: Page-specific content -->

<!-- This self-contained script powers this page exclusively -->
<script>
    // --- START OF SELF-CONTAINED SCRIPT ---

    // Function 1: Handle the wishlist button click.
    // This is copied from main.js to make this page independent and avoid conflicts.
    function toggleWishlist(buttonElement, carId) {
        const isCurrentlyActive = buttonElement.classList.contains('active');
        buttonElement.classList.toggle('active');
        buttonElement.innerHTML = buttonElement.classList.contains('active') ? '<i class="fa-solid fa-heart"></i>' : '<i class="fa-regular fa-heart"></i>';
        buttonElement.disabled = true;
        fetch('api/toggle_wishlist.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ car_id: carId })})
        .then(response => { if (!response.ok) { throw new Error('Server error'); } return response.json(); })
        .then(data => { if (data.status !== 'success') { console.error("Error:", data.message); buttonElement.classList.toggle('active'); buttonElement.innerHTML = isCurrentlyActive ? '<i class="fa-solid fa-heart"></i>' : '<i class="fa-regular fa-heart"></i>'; }})
        .catch(error => { console.error('Error:', error); buttonElement.classList.toggle('active'); buttonElement.innerHTML = isCurrentlyActive ? '<i class="fa-solid fa-heart"></i>' : '<i class="fa-regular fa-heart"></i>'; })
        .finally(() => { buttonElement.disabled = false; });
    }

    // Function 2: Fetch and display cars specifically for THIS showroom.
    document.addEventListener('DOMContentLoaded', function() {
        const listingsContainer = document.getElementById('car-listings');
        const pageTitleElement = document.getElementById('page-main-title');
        const showroomId = <?php echo $showroom_id; ?>; // Get the ID from PHP
        const apiUrl = `api/get_cars_by_showroom.php?id=${showroomId}`;

        listingsContainer.innerHTML = '<p>Loading cars...</p>';

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.error) { throw new Error(data.error); }

                listingsContainer.innerHTML = ''; 

                if (data.cars.length === 0) {
                    pageTitleElement.textContent = "No Cars Currently Available Here";
                    listingsContainer.innerHTML = '<p>Please check back later or browse other <a href="showrooms.php">showrooms</a>.</p>';
                    return;
                }
                
                const plural = data.cars.length === 1 ? "" : "s";
                pageTitleElement.textContent = `Showing ${data.cars.length} Listing${plural} for this Location`;
                
                data.cars.forEach(car => {
                    const formattedPrice = parseFloat(car.price).toLocaleString('en-IN', {
                        style: 'currency', currency: 'INR', maximumFractionDigits: 0
                    });
                    
                    let carSpecsParts = [];
                    if (car.fuel_type) { carSpecsParts.push(car.fuel_type); }
                    if (car.km_driven) { carSpecsParts.push(parseFloat(car.km_driven).toLocaleString('en-IN') + ' km'); }
                    const carSpecs = carSpecsParts.join(' • ');

                    let carLink = data.user_logged_in ? `car_details.php?id=${car.id}` : `login.php?redirect=car_details.php?id=${car.id}`;
                    let wishlistButton = '';
                    if (data.user_logged_in) {
                        const isWished = car.in_wishlist; 
                        const heartIcon = isWished ? '<i class="fa-solid fa-heart"></i>' : '<i class="fa-regular fa-heart"></i>';
                        wishlistButton = `<button class="wishlist-btn ${isWished ? 'active' : ''}" onclick="toggleWishlist(this, ${car.id}); event.stopPropagation();">${heartIcon}</button>`;
                    }

                    const carCard = `
                        <div class="car-card-container">
                            ${wishlistButton}
                            <a href="${carLink}" class="car-card-link">
                                <div class="car-card">
                                    <img src="${car.front_image || 'images/default-placeholder.png'}" alt="${car.make} ${car.model}">
                                    <div class="car-card-content">
                                        <h3>${car.year} ${car.make} ${car.model}</h3>
                                        ${carSpecs ? `<p class="car-specs">${carSpecs}</p>` : ''}
                                        <p class="car-price">${formattedPrice}</p>
                                    </div>
                                </div>
                            </a>
                        </div>`;
                    listingsContainer.innerHTML += carCard;
                });
            })
            .catch(error => {
                console.error('Error fetching cars for showroom:', error);
                listingsContainer.innerHTML = '<p class="message error">Could not load car listings for this location.</p>';
            });
    });
    // --- END OF SELF-CONTAINED SCRIPT ---
</script>

<?php
// STEP 4: Include the footer at the end.
include 'includes/footer.php'; 
?>