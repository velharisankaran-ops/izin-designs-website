<?php
/**
 * Simple breadcrumb fallback.
 */

if (is_front_page()) {
    return;
}
?>
<nav class="izin-breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumbs', 'izin-designs-theme'); ?>">
  <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'izin-designs-theme'); ?></a>

  <?php if (is_category()) : ?>
    <?php $category = get_queried_object(); ?>
    <?php if ($category instanceof WP_Term) : ?>
      <span aria-hidden="true">/</span>
      <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
        <?php echo esc_html($category->name); ?>
      </a>
    <?php endif; ?>
  <?php elseif (is_single()) : ?>
    <?php $categories = get_the_category(); ?>
    <?php if (!empty($categories)) : ?>
      <span aria-hidden="true">/</span>
      <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
        <?php echo esc_html($categories[0]->name); ?>
      </a>
    <?php endif; ?>
  <?php endif; ?>

  <span aria-hidden="true">/</span>
  <span><?php echo esc_html(wp_get_document_title()); ?></span>
</nav>
