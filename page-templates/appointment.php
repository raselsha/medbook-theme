<?php
/**
 * Template Name: Appointment (MedBook)
 *
 * Renders the page's own edited content (freely designable in the block
 * editor, or a full Elementor-built page) followed by the
 * [mdbk_appointment_form] shortcode as a guaranteed fallback — but only for
 * a page that ISN'T already Elementor-managed. An Elementor page's own
 * design has already decided where and how that shortcode appears (as one
 * of its own widgets), so appending it again here would just duplicate it.
 *
 * For a non-Elementor page (the common case: this theme's own default,
 * dependency-free look) this also builds a proper hero + "How Booking
 * Works" + CTA banner around the booking widget, instead of a bare title —
 * this is the site's front page, so it's worth it looking finished out of
 * the box. The hero uses the page's own Featured Image if one is set
 * (Page Attributes → Featured Image), falling back to a plain gradient
 * when none is set yet.
 */
defined('ABSPATH') || exit;
get_header();
$is_elementor_page = get_post_meta(get_the_ID(), '_elementor_edit_mode', true) === 'builder';
$live_queue_page = medbook_get_page_by_template('page-templates/live-queue.php');
?>
<?php if (!$is_elementor_page) : ?>
    <section class="medbook-hero<?php echo has_post_thumbnail() ? ' medbook-hero--has-image' : ''; ?>">
        <?php if (has_post_thumbnail()) : ?>
            <div class="medbook-hero-bg" style="background-image:url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>');"></div>
            <div class="medbook-hero-overlay"></div>
        <?php endif; ?>
        <div class="medbook-hero-inner">
            <h1 class="medbook-hero-title"><?php the_title(); ?></h1>
            <p class="medbook-hero-subtitle"><?php esc_html_e('Connect with trusted specialists and confirm your visit in minutes.', 'medbook'); ?></p>
        </div>
    </section>
<?php endif; ?>
<main class="medbook-page medbook-page--appointment">
    <div class="medbook-page-content">
        <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
    </div>

    <?php if (!$is_elementor_page) : ?>
        <div class="medbook-page-shortcode medbook-page-shortcode--card">
            <?php echo do_shortcode('[mdbk_appointment_form]'); ?>
        </div>

        <section class="medbook-section">
            <h2 class="medbook-section-title"><?php esc_html_e('How Booking Works', 'medbook'); ?></h2>
            <div class="medbook-steps">
                <div class="medbook-step">
                    <span class="medbook-step-number">01</span>
                    <h3><?php esc_html_e('Choose a Specialty', 'medbook'); ?></h3>
                    <p><?php esc_html_e('Browse our specialists and find the right doctor for your needs.', 'medbook'); ?></p>
                </div>
                <div class="medbook-step">
                    <span class="medbook-step-number">02</span>
                    <h3><?php esc_html_e('Pick Your Time', 'medbook'); ?></h3>
                    <p><?php esc_html_e('See real-time availability and select a slot that fits your day.', 'medbook'); ?></p>
                </div>
                <div class="medbook-step">
                    <span class="medbook-step-number">03</span>
                    <h3><?php esc_html_e('Confirm & Visit', 'medbook'); ?></h3>
                    <p><?php esc_html_e('Get instant confirmation, then track your place in the live queue.', 'medbook'); ?></p>
                </div>
            </div>
        </section>

        <?php if ($live_queue_page) : ?>
            <section class="medbook-cta-banner">
                <div class="medbook-cta-banner-text">
                    <h2><?php esc_html_e('Already Booked?', 'medbook'); ?></h2>
                    <p><?php esc_html_e('Track your live position in the queue in real time.', 'medbook'); ?></p>
                </div>
                <a class="medbook-cta-banner-btn" href="<?php echo esc_url(get_permalink($live_queue_page)); ?>"><?php esc_html_e('View Live Queue', 'medbook'); ?></a>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
