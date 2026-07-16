<?php $is_creatives_page = function_exists('is_page') && is_page('izin-creatives'); ?>

<footer class="site-footer<?php echo $is_creatives_page ? ' creatives-site-footer' : ''; ?>">
  <div class="site-footer-inner">
    <?php if ($is_creatives_page) : ?>
    <div class="site-footer-brand">
      <strong><?php esc_html_e('Izin Creatives', 'izin-designs-theme'); ?></strong>
      <p><?php esc_html_e('Graphic Design | Digital Marketing | Web Development', 'izin-designs-theme'); ?></p>
    </div>

    <nav class="site-footer-nav" aria-label="<?php esc_attr_e('Footer', 'izin-designs-theme'); ?>">
      <a href="https://hub.izindesigns.com/"><?php esc_html_e('Izin Group', 'izin-designs-theme'); ?></a>
      <a href="#creatives-form" data-creatives-form-link><?php esc_html_e('Free Consultation', 'izin-designs-theme'); ?></a>
    </nav>
    <?php else : ?>
    <div class="site-footer-brand">
      <strong><?php esc_html_e('IZIN Designs Interior Studio', 'izin-designs-theme'); ?></strong>
      <p><?php esc_html_e('Royalway Building, Near Muttom Metro Station, Choornikkara, Aluva, Kochi, Kerala', 'izin-designs-theme'); ?></p>
      <p>
        <a href="tel:+918714738111"><?php esc_html_e('+91 8714738111', 'izin-designs-theme'); ?></a>
        <span>|</span>
        <a href="mailto:info@izindesigns.com"><?php esc_html_e('info@izindesigns.com', 'izin-designs-theme'); ?></a>
      </p>
      <p class="site-footer-service-area"><?php esc_html_e('Interior Designers in Kochi, Aluva & Ernakulam', 'izin-designs-theme'); ?></p>
    </div>

    <nav class="site-footer-nav" aria-label="<?php esc_attr_e('Footer', 'izin-designs-theme'); ?>">
      <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('home')) : esc_url(home_url('/')) . '#home'; ?>"><?php esc_html_e('Home', 'izin-designs-theme'); ?></a>
      <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('services')) : esc_url(home_url('/')) . '#services'; ?>"><?php esc_html_e('Services', 'izin-designs-theme'); ?></a>
      <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('gallery')) : esc_url(home_url('/')) . '#gallery'; ?>"><?php esc_html_e('Gallery', 'izin-designs-theme'); ?></a>
      <a href="<?php echo function_exists('izin_designs_section_url') ? esc_url(izin_designs_section_url('contact')) : esc_url(home_url('/')) . '#contact'; ?>"><?php esc_html_e('Contact', 'izin-designs-theme'); ?></a>
      <a href="<?php echo esc_url(home_url('/career/')); ?>"><?php esc_html_e('Career', 'izin-designs-theme'); ?></a>
    </nav>
    <?php endif; ?>
  </div>
</footer>
