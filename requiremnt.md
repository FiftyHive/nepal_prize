# Taxpayer Incentive Prize Program — Website Development Specification

## 1. Project Overview

Build a simple, fast, mobile-friendly website for checking coupons from the **Government of Nepal Taxpayer Incentive Prize Program**.

The primary purpose of the website is:

> Allow a user to select a prize period, enter one or multiple coupon codes, complete CAPTCHA verification, and immediately see whether each coupon has been allotted a prize.

The website should have a very simple interface inspired by the usability and minimalism of the CDSC IPO result website:

https://iporesult.cdsc.com.np/

The website should **not redirect the user to a separate result page** after checking. The result must appear dynamically below the existing form using AJAX/fetch.

The system must **not query or scrape the IRD website when an individual user checks a coupon**.

Instead:

1. A scheduled scraper periodically visits the official IRD prize website.
2. It extracts the winner coupon numbers and their corresponding date ranges.
3. It stores/synchronizes the data into our own database.
4. User coupon checks are performed entirely against our own database.

Official IRD source:

https://prize.ird.gov.np/

No IRD API should be used or assumed. The system must work by scraping the publicly available website.

---

# 2. Main Objectives

The system must provide:

### Public users

* Select a prize period.
* Enter one or multiple coupon codes.
* Complete CAPTCHA.
* Click "View Result".
* See the result immediately below the form.
* Winning/allotted coupons should be clearly shown in green.
* Non-winning/not-allotted coupons should be clearly shown in red.
* No page redirect after checking.
* No separate result URL.
* No need to visit the IRD website.

### Administrators

* Manage prize periods.
* Manage winner coupons.
* Manage blog posts.
* View coupon-check statistics/logs.
* Run the IRD scraper manually.
* View scraper logs/status.
* Manage site settings where appropriate.

### Automated system

* Periodically scrape the official IRD website.
* Detect winner sections.
* Extract coupon numbers.
* Extract corresponding date ranges.
* Match the date ranges to local prize periods.
* Store new winners.
* Avoid duplicate records.
* Keep historical data.
* Log scraper failures and changes.

---

# 3. Public Website Route Structure

Keep the public website extremely simple.

Required public routes:

```text
/home
/blog
/blog/{blog-slug}
```

Admin:

```text
/admin
```

The root route `/` may redirect to `/home` if desired.

Do NOT create public routes such as:

```text
/result
/check
/check-result
/winner
/coupon/{coupon}
```

The coupon checking result must remain on `/home`.

The coupon code must never be exposed in the URL.

---

# 4. UI/UX Requirements

## Overall Design Philosophy

The interface should be:

* Minimal
* Clean
* Fast
* Mobile-first
* Government-service-like
* Easy to understand
* Free from unnecessary decoration
* Accessible to users with basic technical knowledge
* Optimized for low-bandwidth users

Take inspiration from:

https://iporesult.cdsc.com.np/

Do NOT copy its branding, logo, colors, or exact design.

The goal is to replicate its **simplicity and task-focused UX**, not its visual identity.

---

# 5. Homepage `/home`

The homepage is the main product.

The user should immediately understand:

> Select your prize period → Enter coupon number → Verify → View result

## Suggested layout

```text
------------------------------------------------
             Taxpayer Incentive Prize
             Coupon Checker

Select Prize Period

[ 2083                         ▼ ]

[ Shrawan 1 - 15               ]
[ Shrawan 16 - 30              ]
[ Bhadra 1 - 15                ]
[ Bhadra 16 - 31               ]

Coupon Number

[ Enter coupon number(s)                  ]

Example:
12345678, 23456789, 34567890

[ CAPTCHA ]

             [ View Result ]


------------------------------------------------
RESULT
------------------------------------------------

12345678
Congratulations! Your coupon has been allotted.
                 GREEN

23456789
Your coupon has not been allotted.
                 RED

------------------------------------------------
```

