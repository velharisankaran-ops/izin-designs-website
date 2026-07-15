<?php
/**
 * IZIN group hub.
 *
 * Template Name: IZIN Hub
 */

get_header();

$hub_divisions = array(
    array(
        'name'        => 'IZIN Interiors',
        'description' => 'Residential, commercial and turnkey interior environments.',
        'image'       => 'izin-interiors.jpg',
        'url'         => home_url('/'),
        'status'      => '',
    ),
    array(
        'name'        => 'IZIN Creatives',
        'description' => 'Graphic design, digital marketing and web development.',
        'image'       => 'izin-creatives.jpg',
        'url'         => home_url('/izin-creatives/'),
        'status'      => '',
    ),
    array(
        'name'        => 'Raion Global Venture',
        'description' => 'Strategic material sourcing and global business operations.',
        'image'       => 'raion-global.jpg',
        'url'         => '',
        'status'      => 'Coming soon',
    ),
);
?>

<header class="izin-hub-header">
  <a class="izin-hub-wordmark" href="#izin-hub-top" aria-label="IZIN Hub home">IZIN</a>
  <a class="izin-hub-header-action" href="#izin-hub-consultation" aria-label="Request a consultation">
    <span class="material-symbols-outlined" aria-hidden="true">chat_bubble</span>
  </a>
</header>

<main class="izin-hub" id="izin-hub-top">
  <section class="izin-hub-hero" aria-labelledby="izin-hub-title">
    <p class="izin-hub-eyebrow">IZIN Group</p>
    <h1 id="izin-hub-title">Welcome to IZIN.</h1>
    <p>One group connecting interior environments, creative services and emerging ventures.</p>
  </section>

  <div class="izin-hub-layout">
    <section class="izin-hub-divisions" id="izin-hub-divisions" aria-labelledby="izin-hub-divisions-title">
      <div class="izin-hub-section-head">
        <p>Our divisions</p>
        <h2 id="izin-hub-divisions-title">Choose where you want to go</h2>
      </div>

      <div class="izin-hub-division-list">
        <?php foreach ($hub_divisions as $division) : ?>
          <?php if ($division['url'] !== '') : ?>
            <a class="izin-hub-division" href="<?php echo esc_url($division['url']); ?>">
          <?php else : ?>
            <article class="izin-hub-division is-upcoming" aria-disabled="true">
          <?php endif; ?>
              <img src="<?php echo esc_url(get_template_directory_uri() . '/frontend/assets/hub/' . $division['image']); ?>" alt="" width="640" height="480" loading="<?php echo $division['name'] === 'IZIN Interiors' ? 'eager' : 'lazy'; ?>" decoding="async">
              <span class="izin-hub-division-copy">
                <strong><?php echo esc_html($division['name']); ?></strong>
                <small><?php echo esc_html($division['description']); ?></small>
                <?php if ($division['status'] !== '') : ?>
                  <em><?php echo esc_html($division['status']); ?></em>
                <?php endif; ?>
              </span>
              <span class="material-symbols-outlined izin-hub-division-arrow" aria-hidden="true"><?php echo $division['url'] !== '' ? 'arrow_forward' : 'schedule'; ?></span>
          <?php if ($division['url'] !== '') : ?>
            </a>
          <?php else : ?>
            </article>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="izin-hub-consultation" id="izin-hub-consultation" aria-labelledby="izin-hub-consultation-title">
      <p class="izin-hub-eyebrow">Start a conversation</p>
      <h2 id="izin-hub-consultation-title">Book a Consultation</h2>
      <p>Choose the division you need and our team will contact you.</p>

      <form class="izin-hub-form" data-lead-form>
        <input type="hidden" name="budget" value="IZIN Hub enquiry">
        <label>
          <span>Your Name</span>
          <input type="text" name="name" autocomplete="name" required>
        </label>
        <label>
          <span>Phone / WhatsApp</span>
          <input type="tel" name="phone" autocomplete="tel" inputmode="tel" required>
        </label>
        <label>
          <span>Service Interest</span>
          <select name="property_type" required>
            <option value="">Select a division</option>
            <?php foreach ($hub_divisions as $division) : ?>
              <option value="<?php echo esc_attr($division['name']); ?>"><?php echo esc_html($division['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <button class="izin-submit" type="submit">Request Call</button>
      </form>
    </section>
  </div>
</main>

<footer class="izin-hub-footer">
  <strong>IZIN</strong>
  <span>Interiors · Creatives · Ventures</span>
</footer>

<nav class="izin-hub-bottom-nav" aria-label="IZIN Hub navigation">
  <a href="#izin-hub-top"><span class="material-symbols-outlined" aria-hidden="true">home</span><small>Home</small></a>
  <a href="#izin-hub-divisions"><span class="material-symbols-outlined" aria-hidden="true">grid_view</span><small>Divisions</small></a>
  <a href="#izin-hub-consultation"><span class="material-symbols-outlined" aria-hidden="true">chat_bubble</span><small>Enquire</small></a>
  <a href="<?php echo esc_url(home_url('/')); ?>"><span class="material-symbols-outlined" aria-hidden="true">chair</span><small>Interiors</small></a>
</nav>

<?php get_footer(); ?>
