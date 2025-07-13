<?php
$pageTitle = "My Wishlist";
// Capture page-specific CSS to be injected into the header
ob_start();
?>
<style>
    .car-card-container { position: relative; display: flex; }
    .remove-wishlist-btn {
        position: absolute; top: 10px; right: 10px; z-index: 10;
        background: rgba(0, 0, 0, 0.6); color: white; border: none; border-radius: 50%;
        width: 35px; height: 35px; font-size: 20px; font-weight: bold;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .remove-wishlist-btn:hover { background-color: #e74c3c; transform: scale(1.1); }
</style>
<?php
$extra_styles = ob_get_clean();
include 'includes/header.php';

// Security Check
if (!isset($_SESSION["loggedin"])) { header("location: login.php"); exit; }
?>

<!-- START: Page-specific content -->
<div class="container">
    <h2>My Wishlist</h2>
    <div id="wishlist-listings" class="listings-grid">
        <p>Loading your wishlisted cars...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const listingsContainer = document.getElementById('wishlist-listings');
    
    // Handles the removal of an item from the wishlist
    function removeFromWishlist(button, carId, cardContainer) {
        button.disabled = true;
        button.innerHTML = '...';
        fetch('api/toggle_wishlist.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ car_id: carId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.action === 'removed') {
                // On success, fade out and remove the card from the page
                cardContainer.style.transition = 'opacity 0.5s ease';
                cardContainer.style.opacity = '0';
                setTimeout(() => { 
                    cardContainer.remove();
                    // If this was the last car, show the empty message.
                    if (listingsContainer.children.length === 0) {
                        listingsContainer.innerHTML = '<p>Your wishlist is now empty.</p>';
                    }
                }, 500);
            } else {
                alert('Could not remove item. Please try again.');
                button.disabled = false;
                button.innerHTML = '×';
            }
        }).catch(error => {
            console.error('Error:', error);
            button.disabled = false;
            button.innerHTML = '×';
        });
    }
    
    // Fetches all wishlisted cars from the API when the page loads
    fetch('api/get_wishlist.php')
        .then(response => response.json())
        .then(cars => {
            if (cars.error) { throw new Error(cars.error); }
            
            listingsContainer.innerHTML = ''; // Clear "Loading..."

            if (cars.length === 0) {
                listingsContainer.innerHTML = '<p>Your wishlist is empty. Go to the <a href="index.php">homepage</a> to add cars!</p>';
                return;
            }
            
            cars.forEach(car => {
                const formattedPrice = parseFloat(car.price).toLocaleString('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 });
                let carSpecs = [];
                if (car.fuel_type) { carSpecs.push(car.fuel_type); }
                if (car.km_driven) { carSpecs.push(parseFloat(car.km_driven).toLocaleString('en-IN') + ' km'); }
                const specsString = carSpecs.join(' • ');
                const cityDisplay = car.city ? `<div class="car-card-location">${car.city}</div>` : '';

                const cardContainer = document.createElement('div');
                cardContainer.className = 'car-card-container';
                
                cardContainer.innerHTML = `
                    <button class="remove-wishlist-btn" title="Remove from Wishlist">×</button>
                    <a href="car_details.php?id=${car.id}" class="car-card-link">
                        <div class="car-card">
                            <img src="${car.front_image || 'images/default-placeholder.png'}" alt="${car.make} ${car.model}">
                            <div class="car-card-content">
                                <h3>${car.year} ${car.make} ${car.model}</h3>
                                ${specsString ? `<p class="car-specs">${specsString}</p>` : ''}
                                ${cityDisplay}
                                <p class="car-price">${formattedPrice}</p>
                            </div>
                        </div>
                    </a>
                `;

                const removeButton = cardContainer.querySelector('.remove-wishlist-btn');
                removeButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    event.preventDefault();
                    removeFromWishlist(this, car.id, cardContainer);
                });
                
                listingsContainer.appendChild(cardContainer);
            });
        })
        .catch(error => {
            console.error('Error fetching wishlist:', error);
            listingsContainer.innerHTML = `<p class="message error">Could not load your wishlist. Please try again later.</p>`;
        });
});
</script>
<!-- END: Unique page content -->
<?php 
include 'includes/footer.php'; 
?>