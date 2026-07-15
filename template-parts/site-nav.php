<?php
$consultation_cta_url = function_exists('izin_designs_section_url') ? izin_designs_section_url('consultation') : esc_url(home_url('/')) . '#consultation';
$is_creatives_page = function_exists('is_page') && is_page('izin-creatives');
?>

<header class="site-header" data-site-header>
  <a class="brand" href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('home')) : esc_url(home_url('/')) . '#home'; ?>" aria-label="<?php esc_attr_e('Izin Designs Interior Studio', 'izin-designs-theme'); ?>">
    <img src="https://izindesigns.com/wp-content/uploads/2026/05/cropped-Izin-Design-Interior-Studio-1-63x64.png" alt="<?php esc_attr_e('Izin Designs Interior Studio', 'izin-designs-theme'); ?>">
    <span>
      <strong><?php echo esc_html($is_creatives_page ? 'izin' : 'Izin Designs'); ?></strong>
      <small><?php echo esc_html($is_creatives_page ? 'CREATIVES' : 'Interior Studio'); ?></small>
    </span>
  </a>

  <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle>
    <span></span>
    <span></span>
    <span></span>
    <span class="sr-only"><?php esc_html_e('Menu', 'izin-designs-theme'); ?></span>
  </button>

  <nav class="nav" id="primary-nav" data-primary-nav>
    <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('home')) : esc_url(home_url('/')) . '#home'; ?>"><?php esc_html_e('Home', 'izin-designs-theme'); ?></a>
    <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('services')) : esc_url(home_url('/')) . '#services'; ?>"><?php esc_html_e('Services', 'izin-designs-theme'); ?></a>
    <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('gallery')) : esc_url(home_url('/')) . '#gallery'; ?>"><?php esc_html_e('Gallery', 'izin-designs-theme'); ?></a>
    <div class="nav-dropdown" data-nav-dropdown>
      <button class="nav-dropdown-toggle" type="button" aria-expanded="false" data-nav-dropdown-toggle>
        <?php esc_html_e('Location', 'izin-designs-theme'); ?>
      </button>
      <div class="nav-dropdown-menu" data-nav-dropdown-menu>
        <?php foreach (izin_designs_location_menu_items() as $location) : ?>
          <span><?php echo esc_html($location); ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('bespoke')) : esc_url(home_url('/')) . '#bespoke'; ?>"><?php esc_html_e('Bespoke', 'izin-designs-theme'); ?></a>
    <a href="<?php echo esc_url(home_url('/izin-creatives/')); ?>"><?php esc_html_e('Creatives', 'izin-designs-theme'); ?></a>
    <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('contact')) : esc_url(home_url('/')) . '#contact'; ?>"><?php esc_html_e('Contact', 'izin-designs-theme'); ?></a>
    <a href="<?php echo esc_url(home_url('/career/')); ?>"><?php esc_html_e('Career', 'izin-designs-theme'); ?></a>
    <a class="nav-cta" href="<?php echo esc_url($consultation_cta_url); ?>"><?php esc_html_e('Free Consultation', 'izin-designs-theme'); ?></a>
  </nav>
</header>