The actual visual design should be more polished than this wireframe, but maintain the same simplicity.

---

# 6. Prize Period Selection

The period selector must be dynamically populated from the database.

Do NOT hard-code periods into frontend code.

Example:

```text
2083

Shrawan 1 - 15
Shrawan 16 - 30
Bhadra 1 - 15
Bhadra 16 - 31
Ashwin 1 - 15
...
```

Future years should be possible:

```text
2084
2085
...
```

The admin must be able to add/edit/disable periods.

---

# 7. Prize Period Data

A prize period should contain at minimum:

```text
id
year
month
start_day
end_day
start_date
end_date
display_label
draw_date
status
created_at
updated_at
```

The system should store actual dates in a normalized format.

Example:

```text
start_date:
2026-07-17

end_date:
2026-07-31
```

Display label:

```text
2083 Shrawan 1 - 15
```

The scraper should use the actual Gregorian date range obtained from IRD to identify the correct local period.

The Nepali display label should be controlled by the administrator.

---

# 8. Coupon Input

The user must be able to enter:

### Single coupon

```text
123456789
```

### Multiple coupons

```text
123456789, 234567890, 345678901
```

The system must:

* Split values by comma.
* Trim whitespace.
* Remove empty values.
* Remove duplicate coupon numbers.
* Validate coupon format.
* Prevent obviously invalid input.
* Preserve the original coupon number for displaying the result.

Example:

```text
123456789,  234567890 ,123456789
```

should become:

```text
123456789
234567890
```

---

# 9. AJAX Result System

The form must submit using AJAX/fetch.

Do NOT perform a traditional redirect.

Do NOT create a result page.

The flow should be:

```text
User clicks View Result
        ↓
Frontend validates input
        ↓
CAPTCHA validation
        ↓
AJAX request to Laravel
        ↓
Laravel checks local database
        ↓
JSON response
        ↓
Frontend renders results below form
```

The URL remains:

```text
/home
```

The result should appear without navigating away.

A small loading indicator should be displayed while the request is processing.

Example:

```text
Checking your coupon...
```

---

# 10. Result UI

Each coupon should receive its own result line/card.

## Winning coupon

Use green styling.

Example:

```text
🟢 123456789

Congratulations!
Your coupon has been allotted.
```

The result should be visually positive but not excessively animated.

## Non-winning coupon

Use red styling.

Example:

```text
🔴 234567890

Your coupon has not been allotted.
```

The distinction between winning and non-winning coupons must be immediately visible.

Use both:

* Color
* Text
* Optional icon

Do not rely on color alone for accessibility.

---

# 11. Multiple Coupon Result

For example, if the user submits:

```text
123456789, 234567890, 345678901, 456789012
```

display:

```text
Results

🟢 123456789
   Congratulations! Your coupon has been allotted.

🔴 234567890
   Your coupon has not been allotted.

🟢 345678901
   Congratulations! Your coupon has been allotted.

🔴 456789012
   Your coupon has not been allotted.
```

Results should be displayed in the same order as the user's input.

---

# 12. CAPTCHA

The coupon-checking form must include CAPTCHA.

The CAPTCHA must be verified server-side.

The system should not perform coupon checking if CAPTCHA validation fails.

Possible implementation:

* Google reCAPTCHA
* Cloudflare Turnstile
* Another appropriate CAPTCHA solution

Keep the CAPTCHA implementation abstract enough that it can be replaced later.

The backend must never trust a client-side CAPTCHA result.

---

# 13. User Flow

## Normal user flow

```text
User visits /home
        ↓
Sees coupon checker
        ↓
Selects prize period
        ↓
Enters one or multiple coupon codes
        ↓
Completes CAPTCHA
        ↓
Clicks View Result
        ↓
AJAX request
        ↓
Backend validates request
        ↓
Backend checks local winner database
        ↓
JSON response
        ↓
Results displayed below form
```

No redirect should occur.

---

# 14. User Error Flow

### No period selected

