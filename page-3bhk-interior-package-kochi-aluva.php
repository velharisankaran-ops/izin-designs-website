<?php
/**
 * Package landing page template.
 */

get_header();
get_template_part('template-parts/site-nav');
?>

<main class="izin-package-page">
  <section class="izin-package-hero-shell">
    <div class="izin-package-hero-copy">
      <?php izin_designs_render_breadcrumbs(); ?>
      <span class="izin-package-eyebrow">3BHK Package</span>
      <h1>3BHK Interior Package in Kochi &amp; Aluva for ₹4,99,999</h1>
      <h2>Complete 3BHK Home Interiors for Modern Kerala Homes</h2>
      <p>Transform your 3BHK home with a practical and elegant interior package designed for Kerala homes. Izin Designs Interior Studio offers a complete 3BHK interior package for <strong>₹4,99,999</strong>, covering your kitchen, master bedroom, two bedrooms and living area.</p>
      <p>This package is suitable for apartments, villas and newly built homes in <strong>Kochi, Aluva, Ernakulam and nearby areas</strong>.</p>

      <div class="izin-package-hero-points">
        <div><span>Package Price</span><strong>₹4,99,999</strong></div>
        <div><span>Includes</span><strong>Kitchen + Master Bedroom + 2 Bedrooms + Living Area</strong></div>
        <div><span>Special Feature</span><strong>WPC Kitchen Included</strong></div>
      </div>

      <a class="izin-package-cta" href="<?php echo esc_url(home_url('/')); ?>#consultation">Book your free consultation today</a>
    </div>

    <div class="izin-package-hero-media">
      <img src="https://izindesigns.com/wp-content/uploads/2026/06/Cover-Page.png" alt="3BHK interior package brochure cover by Izin Designs" loading="eager">
    </div>
  </section>

  <section class="izin-package-page-section">
    <div class="izin-package-page-head">
      <small>Package Includes</small>
      <h2>3BHK Interior Package for ₹4,99,999</h2>
      <p>Izin Designs Interior Studio offers a complete 3BHK interior package for modern Kerala homes. The package includes a WPC kitchen, master bedroom, two bedrooms and living area interiors.</p>
    </div>

    <div class="izin-package-includes-grid">
      <article class="izin-package-include-card">
        <h3>Kitchen</h3>
        <p>WPC Kitchen, Kitchen Accessories, Hood and Hob</p>
      </article>
      <article class="izin-package-include-card">
        <h3>Master Bedroom</h3>
        <p>Queen Size Cot, 3 Door Wardrobe, Dressing Unit</p>
      </article>
      <article class="izin-package-include-card">
        <h3>Bedroom 1</h3>
        <p>Queen Size Cot, 3 Door Wardrobe</p>
      </article>
      <article class="izin-package-include-card">
        <h3>Bedroom 2</h3>
        <p>Queen Size Cot, 3 Door Wardrobe</p>
      </article>
      <article class="izin-package-include-card">
        <h3>Living Area</h3>
        <p>5 Seater Sofa, TV Unit, Centre Table</p>
      </article>
    </div>
  </section>

  <section class="izin-package-page-section izin-package-brochure-section">
    <div class="izin-package-page-head">
      <small>Brochure</small>
      <h2>Package brochure pages</h2>
    </div>

    <div class="izin-package-slider" aria-label="3BHK package brochure slider">
      <article class="izin-package-card">
        <img src="https://izindesigns.com/wp-content/uploads/2026/06/Cover-Page.png" alt="3BHK package brochure cover page" loading="lazy">
      </article>
      <article class="izin-package-card">
        <img src="https://izindesigns.com/wp-content/uploads/2026/06/Kicthen.png" alt="3BHK package brochure kitchen page" loading="lazy">
      </article>
      <article class="izin-package-card">
        <img src="https://izindesigns.com/wp-content/uploads/2026/06/Master-Bedroom.png" alt="3BHK package brochure master bedroom page" loading="lazy">
      </article>
      <article class="izin-package-card">
        <img src="https://izindesigns.com/wp-content/uploads/2026/06/Bedroom-1.png" alt="3BHK package brochure bedroom one page" loading="lazy">
      </article>
      <article class="izin-package-card">
        <img src="https://izindesigns.com/wp-content/uploads/2026/06/Bedroom-2.png" alt="3BHK package brochure bedroom two page" loading="lazy">
      </article>
      <article class="izin-package-card">
        <img src="https://izindesigns.com/wp-content/uploads/2026/06/Living-Area.png" alt="3BHK package brochure living area page" loading="lazy">
      </article>
    </div>
  </section>

  <section class="izin-package-page-section izin-package-faq-section">
    <div class="izin-package-page-head">
      <small>FAQ</small>
      <h2>Common question</h2>
    </div>

    <article class="izin-package-faq-card">
      <h3>What is the price of the 3BHK interior package?</h3>
      <p>The 3BHK interior package price is <strong>₹4,99,999</strong>.</p>
    </article>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
