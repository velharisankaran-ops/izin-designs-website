<?php
$consultation_cta_url = function_exists('izin_designs_section_url') ? izin_designs_section_url('consultation') : esc_url(home_url('/')) . '#consultation';
$is_creatives_page = function_exists('is_page') && is_page('izin-creatives');

if ($is_creatives_page) {
  $consultation_cta_url = '#creatives-form';
}

$brand_url = $is_creatives_page
  ? 'https://hub.izindesigns.com/'
  : (function_exists('izin_designs_section_url') ? izin_designs_section_url('home') : esc_url(home_url('/')) . '#home');
?>

<header class="site-header" data-site-header>
  <a class="brand" href="<?php echo esc_url($brand_url); ?>" aria-label="<?php echo esc_attr($is_creatives_page ? 'Izin Group' : 'Izin Designs Interior Studio'); ?>">
    <img src="https://izindesigns.com/wp-content/uploads/2026/05/cropped-Izin-Design-Interior-Studio-1-63x64.png" alt="<?php echo esc_attr($is_creatives_page ? 'Izin Group' : 'Izin Designs Interior Studio'); ?>">
    <span>
      <strong><?php echo esc_html($is_creatives_page ? 'Izin' : 'Izin Designs'); ?></strong>
      <small><?php echo esc_html($is_creatives_page ? 'Group' : 'Interior Studio'); ?></small>
    </span>
  </a>

  <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle>
    <span></span>
    <span></span>
    <span></span>
    <span class="sr-only"><?php esc_html_e('Menu', 'izin-designs-theme'); ?></span>
  </button>

  <nav class="nav" id="primary-nav" data-primary-nav>
    <a href="https://hub.izindesigns.com/"><?php esc_html_e('Izin Group', 'izin-designs-theme'); ?></a>
    <a class="nav-cta" href="<?php echo esc_url($consultation_cta_url); ?>"><?php esc_html_e('Free Consultation', 'izin-designs-theme'); ?></a>
  </nav>
</header>
