@extends('layouts.app')

@section('title', 'Nepal Prize Checker — IRD Lottery Coupon Check')
@section('meta_description', 'Check whether your IRD taxpayer incentive prize coupon has been allotted. Select your prize period, enter your coupon number, and see the result instantly.')

@section('content')
<div class="card">

    {{-- Logo / Branding --}}
    <div class="card-header">
        <a href="https://whereismybusiness.com/" class="logo" aria-label="Nepal Prize Checker Home" style="text-decoration: none">
            <div class="card-logo-text">
                <span class="brand">WhereIsMyBusiness.com</span>
            </div>
        </a>
        <p class="card-subtitle">IRD Lottery Coupon Check</p>
    </div>

    {{-- Checker Form --}}
    <form id="checker-form" novalidate>
        @csrf

        {{-- Prize Period --}}
        <div class="form-group">
            <label for="period_id">Prize Period</label>
            <select id="period_id" name="period_id" required aria-required="true">
                @if(!$defaultPeriodId)
                    <option value="" disabled selected>— Select a prize period —</option>
                @endif
                @forelse($periods->groupBy('year') as $year => $yearPeriods)
                    <optgroup label="{{ $year }}">
                        @foreach($yearPeriods as $period)
                            <option value="{{ $period->id }}" @selected(($defaultPeriodId ?? null) === $period->id)>
                                {{ $period->display_label }}
                            </option>
                        @endforeach
                    </optgroup>
                @empty
                    <option disabled>No prize periods available yet.</option>
                @endforelse
            </select>
        </div>

        {{-- Coupon Numbers --}}
        <div class="form-group">
            <label for="coupon_codes">Coupon Number(s)</label>
            <input
                type="text"
                id="coupon_codes"
                name="coupon_codes"
                placeholder="e.g. 123456789, 234567890"
                autocomplete="off"
                spellcheck="false"
                maxlength="500"
                required
                aria-required="true"
                aria-describedby="coupon-hint"
            >
            <p class="form-hint" id="coupon-hint">
                Enter one or multiple coupon numbers separated by commas.
            </p>
        </div>

        {{-- reCAPTCHA --}}
        @if($recaptchaSiteKey)
        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}" id="recaptcha-widget"></div>
        </div>
        @endif

        {{-- Error message --}}
        <div id="form-error" class="alert alert-danger" style="display:none;" role="alert" aria-live="polite"></div>

        {{-- Submit --}}
        <button type="submit" id="submit-btn" class="btn btn-brand" style="margin-top:.25rem;">
            View Result
        </button>
    </form>

    {{-- Loading --}}
    <div id="loading" class="loading-indicator" style="display:none;" role="status" aria-label="Checking your coupon">
        <div class="spinner" aria-hidden="true"></div>
        Checking your coupon...
    </div>

    {{-- Results (shown after check) --}}
    <div id="results-section" class="results-section" style="display:none;" aria-live="polite">
        <p class="results-heading" id="results-period-label"></p>
        <div id="results-list"></div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const form           = document.getElementById('checker-form');
    const submitBtn      = document.getElementById('submit-btn');
    const loading        = document.getElementById('loading');
    const formError      = document.getElementById('form-error');
    const resultsSection = document.getElementById('results-section');
    const resultsList    = document.getElementById('results-list');
    const periodLabel    = document.getElementById('results-period-label');

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const CHECK_URL  = '{{ route("home.check") }}';
    const HAS_CAPTCHA = {{ $recaptchaSiteKey ? 'true' : 'false' }};

    function showError(msg) {
        formError.textContent = msg;
        formError.style.display = '';
        formError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    function hideError() {
        formError.style.display = 'none';
    }
    function setLoading(on) {
        submitBtn.disabled = on;
        submitBtn.textContent = on ? 'Checking...' : 'View Result';
        loading.style.display = on ? '' : 'none';
        if (on) resultsSection.style.display = 'none';
    }

    function renderResults(period, results) {
        periodLabel.textContent = 'Results — ' + period;
        resultsList.innerHTML   = '';

        results.forEach(function (r) {
            const card = document.createElement('div');
            card.className = 'result-card ' + (r.allotted ? 'allotted' : 'not-allotted');
            card.setAttribute('role', 'status');

            const icon = document.createElement('div');
            icon.className = 'result-icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.textContent = r.allotted ? '🟢' : '🔴';

            const body       = document.createElement('div');
            const couponEl   = document.createElement('div');
            couponEl.className = 'result-coupon';
            couponEl.textContent = r.coupon;

            const labelEl = document.createElement('div');
            labelEl.className = 'result-label';
            if (r.allotted) {
                labelEl.innerHTML = '<strong>Congratulations!</strong> Your coupon has been allotted.';
                if (r.prize) labelEl.innerHTML += '<br><span class="result-prize-tag">' + escapeHtml(r.prize) + '</span>';
            } else {
                labelEl.textContent = 'Your coupon has not been allotted.';
            }

            body.appendChild(couponEl);
            body.appendChild(labelEl);
            card.appendChild(icon);
            card.appendChild(body);
            resultsList.appendChild(card);
        });

        resultsSection.style.display = '';
        resultsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        hideError();

        const periodId    = document.getElementById('period_id').value;
        const couponCodes = document.getElementById('coupon_codes').value.trim();

        if (!periodId) {
            showError('Please select a prize period.');
            document.getElementById('period_id').focus();
            return;
        }
        if (!couponCodes) {
            showError('Please enter at least one coupon number.');
            document.getElementById('coupon_codes').focus();
            return;
        }

        let recaptchaToken = '';
        if (HAS_CAPTCHA) {
            recaptchaToken = grecaptcha.getResponse();
            if (!recaptchaToken) {
                showError('Please complete the verification.');
                return;
            }
        }

        setLoading(true);

        try {
            const response = await fetch(CHECK_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept':        'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({
                    _token:        CSRF_TOKEN,
                    period_id:     periodId,
                    coupon_codes:  couponCodes,
                    recaptcha:     recaptchaToken,
                }).toString(),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                showError(data.message || 'Unable to check the coupon right now. Please try again.');
                if (HAS_CAPTCHA) grecaptcha.reset();
                return;
            }

            renderResults(data.period, data.results);
            if (HAS_CAPTCHA) grecaptcha.reset();

        } catch (err) {
            showError('Unable to check the coupon right now. Please try again.');
        } finally {
            setLoading(false);
        }
    });
})();
</script>
@endpush