Show:

```text
Please select a prize period.
```

### Coupon field empty

Show:

```text
Please enter at least one coupon number.
```

### Invalid coupon format

Show:

```text
Please enter a valid coupon number.
```

### CAPTCHA failure

Show:

```text
Please complete the verification.
```

### Server error

Show:

```text
Unable to check the coupon right now.
Please try again.
```

Do not expose technical/server errors to the user.

---

# 15. Winner Database

Winner coupons must be stored locally.

Suggested table:

```text
winner_coupons

id
period_id
coupon_code
prize
source
created_at
updated_at
```

Important database constraint:

```text
UNIQUE(period_id, coupon_code)
```

This prevents duplicate winners.

The coupon checker should query this database.

It should NOT scrape IRD.

---

# 16. Critical Architecture Decision

The system MUST follow this architecture:

```text
             IRD WEBSITE
                  │
                  │ Periodic scraping
                  ▼
          ┌─────────────────┐
          │ IRD Scraper     │
          └────────┬────────┘
                   │
                   ▼
          ┌─────────────────┐
          │ Local Database  │
          │                 │
          │ Prize Periods   │
          │ Winner Coupons  │
          └────────┬────────┘
                   │
                   │ AJAX lookup
                   ▼
             Public Website
                   │
                   ▼
                User
```

Never:

```text
User → IRD → Result
```

The user checker must never depend on IRD being online.

---

# 17. IRD Scraper

The official IRD website is:

https://prize.ird.gov.np/

The scraper must extract only the required winner information.

Do not scrape unnecessary content.

## Known IRD HTML hierarchy

The winner information can be reached through the following structure:

```text
1. div.portal-menu

2. div.portal-menu-group

3. div.portal-menu-title
   → Winner

4. button.portal-tab
   → click this button

5. button.winner-section-header
   → click each relevant winner section

6. div.winner-section-body.winner-list

7. article.winner-card

8. div.min-w-0.flex-1

9. div.coupon-numerals.break-all.font-mono.font-bold.text-xl.text-primary
   → coupon number

10. div.mt-0.5.truncate.text-foreground.font-medium
   → date range
```

Example data:

```text
Coupon:
123456789

Date:
Jul 17, 2026 to Jul 31, 2026
```

These are the primary pieces of information required.

---

# 18. Scraper Behavior

The scraper should:

1. Open the IRD prize website.
2. Locate the Winner section.
3. Click the Winner tab.
4. Wait for winner sections to become available.
5. Locate each `winner-section-header`.
6. Click the relevant section.
7. Wait for its winner list to become visible.
8. Find all `article.winner-card` elements.
9. Extract the coupon number.
10. Extract the date range.
11. Normalize the date range.
12. Find the corresponding local prize period.
13. Store the coupon.
14. Skip existing coupon/period combinations.
15. Log the scraper result.

---

# 19. Scraper Technology

Because the winner lists are revealed through button interaction, use browser automation rather than assuming a simple static HTTP request is sufficient.

Recommended:

```text
Playwright
```

The scraper can be implemented as:

* A dedicated Node.js Playwright process called by Laravel, OR
* An appropriate Laravel/browser automation integration if the deployment environment supports it.

The implementation must be compatible with the actual hosting environment.

Do not use or depend on an undocumented IRD API.

The system is explicitly a website scraper.

---

# 20. Scraper Frequency

Use Laravel Scheduler/cron.

Example:

```text
Every hour
```

The exact frequency should be configurable.

The scraper should also be manually executable from the admin panel.

Example:

```text
Admin → Scraper → Run Now
```

The manual action should show:

```text
Scraper started...
```

and eventually:

```text
Scraper completed successfully.

Periods processed: 12
Winner coupons found: 1,250
New coupons added: 15
Existing coupons skipped: 1,235
```

---

# 21. Scraper Idempotency

The scraper must be safe to run repeatedly.

If the same coupon is encountered again:

