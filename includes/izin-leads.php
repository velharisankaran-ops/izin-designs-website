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
        referrer TEXT NULL,
        ip_address VARCHAR(100) DEFAULT '' NOT NULL,
        device_type VARCHAR(80) DEFAULT '' NOT NULL,
        browser VARCHAR(120) DEFAULT '' NOT NULL,
        operating_system VARCHAR(120) DEFAULT '' NOT NULL,
        language VARCHAR(80) DEFAULT '' NOT NULL,
        screen_size VARCHAR(80) DEFAULT '' NOT NULL,
        viewport_size VARCHAR(80) DEFAULT '' NOT NULL,
        timezone VARCHAR(120) DEFAULT '' NOT NULL,
        cookies_enabled TINYINT(1) DEFAULT 0 NOT NULL,
        utm_source VARCHAR(190) DEFAULT '' NOT NULL,
        utm_medium VARCHAR(190) DEFAULT '' NOT NULL,
        utm_campaign VARCHAR(190) DEFAULT '' NOT NULL,
        utm_content VARCHAR(190) DEFAULT '' NOT NULL,
        utm_term VARCHAR(190) DEFAULT '' NOT NULL,
        user_agent TEXT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY created_at (created_at)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function izin_leads_get_ip_address() {
    $ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');

    foreach ($ip_keys as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        $ip = sanitize_text_field(wp_unslash($_SERVER[$key]));
        $ip = explode(',', $ip)[0];
        $ip = trim($ip);

        if ($ip !== '') {
            return $ip;
        }
    }

    return '';
}

function izin_leads_detect_device_type($user_agent) {
    if (preg_match('/tablet|ipad|playbook|silk/i', $user_agent)) {
        return 'Tablet';
    }

    if (preg_match('/mobile|iphone|ipod|android|blackberry|phone/i', $user_agent)) {
        return 'Mobile';
    }

    return 'Desktop';
}

function izin_leads_detect_browser($user_agent) {
    if (stripos($user_agent, 'Edg/') !== false) {
        return 'Microsoft Edge';
    }

    if (stripos($user_agent, 'Chrome/') !== false && stripos($user_agent, 'Chromium') === false) {
        return 'Chrome';
    }

    if (stripos($user_agent, 'Safari/') !== false && stripos($user_agent, 'Chrome/') === false) {
        return 'Safari';
    }

    if (stripos($user_agent, 'Firefox/') !== false) {
        return 'Firefox';
    }

    return 'Unknown';
}

function izin_leads_detect_operating_system($user_agent) {
    if (stripos($user_agent, 'Windows') !== false) {
        return 'Windows';
    }

    if (stripos($user_agent, 'Android') !== false) {
        return 'Android';
    }

    if (stripos($user_agent, 'iPhone') !== false || stripos($user_agent, 'iPad') !== false) {
        return 'iOS';
    }

    if (stripos($user_agent, 'Mac OS') !== false || stripos($user_agent, 'Macintosh') !== false) {
        return 'macOS';
    }

    if (stripos($user_agent, 'Linux') !== false) {
        return 'Linux';
    }

    return 'Unknown';
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

    $user_agent = sanitize_textarea_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? ''));
    $cookies_enabled = isset($params['cookies_enabled']) && (string) $params['cookies_enabled'] === '1' ? 1 : 0;

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
            'referrer' => esc_url_raw($params['referrer'] ?? ''),
            'ip_address' => izin_leads_get_ip_address(),
            'device_type' => izin_leads_detect_device_type($user_agent),
            'browser' => izin_leads_detect_browser($user_agent),
            'operating_system' => izin_leads_detect_operating_system($user_agent),
            'language' => sanitize_text_field($params['language'] ?? ''),
            'screen_size' => sanitize_text_field($params['screen_size'] ?? ''),
            'viewport_size' => sanitize_text_field($params['viewport_size'] ?? ''),
            'timezone' => sanitize_text_field($params['timezone'] ?? ''),
            'cookies_enabled' => $cookies_enabled,
            'utm_source' => sanitize_text_field($params['utm_source'] ?? ''),
            'utm_medium' => sanitize_text_field($params['utm_medium'] ?? ''),
            'utm_campaign' => sanitize_text_field($params['utm_campaign'] ?? ''),
            'utm_content' => sanitize_text_field($params['utm_content'] ?? ''),
            'utm_term' => sanitize_text_field($params['utm_term'] ?? ''),
            'user_agent' => $user_agent,
            'created_at' => current_time('mysql'),
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
        )
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

        <table class="widefat striped">
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
                    <th><?php esc_html_e('Device', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Source', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Tracking', 'izin-designs-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="11"><?php esc_html_e('No leads found yet.', 'izin-designs-theme'); ?></td>
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
                            <td>
                                <?php echo esc_html(trim($lead->device_type . ' / ' . $lead->browser . ' / ' . $lead->operating_system, ' /')); ?><br>
                                <small>
                                    <?php echo esc_html($lead->screen_size); ?>
                                    <?php if (!empty($lead->viewport_size)): ?>
                                        <?php echo esc_html(' viewport ' . $lead->viewport_size); ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <?php if (!empty($lead->source_url)): ?>
                                    <a href="<?php echo esc_url($lead->source_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Page', 'izin-designs-theme'); ?></a>
                                <?php endif; ?>
                                <?php if (!empty($lead->referrer)): ?>
                                    <br><a href="<?php echo esc_url($lead->referrer); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Referrer', 'izin-designs-theme'); ?></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small>
                                    <?php if (!empty($lead->ip_address)): ?>
                                        <?php echo esc_html('IP: ' . $lead->ip_address); ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($lead->language)): ?>
                                        <?php echo esc_html('Lang: ' . $lead->language); ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($lead->timezone)): ?>
                                        <?php echo esc_html('TZ: ' . $lead->timezone); ?><br>
                                    <?php endif; ?>
                                    <?php echo esc_html('Cookies: ' . (!empty($lead->cookies_enabled) ? 'Enabled' : 'Disabled')); ?><br>
                                    <?php if (!empty($lead->utm_source) || !empty($lead->utm_medium) || !empty($lead->utm_campaign)): ?>
                                        <?php echo esc_html('UTM: ' . trim($lead->utm_source . ' / ' . $lead->utm_medium . ' / ' . $lead->utm_campaign, ' /')); ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
