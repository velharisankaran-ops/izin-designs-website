document.addEventListener("DOMContentLoaded", function () {
  var header = document.querySelector("[data-site-header]");
  var nav = document.querySelector("[data-primary-nav]");
  var navToggle = document.querySelector("[data-nav-toggle]");
  var navLinks = nav ? Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]')) : [];

  if (navToggle && nav) {
    navToggle.addEventListener("click", function () {
      var isOpen = nav.classList.toggle("is-open");
      navToggle.setAttribute("aria-expanded", String(isOpen));
      if (header) header.classList.toggle("nav-open", isOpen);
    });

    nav.addEventListener("click", function (event) {
      if (!(event.target instanceof HTMLAnchorElement)) return;
      nav.classList.remove("is-open");
      navToggle.setAttribute("aria-expanded", "false");
      if (header) header.classList.remove("nav-open");
    });
  }

  if (navLinks.length && "IntersectionObserver" in window) {
    var sectionMap = new Map();
    navLinks.forEach(function (link) {
      var target = document.querySelector(link.getAttribute("href"));
      if (target) sectionMap.set(target.id, link);
    });

    var navObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        navLinks.forEach(function (link) { link.classList.remove("is-active"); });
        var activeLink = sectionMap.get(entry.target.id);
        if (activeLink) activeLink.classList.add("is-active");
      });
    }, {
      rootMargin: "-28% 0px -62% 0px",
      threshold: 0.01
    });

    sectionMap.forEach(function (_, id) {
      var section = document.getElementById(id);
      if (section) navObserver.observe(section);
    });
  }

  var consultationForm = document.querySelector(".izin-form-card");
  var consultationOpen = document.querySelector("[data-consultation-open]");
  var consultationClose = document.querySelector("[data-consultation-close]");

  function openConsultationForm() {
    if (!consultationForm) return;
    consultationForm.classList.add("is-open");
    consultationForm.scrollIntoView({ behavior: "smooth", block: "center" });
  }

  function closeConsultationForm() {
    if (!consultationForm) return;
    consultationForm.classList.remove("is-open");
  }

  if (consultationOpen) {
    consultationOpen.addEventListener("click", openConsultationForm);
  }

  if (consultationClose) {
    consultationClose.addEventListener("click", closeConsultationForm);
  }

  var consultationAnchors = document.querySelectorAll('a[href="#consultation"]');
  consultationAnchors.forEach(function (anchor) {
    anchor.addEventListener("click", function () {
      openConsultationForm();
    });
  });

  var form = document.querySelector("[data-lead-form]");

  if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      var data = new FormData(form);
      var message = [
        "New Izin Designs Consultation Enquiry",
        "",
        "Name: " + (data.get("name") || ""),
        "Phone: " + (data.get("phone") || ""),
        "Property Type: " + (data.get("property_type") || ""),
        "Budget: " + (data.get("budget") || ""),
        "Email: " + (data.get("email") || "Not provided"),
        "Approx. Sq.Ft: " + (data.get("sqft") || "Not provided"),
        "Location: " + (data.get("location") || "Not provided")
      ].join("\n");

      window.location.href = "https://wa.me/918714737111?text=" + encodeURIComponent(message);
    });
  }

  var counters = document.querySelectorAll(".izin-metric-number");
  var animated = false;

  function formatNumber(value, format) {
    if (format === "lakh") {
      if (value >= 100000) return Math.round(value / 100000) + "L";
      return Math.round(value / 1000) + "K";
    }
    return Math.round(value);
  }

  function animateCounters() {
    if (animated) return;
    animated = true;

    counters.forEach(function (counter) {
      var target = parseInt(counter.getAttribute("data-target"), 10);
      var format = counter.getAttribute("data-format");
      var duration = 1300;
      var startTime = null;

      function updateCounter(timestamp) {
        if (!startTime) startTime = timestamp;
        var progress = Math.min((timestamp - startTime) / duration, 1);
        counter.textContent = formatNumber(target * progress, format);
        if (progress < 1) requestAnimationFrame(updateCounter);
        else counter.textContent = formatNumber(target, format);
      }

      requestAnimationFrame(updateCounter);
    });
  }

  var metricSection = document.querySelector(".izin-metric-strip");
  if ("IntersectionObserver" in window && metricSection) {
    var observer = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting) {
        animateCounters();
        observer.disconnect();
      }
    }, { threshold: 0.25 });
    observer.observe(metricSection);
  } else {
    animateCounters();
  }

  var gallery = document.querySelector("[data-gallery]");
  var prev = document.querySelector("[data-slide-prev]");
  var next = document.querySelector("[data-slide-next]");

  function slideGallery(direction) {
    if (!gallery) return;
    gallery.scrollBy({ left: direction * 280, behavior: "smooth" });
  }

  if (prev) prev.addEventListener("click", function () { slideGallery(-1); });
  if (next) next.addEventListener("click", function () { slideGallery(1); });
});
