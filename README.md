# MedBook

Custom WordPress theme for the clinic's public site. It provides the top
navigation (Doctors / Appointment / Live Queue / Login) and four page
templates that each embed one [doctor-appointment](https://github.com/raselsha/doctor-appointment)
plugin shortcode (`[mdbk_doctor_list]`, `[mdbk_appointment_form]`,
`[mdbk_queue_list]`) plus a custom Login screen — every template still
renders the page's own edited content too, so a page using one of these
templates can be freely designed in the block editor while the functional
widget always renders alongside it. The Book Appointment page is the site's
front page; the plugin's own admin panel (`?page=mdbk-*`) is unaffected by
this theme.

## What it does

- **Front page = Book Appointment**, not a login screen — unlike the
  tailor-manager plugin's own Tailors theme, this site's actual booking
  workflow stays the default landing experience.
- **Login page** (`page-templates/login.php`), custom-styled and
  AJAX-submitted via `wp_signon()` (plain-HTML `wp-login.php` fallback if JS
  is unavailable), reuses the doctor-appointment plugin's own
  `login_redirect` filter (`MDBK_Admin_Dashboard::doctor_login_redirect()`)
  so a Manager/admin lands on `?page=mdbk-dashboard` and a
  doctor/receptionist lands on `?page=mdbk-schedule` — one source of truth
  for "who goes where" either way.
- **Forgot password**, handled in the same card via `retrieve_password()`
  (the same core function `wp-login.php`'s own "Lost your password?" screen
  uses) — always returns the same response whether or not the account
  exists, to avoid username enumeration.
- Nav dynamically swaps "Login" for "Dashboard" + "Logout" once signed in.
- No build step — plain PHP/CSS/JS, matching the doctor-appointment plugin's
  own conventions.

## Structure

```
medbook/
├── style.css                       # theme header + all site + login-page styling
├── functions.php                   # theme setup, nav menu location, AJAX login + forgot-password handlers
├── header.php / footer.php         # site chrome — clinic name/logo, nav, footer
├── index.php                       # fallback for anything without a specific template
├── page.php                        # generic page template (per-doctor pages etc.)
├── page-templates/
│   ├── doctors.php                 # Template Name: Doctors (MedBook)
│   ├── appointment.php             # Template Name: Appointment (MedBook)
│   ├── live-queue.php              # Template Name: Live Queue (MedBook)
│   └── login.php                   # Template Name: Login (MedBook)
└── assets/js/{login,nav}.js
```
