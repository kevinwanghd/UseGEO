# FAQ / Article Schema Plan

CR-012 does not implement JSON-LD in WordPress. It defines safe schema requirements for a future CR.

## FAQ schema rules

- Only emit FAQ schema for FAQ content visible in the article body.
- Each FAQ item must have one question and one answer.
- Do not add promotional CTAs inside schema answers.
- Do not create FAQ schema for questions not shown to users.

## Article schema rules

- Use published title, canonical URL, and modified date from WordPress.
- Do not invent author credentials.
- Do not add fake author credentials.
- Do not add ratings/reviews unless they are visible and real.

## Future verifier requirements

A future schema CR should verify rendered JSON-LD using a browser-rendered page or Rich Results Test compatible flow. Static curl alone can miss plugin-injected schema.
