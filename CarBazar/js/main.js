// The toggleWishlist function remains the same.
function toggleWishlist(buttonElement, carId) {
  const isCurrentlyActive = buttonElement.classList.contains("active");
  buttonElement.classList.toggle("active");
  buttonElement.innerHTML = buttonElement.classList.contains("active")
    ? '<i class="fa-solid fa-heart"></i>'
    : '<i class="fa-regular fa-heart"></i>';
  buttonElement.disabled = true;
  fetch("api/toggle_wishlist.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ car_id: carId }),
  })
    .then((response) => {
      if (!response.ok) throw new Error("Server error");
      return response.json();
    })
    .then((data) => {
      if (data.status !== "success") {
        console.error("Error:", data.message);
        buttonElement.classList.toggle("active");
        buttonElement.innerHTML = isCurrentlyActive
          ? '<i class="fa-solid fa-heart"></i>'
          : '<i class="fa-regular fa-heart"></i>';
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      buttonElement.classList.toggle("active");
      buttonElement.innerHTML = isCurrentlyActive
        ? '<i class="fa-solid fa-heart"></i>'
        : '<i class="fa-regular fa-heart"></i>';
    })
    .finally(() => {
      buttonElement.disabled = false;
    });
}

// Main logic for the index page.
document.addEventListener("DOMContentLoaded", function () {
  const filterForm = document.getElementById("filter-form");
  // Exit if we're not on a page that has the filter form.
  if (!filterForm) return;

  // --- This is the main function that reads all filters and fetches cars ---
  function performSearch() {
    const listingsContainer = document.getElementById("car-listings");
    if (!listingsContainer) return;

    // Gather all values from the new filter fields
    const searchTerm = document.getElementById("search-input").value;
    const city = document.getElementById("city-filter").value;
    const brand = document.getElementById("brand-filter").value;
    const fuelType = document.getElementById("fuel-filter").value;
    const maxPrice = document.getElementById("max-price-filter").value;
    const maxKm = document.getElementById("max-km-filter").value;

    // Build a query string from the filter values.
    // URLSearchParams automatically handles encoding.
    const queryParams = new URLSearchParams({
      search: searchTerm,
      city: city,
      brand: brand,
      fuel_type: fuelType,
      max_price: maxPrice,
      max_km: maxKm,
    });

    const apiUrl = `api/get_cars.php?${queryParams.toString()}`;
    listingsContainer.innerHTML = "<p>Searching for cars...</p>";

    fetch(apiUrl)
      .then((response) => response.json())
      .then((data) => {
        const listingsTitle = document.getElementById("listings-title");
        listingsContainer.innerHTML = "";
        if (data.error || !data.cars || data.cars.length === 0) {
          listingsTitle.textContent = "No Cars Found";
          listingsContainer.innerHTML =
            "<p>No cars match your filter criteria. Try broadening your search.</p>";
          return;
        }

        listingsTitle.textContent = `Showing ${data.cars.length} Matching Cars`;

        // The car card building logic remains correct
        data.cars.forEach((car) => {
          let formattedPrice = parseFloat(car.price).toLocaleString("en-IN", {
            style: "currency",
            currency: "INR",
            maximumFractionDigits: 0,
          });
          let carLink = data.user_logged_in
            ? `car_details.php?id=${car.id}`
            : `login.php?redirect=car_details.php?id=${car.id}`;
          let specParts = [];
          if (car.fuel_type) {
            specParts.push(car.fuel_type);
          }
          if (car.km_driven) {
            specParts.push(
              parseFloat(car.km_driven).toLocaleString("en-IN") + " km"
            );
          }
          const carSpecs = specParts.join(" • ");
          const cityDisplay = car.city
            ? `<div class="car-card-location">${car.city}</div>`
            : "";
          let wishlistButton = "";
          if (data.user_logged_in) {
            const isWished = car.in_wishlist;
            const heartIcon = isWished
              ? '<i class="fa-solid fa-heart"></i>'
              : '<i class="fa-regular fa-heart"></i>';
            wishlistButton = `<button class="wishlist-btn ${
              isWished ? "active" : ""
            }" onclick="toggleWishlist(this, ${
              car.id
            }); event.stopPropagation();">${heartIcon}</button>`;
          }
          const carCard = `<div class="car-card-container">${wishlistButton}<a href="${carLink}" class="car-card-link"><div class="car-card"><img src="${
            car.front_image || "images/default-placeholder.png"
          }" alt="${car.make} ${car.model}"><div class="car-card-content"><h3>${
            car.year
          } ${car.make} ${car.model}</h3>${
            carSpecs ? `<p class="car-specs">${carSpecs}</p>` : ""
          }${cityDisplay}<p class="car-price">${formattedPrice}</p></div></div></a></div>`;
          listingsContainer.innerHTML += carCard;
        });
      })
      .catch((error) => {
        console.error("Error fetching listings:", error);
        listingsContainer.innerHTML = `<p class="message error">Could not perform search. Please try again.</p>`;
      });
  }

  // --- EVENT LISTENER AND INITIAL PAGE LOAD ---

  // Listen for the "Find Cars" button click
  filterForm.addEventListener("submit", function (event) {
    event.preventDefault();
    performSearch();
  });

  // Perform an initial search when the page loads to show all cars
  performSearch();
});