```text
123456789
```

do not create another database row.

Use:

```text
UNIQUE(period_id, coupon_code)
```

Example:

First scrape:

```text
123456789
234567890
345678901
```

Second scrape:

```text
123456789
234567890
345678901
```

No duplicates should be created.

If IRD adds:

```text
456789012
```

the next scrape should add only the new coupon.

---

# 22. Historical Data

Do NOT automatically delete winner coupons just because they are no longer visible on a subsequent IRD scrape.

Historical winner data should be preserved.

If a change/disappearance is detected, record it in scraper logs rather than silently deleting historical data.

---

# 23. Scraper Logs

Create a scraper log table.

Suggested fields:

```text
scraper_logs

id
started_at
completed_at
status
periods_processed
coupons_found
new_coupons
existing_coupons
errors
error_message
created_at
updated_at
```

Admin should be able to see:

```text
Date/Time
Status
Periods Processed
Coupons Found
New Coupons
Errors
```

Example:

```text
10 Aug 2026 02:00
SUCCESS
15 periods
1,250 coupons
12 new
0 errors
```

If the IRD page structure changes and scraping fails, the failure must be visible to the administrator.

---

# 24. Unknown Prize Period

If the scraper finds:

```text
Jul 17, 2026 to Jul 31, 2026
```

but cannot find a matching local period, it must NOT silently insert the winner without a period.

Instead:

```text
Unknown prize period detected:
Jul 17, 2026 to Jul 31, 2026
```

Record it in the scraper log/error system.

Admin can then create the corresponding period.

---

# 25. Admin Panel

The admin panel should be functional but simple.

Main dashboard:

```text
Dashboard

Prize Periods
Winner Coupons
Blog Posts
Coupon Checks
Scraper
Settings
```

---

# 26. Prize Period Management

Admin should be able to:

* Create period
* Edit period
* Enable/disable period
* Set year
* Set month
* Set start/end day
* Set actual start/end dates
* Set display label
* Set draw date

Example:

```text
Year:
2083

Month:
Shrawan

Start Day:
1

End Day:
15

Start Date:
2026-07-17

End Date:
2026-07-31

Display:
2083 Shrawan 1 - 15

Status:
Active
```

---

# 27. Winner Coupon Management

Admin should be able to:

* View winner coupons
* Search coupon
* Filter by period
* Manually add a winner
* Edit winner information if necessary
* Delete/correct erroneous records
* Import winners if required
* See source/scraper information

Example:

```text
Period:
2083 Shrawan 1 - 15

Coupon:
123456789

Prize:
Optional
```

---

# 28. Blog System

Create a simple blog.

Public:

```text
/blog
```

Individual article:

```text
/blog/{blog-slug}
```

Admin should be able to:

* Create post
* Edit post
* Delete post
* Publish/unpublish
* Set title
* Set slug
* Set featured image
* Write content
* Set SEO title
* Set SEO description
* Set publication date

Suggested fields:

```text
blog_posts

id
title
slug
excerpt
content
featured_image
seo_title
seo_description
status
published_at
created_at
updated_at
```

---

# 29. Blog UI

The blog should follow the same clean visual language as the homepage.

Example:

```text
Latest Information

How to Check Your Taxpayer Incentive Prize Coupon

Learn how to check whether your coupon has been
allotted a prize.

[Read More]
```

Avoid turning the website into a large news portal.

The blog is secondary to the coupon checker.

---

# 30. Coupon Check Logging

The system may record basic statistics about coupon checking.

Suggested table:

```text
coupon_checks

id
period_id
coupon_count
winner_count
created_at
```

For privacy and simplicity, do not store unnecessary user information.

The admin can see:

```text
Today's Checks: 2,540
Coupons Checked: 7,850
Winning Coupons Found: 31
```

If IP-based rate limiting is required, IP information can be handled separately according to the site's privacy requirements.

---

# 31. Rate Limiting

The AJAX coupon-check endpoint must have rate limiting.

