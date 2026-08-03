<?php
/**
 * Template Name: Live Queue (MedBook)
 *
 * Renders the page's own edited content (freely designable in the block
 * editor, or a full Elementor-built page) followed by the
 * [mdbk_queue_list] shortcode as a guaranteed fallback — but only for a
 * page that ISN'T already Elementor-managed. An Elementor page's own design
 * has already decided where and how that shortcode appears (as one of its
 * own widgets), so appending it again here would just duplicate it; the
 * plain generic title/wrapper are skipped for the same reason — an
 * Elementor page supplies its own heading and layout entirely.
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
?>
<main class="medbook-page medbook-page--live-queue">
    <?php if (!$is_elementor_page) : ?>
        <h1 class="medbook-page-title"><?php the_title(); ?></h1>
    <?php endif; ?>
    <?php if ('' !== $page_content) : ?>
        <div class="medbook-page-content"><?php echo $page_content; // phpcs:ignore -- the_content() output, already filtered/escaped by core ?></div>
    <?php endif; ?>
    <?php if (!$is_elementor_page) : ?>
        <div class="medbook-page-shortcode">
            <?php echo do_shortcode('[mdbk_queue_list]'); ?>
        </div>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
