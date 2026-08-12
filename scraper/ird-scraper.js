/**
 * IRD Prize Winner Scraper (Node.js)
 *
 * Scrapes official IRD prize winners via https://prize.ird.gov.np/api/v1/public/winners
 * Zero external browser dependencies for 100% reliability in any environment.
 * Outputs JSON to stdout for GitHub Actions or CLI execution.
 */

const https = require('https');

const IRD_BASE_URL = 'https://prize.ird.gov.np/api/v1/public';
const TIMEOUT = 20000;

function fetchJson(url) {
    return new Promise((resolve, reject) => {
        const req = https.get(url, {
            timeout: TIMEOUT,
            headers: {
                'Accept': 'application/json',
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            }
        }, (res) => {
            if (res.statusCode < 200 || res.statusCode >= 300) {
                return reject(new Error(`HTTP status code ${res.statusCode}`));
            }
            let body = '';
            res.on('data', chunk => body += chunk);
            res.on('end', () => {
                try {
                    resolve(JSON.parse(body));
                } catch (e) {
                    reject(new Error(`JSON parse error: ${e.message}`));
                }
            });
        });

        req.on('error', reject);
        req.on('timeout', () => {
            req.destroy();
            reject(new Error('Request timed out'));
        });
    });
}

(async () => {
    const winners = [];
    const errors = [];
    let sectionsProcessed = 0;

    try {
        let offset = 0;
        const limit = 50;
        let hasMore = true;

        while (hasMore) {
            const url = `${IRD_BASE_URL}/winners?limit=${limit}&offset=${offset}`;
            const data = await fetchJson(url);

            const draws = data.draws || [];
            if (draws.length === 0) break;

            for (const draw of draws) {
                sectionsProcessed++;
                const startDate = draw.eligible_from;
                const endDate   = draw.eligible_to;
                const prizeName = draw.category_title_en || draw.category_title_ne || draw.title_en || 'Winner';

                if (!startDate || !endDate) {
                    errors.push(`Draw ${draw.draw_id || 'unknown'} missing start or end date.`);
                    continue;
                }

                const drawWinners = draw.winners || [];
                for (const winner of drawWinners) {
                    const rawCoupon = winner.prize_coupon_number;
                    if (!rawCoupon) continue;

                    const coupon = String(rawCoupon).replace(/\D/g, '');
                    if (!coupon) continue;

                    const rank = winner.winner_rank;
                    const fullPrize = rank ? `${prizeName} (Rank #${rank})` : prizeName;

                    winners.push({
                        coupon,
                        start_date: startDate,
                        end_date: endDate,
                        prize: fullPrize,
                    });
                }
            }

            hasMore = Boolean(data.has_more);
            offset += limit;
            if (offset > 2000) break;
        }

    } catch (err) {
        errors.push(`Fatal scraper error: ${err.message}`);
    }

    const output = {
        winners,
        errors,
        sections_processed: sectionsProcessed,
        total_found: winners.length,
        scraped_at: new Date().toISOString(),
    };

    process.stdout.write(JSON.stringify(output, null, 2) + '\n');
    process.exitCode = errors.length > 0 && winners.length === 0 ? 1 : 0;
})();
