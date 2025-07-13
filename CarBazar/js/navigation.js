// --- MOBILE NAVIGATION SCRIPT ---
const mainNav = document.querySelector("#main-nav");
const navToggle = document.querySelector(".mobile-nav-toggle");

navToggle.addEventListener("click", () => {
  const isVisible = mainNav.getAttribute("data-visible");

  if (isVisible === "false") {
    mainNav.setAttribute("data-visible", true);
    navToggle.setAttribute("aria-expanded", true);
  } else {
    mainNav.setAttribute("data-visible", false);
    navToggle.setAttribute("aria-expanded", false);
  }
});
