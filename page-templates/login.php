<?php
/**
 * Template Name: Login (MedBook)
 *
 * A custom-styled login screen (AJAX-submitted via wp_signon(), plain-HTML
 * action="wp-login.php" fallback if JS is unavailable), with an in-card
 * "forgot password" panel. template_redirect() in functions.php already
 * sends an already-logged-in visitor straight to their dashboard, so this
 * template only ever actually renders for a logged-out one.
 */
defined('ABSPATH') || exit;

$clinic_name = get_option('mdbk_clinic_name', get_bloginfo('name'));
$clinic_logo_id = (int) get_option('mdbk_clinic_logo', 0);

get_header();
?>
<div class="medbook-login-body">
    <div class="medbook-login-wrap">
        <div class="medbook-login-card">
            <div class="medbook-login-brand">
                <div class="medbook-login-mark">
                    <?php if ($clinic_logo_id) : ?>
                        <?php echo wp_get_attachment_image($clinic_logo_id, array(56, 56), false, array('alt' => esc_attr($clinic_name))); ?>
                    <?php else : ?>
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"></path></svg>
                    <?php endif; ?>
                </div>
                <h1 class="medbook-login-logo"><?php echo esc_html($clinic_name); ?></h1>
                <p class="medbook-login-tagline"><?php esc_html_e('Sign in to your MedBook account', 'medbook'); ?></p>
            </div>

            <div class="medbook-login-alert medbook-login-alert-error" id="medbook-login-error" style="display:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span id="medbook-login-error-text"></span>
            </div>
            <div class="medbook-login-alert medbook-login-alert-success" id="medbook-forgot-success" style="display:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span id="medbook-forgot-success-text"></span>
            </div>

            <form id="medbook-login-form" method="post" action="<?php echo esc_url(wp_login_url()); ?>" novalidate>
                <div class="medbook-form-row">
                    <label for="medbook-log"><?php esc_html_e('Username or Email', 'medbook'); ?></label>
                    <input type="text" name="log" id="medbook-log" autocomplete="username" autocapitalize="off" autocorrect="off" required />
                </div>
                <div class="medbook-form-row">
                    <label for="medbook-pwd"><?php esc_html_e('Password', 'medbook'); ?></label>
                    <div class="medbook-password-field">
                        <input type="password" name="pwd" id="medbook-pwd" autocomplete="current-password" required />
                        <button type="button" class="medbook-password-toggle" id="medbook-pwd-toggle" aria-label="<?php esc_attr_e('Show password', 'medbook'); ?>">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>
                <div class="medbook-form-row medbook-form-row-inline">
                    <label class="medbook-checkbox-label">
                        <input type="checkbox" name="rememberme" id="medbook-rememberme" value="forever" />
                        <?php esc_html_e('Remember me', 'medbook'); ?>
                    </label>
                    <a href="#" class="medbook-login-link" id="medbook-forgot-link"><?php esc_html_e('Forgot password?', 'medbook'); ?></a>
                </div>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url(medbook_dashboard_url()); ?>" />
                <button type="submit" class="medbook-login-submit" id="medbook-login-submit">
                    <span class="medbook-login-spinner"></span>
                    <span class="medbook-login-submit-label"><?php esc_html_e('Sign In', 'medbook'); ?></span>
                </button>
            </form>

            <form id="medbook-forgot-form" style="display:none;">
                <p class="medbook-forgot-hint"><?php esc_html_e('Enter your username or email and we\'ll send you a password reset link.', 'medbook'); ?></p>
                <div class="medbook-form-row">
                    <label for="medbook-forgot-login"><?php esc_html_e('Username or Email', 'medbook'); ?></label>
                    <input type="text" id="medbook-forgot-login" autocomplete="username" autocapitalize="off" autocorrect="off" required />
                </div>
                <button type="submit" class="medbook-login-submit" id="medbook-forgot-submit">
                    <span class="medbook-login-spinner"></span>
                    <span class="medbook-login-submit-label"><?php esc_html_e('Send Reset Link', 'medbook'); ?></span>
                </button>
                <a href="#" class="medbook-login-link medbook-back-link" id="medbook-back-to-login">&larr; <?php esc_html_e('Back to Sign In', 'medbook'); ?></a>
            </form>
        </div>
    </div>
</div>
<?php get_footer(); ?>
