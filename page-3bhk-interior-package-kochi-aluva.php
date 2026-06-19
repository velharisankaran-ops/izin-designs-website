<?php
/**
 * Package landing page template.
 */

$package_slides = array(
    array(
        'title'   => '3BHK Package Overview',
        'label'   => 'Overview',
        'image'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Cover-Page.png',
        'alt'     => '3BHK interior package brochure cover by Izin Designs',
        'summary' => 'A complete 3BHK package planned for modern Kerala homes in Kochi, Aluva and nearby areas.',
        'items'   => array(
            'Price: &#8377;4,99,999',
            'Includes kitchen, master bedroom, two bedrooms and living area',
            'Designed for apartments, villas and newly built homes',
        ),
    ),
    array(
        'title'   => 'Kitchen',
        'label'   => 'Space 01',
        'image'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Kicthen.png',
        'alt'     => '3BHK package kitchen page',
        'summary' => 'A WPC kitchen package designed for durability, easy maintenance and daily family use.',
        'items'   => array(
            'WPC kitchen base and overhead units',
            'Kitchen accessories package',
            'Hood and hob included',
        ),
    ),
    array(
        'title'   => 'Master Bedroom',
        'label'   => 'Space 02',
        'image'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Master-Bedroom.png',
        'alt'     => '3BHK package master bedroom page',
        'summary' => 'A complete master bedroom setup balancing storage, comfort and daily convenience.',
        'items'   => array(
            'Queen size cot',
            '3 door wardrobe',
            'Dressing unit',
        ),
    ),
    array(
        'title'   => 'Bedroom 1',
        'label'   => 'Space 03',
        'image'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Bedroom-1.png',
        'alt'     => '3BHK package bedroom one page',
        'summary' => 'A practical secondary bedroom setup with core furniture and storage included.',
        'items'   => array(
            'Queen size cot',
            '3 door wardrobe',
        ),
    ),
    array(
        'title'   => 'Bedroom 2',
        'label'   => 'Space 04',
        'image'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Bedroom-2.png',
        'alt'     => '3BHK package bedroom two page',
        'summary' => 'A matching additional bedroom layout for a family home or guest room.',
        'items'   => array(
            'Queen size cot',
            '3 door wardrobe',
        ),
    ),
    array(
        'title'   => 'Living Area',
        'label'   => 'Space 05',
        'image'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Living-Area.png',
        'alt'     => '3BHK package living area page',
        'summary' => 'A living room package focused on everyday comfort and a clean front-facing setup.',
        'items'   => array(
            '5 seater sofa',
            'TV unit',
            'Centre table',
        ),
    ),
);

get_header();
get_template_part('template-parts/site-nav');
?>

<main class="izin-package-page">
  <section class="izin-package-product" data-package-gallery>
    <div class="izin-package-product-media">
      <div class="izin-package-stage">
        <?php foreach ($package_slides as $index => $slide) : ?>
          <figure class="izin-package-stage-card<?php echo 0 === $index ? ' is-active' : ''; ?>" data-package-slide="<?php echo esc_attr($index); ?>">
            <img src="<?php echo esc_url($slide['image']); ?>" alt="<?php echo esc_attr($slide['alt']); ?>" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>">
          </figure>
        <?php endforeach; ?>

        <button class="izin-package-stage-control is-prev" type="button" aria-label="Previous package page" data-package-prev>
          <span aria-hidden="true">&#8249;</span>
        </button>
        <button class="izin-package-stage-control is-next" type="button" aria-label="Next package page" data-package-next>
          <span aria-hidden="true">&#8250;</span>
        </button>
      </div>

      <div class="izin-package-thumb-row" aria-label="Package brochure pages">
        <?php foreach ($package_slides as $index => $slide) : ?>
          <button class="izin-package-thumb<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button" data-package-thumb="<?php echo esc_attr($index); ?>" aria-label="<?php echo esc_attr($slide['title']); ?>">
            <img src="<?php echo esc_url($slide['image']); ?>" alt="" loading="lazy">
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="izin-package-product-info">
      <?php izin_designs_render_breadcrumbs(); ?>
      <span class="izin-package-eyebrow">3BHK Package</span>
      <h1>3BHK Interior Package in Kochi &amp; Aluva for &#8377;4,99,999</h1>
      <p class="izin-package-price">&#8377;4,99,999</p>
      <p class="izin-package-intro">Complete 3BHK home interiors for modern Kerala homes. Designed for apartments, villas and newly built homes across Kochi, Aluva, Ernakulam and nearby areas.</p>

      <div class="izin-package-product-meta">
        <div><span>Includes</span><strong>Kitchen + 3 Bedrooms + Living Area</strong></div>
        <div><span>Feature</span><strong>WPC Kitchen Included</strong></div>
        <div><span>Service Area</span><strong>Kochi, Aluva &amp; Ernakulam</strong></div>
      </div>

      <div class="izin-package-detail-card">
        <?php foreach ($package_slides as $index => $slide) : ?>
          <section class="izin-package-detail-pane<?php echo 0 === $index ? ' is-active' : ''; ?>" data-package-detail="<?php echo esc_attr($index); ?>">
            <small><?php echo esc_html($slide['label']); ?></small>
            <h2><?php echo esc_html($slide['title']); ?></h2>
            <p><?php echo esc_html($slide['summary']); ?></p>
            <ul>
              <?php foreach ($slide['items'] as $item) : ?>
                <li><?php echo esc_html($item); ?></li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endforeach; ?>
      </div>

      <a class="izin-package-cta" href="<?php echo esc_url(home_url('/')); ?>#consultation">Book your free consultation today</a>
    </div>
  </section>

  <section class="izin-package-page-section izin-package-faq-section">
    <div class="izin-package-page-head">
      <small>FAQ</small>
      <h2>Common question</h2>
    </div>

    <article class="izin-package-faq-card">
      <h3>What is the price of the 3BHK interior package?</h3>
      <p>The 3BHK interior package price is <strong>&#8377;4,99,999</strong>.</p>
    </article>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
