(function () {
  var form = document.querySelector("[data-hub-form]");
  var partnerAction = document.querySelector("[data-partner-action]");

  function trackingData() {
    var params = new URLSearchParams(window.location.search);
    var timezone = "";

    try {
      timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || "";
    } catch (error) {
      timezone = "";
    }

    return {
      referrer: document.referrer || "",
      language: navigator.language || "",
      screen_size: window.screen ? window.screen.width + "x" + window.screen.height : "",
      viewport_size: window.innerWidth + "x" + window.innerHeight,
      timezone: timezone,
      cookies_enabled: navigator.cookieEnabled ? "1" : "0",
      utm_source: params.get("utm_source") || "",
      utm_medium: params.get("utm_medium") || "",
      utm_campaign: params.get("utm_campaign") || "",
      utm_content: params.get("utm_content") || "",
      utm_term: params.get("utm_term") || ""
    };
  }

  if (partnerAction && form) {
    partnerAction.addEventListener("click", function () {
      form.elements.service_interest.value = "Partner Program";
      document.querySelector("#consultation").scrollIntoView({ behavior: "smooth" });
      window.setTimeout(function () {
        form.elements.name.focus();
      }, 450);
    });
  }

  if (!form) return;

  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    if (!form.reportValidity()) return;

    var button = form.querySelector(".submit-button");
    var status = form.querySelector("[data-form-status]");
    var data = new FormData(form);
    var name = String(data.get("name") || "").trim();
    var phone = String(data.get("phone") || "").trim();
    var interest = String(data.get("service_interest") || "").trim();
    var payload = Object.assign({
      name: name,
      phone: phone,
      property_type: interest,
      budget: "IZIN Hub enquiry",
      source_url: window.location.href
    }, trackingData());

    button.disabled = true;
    button.textContent = "Sending...";
    status.textContent = "";
    status.classList.remove("is-error");

    try {
      var response = await fetch("https://izindesigns.com/wp-json/izin-leads/v1/submit", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      if (!response.ok) throw new Error("Request could not be saved.");

      form.reset();
      status.textContent = "Request received. Our team will contact you shortly.";
      button.textContent = "Request Sent";
    } catch (error) {
      var message = "IZIN Hub enquiry%0A%0AName: " + encodeURIComponent(name) + "%0APhone: " + encodeURIComponent(phone) + "%0AInterest: " + encodeURIComponent(interest);
      status.innerHTML = 'The request could not be saved. <a href="https://wa.me/918714737111?text=' + message + '">Continue on WhatsApp</a>.';
      status.classList.add("is-error");
      button.disabled = false;
      button.textContent = "Try Again";
    }
  });
})();
