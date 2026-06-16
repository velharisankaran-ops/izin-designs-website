<?php
/**
 * Career page template.
 *
 * Template Name: Izin Careers
 */

get_header();
?>

<?php get_template_part('template-parts/site-nav'); ?>

<main class="career-page">
  <section class="career-hero">
    <div class="career-hero-copy">
      <span class="izin-small-label">Careers</span>
      <h1>Build thoughtful interiors with Izin Designs.</h1>
      <p>Join our interior studio in Kochi and work across residential, commercial, modular kitchen, bespoke furniture and turnkey execution projects.</p>
    </div>

    <form class="career-form-card" data-career-form enctype="multipart/form-data">
      <input class="izin-honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">

      <div class="career-form-head">
        <small>Apply Now</small>
        <h2>Submit your profile</h2>
      </div>

      <div class="career-form-grid">
        <label>
          <span>Full Name *</span>
          <input type="text" name="full_name" autocomplete="name" required>
        </label>

        <label>
          <span>Phone *</span>
          <input type="tel" name="phone" autocomplete="tel" required>
        </label>
      </div>

      <div class="career-form-grid">
        <label>
          <span>Email</span>
          <input type="email" name="email" autocomplete="email">
        </label>

        <label>
          <span>Applying For *</span>
          <select name="position" required>
            <option value="">Select role</option>
            <option>Interior Designer</option>
            <option>Site Supervisor</option>
            <option>3D Visualizer</option>
            <option>Modular Kitchen Designer</option>
            <option>Sales / Client Coordinator</option>
            <option>Internship</option>
            <option>Other</option>
          </select>
        </label>
      </div>

      <div class="career-form-grid">
        <label>
          <span>Experience</span>
          <select name="experience">
            <option value="">Select</option>
            <option>Fresher</option>
            <option>0 - 1 year</option>
            <option>1 - 3 years</option>
            <option>3 - 5 years</option>
            <option>5+ years</option>
          </select>
        </label>

        <label>
          <span>Current Location</span>
          <input type="text" name="location" autocomplete="address-level2">
        </label>
      </div>

      <label>
        <span>Portfolio Link</span>
        <input type="url" name="portfolio_url" placeholder="https://">
      </label>

      <label>
        <span>Resume / Portfolio File *</span>
        <input type="file" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
        <small>PDF, DOC or DOCX. Max 5 MB.</small>
      </label>

      <label>
        <span>Tell us about your work</span>
        <textarea name="message" rows="4" placeholder="Share your skills, project experience or role preference."></textarea>
      </label>

      <button class="career-submit" type="submit">Submit Application</button>
      <p class="career-form-status" data-career-status aria-live="polite"></p>
    </form>
  </section>

  <section class="career-roles">
    <div>
      <small>Open Profiles</small>
      <h2>We are interested in practical, detail-focused people.</h2>
    </div>

    <div class="career-role-grid">
      <article>
        <h3>Interior Design</h3>
        <p>Space planning, client presentation, material selection and execution-ready detailing.</p>
      </article>
      <article>
        <h3>Site & Execution</h3>
        <p>Site coordination, vendor follow-up, quality checks and practical project delivery.</p>
      </article>
      <article>
        <h3>3D & Visualization</h3>
        <p>Interior views, presentation renders and visual support for residential and commercial projects.</p>
      </article>
    </div>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>

<?php
get_footer();
