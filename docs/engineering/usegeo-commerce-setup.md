# UseGEO automated report commerce setup

## Product

- Product: Homepage GEO Action Report
- Price: USD 29 one-time
- Delivery: token-protected HTML report with print-to-PDF support
- Account requirement: none
- Subscription: none

## WordPress configuration

Add the following server-side constants to `wp-config.php`. Never commit real values.

```php
define('USEGEO_STRIPE_SECRET_KEY', 'sk_live_...');
define('USEGEO_STRIPE_WEBHOOK_SECRET', 'whsec_...');
```

Use Stripe test-mode keys in staging.

## Stripe webhook

Create a Stripe webhook endpoint:

```text
https://usegeo.blog/wp-json/usegeo/v1/stripe-webhook
```

Subscribe to:

```text
checkout.session.completed
```

Copy the endpoint signing secret into `USEGEO_STRIPE_WEBHOOK_SECRET`.

## Smoke test

1. Run a real homepage scan.
2. Enter an email and request the free snapshot.
3. Confirm the private report link opens and paid sections remain locked.
4. Run another scan and select `Unlock full report · $29`.
5. Complete Stripe test checkout with card `4242 4242 4242 4242`.
6. Confirm the success URL unlocks findings and the 30-day plan.
7. Confirm Stripe records a successful `checkout.session.completed` webhook.
8. Confirm the buyer receives the unlocked-report email.
9. Confirm WordPress admin shows the report with `payment_status = paid`.

## Analytics events

- `scanner_complete`
- `free_report_requested`
- `begin_checkout`
- `cta_click`

Map `begin_checkout` to a GA4 key event. Revenue remains authoritative in Stripe.

## Operational boundary

The report is directional. It evaluates homepage readiness signals and does not promise placement or ranking in AI-generated answers.
