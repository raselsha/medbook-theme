<?php
defined('ABSPATH') || exit;

/**
 * Theme setup — nothing here replaces the doctor-appointment plugin's own
 * admin panel (MedBook, ?page=mdbk-*); this theme only provides the public
 * site's header/nav/footer and four page templates
 * (page-templates/{doctors,appointment,live-queue,login}.php), each of which
 * still renders the page's own edited content (the_content()) alongside its
 * one baked-in shortcode — so a page using one of these templates stays
 * fully designable in the block editor without risking the shortcode being
 * accidentally deleted from it.
 */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    register_nav_menus(array(
        'medbook-primary' => __('Primary Menu', 'medbook'),
    ));
});

/**
 * Finds the one published page assigned to a given page-templates/*.php
 * template — the portable way to find e.g. "the Live Queue page" on any
 * site regardless of what its slug/title happen to be there.
 */
function medbook_get_page_by_template($template)
{
    $matches = get_posts(array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => $template,
    ));
    return $matches ? $matches[0] : null;
}

/**
 * First-activation default menu — Appointment, Doctors, Live Queue, Login,
 * in that order (Appointment first: it's the site's front page). Only runs
 * when NO menu is already assigned to medbook-primary, so it never touches
 * (or reorders) a menu an admin has already set up or hand-edited — this is
 * purely what a *fresh* install gets automatically, not an ongoing sync.
 * Matches pages by which page-templates/*.php template they're assigned to
 * (set via the block editor's Page Attributes panel), not by slug/title,
 * since those vary per site (e.g. "book-appointment" vs "appointment").
 */
add_action('after_switch_theme', 'medbook_setup_default_menu');
function medbook_setup_default_menu()
{
    if (has_nav_menu('medbook-primary')) {
        return;
    }

    $menu_items = array(
        'page-templates/appointment.php' => __('Appointment', 'medbook'),
        'page-templates/doctors.php'     => __('Doctors', 'medbook'),
        'page-templates/live-queue.php'  => __('Live Queue', 'medbook'),
        'page-templates/login.php'       => __('Login', 'medbook'),
    );

    $pages_found = array();
    foreach ($menu_items as $template => $label) {
        $page = medbook_get_page_by_template($template);
        if ($page) {
            $pages_found[$template] = $page;
        }
    }

    // Nothing to build a menu out of yet (e.g. activated before any of the
    // four pages were created) — leave it for a later activation once pages
    // exist, rather than creating an empty menu now.
    if (!$pages_found) {
        return;
    }

    $menu_name = __('MedBook Menu', 'medbook');
    $menu_id = wp_create_nav_menu($menu_name);
    if (is_wp_error($menu_id)) {
        return;
    }

    $position = 0;
    foreach ($menu_items as $template => $label) {
        if (!isset($pages_found[$template])) {
            continue;
        }
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title'     => $label,
            'menu-item-object-id' => $pages_found[$template]->ID,
            'menu-item-object'    => 'page',
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-position'  => ++$position,
        ));
    }

    $locations = get_theme_mod('nav_menu_locations', array());
    $locations['medbook-primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('medbook-theme', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
    wp_enqueue_script('medbook-nav', get_theme_file_uri('assets/js/nav.js'), array(), wp_get_theme()->get('Version'), true);

    if (is_page_template('page-templates/login.php') && !is_user_logged_in()) {
        wp_enqueue_script('medbook-login', get_theme_file_uri('assets/js/login.js'), array('jquery'), wp_get_theme()->get('Version'), true);
        wp_localize_script('medbook-login', 'MedbookLogin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('medbook_login'),
        ));
    }
});

/**
 * The nav menu's "Login" item becomes "Dashboard" (pointing at the visitor's
 * own dashboard) once they're actually signed in, with a "Logout" item
 * appended after it — editing the registered menu's rendered items here
 * instead of duplicating menu-management UI in the theme itself.
 */
add_filter('wp_nav_menu_objects', function ($items, $args) {
    if (!isset($args->theme_location) || 'medbook-primary' !== $args->theme_location) {
        return $items;
    }
    if (!is_user_logged_in()) {
        return $items;
    }

    $login_page = get_page_by_path('login');
    $login_page_id = $login_page ? $login_page->ID : 0;

    foreach ($items as $item) {
        if ((int) $item->object_id === $login_page_id && 'page' === $item->object) {
            $item->title = __('Dashboard', 'medbook');
            $item->url = medbook_dashboard_url();
        }
    }

    $logout_item = new stdClass();
    $logout_item->ID = 0;
    $logout_item->db_id = 0;
    $logout_item->object_id = 0;
    $logout_item->object = 'custom';
    $logout_item->menu_item_parent = 0;
    $logout_item->type = 'custom';
    $logout_item->type_label = __('Custom Link', 'medbook');
    $logout_item->title = __('Logout', 'medbook');
    $logout_item->url = wp_logout_url(home_url('/'));
    $logout_item->target = '';
    $logout_item->attr_title = '';
    $logout_item->description = '';
    $logout_item->classes = array('menu-item', 'medbook-nav-cta');
    $logout_item->xfn = '';
    $logout_item->current = false;
    $logout_item->current_item_ancestor = false;
    $logout_item->current_item_parent = false;
    $logout_item->menu_order = count($items) + 1;

    $items[] = $logout_item;

    return $items;
}, 10, 2);