Example:

```text
Maximum requests per IP within a defined time period
```

This protects the endpoint from abuse.

CAPTCHA should also be validated server-side.

---

# 32. Security

Implement:

* CSRF protection
* Server-side validation
* CAPTCHA validation
* Rate limiting
* Authentication for admin
* Authorization for admin functions
* SQL injection protection through Laravel ORM/query builder
* XSS protection
* Secure file upload handling for blog images
* Input normalization
* Secure session handling

Do not expose:

* Database errors
* Scraper errors
* Stack traces
* Internal paths
* Server configuration

to public users.

---

# 33. Admin Authentication

Admin area must require authentication.

Example:

```text
/admin
```

should not be publicly accessible without login.

Use Laravel's authentication system.

Admin credentials must never be hard-coded.

---

# 34. Recommended Tech Stack

## Backend

```text
Laravel
PHP 8.2+
```

Use the latest stable Laravel version compatible with the deployment environment.

## Database

Preferred:

```text
MySQL 8+
```

PostgreSQL is also acceptable if the hosting environment supports it.

## Frontend

```text
Blade
Tailwind CSS
Alpine.js
Vanilla JavaScript / Fetch API
```

Do not introduce React/Next.js unless there is a strong technical reason.

The project is intentionally simple.

## AJAX

Use:

```text
Fetch API
```

or Alpine.js.

The endpoint should return JSON.

Example:

```json
{
    "success": true,
    "results": [
        {
            "coupon": "123456789",
            "allotted": true
        },
        {
            "coupon": "234567890",
            "allotted": false
        }
    ]
}
```

## Scraper

```text
Playwright
```

Use browser automation because the winner sections require interaction.

## Scheduling

```text
Laravel Scheduler
Linux Cron
```

## Web Server

```text
Nginx
```

or Apache depending on hosting.

---

# 35. Suggested Laravel Project Structure

```text
app/
├── Console/
│   └── Commands/
│       └── ScrapeIRDPrizeWinners.php
│
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── BlogController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── PrizePeriodController.php
│   │       ├── WinnerCouponController.php
│   │       ├── BlogPostController.php
│   │       ├── ScraperController.php
│   │       └── CouponCheckController.php
│   │
│   └── Requests/
│       └── CouponCheckRequest.php
│
├── Models/
│   ├── PrizePeriod.php
│   ├── WinnerCoupon.php
│   ├── BlogPost.php
│   ├── CouponCheck.php
│   └── ScraperLog.php
│
└── Services/
    ├── CouponCheckerService.php
    └── IRDScraperService.php
```

---

# 36. Suggested Routes

Public:

```php
Route::get('/', fn () => redirect('/home'));

Route::get('/home', [HomeController::class, 'index'])
    ->name('home');

Route::get('/blog', [BlogController::class, 'index'])
    ->name('blog.index');

Route::get('/blog/{slug}', [BlogController::class, 'show'])
    ->name('blog.show');
```

The AJAX endpoint can be an internal POST endpoint used by `/home`.

It does not need to create a public result page.

For example:

```text
POST /home/check
```

This endpoint exists only to process the AJAX request.

The important requirement is:

> There must be no public result page and no redirect.

Admin routes:

```text
/admin/...
```

---

# 37. Coupon Checking Backend Flow

When `/home/check` receives a request:

```text
1. Validate CSRF
2. Validate CAPTCHA
3. Validate period_id
4. Validate coupon input
5. Normalize coupon numbers
6. Remove duplicates
7. Apply rate limit
8. Query winner_coupons
9. Generate result for each submitted coupon
10. Optionally record statistics
11. Return JSON
```

The database query should be optimized.

Do not execute one database query per coupon.

Instead:

```text
WHERE coupon_code IN (...)
AND period_id = selected_period
```

Then map the results back to the original coupon list.

---

# 38. Performance Requirements

The coupon checker should be extremely fast.

