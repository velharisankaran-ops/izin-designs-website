<?php
/**
 * Career application storage, REST endpoint, and WordPress admin screen.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('IZIN_CAREERS_LOADED')) {
    return;
}

define('IZIN_CAREERS_LOADED', true);

function izin_careers_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'izin_career_applications';
}

function izin_careers_install() {
    global $wpdb;

    $table_name = izin_careers_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        full_name VARCHAR(190) NOT NULL,
        phone VARCHAR(80) NOT NULL,
        email VARCHAR(190) DEFAULT '' NOT NULL,
        position VARCHAR(190) DEFAULT '' NOT NULL,
        experience VARCHAR(120) DEFAULT '' NOT NULL,
        location VARCHAR(190) DEFAULT '' NOT NULL,
        portfolio_url TEXT NULL,
        message TEXT NULL,
        resume_url TEXT NULL,
        resume_name VARCHAR(255) DEFAULT '' NOT NULL,
        resume_type VARCHAR(120) DEFAULT '' NOT NULL,
        source_url TEXT NULL,
        ip_address VARCHAR(100) DEFAULT '' NOT NULL,
        user_agent TEXT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY created_at (created_at)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function izin_careers_get_ip_address() {
    $ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');

    foreach ($ip_keys as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        $ip = sanitize_text_field(wp_unslash($_SERVER[$key]));
        $ip = trim(explode(',', $ip)[0]);

        if ($ip !== '') {
            return $ip;
        }
    }

    return '';
}

function izin_careers_register_rest_routes() {
    register_rest_route('izin-careers/v1', '/apply', array(
        'methods' => 'POST',
        'callback' => 'izin_careers_submit_rest',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'izin_careers_register_rest_routes');

function izin_careers_handle_resume_upload(WP_REST_Request $request) {
    $files = $request->get_file_params();

    if (empty($files['resume']) || empty($files['resume']['name'])) {
        return new WP_Error('izin_resume_missing', __('Resume or portfolio file is required.', 'izin-designs-theme'), array('status' => 400));
    }

    $file = $files['resume'];
    $allowed_mimes = array(
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );
    $file_type = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $allowed_mimes);

    if (empty($file_type['ext']) || empty($file_type['type'])) {
        return new WP_Error('izin_resume_type', __('Only PDF, DOC, or DOCX files are allowed.', 'izin-designs-theme'), array('status' => 400));
    }

    if (!empty($file['size']) && (int) $file['size'] > 5 * 1024 * 1024) {
        return new WP_Error('izin_resume_size', __('Resume file must be 5 MB or smaller.', 'izin-designs-theme'), array('status' => 400));
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    $upload = wp_handle_upload($file, array(
        'test_form' => false,
        'mimes' => $allowed_mimes,
    ));

    if (!empty($upload['error'])) {
        return new WP_Error('izin_resume_upload', sanitize_text_field($upload['error']), array('status' => 500));
    }

    return array(
        'url' => esc_url_raw($upload['url'] ?? ''),
        'name' => sanitize_file_name($file['name']),
        'type' => sanitize_text_field($upload['type'] ?? ''),
    );
}

function izin_careers_submit_rest(WP_REST_Request $request) {
    global $wpdb;

    if (sanitize_text_field($request->get_param('website')) !== '') {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Ignored.',
        ), 200);
    }

    $full_name = sanitize_text_field($request->get_param('full_name'));
    $phone = sanitize_text_field($request->get_param('phone'));
    $position = sanitize_text_field($request->get_param('position'));
    $email = sanitize_email($request->get_param('email'));

    if ($full_name === '' || $phone === '' || $position === '') {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Name, phone and position are required.',
        ), 400);
    }

    if ($email !== '' && !is_email($email)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Invalid email address.',
        ), 400);
    }

    $resume = izin_careers_handle_resume_upload($request);

    if (is_wp_error($resume)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => $resume->get_error_message(),
        ), (int) ($resume->get_error_data()['status'] ?? 400));
    }

    izin_careers_install();

    $inserted = $wpdb->insert(
        izin_careers_table_name(),
        array(
            'full_name' => $full_name,
            'phone' => $phone,
            'email' => $email,
            'position' => $position,
            'experience' => sanitize_text_field($request->get_param('experience')),
            'location' => sanitize_text_field($request->get_param('location')),
            'portfolio_url' => esc_url_raw($request->get_param('portfolio_url')),
            'message' => sanitize_textarea_field($request->get_param('message')),
            'resume_url' => $resume['url'],
            'resume_name' => $resume['name'],
            'resume_type' => $resume['type'],
            'source_url' => esc_url_raw($request->get_param('source_url')),
            'ip_address' => izin_careers_get_ip_address(),
            'user_agent' => sanitize_textarea_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? '')),
            'created_at' => current_time('mysql'),
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if (!$inserted) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Application could not be saved.',
        ), 500);
    }

    return new WP_REST_Response(array(
        'success' => true,
        'application_id' => (int) $wpdb->insert_id,
    ), 201);
}

function izin_careers_admin_menu() {
    add_menu_page(
        __('Izin Careers', 'izin-designs-theme'),
        __('Izin Careers', 'izin-designs-theme'),
        'manage_options',
        'izin-careers',
        'izin_careers_admin_page',
        'dashicons-id',
        27
    );
}
add_action('admin_menu', 'izin_careers_admin_menu');

function izin_careers_admin_page() {
    global $wpdb;

    izin_careers_install();

    $applications = $wpdb->get_results("SELECT * FROM " . izin_careers_table_name() . " ORDER BY created_at DESC, id DESC LIMIT 200");
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Izin Careers', 'izin-designs-theme'); ?></h1>
        <p><?php esc_html_e('Latest career applications submitted from the website.', 'izin-designs-theme'); ?></p>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Date', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Name', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Contact', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Role', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Experience', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Location', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Message', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('File', 'izin-designs-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                    <tr>
                        <td colspan="8"><?php esc_html_e('No career applications found yet.', 'izin-designs-theme'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo esc_html($application->created_at); ?></td>
                            <td><?php echo esc_html($application->full_name); ?></td>
                            <td>
                                <a href="<?php echo esc_url('tel:' . preg_replace('/[^0-9+]/', '', $application->phone)); ?>"><?php echo esc_html($application->phone); ?></a>
                                <?php if (!empty($application->email)): ?>
                                    <br><a href="<?php echo esc_url('mailto:' . $application->email); ?>"><?php echo esc_html($application->email); ?></a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($application->position); ?></td>
                            <td><?php echo esc_html($application->experience); ?></td>
                            <td><?php echo esc_html($application->location); ?></td>
                            <td>
                                <?php echo esc_html($application->message); ?>
                                <?php if (!empty($application->portfolio_url)): ?>
                                    <br><a href="<?php echo esc_url($application->portfolio_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Portfolio', 'izin-designs-theme'); ?></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($application->resume_url)): ?>
                                    <a class="button button-small" href="<?php echo esc_url($application->resume_url); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php esc_html_e('Open', 'izin-designs-theme'); ?>
                                    </a>
                                    <br><small><?php echo esc_html($application->resume_name); ?></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
