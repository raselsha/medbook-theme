<?php
/**
 * Template Name: Home (MedBook)
 *
 * A full landing page built around the plugin's real booking widget
 * ([mdbk_appointment_form] — specialty dropdown → doctor → date/slot →
 * details), so the front page books for real instead of just linking away.
 * Sections: hero, specialties, the booking widget, how it works, a doctor
 * teaser (live mdbk_doctor posts), a Live Queue banner and a staff-login
 * block. The page's own edited content renders between the doctor teaser
 * and the Live Queue banner, so extra copy can still be added in the block
 * editor without touching this file.
 *
 * Demo sites only: when the `medbook_demo_accounts` option holds a list of
 * {title, user, pass, blurb, tests[], i18n{locale: {title, blurb, tests[]}}}
 * entries (the site-wide "demo" ribbon itself is in header.php),
 * the staff block becomes one login card per role with its credentials.
 * Without that option (a real clinic) it's a single plain "Staff login" link.
 *
 * Stock photos live in assets/images/home/ (Unsplash licence); the hero
 * photo is replaced by the page's own Featured Image when one is set.
 */
defined('ABSPATH') || exit;
get_header();

$img = function ($name) {
    return get_theme_file_uri('assets/images/home/' . $name . '.webp');
};
$hero_img = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'full') : $img('hero-doctor');

$clinic_name = get_option('mdbk_clinic_name', get_bloginfo('name'));
$clinic_address = get_option('mdbk_clinic_address', '');
$clinic_contact = get_option('mdbk_clinic_contact', '');
$live_queue_page = medbook_get_page_by_template('page-templates/live-queue.php');
$doctors_page = medbook_get_page_by_template('page-templates/doctors.php');
$login_page = medbook_get_page_by_template('page-templates/login.php');
$login_url = $login_page ? get_permalink($login_page) : wp_login_url();

$demo_accounts = get_option('medbook_demo_accounts', array());
$demo_accounts = is_array($demo_accounts) ? $demo_accounts : array();
$sales = medbook_demo_sales();

$specialties = get_terms(array('taxonomy' => 'mdbk_department', 'hide_empty' => true));
$specialties = is_wp_error($specialties) ? array() : $specialties;

$doctors = get_posts(array(
    'post_type'      => 'mdbk_doctor',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
));

$initials = function ($name) {
    $parts = preg_split('/\s+/', trim(preg_replace('/^Dr\.?\s*/i', '', $name)));
    $out = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $out .= mb_substr($p, 0, 1);
    }
    return mb_strtoupper($out);
};

$check_icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>';

