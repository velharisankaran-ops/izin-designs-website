<?php
/**
 * Lead capture storage, REST endpoint, and WordPress admin screen.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('IZIN_LEADS_LOADED')) {
    return;
}

define('IZIN_LEADS_LOADED', true);

function izin_leads_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'izin_leads';
}

function izin_leads_install() {
    global $wpdb;

    $table_name = izin_leads_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(190) NOT NULL,
        phone VARCHAR(80) NOT NULL,
        property_type VARCHAR(190) DEFAULT '' NOT NULL,
        budget VARCHAR(190) DEFAULT '' NOT NULL,
        email VARCHAR(190) DEFAULT '' NOT NULL,
        sqft VARCHAR(190) DEFAULT '' NOT NULL,
        location VARCHAR(190) DEFAULT '' NOT NULL,
        source_url TEXT NULL,
        user_agent TEXT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY created_at (created_at)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function izin_leads_register_rest_routes() {
    register_rest_route('izin-leads/v1', '/submit', array(
        'methods' => 'POST',
        'callback' => 'izin_leads_submit_rest',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'izin_leads_register_rest_routes');

function izin_leads_submit_rest(WP_REST_Request $request) {
    global $wpdb;

    $params = $request->get_json_params();
    if (!is_array($params)) {
        $params = $request->get_body_params();
    }

    $name = sanitize_text_field($params['name'] ?? '');
    $phone = sanitize_text_field($params['phone'] ?? '');

    if ($name === '' || $phone === '') {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Name and phone are required.',
        ), 400);
    }

    izin_leads_install();

    $inserted = $wpdb->insert(
        izin_leads_table_name(),
        array(
            'name' => $name,
            'phone' => $phone,
            'property_type' => sanitize_text_field($params['property_type'] ?? ''),
            'budget' => sanitize_text_field($params['budget'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'sqft' => sanitize_text_field($params['sqft'] ?? ''),
            'location' => sanitize_text_field($params['location'] ?? ''),
            'source_url' => esc_url_raw($params['source_url'] ?? ''),
            'user_agent' => sanitize_textarea_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'created_at' => current_time('mysql'),
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if (!$inserted) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Lead could not be saved.',
        ), 500);
    }

    return new WP_REST_Response(array(
        'success' => true,
        'lead_id' => (int) $wpdb->insert_id,
    ), 201);
}

function izin_leads_admin_menu() {
    add_menu_page(
        __('Izin Leads', 'izin-designs-theme'),
        __('Izin Leads', 'izin-designs-theme'),
        'manage_options',
        'izin-leads',
        'izin_leads_admin_page',
        'dashicons-groups',
        26
    );
}
add_action('admin_menu', 'izin_leads_admin_menu');

function izin_leads_admin_page() {
    global $wpdb;

    izin_leads_install();

    $table_name = izin_leads_table_name();
    $leads = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC, id DESC LIMIT 200");
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Izin Leads', 'izin-designs-theme'); ?></h1>
        <p><?php esc_html_e('Latest consultation enquiries submitted from the website form.', 'izin-designs-theme'); ?></p>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Date', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Name', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Phone', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Property', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Budget', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Email', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Sq.Ft', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Location', 'izin-designs-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="8"><?php esc_html_e('No leads found yet.', 'izin-designs-theme'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td><?php echo esc_html($lead->created_at); ?></td>
                            <td><?php echo esc_html($lead->name); ?></td>
                            <td>
                                <a href="<?php echo esc_url('tel:' . preg_replace('/[^0-9+]/', '', $lead->phone)); ?>">
                                    <?php echo esc_html($lead->phone); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html($lead->property_type); ?></td>
                            <td><?php echo esc_html($lead->budget); ?></td>
                            <td>
                                <?php if (!empty($lead->email)): ?>
                                    <a href="<?php echo esc_url('mailto:' . $lead->email); ?>"><?php echo esc_html($lead->email); ?></a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($lead->sqft); ?></td>
                            <td><?php echo esc_html($lead->location); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
