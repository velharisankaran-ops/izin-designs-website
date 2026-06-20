<?php
/**
 * Bid Project page template.
 */

get_header();
?>

<?php get_template_part('template-parts/site-nav'); ?>

<main class="career-page bid-project-page">
  <section class="career-hero bid-project-hero">
    <div class="career-hero-copy bid-project-copy">
      <span class="izin-small-label">Bid Project</span>
      <h1>Share your project scope and request a quotation from Izin Designs.</h1>
      <p>Tell us about your home, commercial space, renovation or turnkey requirement. Our team will review the details, check the floor plan or references you share, and get back with the next steps.</p>

      <div class="bid-project-notes" aria-label="How the quotation request works">
        <article>
          <h2>What to prepare</h2>
          <p>Property size, expected timeline, budget range and any floor plans or room references that help us review faster.</p>
        </article>
        <article>
          <h2>What happens next</h2>
          <p>Your request is saved in the Izin dashboard and sent to the team for review before quotation follow-up.</p>
        </article>
      </div>
    </div>

    <form class="career-form-card bid-project-form-card" data-bid-project-form enctype="multipart/form-data">
      <input class="izin-honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">

      <div class="career-form-head">
        <small>Project Enquiry</small>
        <h2>Request your quotation</h2>
      </div>

      <div class="career-form-grid">
        <label>
          <span>Name *</span>
          <input type="text" name="client_name" autocomplete="name" required>
        </label>

        <label>
          <span>Phone *</span>
          <input type="tel" name="phone" autocomplete="tel" required>
        </label>
      </div>

      <div class="career-form-grid">
        <label>
          <span>Email *</span>
          <input type="email" name="email" autocomplete="email" required>
        </label>

        <label>
          <span>Project Type *</span>
          <select name="project_type" required>
            <option value="">Select project type</option>
            <option>Interior Design</option>
            <option>Turnkey Project</option>
            <option>Renovation</option>
            <option>Commercial Space</option>
          </select>
        </label>
      </div>

      <div class="career-form-grid">
        <label>
          <span>Location *</span>
          <input type="text" name="location" autocomplete="address-level2" required>
        </label>

        <label>
          <span>Budget Range *</span>
          <select name="budget_range" required>
            <option value="">Select budget</option>
            <option>Below Rs 5 Lakh</option>
            <option>Rs 5 Lakh - Rs 10 Lakh</option>
            <option>Rs 10 Lakh - Rs 20 Lakh</option>
            <option>Rs 20 Lakh - Rs 35 Lakh</option>
            <option>Rs 35 Lakh+</option>
          </select>
        </label>
      </div>

      <div class="career-form-grid">
        <label>
          <span>Property Size (Sq.Ft) *</span>
          <input type="text" name="property_size_sqft" inputmode="numeric" required>
        </label>

        <label>
          <span>Expected Start Date *</span>
          <input type="date" name="expected_start_date" required>
        </label>
      </div>

      <label>
        <span>Upload Floor Plan / Images</span>
        <input type="file" name="attachments[]" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,image/jpeg,image/png,image/webp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple>
        <small>JPG, PNG, WEBP, PDF, DOC or DOCX. Up to 5 files, 8 MB each.</small>
      </label>

      <label>
        <span>Project Description *</span>
        <textarea name="project_description" rows="5" placeholder="Share the rooms involved, project scope, current site stage, preferred style, materials, and anything important for the quotation." required></textarea>
      </label>

      <button class="career-submit" type="submit">Submit Project Request</button>
      <p class="career-form-status" data-bid-project-status aria-live="polite"></p>
    </form>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>

<?php
get_footer();
