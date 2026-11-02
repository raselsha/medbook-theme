<?php
/**
 * Template Name: Doctors (MedBook)
 *
 * Renders the page's own edited content (freely designable in the block
 * editor, or a full Elementor-built page) followed by the
 * [mdbk_doctor_list] shortcode as a guaranteed fallback — but only for a
 * page that ISN'T already Elementor-managed. An Elementor page's own design
 * has already decided where and how that shortcode appears (as one of its
 * own widgets), so appending it again here would just duplicate it; the
 * hero and the sections below the list are skipped for the same reason —
 * an Elementor page supplies its own heading and layout entirely.
 *
 * Non-Elementor pages get a hero (page title, live doctor/specialty counts,
 * booking CTA; the page's Featured Image replaces the stock photo), then
 * the list, then "before your visit" notes, an FAQ about how booking and
 * the live queue work, and a closing booking banner. Every claim in that
 * copy describes the plugin's own behaviour, so it holds on any clinic.
 */
defined('ABSPATH') || exit;
get_header();
$is_elementor_page = get_post_meta(get_the_ID(), '_elementor_edit_mode', true) === 'builder';
ob_start();
while (have_posts()) :
    the_post();
    the_content();
endwhile;
$page_content = trim(ob_get_clean());

if (!$is_elementor_page) {
    $hero_img = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'full') : get_theme_file_uri('assets/images/home/doctors-room.webp');
    $doctor_count = (int) wp_count_posts('mdbk_doctor')->publish;
    $specialty_count = (int) wp_count_terms(array('taxonomy' => 'mdbk_department', 'hide_empty' => true));
    $booking_page = medbook_get_page_by_template('page-templates/home.php') ?: medbook_get_page_by_template('page-templates/appointment.php');
    $booking_url = $booking_page ? get_permalink($booking_page) . '#book' : home_url('/#book');
    $live_queue_page = medbook_get_page_by_template('page-templates/live-queue.php');
}
?>
<?php if (!$is_elementor_page) : ?>
    <section class="medbook-home medbook-home-hero medbook-doctors-hero">
        <div class="medbook-home-wrap medbook-home-hero-grid">
            <div>
                <p class="medbook-home-eyebrow"><?php the_title(); ?></p>
                <h1 class="medbook-home-hero-title"><?php esc_html_e('Find the right specialist and book in a minute', 'medbook'); ?></h1>
                <p class="medbook-home-hero-lead"><?php esc_html_e('See each doctor\'s chamber days and hours, then pick a free slot. Your serial number appears instantly.', 'medbook'); ?></p>
                <div class="medbook-home-actions">
                    <a class="medbook-home-btn medbook-home-btn--primary" href="<?php echo esc_url($booking_url); ?>"><?php esc_html_e('Book an appointment', 'medbook'); ?></a>
                    <?php if ($live_queue_page) : ?>
                        <a class="medbook-home-btn medbook-home-btn--soft" href="<?php echo esc_url(get_permalink($live_queue_page)); ?>"><?php esc_html_e('View live queue', 'medbook'); ?></a>
                    <?php endif; ?>
                </div>
                <dl class="medbook-doctors-stats">
                    <div>
                        <dt><?php esc_html_e('Doctors', 'medbook'); ?></dt>
                        <dd><?php echo esc_html(number_format_i18n($doctor_count)); ?></dd>
                    </div>
                    <div>
                        <dt><?php esc_html_e('Specialties', 'medbook'); ?></dt>
                        <dd><?php echo esc_html(number_format_i18n($specialty_count)); ?></dd>
                    </div>
                    <div>
                        <dt><?php esc_html_e('Serial', 'medbook'); ?></dt>
                        <dd><?php esc_html_e('Instant', 'medbook'); ?></dd>
                    </div>
                </dl>
            </div>
            <div class="medbook-home-hero-media">
                <img src="<?php echo esc_url($hero_img); ?>" alt="<?php esc_attr_e('A bright, clean treatment room with a dental chair', 'medbook'); ?>" width="1200" height="800" fetchpriority="high">
            </div>
        </div>
    </section>
<?php endif; ?>

<main class="medbook-page medbook-page--doctors">
    <?php if ('' !== $page_content) : ?>
        <div class="medbook-page-content"><?php echo $page_content; // phpcs:ignore -- the_content() output, already filtered/escaped by core ?></div>
    <?php endif; ?>
    <?php if (!$is_elementor_page) : ?>
        <div class="medbook-page-shortcode">
            <?php echo do_shortcode('[mdbk_doctor_list]'); ?>
        </div>
    <?php endif; ?>
</main>