$svg = function ($paths) {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
$role_icons = array(
    'mdbk_manager_role' => $svg('<rect x="3" y="7" width="18" height="13" rx="2"></rect><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"></path>'),
    'mdbk_receptionist' => $svg('<rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V3h6v1M9 13l2 2 4-4"></path>'),
    'mdbk_doctor_role'  => $svg('<path d="M6 3v6a4 4 0 0 0 8 0V3"></path><path d="M10 13v3a5 5 0 0 0 10 0v-2"></path><circle cx="20" cy="12" r="2"></circle>'),
);

ob_start();
while (have_posts()) :
    the_post();
    the_content();
endwhile;
$page_content = trim(ob_get_clean());
?>
<main class="medbook-home">

    <section class="medbook-home-hero">
        <div class="medbook-home-wrap medbook-home-hero-grid">
            <div class="medbook-home-hero-text">
                <p class="medbook-home-eyebrow"><?php esc_html_e('Doctor appointments & live queue', 'medbook'); ?></p>
                <h1 class="medbook-home-hero-title"><?php esc_html_e('Book your serial online. Wait at home, not in the chamber.', 'medbook'); ?></h1>
                <p class="medbook-home-hero-lead"><?php esc_html_e('Pick a specialty and a doctor, choose a time, and get your serial number instantly. Then follow the live queue from your phone and arrive just before your turn.', 'medbook'); ?></p>
                <div class="medbook-home-actions">
                    <a class="medbook-home-btn medbook-home-btn--primary" href="#book"><?php esc_html_e('Book an appointment', 'medbook'); ?></a>
                    <?php if ($live_queue_page) : ?>
                        <a class="medbook-home-btn medbook-home-btn--soft" href="<?php echo esc_url(get_permalink($live_queue_page)); ?>"><?php esc_html_e('View live queue', 'medbook'); ?></a>
                    <?php endif; ?>
                </div>
                <ul class="medbook-home-points">
                    <li><?php echo $check_icon; // phpcs:ignore -- static SVG ?><?php esc_html_e('Instant serial number', 'medbook'); ?></li>
                    <li><?php echo $check_icon; // phpcs:ignore ?><?php esc_html_e('QR check-in at the desk', 'medbook'); ?></li>
                    <li><?php echo $check_icon; // phpcs:ignore ?><?php esc_html_e('Live queue on any phone', 'medbook'); ?></li>
                </ul>
            </div>
            <div class="medbook-home-hero-media">
                <img src="<?php echo esc_url($hero_img); ?>" alt="<?php esc_attr_e('A doctor in a white coat with a stethoscope', 'medbook'); ?>" width="1100" height="733" fetchpriority="high">
                <div class="medbook-home-hero-badge">
                    <span class="medbook-home-hero-badge-icon"><?php echo $check_icon; // phpcs:ignore ?></span>
                    <span>
                        <strong><?php esc_html_e('Booking confirmed', 'medbook'); ?></strong>
                        <small><?php esc_html_e('Your serial arrives on screen in seconds', 'medbook'); ?></small>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <?php if ($specialties) : ?>
        <section class="medbook-home-specialties" aria-label="<?php esc_attr_e('Specialties', 'medbook'); ?>">
            <div class="medbook-home-wrap">
                <ul class="medbook-home-chips">
                    <?php foreach ($specialties as $spec) : ?>
                        <li><a href="#book"><?php echo esc_html($spec->name); ?> <span><?php echo esc_html(number_format_i18n($spec->count)); ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <section class="medbook-home-section" id="book">
        <div class="medbook-home-wrap medbook-home-book-grid">
            <div class="medbook-home-book-text">
                <h2><?php esc_html_e('Book an appointment', 'medbook'); ?></h2>
                <p><?php esc_html_e('Four quick steps — it takes about a minute.', 'medbook'); ?></p>
                <ol class="medbook-home-book-steps">
                    <li><span>1</span><?php esc_html_e('Choose a specialty from the dropdown', 'medbook'); ?></li>
                    <li><span>2</span><?php esc_html_e('Pick your doctor', 'medbook'); ?></li>
                    <li><span>3</span><?php esc_html_e('Select a date and time slot', 'medbook'); ?></li>
                    <li><span>4</span><?php esc_html_e('Enter your name and mobile — get your serial', 'medbook'); ?></li>
                </ol>
                <?php if ($demo_accounts) : ?>
                    <p class="medbook-home-note"><?php esc_html_e('Demo site: use any made-up name and a number like 01700000999 — nothing is sent anywhere.', 'medbook'); ?></p>
                <?php endif; ?>
            </div>
            <div class="medbook-home-booking">
                <?php echo do_shortcode('[mdbk_appointment_form flush="1"]'); ?>
            </div>
        </div>
    </section>

    <section class="medbook-home-section medbook-home-section--white">
        <div class="medbook-home-wrap medbook-home-split">
            <div class="medbook-home-split-media">
                <img src="<?php echo esc_url($img('consultation')); ?>" alt="<?php esc_attr_e('A doctor talking with a patient', 'medbook'); ?>" width="1100" height="717" loading="lazy">
            </div>
            <div class="medbook-home-split-text">
                <h2><?php esc_html_e('How it works', 'medbook'); ?></h2>
                <ol class="medbook-home-steps">
                    <li>
                        <span class="medbook-home-step-no">01</span>
                        <div>
                            <h3><?php esc_html_e('Book online', 'medbook'); ?></h3>
                            <p><?php esc_html_e('Pick a specialty, a doctor and a free slot. Your serial number appears straight away, with a QR code to show at the desk.', 'medbook'); ?></p>
                        </div>
                    </li>
                    <li>
                        <span class="medbook-home-step-no">02</span>
                        <div>
                            <h3><?php esc_html_e('Check in when you arrive', 'medbook'); ?></h3>
                            <p><?php esc_html_e('The front desk scans your QR code and your serial joins the doctor\'s live queue.', 'medbook'); ?></p>
                        </div>
                    </li>
                    <li>
                        <span class="medbook-home-step-no">03</span>
                        <div>
                            <h3><?php esc_html_e('Watch your turn live', 'medbook'); ?></h3>
                            <p><?php esc_html_e('See who is being served and how many are ahead — on the waiting-room screen or your own phone.', 'medbook'); ?></p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <?php if ($doctors) : ?>
        <section class="medbook-home-section">
            <div class="medbook-home-wrap">
                <div class="medbook-home-heading medbook-home-heading--row">
                    <div>
                        <h2><?php esc_html_e('Our doctors', 'medbook'); ?></h2>
                        <p><?php esc_html_e('Specialists with morning and evening chambers.', 'medbook'); ?></p>
                    </div>
                    <?php if ($doctors_page) : ?>
                        <a class="medbook-home-link" href="<?php echo esc_url(get_permalink($doctors_page)); ?>"><?php esc_html_e('See all doctors →', 'medbook'); ?></a>
                    <?php endif; ?>
                </div>
                <div class="medbook-home-doctors">
                    <?php foreach ($doctors as $doc) :
                        $depts = wp_get_post_terms($doc->ID, 'mdbk_department', array('fields' => 'names'));
                        $fee = get_post_meta($doc->ID, '_mdbk_doc_fee', true);
                        $bio = get_post_meta($doc->ID, '_mdbk_doc_bio', true);
                        ?>
                        <article class="medbook-home-doctor">
                            <div class="medbook-home-doctor-head">
                                <?php if (has_post_thumbnail($doc)) : ?>
                                    <?php echo get_the_post_thumbnail($doc, array(64, 64), array('class' => 'medbook-home-avatar', 'alt' => esc_attr($doc->post_title))); ?>
                                <?php else : ?>
                                    <span class="medbook-home-avatar" aria-hidden="true"><?php echo esc_html($initials($doc->post_title)); ?></span>
                                <?php endif; ?>
                                <div>
                                    <h3><?php echo esc_html($doc->post_title); ?></h3>
                                    <?php if ($depts && !is_wp_error($depts)) : ?>
                                        <p class="medbook-home-doctor-dept"><?php echo esc_html(implode(', ', $depts)); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($bio) : ?>
                                <p class="medbook-home-doctor-bio"><?php echo esc_html(strtok($bio, "\r\n")); ?></p>
                            <?php endif; ?>
                            <div class="medbook-home-doctor-foot">
                                <?php if ('' !== (string) $fee) : ?>
                                    <span class="medbook-home-fee">৳<?php echo esc_html(number_format_i18n((float) $fee)); ?> <small><?php esc_html_e('fee', 'medbook'); ?></small></span>
                                <?php endif; ?>
                                <a class="medbook-home-btn medbook-home-btn--small" href="<?php echo esc_url(add_query_arg('doctor', $doc->ID, home_url('/')) . '#book'); ?>"><?php esc_html_e('Book', 'medbook'); ?></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ('' !== $page_content) : ?>
        <section class="medbook-home-section">
            <div class="medbook-home-wrap medbook-page-content"><?php echo $page_content; // phpcs:ignore -- the_content() output ?></div>
        </section>
    <?php endif; ?>

    <?php if ($live_queue_page) : ?>
        <section class="medbook-home-wrap">
            <div class="medbook-cta-banner medbook-home-queue">
                <img src="<?php echo esc_url($img('reception')); ?>" alt="" width="1100" height="749" loading="lazy">
                <div class="medbook-cta-banner-text">
                    <h2><?php esc_html_e('Already booked?', 'medbook'); ?></h2>
                    <p><?php esc_html_e('Track your place in the queue in real time and arrive just before your turn.', 'medbook'); ?></p>
                </div>
                <a class="medbook-cta-banner-btn" href="<?php echo esc_url(get_permalink($live_queue_page)); ?>"><?php esc_html_e('View Live Queue', 'medbook'); ?></a>
            </div>
        </section>
    <?php endif; ?>

    <section class="medbook-home-staff" id="staff">
        <div class="medbook-home-wrap">
            <?php if ($demo_accounts) : ?>
                <div class="medbook-home-staff-head">
                    <p class="medbook-home-eyebrow"><?php esc_html_e('For clinics evaluating MedBook', 'medbook'); ?></p>
                    <h2><?php esc_html_e('Try the staff dashboard', 'medbook'); ?></h2>
                    <p><?php esc_html_e('Pick a role and log in with one click — no typing needed.', 'medbook'); ?></p>
                </div>
                <div class="medbook-home-roles">
                    <?php foreach ($demo_accounts as $acct) :
                        $acct = wp_parse_args($acct, array('title' => '', 'user' => '', 'pass' => '', 'role' => '', 'blurb' => '', 'tests' => array(), 'i18n' => array()));
                        // Optional per-language copy, keyed by locale (e.g. 'bn_BD' => {title, blurb, tests}).
                        $locale = determine_locale();
                        if (!empty($acct['i18n'][$locale]) && is_array($acct['i18n'][$locale])) {
                            $acct = array_merge($acct, array_intersect_key($acct['i18n'][$locale], array_flip(array('title', 'blurb', 'tests'))));
                        }
                        $icon = isset($role_icons[$acct['role']]) ? $role_icons[$acct['role']] : $role_icons['mdbk_manager_role'];
                        ?>
                        <article class="medbook-home-role">
                            <div class="medbook-home-role-head">
                                <span class="medbook-home-role-icon"><?php echo $icon; // phpcs:ignore -- static SVG ?></span>
                                <div>
                                    <h3><?php echo esc_html($acct['title']); ?></h3>
                                    <p><?php echo esc_html($acct['blurb']); ?></p>
                                </div>
                            </div>
                            <?php if ($acct['tests']) : ?>
                                <ul>
                                    <?php foreach ((array) $acct['tests'] as $test) : ?>
                                        <li><?php echo esc_html($test); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php // Demo accounts only, whose credentials are printed on this page anyway — one click posts them straight to wp-login.php; the plugin's login_redirect then sends each role to its own screen. ?>
                            <form class="medbook-home-role-login" method="post" action="<?php echo esc_url(wp_login_url()); ?>">
                                <input type="hidden" name="log" value="<?php echo esc_attr($acct['user']); ?>">
                                <input type="hidden" name="pwd" value="<?php echo esc_attr($acct['pass']); ?>">
                                <button type="submit" class="medbook-home-btn medbook-home-btn--primary">
                                    <?php
                                    /* translators: %s: demo role name, e.g. Manager */
                                    echo esc_html(sprintf(__('Log in as %s', 'medbook'), $acct['title']));
                                    ?>
                                </button>
                                <p class="medbook-home-cred">
                                    <span><?php esc_html_e('Username', 'medbook'); ?> <code><?php echo esc_html($acct['user']); ?></code></span>
                                    <span><?php esc_html_e('Password', 'medbook'); ?> <code><?php echo esc_html($acct['pass']); ?></code></span>
                                </p>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if ($sales) : ?>
                    <div class="medbook-home-buy">
                        <div>
                            <h3><?php esc_html_e('Like what you see? Get MedBook for your clinic.', 'medbook'); ?></h3>
                            <p><?php esc_html_e('Lieusoft sets it up on your own website with your doctors, schedules and branding.', 'medbook'); ?></p>
                        </div>
                        <div class="medbook-home-buy-actions">
                            <a class="medbook-home-btn medbook-home-btn--primary" href="<?php echo esc_url($sales['buy_url']); ?>" target="_blank" rel="noopener"><?php esc_html_e('View price', 'medbook'); ?></a>
                            <?php if ($sales['whatsapp_url']) : ?>
                                <a class="medbook-home-btn medbook-home-btn--whatsapp" href="<?php echo esc_url($sales['whatsapp_url']); ?>" target="_blank" rel="noopener"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a9 9 0 0 0-7.8 13.4L3 21l4.7-1.2A9 9 0 1 0 12 3Z"></path><path d="M8.5 8.6c.3-.7.6-.7.9-.7h.6c.2 0 .4 0 .6.5l.7 1.7c.1.3 0 .5-.1.7l-.5.6c-.1.2-.2.4 0 .7.5.8 1.5 1.8 2.3 2.2.3.2.5.2.7 0l.6-.6c.2-.2.4-.2.7-.1l1.7.8c.4.2.4.4.4.6-.1.9-1.3 1.7-2.2 1.7-1.8 0-4.3-1.4-5.9-3-1.6-1.6-2.6-3.7-2.5-5.4 0-.6.2-1.2.5-1.7Z" fill="currentColor" stroke="none"></path></svg><?php esc_html_e('Chat on WhatsApp', 'medbook'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="medbook-home-staff-plain">
                    <div>
                        <h2><?php esc_html_e('Clinic staff', 'medbook'); ?></h2>
                        <p><?php esc_html_e('Doctors and front-desk staff can sign in to manage the day\'s queue.', 'medbook'); ?></p>
                    </div>
                    <a class="medbook-home-btn medbook-home-btn--primary" href="<?php echo esc_url($login_url); ?>"><?php esc_html_e('Staff login', 'medbook'); ?></a>
                </div>
            <?php endif; ?>
            <?php if ($clinic_address || $clinic_contact) : ?>
                <p class="medbook-home-clinic"><strong><?php echo esc_html($clinic_name); ?></strong><?php echo $clinic_address ? ' · ' . esc_html($clinic_address) : ''; ?><?php echo $clinic_contact ? ' · ' . esc_html($clinic_contact) : ''; ?></p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>
