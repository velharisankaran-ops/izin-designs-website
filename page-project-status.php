<?php
/**
 * Project status page template.
 */

get_header();
get_template_part('template-parts/site-nav');

$token = sanitize_text_field(wp_unslash($_GET['token'] ?? ''));
$project = $token !== '' && function_exists('izin_projects_get_project_by_token') ? izin_projects_get_project_by_token($token) : null;
$has_access = $project && !empty($project->status_token);

if (!$has_access) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
}

$current_status = $has_access ? (string) $project->status : '';
$status_descriptions = function_exists('izin_projects_status_descriptions') ? izin_projects_status_descriptions() : array();
$steps = $has_access && function_exists('izin_projects_status_steps') ? izin_projects_status_steps($current_status) : array();
$status_description = $status_descriptions[$current_status] ?? '';
$show_quotation_files = $has_access && function_exists('izin_projects_should_show_quotation_files') ? izin_projects_should_show_quotation_files($current_status) : false;
$quotation_urls = $has_access ? izin_projects_decode_json_list($project->quotation_urls) : array();
$quotation_names = $has_access ? izin_projects_decode_json_list($project->quotation_names) : array();
?>

<main class="project-status-page">
  <section class="project-status-shell">
    <?php if (!$has_access) : ?>
      <div class="project-status-card">
        <span class="izin-small-label">Project Status</span>
        <h1>Secure status link not available.</h1>
        <p>This project status link is missing, invalid, or no longer active. Contact IZIN Designs if you need a fresh access link.</p>
      </div>
    <?php else : ?>
      <div class="project-status-card">
        <span class="izin-small-label">Project Status</span>
        <div class="project-status-hero">
          <div class="project-status-current">
            <p class="project-status-kicker"><?php esc_html_e('Current Stage', 'izin-designs-theme'); ?></p>
            <h1><?php echo esc_html($current_status); ?></h1>
            <?php if ($status_description !== '') : ?>
              <p class="project-status-lead"><?php echo esc_html($status_description); ?></p>
            <?php endif; ?>
          </div>

          <div class="project-status-current-meta">
            <article>
              <h2><?php esc_html_e('Last Updated', 'izin-designs-theme'); ?></h2>
              <p><?php echo esc_html($project->status_updated_at ?: $project->created_at); ?></p>
            </article>
            <article>
              <h2><?php esc_html_e('Shared Files', 'izin-designs-theme'); ?></h2>
              <p>
                <?php
                if (!$show_quotation_files) {
                    esc_html_e('Will appear when the quotation stage is reached.', 'izin-designs-theme');
                } elseif (empty($quotation_urls)) {
                    esc_html_e('No files shared yet.', 'izin-designs-theme');
                } else {
                    esc_html_e('Available below.', 'izin-designs-theme');
                }
                ?>
              </p>
            </article>
          </div>
        </div>

        <div class="project-status-progress">
          <h2><?php esc_html_e('Progress Tracker', 'izin-designs-theme'); ?></h2>
          <div class="project-status-steps" aria-label="<?php esc_attr_e('Project progress tracker', 'izin-designs-theme'); ?>">
            <?php foreach ($steps as $step) : ?>
              <article class="project-status-step is-<?php echo esc_attr($step['state']); ?>">
                <span class="project-status-step-marker" aria-hidden="true"></span>
                <div class="project-status-step-copy">
                  <h3><?php echo esc_html($step['label']); ?></h3>
                  <p><?php echo esc_html($step['description']); ?></p>
                  <?php if ($step['state'] === 'current' && !empty($project->client_notes)) : ?>
                    <div class="project-status-step-note">
                      <strong><?php esc_html_e('Note from the team', 'izin-designs-theme'); ?></strong>
                      <p><?php echo nl2br(esc_html($project->client_notes)); ?></p>
                    </div>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ($show_quotation_files) : ?>
          <div class="project-status-files">
            <h2><?php esc_html_e('Quotation & Proposal Files', 'izin-designs-theme'); ?></h2>
            <?php if (empty($quotation_urls)) : ?>
              <p><?php esc_html_e('The IZIN team has moved this request to the quotation stage, but files have not been shared yet.', 'izin-designs-theme'); ?></p>
            <?php else : ?>
              <div class="project-status-file-list">
                <?php foreach ($quotation_urls as $index => $url) : ?>
                  <a class="project-status-file" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                    <strong><?php echo esc_html($quotation_names[$index] ?? sprintf(__('Quotation File %d', 'izin-designs-theme'), $index + 1)); ?></strong>
                    <span><?php esc_html_e('Open document', 'izin-designs-theme'); ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