The public checker should:

* Never access IRD
* Never invoke the scraper
* Never wait for external services except CAPTCHA
* Use indexed database queries
* Return JSON quickly

Expected flow:

```text
User → AJAX → Database → JSON → UI
```

not:

```text
User → AJAX → IRD → scrape → parse → database → JSON
```

---

# 39. SEO

The homepage should have:

```text
SEO Title
SEO Description
Canonical URL
```

Blog articles should have individual:

```text
Title
Meta Description
Canonical URL
Open Graph metadata
```

Use clean URLs:

```text
/blog/how-to-check-taxpayer-incentive-prize
```

No unnecessary query parameters.

---

# 40. Mobile UX

The majority of users may access the website through mobile devices.

Therefore:

* Form must be mobile-first.
* Coupon input must be large enough to type comfortably.
* Buttons must have sufficient touch area.
* Result cards must fit narrow screens.
* Avoid horizontal scrolling.
* Blog must be responsive.
* Admin can be responsive but does not need to be as optimized as public pages.

---

# 41. Accessibility

Use:

* Proper labels
* Semantic HTML
* Keyboard-accessible controls
* Visible focus states
* Accessible error messages
* Color + text/icon for result states
* Adequate contrast

Do not communicate:

> Winner = green only

Instead:

```text
Green + Congratulations + icon
```

and:

```text
Red + Not Allotted + icon
```

---

# 42. Empty/Initial Result State

When the user first opens `/home`, do not show an empty result box.

The result section should only appear after a successful check.

Before checking:

```text
Form
```

After checking:

```text
Form

Results
```

If the user performs another search, replace the previous results with the new results rather than continuously appending results.

---

# 43. Scraper Error Handling

If the IRD website is unavailable:

```text
Scraper Status: Failed

Reason:
Unable to access IRD website.
```

Do not delete existing data.

The public coupon checker should continue operating using the last successfully synchronized database.

If the HTML structure changes:

```text
Scraper Status: Failed

Reason:
Winner section could not be located.
```

This should trigger an obvious admin warning.

---

# 44. Data Synchronization Principle

The local database is the source used for public coupon checking.

IRD is the external source used for synchronization.

Therefore:

```text
IRD = External source
Local DB = Operational source
```

This means historical checking remains possible even when the IRD website is temporarily unavailable.

---

# 45. UI Navigation

Keep navigation minimal.

Suggested desktop header:

```text
Logo / Site Name

Home
Blog

                         Admin/Login
```

On mobile:

```text
Logo / Site Name

☰
```

or a simple compact navigation.

Do not add unnecessary navigation items.

---

# 46. Footer

Simple footer:

```text
Taxpayer Incentive Prize Program

Home | Blog

Information provided through this website
is for coupon checking purposes.

© 2026
```

Include an appropriate disclaimer explaining that the website is an independent information/checking interface if applicable.

Do not imply that the website is an official Government of Nepal website unless officially authorized.

---

# 47. Important Disclaimer

Because the website is scraping and presenting information from IRD, include a clear but unobtrusive disclaimer.

Example concept:

```text
This website provides a convenient way to check taxpayer
incentive prize coupon information. For official information,
please refer to the Inland Revenue Department.
```

The exact wording should be finalized before production.

---

# 48. Complete System Flow

## A. Initial Setup

```text
Admin
  ↓
Creates prize periods
  ↓
Scraper runs
  ↓
IRD winner data collected
  ↓
Coupons stored in database
```

## B. Automated Update

```text
Laravel Scheduler
  ↓
Run IRD scraper
  ↓
Open IRD website
  ↓
Click Winner
  ↓
Open winner sections
  ↓
Extract coupon + date range
  ↓
Match local period
  ↓
Insert new winners
  ↓
Skip duplicates
  ↓
Log result
```

## C. User Check

