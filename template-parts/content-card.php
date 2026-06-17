<?php
/**
 * Reusable post card.
 */

$featured_id = get_post_thumbnail_id();
$primary_category = izin_designs_get_primary_term_name();
?>
<article <?php post_class('post-card'); ?>>
  <a class="post-card-link" href="<?php the_permalink(); ?>">
    <div class="post-card-media">
      <?php if ($featured_id) : ?>
        <?php echo wp_get_attachment_image($featured_id, 'izin-card', false, array('loading' => 'lazy', 'alt' => izin_designs_get_post_image_alt($featured_id, get_the_title()))); ?>
      <?php else : ?>
        <span class="post-card-placeholder"><?php esc_html_e('IZIN Insights', 'izin-designs-theme'); ?></span>
      <?php endif; ?>
    </div>
    <div class="post-card-body">
      <?php if ($primary_category) : ?>
        <span class="post-card-category"><?php echo esc_html($primary_category); ?></span>
      <?php endif; ?>
      <h2 class="post-card-title"><?php the_title(); ?></h2>
      <p class="post-card-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
      <div class="post-card-meta">
        <span><?php echo esc_html(get_the_date()); ?></span>
        <span><?php esc_html_e('Read More', 'izin-designs-theme'); ?></span>
      </div>
    </div>
  </a>
</article>
