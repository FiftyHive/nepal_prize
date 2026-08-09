/**
 * IRD Prize Winner Scraper
 *
 * Scrapes https://prize.ird.gov.np/ for winner coupon numbers and their date ranges.
 * Outputs JSON to stdout. Called by GitHub Actions, which then POSTs to the Laravel webhook.
 *
 * Output format:
 * {
 *   "winners": [
 *     { "coupon": "123456789", "start_date": "2026-07-17", "end_date": "2026-07-31", "prize": null }
 *   ],
 *   "errors": [],
 *   "sections_processed": 5
 * }
 */

const { chromium } = require('playwright');

const IRD_URL = 'https://prize.ird.gov.np/';
const TIMEOUT  = 30_000; // 30 seconds

/**
 * Parse a date string like "Jul 17, 2026" to "2026-07-17"
 */
function parseDate(str) {
    if (!str) return null;
    const cleaned = str.trim().replace(/\s+/g, ' ');
    const d = new Date(cleaned);
    if (isNaN(d.getTime())) return null;
    const year  = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day   = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Parse a date range string like "Jul 17, 2026 to Jul 31, 2026"
 * Returns { startDate, endDate } in YYYY-MM-DD format, or null if parsing fails.
 */
function parseDateRange(rangeStr) {
    if (!rangeStr) return null;
    const parts = rangeStr.split(/\bto\b/i);
    if (parts.length !== 2) return null;
    const startDate = parseDate(parts[0].trim());
    const endDate   = parseDate(parts[1].trim());
    if (!startDate || !endDate) return null;
    return { startDate, endDate };
}

(async () => {
    const winners = [];
    const errors  = [];
    let sectionsProcessed = 0;

    const browser = await chromium.launch({
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });

    const page = await browser.newPage();
    page.setDefaultTimeout(TIMEOUT);

    try {
        // Step 1: Navigate to IRD prize page
        await page.goto(IRD_URL, { waitUntil: 'domcontentloaded', timeout: TIMEOUT });

        // Step 2: Find the Winner tab button and click it
        // Structure: div.portal-menu > div.portal-menu-group (containing "Winner") > button.portal-tab
        const winnerTab = await page.locator('div.portal-menu-group:has(div.portal-menu-title:has-text("Winner")) button.portal-tab').first();
        await winnerTab.waitFor({ state: 'visible', timeout: TIMEOUT });
        await winnerTab.click();

        // Step 3: Wait for winner sections to appear
        await page.waitForSelector('button.winner-section-header', { timeout: TIMEOUT });

        const sectionHeaders = await page.locator('button.winner-section-header').all();

        if (sectionHeaders.length === 0) {
            errors.push('No winner section headers found on the page.');
        }

        // Step 4: Process each section
        for (const header of sectionHeaders) {
            try {
                await header.click();

                // Wait for the winner list to be visible inside this section
                // The list follows the header button in the DOM
                await page.waitForTimeout(800); // Small delay for animation

                const winnerCards = await page.locator('div.winner-section-body.winner-list article.winner-card:visible').all();

                if (winnerCards.length === 0) {
                    continue;
                }

                sectionsProcessed++;

                for (const card of winnerCards) {
                    try {
                        // Extract coupon number
                        const couponEl = card.locator('div.coupon-numerals').first();
                        const couponText = (await couponEl.textContent({ timeout: 5000 }))?.trim() ?? '';
                        const coupon = couponText.replace(/\D/g, ''); // digits only

                        if (!coupon) {
                            errors.push('Found winner card with no readable coupon number.');
                            continue;
                        }

                        // Extract date range
                        const dateEl   = card.locator('div.mt-0\\.5').first();
                        const dateText = (await dateEl.textContent({ timeout: 5000 }))?.trim() ?? '';
                        const parsed   = parseDateRange(dateText);

                        if (!parsed) {
                            errors.push(`Could not parse date range: "${dateText}" for coupon ${coupon}`);
                            // Still push with null dates so we don't lose data
                            winners.push({ coupon, start_date: null, end_date: null, prize: null, raw_date: dateText });
                            continue;
                        }

                        winners.push({
                            coupon,
                            start_date: parsed.startDate,
                            end_date:   parsed.endDate,
                            prize:      null,
                        });

                    } catch (cardErr) {
                        errors.push(`Error processing winner card: ${cardErr.message}`);
                    }
                }

            } catch (sectionErr) {
                errors.push(`Error processing section: ${sectionErr.message}`);
            }
        }

    } catch (err) {
        errors.push(`Fatal scraper error: ${err.message}`);
    } finally {
        await browser.close();
    }

    // Remove winners with null dates before sending (they were already logged as errors)
    const validWinners = winners.filter(w => w.start_date && w.end_date);

    const output = {
        winners: validWinners,
        errors,
        sections_processed: sectionsProcessed,
        total_found: validWinners.length,
        scraped_at: new Date().toISOString(),
    };

    process.stdout.write(JSON.stringify(output, null, 2));
    process.exitCode = errors.length > 0 && validWinners.length === 0 ? 1 : 0;
})();
