document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  const carId = params.get("id");
  const contentContainer = document.getElementById("car-detail-content");
  if (!carId) {
    contentContainer.innerHTML = '<p class="message error">Invalid car ID.</p>';
    return;
  }

  fetch(`api/get_car_details.php?id=${carId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        throw new Error(data.error);
      }
      const car = data.car;
      document.title = `${car.year} ${car.make} ${car.model} - CarBazar`;

      const formattedPrice = parseFloat(car.price).toLocaleString("en-IN", {
        style: "currency",
        currency: "INR",
      });

      // This builds the Specifications Box correctly.
      const specsBox = `
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap:1rem; margin-top:1.5rem; padding: 1rem; background:#f9f9f9; border-radius:5px; border: 1px solid #e0e0e0;">
                    <div><strong style="display:block; font-size: 0.8rem; color: #666; text-transform: uppercase;">Fuel Type</strong>${
                      car.fuel_type || "N/A"
                    }</div>
                    <div><strong style="display:block; font-size: 0.8rem; color: #666; text-transform: uppercase;">KM Driven</strong>${
                      car.km_driven
                        ? parseFloat(car.km_driven).toLocaleString("en-IN") +
                          " km"
                        : "N/A"
                    }</div>
                    <div><strong style="display:block; font-size: 0.8rem; color: #666; text-transform: uppercase;">Ownership</strong>${
                      car.owner_count
                        ? car.owner_count == 1
                          ? "First Owner"
                          : car.owner_count + " Owners"
                        : "N/A"
                    }</div>
                </div>
            `;

      let contactBox = car.showroom_id
        ? `<div class="contact-seller-box"><h4>Contact Sales</h4><p>Inquire about this vehicle (<strong>ID: ${car.listing_id}</strong>) at our <strong>${car.city}</strong> showroom:</p><p style="font-size:1.2rem;font-weight:bold;">Phone: ${car.showroom_phone}</p></div>`
        : `<div class="contact-seller-box"><h4>Contact Head Office</h4><p>Inquire about this vehicle (<strong>ID: ${car.listing_id}</strong>).</p><p style="font-size:1.2rem;font-weight:bold;">Phone: 1-800-CAR-BAZAR</p></div>`;
      let locationBox = car.showroom_id
        ? `<div style="border:1px solid #e0e0e0; padding:1rem; border-radius:5px; margin-top:1.5rem;"><p style="margin:0;font-weight:bold;">Vehicle Location:</p><p style="margin:0;">${car.city} Showroom</p><p style="margin:0; font-size: 0.9rem;">${car.showroom_address}</p></div>`
        : "";
      const images = [
        { src: car.front_image, label: "Front" },
        { src: car.image_1, label: "Image 1" },
        { src: car.image_2, label: "Image 2" },
        { src: car.image_3, label: "Image 3" },
        { src: car.image_4, label: "Image 4" },
      ].filter((img) => img.src);
      let thumbnailsHtml =
        images.length > 0
          ? images
              .map(
                (img, index) =>
                  `<img src="${img.src}" class="thumbnail ${
                    index === 0 ? "active" : ""
                  }" data-src="${img.src}" alt="${img.label} view">`
              )
              .join("")
          : "";

      // --- THIS IS THE CORRECTED FINAL HTML TEMPLATE ---
      // The `${specsBox}` variable is now correctly placed after the price.
      const carDetailHtml = `
                <div class="details-container">
                    <div class="image-gallery">
                        <div class="main-image-container"><img src="${
                          car.front_image || "images/default-placeholder.png"
                        }" class="main-image" id="mainCarImage"></div>
                        <div class="thumbnail-container">${thumbnailsHtml}</div>
                    </div>
                    <div class="car-info">
                        <h2>${car.make} ${car.model}</h2>
                        <p style="font-size:1.2rem; color:#6c757d; margin-top:0;">${
                          car.year
                        }</p>
                        <p style="font-size:1rem; color:#888; margin-top:-1rem; margin-bottom:1.5rem;">Listing ID: <strong>${
                          car.listing_id || "N/A"
                        }</strong></p>
                        <p style="font-size:2.5rem; font-weight:bold; color:#28a745;">${formattedPrice}</p>
                        
                        ${specsBox}
                        
                        <p style="font-weight:bold; margin-top: 1.5rem;">About this car:</p>
                        <p>${car.description || "No description available."}</p>
                        ${locationBox}
                        ${contactBox}
                    </div>
                </div>`;
      contentContainer.innerHTML = carDetailHtml;

      // Adds interactivity to the image gallery thumbnails
      const mainImage = document.getElementById("mainCarImage");
      const thumbnails = document.querySelectorAll(".thumbnail");
      thumbnails.forEach((thumb) => {
        thumb.addEventListener("click", function () {
          mainImage.src = this.dataset.src;
          thumbnails.forEach((t) => t.classList.remove("active"));
          this.classList.add("active");
        });
      });
    })
    .catch((error) => {
      console.error("Error fetching details:", error);
      contentContainer.innerHTML = `<p class="message error">Could not load car details. It may have been removed.</p>`;
    });
});