```text
User
  ↓
/home
  ↓
Select period
  ↓
Enter coupon(s)
  ↓
CAPTCHA
  ↓
View Result
  ↓
AJAX POST
  ↓
Laravel
  ↓
Local winner database
  ↓
JSON
  ↓
Results displayed below form
```

## D. Blog

```text
Admin
  ↓
Create blog
  ↓
Publish
  ↓
/blog
  ↓
/blog/{slug}
```

---

# 49. Example End-to-End Scenario

Assume IRD contains:

```text
Period:
Jul 17, 2026 to Jul 31, 2026

Winner coupons:

123456789
234567890
345678901
```

The scraper finds these values.

The administrator has configured:

```text
2083 Shrawan 1 - 15

start_date:
2026-07-17

end_date:
2026-07-31
```

The scraper matches:

```text
IRD period
Jul 17, 2026 → Jul 31, 2026

        ↓

Local period
2083 Shrawan 1 - 15
```

and stores:

```text
123456789 → period_id 1
234567890 → period_id 1
345678901 → period_id 1
```

A user enters:

```text
123456789, 888888888, 345678901
```

The AJAX request checks the local database.

Response:

```text
123456789
Congratulations! Your coupon has been allotted.

888888888
Your coupon has not been allotted.

345678901
Congratulations! Your coupon has been allotted.
```

The page remains:

```text
/home
```

No redirect occurs.

---

# 50. Non-Goals

Do NOT build:

* User registration
* User accounts
* User dashboards
* User profiles
* Payment system
* Coupon purchasing
* Complex analytics dashboard
* Real-time IRD querying
* IRD API integration
* Social network features
* Complex frontend SPA
* Separate result pages
* Coupon-specific public URLs

Keep the application focused.

---

# 51. Development Priorities

Build in this order:

### Phase 1 — Database

Implement:

* Prize periods
* Winner coupons
* Blog posts
* Scraper logs
* Coupon check statistics
* Admin authentication

### Phase 2 — Public UI

Implement:

* `/home`
* Period selector
* Coupon input
* CAPTCHA
* AJAX checking
* Result rendering

### Phase 3 — Admin

Implement:

* Dashboard
* Period management
* Winner management
* Blog management
* Scraper management
* Logs

### Phase 4 — IRD Scraper

Implement:

* Browser automation
* Winner navigation
* Winner section extraction
* Coupon extraction
* Date extraction
* Period matching
* Database synchronization
* Logging

### Phase 5 — Security and optimization

Implement:

* Rate limiting
* Validation
* CAPTCHA
* Database indexes
* Caching where useful
* Error handling
* Security hardening

### Phase 6 — Testing

Test:

* Single coupon
* Multiple coupons
* Duplicate coupons
* Invalid coupons
* Empty input
* Invalid period
* CAPTCHA failure
* Winning coupon
* Non-winning coupon
* Mixed winning/non-winning coupons
* Scraper failure
* IRD unavailable
* Duplicate scraper execution
* New winner added by IRD
* Historical winner preservation
* Mobile interface

---

# 52. Final Technical Principle

The most important architectural rule of this project is:

> **Scrape IRD periodically, store the winner data locally, and perform all user coupon checks against the local database.**

The user should never wait for IRD.

The public website should never scrape IRD.

The public website should only communicate with our Laravel application.

The scraper is a separate background synchronization process.

This gives the application:

* Fast coupon checking
* Low IRD traffic
* Better reliability
* Historical data preservation
* Easy administration
* Better scalability
* Easier debugging
* Independence from temporary IRD downtime

The final public experience should remain extremely simple:

```text
                 TAXPAYER INCENTIVE PRIZE

                  Check Your Coupon

              [ Select Prize Period ]

              [ Enter Coupon Number ]

                   [ CAPTCHA ]

                 [ View Result ]


                    RESULTS

        🟢 Congratulations — Allotted

        🔴 Not Allotted

        🟢 Congratulations — Allotted


                 Home | Blog
```

The complexity should exist **behind the scenes**, not in the user's interface.
