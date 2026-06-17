<?php
/**
 * Standard page template.
 */

get_header();
get_template_part('template-parts/site-nav');
?>

<main class="page-main">
  <?php while (have_posts()) : the_post(); ?>
    <?php $featured_id = get_post_thumbnail_id(); ?>
    <article <?php post_class('page-article'); ?>>
      <div class="post-shell">
        <?php izin_designs_render_breadcrumbs(); ?>
        <header class="page-header">
          <h1><?php the_title(); ?></h1>
        </header>

        <?php if (has_post_thumbnail()) : ?>
          <figure class="page-featured-image">
            <?php echo wp_get_attachment_image($featured_id, 'izin-featured-large', false, array('loading' => 'eager', 'alt' => izin_designs_get_post_image_alt($featured_id, get_the_title()))); ?>
          </figure>
        <?php endif; ?>

        <section class="page-content">
          <?php the_content(); ?>
          <?php wp_link_pages(); ?>
        </section>
      </div>
    </article>
  <?php endwhile; ?>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
