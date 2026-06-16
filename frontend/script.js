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
    if (consultationOpen && window.matchMedia("(max-width: 767px)").matches) {
      consultationOpen.scrollIntoView({ behavior: "smooth", block: "center" });
    }
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

  function getLeadTrackingData() {
    var params = new URLSearchParams(window.location.search);
    var screenSize = "";
    var viewportSize = "";
    var timezone = "";

    if (window.screen) {
      screenSize = window.screen.width + "x" + window.screen.height;
    }

    viewportSize = window.innerWidth + "x" + window.innerHeight;

    try {
      timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || "";
    } catch (error) {
      timezone = "";
    }

    return {
      referrer: document.referrer || "",
      language: navigator.language || "",
      screen_size: screenSize,
      viewport_size: viewportSize,
      timezone: timezone,
      cookies_enabled: navigator.cookieEnabled ? "1" : "0",
      utm_source: params.get("utm_source") || "",
      utm_medium: params.get("utm_medium") || "",
      utm_campaign: params.get("utm_campaign") || "",
      utm_content: params.get("utm_content") || "",
      utm_term: params.get("utm_term") || ""
    };
  }

  if (form) {
    form.addEventListener("submit", async function (event) {
      event.preventDefault();

      var data = new FormData(form);
      var leadData = {
        name: data.get("name") || "",
        phone: data.get("phone") || "",
        property_type: data.get("property_type") || "",
        budget: data.get("budget") || "",
        email: data.get("email") || "",
        sqft: data.get("sqft") || "",
        location: data.get("location") || "",
        source_url: window.location.href
      };
      Object.assign(leadData, getLeadTrackingData());
      var message = [
        "New Izin Designs Consultation Enquiry",
        "",
        "Name: " + leadData.name,
        "Phone: " + leadData.phone,
        "Property Type: " + leadData.property_type,
        "Budget: " + leadData.budget,
        "Email: " + (leadData.email || "Not provided"),
        "Approx. Sq.Ft: " + (leadData.sqft || "Not provided"),
        "Location: " + (leadData.location || "Not provided")
      ].join("\n");
      var whatsappURL = "https://wa.me/918714737111?text=" + encodeURIComponent(message);
      var submitButton = form.querySelector(".izin-submit");

      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "Saving...";
      }

      try {
        await fetch("/wp-json/izin-leads/v1/submit", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(leadData)
        });
      } catch (error) {
        // Continue to WhatsApp even if the lead endpoint is unavailable.
      }

      window.location.href = whatsappURL;
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