<?php if (!$is_elementor_page) : ?>
    <div class="medbook-home">
        <section class="medbook-home-section medbook-home-section--white">
            <div class="medbook-home-wrap">
                <div class="medbook-home-heading">
                    <h2><?php esc_html_e('Before your visit', 'medbook'); ?></h2>
                    <p><?php esc_html_e('Three things that make the day go smoothly.', 'medbook'); ?></p>
                </div>
                <div class="medbook-doctors-notes">
                    <article>
                        <span class="medbook-home-role-icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><path d="M14 14h3v3h-3zM20 14v.01M14 20h.01M17 20h4v-3"></path></svg></span>
                        <h3><?php esc_html_e('Keep your QR code', 'medbook'); ?></h3>
                        <p><?php esc_html_e('Save or print the confirmation after booking. The front desk scans it to check you in.', 'medbook'); ?></p>
                    </article>
                    <article>
                        <span class="medbook-home-role-icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
                        <h3><?php esc_html_e('Check in on arrival', 'medbook'); ?></h3>
                        <p><?php esc_html_e('Your serial joins the doctor\'s live queue only after you check in at the desk.', 'medbook'); ?></p>
                    </article>
                    <article>
                        <span class="medbook-home-role-icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="20" rx="2"></rect><path d="M11 18h2"></path></svg></span>
                        <h3><?php esc_html_e('Follow the queue on your phone', 'medbook'); ?></h3>
                        <p><?php esc_html_e('The live queue shows who is with the doctor now and when the doctor is on a short break.', 'medbook'); ?></p>
                    </article>
                </div>
            </div>
        </section>

        <section class="medbook-home-section">
            <div class="medbook-home-wrap medbook-faq-grid">
                <div class="medbook-faq-intro">
                    <p class="medbook-home-eyebrow"><?php esc_html_e('FAQ', 'medbook'); ?></p>
                    <h2><?php esc_html_e('Common questions', 'medbook'); ?></h2>
                    <p><?php esc_html_e('How booking, check-in and the live queue work.', 'medbook'); ?></p>
                    <?php if ($live_queue_page) : ?>
                        <a class="medbook-home-btn medbook-home-btn--soft" href="<?php echo esc_url(get_permalink($live_queue_page)); ?>"><?php esc_html_e('View live queue', 'medbook'); ?></a>
                    <?php endif; ?>
                </div>
                <div class="medbook-faq">
                        <details name="medbook-faq" open>
                            <summary>
                                <span class="medbook-faq-no"><?php echo esc_html(number_format_i18n(1)); ?></span>
                                <span class="medbook-faq-q"><?php esc_html_e('How do I get a serial number?', 'medbook'); ?></span>
                                <span class="medbook-faq-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg></span>
                            </summary>
                            <p><?php esc_html_e('Choose a specialty and a doctor, pick a date the doctor sits and a free time, then enter your name and mobile number. The serial number is shown on screen as soon as you confirm.', 'medbook'); ?></p>
                        </details>
                        <details name="medbook-faq">
                            <summary>
                                <span class="medbook-faq-no"><?php echo esc_html(number_format_i18n(2)); ?></span>
                                <span class="medbook-faq-q"><?php esc_html_e('Why are some days or times greyed out?', 'medbook'); ?></span>
                                <span class="medbook-faq-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg></span>
                            </summary>
                            <p><?php esc_html_e('Each doctor only sits on certain days. Times that are already booked, or that fall in the doctor\'s break, cannot be chosen.', 'medbook'); ?></p>
                        </details>
                        <details name="medbook-faq">
                            <summary>
                                <span class="medbook-faq-no"><?php echo esc_html(number_format_i18n(3)); ?></span>
                                <span class="medbook-faq-q"><?php esc_html_e('What happens when I reach the clinic?', 'medbook'); ?></span>
                                <span class="medbook-faq-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg></span>
                            </summary>
                            <p><?php esc_html_e('Show your QR code at the front desk. Once you are checked in, your serial appears in the doctor\'s live queue and you are called in order.', 'medbook'); ?></p>
                        </details>
                        <details name="medbook-faq">
                            <summary>
                                <span class="medbook-faq-no"><?php echo esc_html(number_format_i18n(4)); ?></span>
                                <span class="medbook-faq-q"><?php esc_html_e('Can I see how many patients are ahead of me?', 'medbook'); ?></span>
                                <span class="medbook-faq-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg></span>
                            </summary>
                            <p><?php esc_html_e('Yes. Open the Live Queue page on any phone to see who is with the doctor now and the patients still waiting.', 'medbook'); ?></p>
                        </details>
                </div>
            </div>
        </section>

        <section class="medbook-home-wrap">
            <div class="medbook-cta-banner medbook-doctors-cta">
                <div class="medbook-cta-banner-text">
                    <h2><?php esc_html_e('Found your doctor?', 'medbook'); ?></h2>
                    <p><?php esc_html_e('Book now and get your serial number in seconds.', 'medbook'); ?></p>
                </div>
                <a class="medbook-cta-banner-btn" href="<?php echo esc_url($booking_url); ?>"><?php esc_html_e('Book an appointment', 'medbook'); ?></a>
            </div>
        </section>
    </div>
<?php endif; ?>
<?php get_footer(); ?>
