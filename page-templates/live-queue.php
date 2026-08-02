<?php
/**
 * Template Name: Live Queue (MedBook)
 *
 * Renders the page's own edited content (freely designable in the block
 * editor) followed by the [mdbk_queue_list] shortcode, which always
 * renders regardless of what the page's content is.
 */
defined('ABSPATH') || exit;
get_header();
?>
<main class="medbook-page medbook-page--live-queue">
    <h1 class="medbook-page-title"><?php the_title(); ?></h1>
    <div class="medbook-page-content">
        <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
    </div>
    <div class="medbook-page-shortcode">
        <?php echo do_shortcode('[mdbk_queue_list]'); ?>
    </div>
</main>
<?php get_footer(); ?>
