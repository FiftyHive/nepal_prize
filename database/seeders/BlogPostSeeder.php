<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'How to Check Your Taxpayer Incentive Prize Coupon Online',
                'slug' => 'how-to-check-taxpayer-incentive-prize-coupon-online',
                'excerpt' => 'A complete step-by-step guide to checking whether your purchase receipt coupon has been selected for a cash prize by the Inland Revenue Department.',
                'content' => <<<HTML
<h2>What is the Taxpayer Incentive Prize Program?</h2>
<p>The <strong>Government of Nepal Inland Revenue Department (IRD)</strong> conducts a regular incentive lottery draw to encourage consumers to obtain official tax invoices (VAT/PAN receipts) when purchasing goods and services. Every eligible purchase invoice generates a unique coupon code that enters into bi-monthly or monthly lucky draws.</p>

<h2>Step-by-Step: How to Check Your Coupon Result</h2>
<ol>
    <li><strong>Select the Prize Period:</strong> Choose the Nepali calendar draw period corresponding to your invoice date (for example, <em>2083 Shrawan 1 - 15</em>).</li>
    <li><strong>Enter Your Coupon Number(s):</strong> Type in your coupon code. If you have multiple coupons from different purchases, you can enter them separated by commas (e.g., <code>007315254493, 007755590670</code>).</li>
    <li><strong>Verify CAPTCHA:</strong> Complete the anti-bot verification if requested.</li>
    <li><strong>Click "View Result":</strong> The result will instantly appear on your screen without leaving or reloading the page.</li>
</ol>

<blockquote>
    <strong>Tip:</strong> All winning coupons are highlighted in green with prize rank details (such as Bumper Prize or Daily Prize), while non-winning coupons are shown in red.
</blockquote>

<h2>What to Do If Your Coupon Wins</h2>
<p>If your coupon number is allotted a prize, congratulations! Keep your original purchase invoice and receipt safe. You will need to submit the original bill along with your valid identification (Citizenship card or National ID) to the nearest Inland Revenue Office or through the official IRD portal to claim your reward.</p>
HTML,
                'seo_title' => 'How to Check Your Taxpayer Incentive Prize Coupon Online — Guide',
                'seo_description' => 'Learn how to check your Nepal IRD taxpayer incentive prize coupon numbers instantly using the fast online checker.',
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Everything You Need to Know About the Nepal IRD Lottery Scheme',
                'slug' => 'everything-you-need-to-know-about-nepal-ird-lottery-scheme',
                'excerpt' => 'Learn how the Government of Nepal rewards tax-compliant consumers with bumper and daily prizes on VAT purchase receipts.',
                'content' => <<<HTML
<h2>Why Did the Government Introduce This Program?</h2>
<p>The primary goal of the Taxpayer Incentive Program is to build a culture of billing transparency across retail and consumer markets in Nepal. When consumers ask for genuine tax invoices, businesses record transactions through computerized billing systems integrated with the Central Billing Monitoring System (CBMS).</p>

<h2>Prize Categories & Draws</h2>
<p>The IRD conducts draws under two major prize categories:</p>
<ul>
    <li><strong>Bumper Prize:</strong> Top monetary reward allotted to selected lucky consumer receipts across the draw period.</li>
    <li><strong>Daily / Regular Prizes:</strong> Tiered rewards distributed across multiple winning purchase receipts.</li>
</ul>

<h2>Eligibility Criteria</h2>
<p>To participate, ensure that:</p>
<ul>
    <li>You obtain a genuine fiscal invoice from a registered VAT/PAN retailer.</li>
    <li>The invoice contains a valid QR code or printed coupon number.</li>
    <li>The invoice was issued within the eligible date window of the draw.</li>
</ul>
HTML,
                'seo_title' => 'Nepal IRD Taxpayer Lottery Scheme Explained',
                'seo_description' => 'Comprehensive guide explaining the Nepal IRD taxpayer incentive program, prize categories, and invoice requirements.',
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'How to Claim Your IRD Incentive Prize Money',
                'slug' => 'how-to-claim-your-ird-incentive-prize-money',
                'excerpt' => 'Essential documents, deadlines, and the verification process for claiming prize money from the Inland Revenue Department.',
                'content' => <<<HTML
<h2>Claim Deadlines & Important Timelines</h2>
<p>Winners must submit their prize claim within the specified deadline announced by the Inland Revenue Department (usually within 15 to 30 days of the draw announcement). Failure to claim within the designated period may result in forfeiture of the reward.</p>

<h2>Required Documents for Prize Claim</h2>
<p>When claiming your prize, prepare the following documents:</p>
<ol>
    <li>Original purchase invoice / tax receipt matching the winning coupon number.</li>
    <li>Copy of your Nepali Citizenship Card (नागरिकता) or National Identity Card (राष्ट्रिय परिचयपत्र).</li>
    <li>Bank account details (Cheque copy or Bank Statement) under your name for direct bank deposit.</li>
    <li>Completed IRD prize claim application form.</li>
</ol>

<h2>Where to Submit</h2>
<p>Claims can be lodged directly at your local <strong>Inland Revenue Office (आन्तरिक राजस्व कार्यालय - IRO)</strong> or submitted electronically through the official portal at <a href="https://prize.ird.gov.np/" target="_blank" rel="noopener noreferrer">prize.ird.gov.np</a>.</p>
HTML,
                'seo_title' => 'How to Claim IRD Taxpayer Incentive Prize Money — Steps & Documents',
                'seo_description' => 'Complete guide on documents required, deadlines, and procedure to claim prize money from Nepal Inland Revenue Department.',
                'status' => 'published',
                'published_at' => now()->subDays(7),
            ],
        ];

        foreach ($posts as $postData) {
            BlogPost::updateOrCreate(
                ['slug' => $postData['slug']],
                $postData
            );
        }
    }
}
