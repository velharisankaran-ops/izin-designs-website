document.addEventListener("DOMContentLoaded", function () {
  var header = document.querySelector("[data-site-header]");
  var nav = document.querySelector("[data-primary-nav]");
  var navToggle = document.querySelector("[data-nav-toggle]");
  var navLinks = nav ? Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]')) : [];
  var navDropdowns = nav ? Array.prototype.slice.call(nav.querySelectorAll("[data-nav-dropdown]")) : [];

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

  navDropdowns.forEach(function (dropdown) {
    var toggle = dropdown.querySelector("[data-nav-dropdown-toggle]");
    if (!toggle) return;

    toggle.addEventListener("click", function () {
      var shouldOpen = !dropdown.classList.contains("is-open");

      navDropdowns.forEach(function (item) {
        item.classList.remove("is-open");
        var itemToggle = item.querySelector("[data-nav-dropdown-toggle]");
        if (itemToggle) itemToggle.setAttribute("aria-expanded", "false");
      });

      dropdown.classList.toggle("is-open", shouldOpen);
      toggle.setAttribute("aria-expanded", String(shouldOpen));
    });
  });

  document.addEventListener("click", function (event) {
    navDropdowns.forEach(function (dropdown) {
      if (dropdown.contains(event.target)) return;
      dropdown.classList.remove("is-open");
      var toggle = dropdown.querySelector("[data-nav-dropdown-toggle]");
      if (toggle) toggle.setAttribute("aria-expanded", "false");
    });
  });

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

  var form = document.querySelector("[data-lead-form]");
  var isFreeConsultationPreview = !!document.querySelector(".free-consultation-form-only");
  var feedbackAudio = isFreeConsultationPreview ? {
    success: new Audio("assets/thank-you.mp3"),
    invalid: new Audio("assets/excuse-you.mp3")
  } : null;

  function playFeedbackAudio(type, delay) {
    if (!feedbackAudio || !feedbackAudio[type]) {
      return Promise.resolve();
    }

    var clip = feedbackAudio[type];

    try {
      clip.pause();
      clip.currentTime = 0;
      clip.play().catch(function () {});
    } catch (error) {
      return Promise.resolve();
    }

    return new Promise(function (resolve) {
      setTimeout(resolve, delay || 0);
    });
  }

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

  function trackLeadConversion() {
    if (typeof window.gtag !== "function") {
      return Promise.resolve();
    }

    return new Promise(function (resolve) {
      var resolved = false;

      function finish() {
        if (resolved) return;
        resolved = true;
        resolve();
      }

      window.gtag("event", "conversion", {
        send_to: "AW-18188025014/GQ_mCJG6rbQcELb53OBD",
        event_callback: finish,
        event_timeout: 1200
      });

      setTimeout(finish, 1300);
    });
  }

  if (form) {
    var currentStep = 1;
    var stepPanels = Array.prototype.slice.call(form.querySelectorAll("[data-form-step]"));
    var progressSteps = Array.prototype.slice.call(form.querySelectorAll("[data-form-progress]"));
    var progressLine = form.querySelector("[data-form-progress-line]");
    var nextButton = form.querySelector("[data-form-next]");
    var backButton = form.querySelector("[data-form-back]");
    var stepOneFields = Array.prototype.slice.call(form.querySelectorAll("[name='location'], [name='property_type'], [name='sqft'], [name='budget']"));
    var stepTwoFields = Array.prototype.slice.call(form.querySelectorAll("[name='name'], [name='phone'], [name='email']"));

    function setStep(step) {
      currentStep = step;

      stepPanels.forEach(function (panel) {
        var panelStep = Number(panel.getAttribute("data-form-step"));
        var isActive = panelStep === step;
        panel.classList.toggle("is-active", isActive);
        panel.hidden = !isActive;
      });

      progressSteps.forEach(function (item) {
        var itemStep = Number(item.getAttribute("data-form-progress"));
        item.classList.toggle("is-active", itemStep === step);
      });

      if (progressLine) {
        progressLine.style.width = step === 2 ? "100%" : "0";
      }
    }

    function validateFields(fields) {
      for (var i = 0; i < fields.length; i += 1) {
        var field = fields[i];
        if (!field || field.disabled || field.hidden) continue;
        if (!field.checkValidity()) {
          playFeedbackAudio("invalid", 0);
          field.reportValidity();
          field.focus();
          return false;
        }
      }

      return true;
    }

    if (nextButton) {
      nextButton.addEventListener("click", async function () {
        if (!validateFields(stepOneFields)) return;
        await playFeedbackAudio("success", 220);
        setStep(2);
      });
    }

    if (backButton) {
      backButton.addEventListener("click", function () {
        setStep(1);
      });
    }

    setStep(1);

    form.addEventListener("submit", async function (event) {
      event.preventDefault();

      if (!validateFields(stepTwoFields)) return;

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

      await trackLeadConversion();
      await playFeedbackAudio("success", 950);
      window.location.href = whatsappURL;
    });
  }

  var careerForm = document.querySelector("[data-career-form]");
  var bidProjectForm = document.querySelector("[data-bid-project-form]");
  var creativesForm = document.querySelector("[data-izin-creatives-form]");

  var creativesMobileSections = Array.prototype.slice.call(document.querySelectorAll("[data-mobile-collapsible]"));
  var creativesMobileQuery = window.matchMedia("(max-width: 820px)");

  function syncCreativesMobileSections(event) {
    if (!event.matches) {
      creativesMobileSections.forEach(function (section) {
        section.open = true;
      });
      return;
    }

    creativesMobileSections.forEach(function (section) {
      section.open = false;
    });
  }

  if (creativesMobileSections.length) {
    syncCreativesMobileSections(creativesMobileQuery);
    if (creativesMobileQuery.addEventListener) {
      creativesMobileQuery.addEventListener("change", syncCreativesMobileSections);
    } else {
      creativesMobileQuery.addListener(syncCreativesMobileSections);
    }

    Array.prototype.slice.call(document.querySelectorAll(".creatives-mobile-index a")).forEach(function (link) {
      link.addEventListener("click", function () {
        var target = document.querySelector(link.getAttribute("href"));
        var accordion = target ? target.querySelector("[data-mobile-collapsible]") : null;
        if (accordion && creativesMobileQuery.matches) {
          accordion.open = true;
        }
      });
    });
  }

  if (careerForm) {
    careerForm.addEventListener("submit", async function (event) {
      event.preventDefault();

      var status = careerForm.querySelector("[data-career-status]");
      var submitButton = careerForm.querySelector(".career-submit");
      var data = new FormData(careerForm);
      data.append("source_url", window.location.href);

      if (status) {
        status.textContent = "";
        status.classList.remove("is-error", "is-success");
      }

      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "Submitting...";
      }

      try {
        var response = await fetch("/wp-json/izin-careers/v1/apply", {
          method: "POST",
          body: data
        });
        var result = await response.json().catch(function () { return {}; });

        if (!response.ok || !result.success) {
          throw new Error(result.message || "Application could not be submitted.");
        }

        careerForm.reset();
        if (status) {
          status.textContent = "Application submitted. Our team will review your profile.";
          status.classList.add("is-success");
        }
      } catch (error) {
        if (status) {
          status.textContent = error.message || "Application could not be submitted.";
          status.classList.add("is-error");
        }
      } finally {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = "Submit Application";
        }
      }
    });
  }

  if (bidProjectForm) {
    bidProjectForm.addEventListener("submit", async function (event) {
      event.preventDefault();

      var status = bidProjectForm.querySelector("[data-bid-project-status]");
      var submitButton = bidProjectForm.querySelector(".career-submit");
      var data = new FormData(bidProjectForm);
      var tracking = getLeadTrackingData();

      data.append("source_url", window.location.href);
      Object.keys(tracking).forEach(function (key) {
        data.append(key, tracking[key]);
      });

      if (status) {
        status.textContent = "";
        status.classList.remove("is-error", "is-success");
      }

      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "Submitting...";
      }

      try {
        var projectResponse = await fetch("/wp-json/izin-projects/v1/submit", {
          method: "POST",
          body: data
        });
        var projectResult = await projectResponse.json().catch(function () { return {}; });

        if (!projectResponse.ok || !projectResult.success) {
          throw new Error(projectResult.message || "Project request could not be submitted.");
        }

        bidProjectForm.reset();
        if (status) {
          status.textContent = "Project request submitted. The Izin team will review your details and get back to you with the next steps.";
          status.classList.add("is-success");
        }
      } catch (error) {
        if (status) {
          status.textContent = error.message || "Project request could not be submitted.";
          status.classList.add("is-error");
        }
      } finally {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = "Submit Project Request";
        }
      }
    });
  }

  if (creativesForm) {
    var creativesStages = Array.prototype.slice.call(creativesForm.querySelectorAll("[data-form-stage]"));
    var creativesIndicators = Array.prototype.slice.call(creativesForm.querySelectorAll("[data-step-indicator]"));
    var creativesNextButton = creativesForm.querySelector("[data-step-next]");
    var creativesBackButton = creativesForm.querySelector("[data-step-back]");
    var creativesStepStatus = creativesForm.querySelector("[data-izin-creatives-status]");
    var creativesCurrentStep = 1;

    function setCreativesStep(step) {
      creativesCurrentStep = step;

      creativesStages.forEach(function (stage) {
        var stageNumber = Number(stage.getAttribute("data-form-stage"));
        var isActive = stageNumber === step;
        stage.hidden = !isActive;
        stage.classList.toggle("is-active", isActive);
      });

      creativesIndicators.forEach(function (indicator) {
        var indicatorStep = Number(indicator.getAttribute("data-step-indicator"));
        indicator.classList.toggle("is-active", indicatorStep === step);
        indicator.classList.toggle("is-complete", indicatorStep < step);
        if (indicatorStep === step) {
          indicator.setAttribute("aria-current", "step");
        } else {
          indicator.removeAttribute("aria-current");
        }
      });
    }

    function validateCreativesStep(step) {
      var requiredFields = Array.prototype.slice.call(creativesForm.querySelectorAll("[data-step-required='" + step + "']"));

      for (var index = 0; index < requiredFields.length; index += 1) {
        var field = requiredFields[index];
        if (!field.checkValidity()) {
          field.reportValidity();
          return false;
        }
      }

      return true;
    }

    if (creativesNextButton) {
      creativesNextButton.addEventListener("click", function () {
        if (!validateCreativesStep(1)) {
          if (creativesStepStatus) {
            creativesStepStatus.textContent = "Complete the required fields before moving to contact details.";
            creativesStepStatus.classList.remove("is-success");
            creativesStepStatus.classList.add("is-error");
          }
          return;
        }

        if (creativesStepStatus) {
          creativesStepStatus.textContent = "";
          creativesStepStatus.classList.remove("is-error", "is-success");
        }

        setCreativesStep(2);
      });
    }

    if (creativesBackButton) {
      creativesBackButton.addEventListener("click", function () {
        if (creativesStepStatus) {
          creativesStepStatus.textContent = "";
          creativesStepStatus.classList.remove("is-error", "is-success");
        }
        setCreativesStep(1);
      });
    }

    setCreativesStep(1);

    creativesForm.addEventListener("submit", async function (event) {
      event.preventDefault();

      if (!validateCreativesStep(2)) {
        if (creativesStepStatus) {
          creativesStepStatus.textContent = "Complete the required contact fields before submitting.";
          creativesStepStatus.classList.remove("is-success");
          creativesStepStatus.classList.add("is-error");
        }
        return;
      }

      var status = creativesStepStatus;
      var submitButton = creativesForm.querySelector(".career-submit");
      var data = new FormData(creativesForm);

      data.append("source_url", window.location.href);

      if (status) {
        status.textContent = "";
        status.classList.remove("is-error", "is-success");
      }

      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "Submitting...";
      }

      try {
        var response = await fetch("/wp-json/izin-creatives/v1/enquiry", {
          method: "POST",
          body: data
        });
        var result = await response.json().catch(function () { return {}; });

        if (!response.ok || !result.success) {
          throw new Error(result.message || "Requirement could not be submitted.");
        }

        creativesForm.reset();
        setCreativesStep(1);
        if (status) {
          status.textContent = "Requirement submitted. IZIN Creatives will review and contact you.";
          status.classList.add("is-success");
        }
      } catch (error) {
        if (status) {
          status.textContent = error.message || "Requirement could not be submitted.";
          status.classList.add("is-error");
        }
      } finally {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = "Submit Requirement";
        }
      }
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

  var packageGallery = document.querySelector("[data-package-gallery]");
  if (packageGallery) {
    var packageSlides = Array.prototype.slice.call(packageGallery.querySelectorAll("[data-package-slide]"));
    var packageDetails = Array.prototype.slice.call(packageGallery.querySelectorAll("[data-package-detail]"));
    var packageThumbs = Array.prototype.slice.call(packageGallery.querySelectorAll("[data-package-thumb]"));
    var packagePrev = packageGallery.querySelector("[data-package-prev]");
    var packageNext = packageGallery.querySelector("[data-package-next]");
    var packageIndex = packageSlides.findIndex(function (slide) { return slide.classList.contains("is-active"); });

    if (packageIndex < 0) packageIndex = 0;

    function setPackageSlide(nextIndex) {
      packageIndex = (nextIndex + packageSlides.length) % packageSlides.length;

      packageSlides.forEach(function (slide, index) {
        slide.classList.toggle("is-active", index === packageIndex);
      });

      packageDetails.forEach(function (detail, index) {
        detail.classList.toggle("is-active", index === packageIndex);
      });

      packageThumbs.forEach(function (thumb, index) {
        thumb.classList.toggle("is-active", index === packageIndex);
      });
    }

    if (packagePrev) {
      packagePrev.addEventListener("click", function () {
        setPackageSlide(packageIndex - 1);
      });
    }

    if (packageNext) {
      packageNext.addEventListener("click", function () {
        setPackageSlide(packageIndex + 1);
      });
    }

    packageThumbs.forEach(function (thumb, index) {
      thumb.addEventListener("click", function () {
        setPackageSlide(index);
      });
    });
  }
});
