document.addEventListener("DOMContentLoaded", function () {
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
