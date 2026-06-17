<?php
/**
 * Author archive template.
 */

get_header();
get_template_part('template-parts/site-nav');

$author = get_queried_object();
?>

<main class="archive-main">
  <section class="archive-shell">
    <?php izin_designs_render_breadcrumbs(); ?>
    <header class="archive-header archive-header-author">
      <span><?php esc_html_e('Author', 'izin-designs-theme'); ?></span>
      <h1><?php echo esc_html($author->display_name ?? ''); ?></h1>
      <?php if (!empty($author->description)) : ?>
        <div class="archive-description"><?php echo wp_kses_post(wpautop($author->description)); ?></div>
      <?php endif; ?>
    </header>

    <?php if (have_posts()) : ?>
      <div class="post-grid">
        <?php while (have_posts()) : the_post(); ?>
          <?php get_template_part('template-parts/content', 'card'); ?>
        <?php endwhile; ?>
      </div>

      <div class="archive-pagination">
        <?php the_posts_pagination(); ?>
      </div>
    <?php else : ?>
      <p class="archive-empty"><?php esc_html_e('No articles from this author yet.', 'izin-designs-theme'); ?></p>
    <?php endif; ?>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