/**
 * Where a signed-in visitor belongs — reuses the doctor-appointment plugin's
 * OWN 'login_redirect' filter (see MDBK_Admin_Dashboard::doctor_login_redirect())
 * instead of re-deciding "which role goes where" here: a Manager/admin gets
 * ?page=mdbk-dashboard, a doctor/receptionist gets ?page=mdbk-schedule,
 * anyone else falls back to admin_url(). One source of truth either way.
 */
function medbook_dashboard_url($user = null)
{
    $user = $user ?: wp_get_current_user();
    return apply_filters('login_redirect', admin_url(), admin_url(), $user);
}

/**
 * The Login page template (page-templates/login.php) is the only page this
 * theme ever needs to keep an already-authenticated visitor away from —
 * unlike the Tailors theme's front page, the site's actual front page here
 * stays the Book Appointment page for everyone, logged in or not.
 */
add_action('template_redirect', function () {
    if (is_page_template('page-templates/login.php') && is_user_logged_in()) {
        wp_safe_redirect(medbook_dashboard_url());
        exit;
    }
});

/**
 * AJAX login — nopriv-only since, by definition, nobody calling this is
 * authenticated yet. Delegates to wp_signon(), the exact same core function
 * wp-login.php's own form submits to, so every normal WP auth behavior
 * (hooks, wp_login_failed logging, any security/2FA plugin) keeps working
 * unchanged; this just keeps the visitor on the themed page instead of
 * bouncing them through wp-login.php's default screen. The <form>'s own
 * action="wp-login.php" stays as a plain-HTML fallback if JS is unavailable.
 */
add_action('wp_ajax_nopriv_medbook_login', 'medbook_ajax_login');
function medbook_ajax_login()
{
    check_ajax_referer('medbook_login', 'nonce');

    $log = isset($_POST['log']) ? sanitize_user(wp_unslash($_POST['log'])) : '';
    $pwd = isset($_POST['pwd']) ? (string) wp_unslash($_POST['pwd']) : '';

    if ('' === $log || '' === $pwd) {
        wp_send_json_error(array('message' => __('Please enter your username and password.', 'medbook')));
    }

    $user = wp_signon(array(
        'user_login'    => $log,
        'user_password' => $pwd,
        'remember'      => !empty($_POST['rememberme']),
    ), is_ssl());

    if (is_wp_error($user)) {
        // A generic message on purpose — WP core's own default messages state
        // outright whether the *username* or the *password* was wrong, which
        // lets anyone probe for valid usernames. This avoids that.
        wp_send_json_error(array('message' => __('Incorrect username or password.', 'medbook')));
    }

    wp_send_json_success(array('redirect' => medbook_dashboard_url($user)));
}

/**
 * "Forgot password?" — delegates to retrieve_password() (wp-includes/user.php),
 * the exact same function wp-login.php's own "Lost your password?" screen
 * calls. The emailed link itself still lands on the native
 * wp-login.php?action=rp screen to actually set a new password — only the
 * *request* step is themed.
 */
add_action('wp_ajax_nopriv_medbook_forgot_password', 'medbook_ajax_forgot_password');
function medbook_ajax_forgot_password()
{
    check_ajax_referer('medbook_login', 'nonce');

    $login = isset($_POST['user_login']) ? sanitize_text_field(wp_unslash($_POST['user_login'])) : '';

    if ('' === $login) {
        wp_send_json_error(array('message' => __('Please enter your username or email.', 'medbook')));
    }

    retrieve_password($login);

    // Always the same response whether or not a matching account was found —
    // an account-enumeration-safe pattern; the presence (or absence) of the
    // email in their inbox is the only real signal either way.
    wp_send_json_success(array(
        'message' => __('If an account matches that information, a password reset link has been emailed to it.', 'medbook'),
    ));
}

/**
 * Keep usernames private. WP core exposes every account's login slug to
 * anonymous visitors through four routes — /wp-json/wp/v2/users, the
 * ?author=N → /author/<slug>/ redirect, the users sitemap, and oEmbed's
 * author_name — which hands a brute-forcer half the credentials for free.
 * Logged-in users keep REST access, since wp-admin itself uses it.
 */
add_filter('rest_endpoints', function ($endpoints) {
    if (!is_user_logged_in()) {
        unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});

add_action('template_redirect', function () {
    if (!is_user_logged_in() && (is_author() || isset($_GET['author']))) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}, 1);

add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    return 'users' === $name ? false : $provider;
}, 10, 2);

add_filter('oembed_response_data', function ($data) {
    unset($data['author_name'], $data['author_url']);
    return $data;
});

/**
 * Baseline security headers on every WP-rendered response (front end,
 * wp-admin, AJAX, REST). HSTS deliberately omits includeSubDomains/preload
 * so it can't strand a subdomain that isn't on HTTPS.
 */
add_action('send_headers', 'medbook_security_headers');
add_action('admin_init', 'medbook_security_headers');
add_action('login_init', 'medbook_security_headers');
function medbook_security_headers()
{
    if (headers_sent()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    if (is_ssl()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
}
