# UseGEO Content Template & Publishing System

> Purpose: make every UseGEO article follow a repeatable SEO/GEO structure instead of starting from a blank page.
> Scope: editorial content posts and supporting guides. Money pages can reuse the same sections but may need deeper buying-guide structure.

## 1. Article Brief Fields

Every new article starts with this brief:

| Field | Required | Notes |
|---|---:|---|
| `primary_keyword` | yes | One main query such as `how to improve entity clarity for AI search` |
| `secondary_keywords` | yes | 3-6 supporting phrases |
| `search_intent` | yes | informational / commercial / comparison / troubleshooting |
| `target_reader` | yes | B2B founder, growth lead, SEO lead, agency operator, etc. |
| `reader_problem` | yes | What the reader is trying to decide or fix |
| `related_money_page` | yes | `/geo-checklist/`, `/ai-visibility-tools/`, or `/best-geo-agencies/` |
| `scanner_cta` | yes | Usually `/#scanner` with a specific CTA label |
| `partner_angle` | yes | tool, agency, expert follow-up, sponsor, affiliate, or lead referral angle |
| `internal_links` | yes | At least 3 internal links, including Scanner or one money page |
| `outline` | yes | Main section headings before drafting |
| `faq_items` | yes | 4-7 FAQ questions with standalone answers |
| `forbidden_claims` | yes | No promised AI ranking, no precise tracking of all AI recommendations, no “SEO is dead” |

## 2. Standard Article Structure

Each article should use this order unless there is a specific editorial reason to change it:

1. `H1`
2. Opening 100 words that directly answer the query
3. `Quick answer` block
4. Main sections with descriptive `H2` headings
5. Checklist / criteria / decision framework
6. Example or before/after scenario when relevant
7. Common mistakes
8. Related reading and internal links
9. Next-step CTA back to Scanner or a money page
10. FAQ

## 3. Section Requirements

### Opening
- Answer the search intent in the first 100 words.
- Use the primary keyword naturally once.
- State uncertainty where the topic involves AI recommendation behavior.

### Quick answer
- 60-120 words.
- Must be quotable without the rest of the article.
- No hype, no fake precision.

### Main sections
- Each `H2` should answer one reader question.
- Prefer practical signals: entity clarity, proof, comparison context, extractability, internal links, measurement limits.
- Avoid generic filler like “optimize your strategy” unless the section gives concrete actions.

### Checklist / criteria
- Include at least 5 bullets.
- Each bullet should be something a team can inspect on a real website.

### Common mistakes
- Include at least 4 mistakes.
- At least one mistake should address overclaiming AI ranking or measurement certainty.

### FAQ
- Include 4-7 questions.
- Each answer should be 60-140 words.
- FAQ content must be visible in the article body before any future schema markup mirrors it.

## 4. Internal Linking Rules

Every new article must include at least 3 internal links:

- One Scanner CTA: `/#scanner`
- One related money page:
  - `/geo-checklist/`
  - `/ai-visibility-tools/`
  - `/best-geo-agencies/`
- One supporting article or cluster page when available

Use descriptive anchor text. Do not use repeated “click here”.

## 5. CTA Rules

Use direct, low-hype CTAs:

- `Run the free GEO scanner`
- `Check your site with the free scanner`
- `Use the GEO checklist before you rewrite the page`
- `Compare AI visibility tools before buying software`
- `Evaluate GEO agencies before signing a retainer`

Do not use CTAs that imply UseGEO personally delivers a full GEO service.

## 6. JSON-LD / FAQ Schema Planning

CR-012 does not implement schema output in WordPress. It defines the content requirements so schema can be safely added later:

- FAQ schema may only mirror visible FAQ content.
- Article schema should use the published article title and canonical URL.
- Do not add fake author credentials, fake ratings, fake reviews, or schema for content not visible on the page.
- Future schema CR should validate rendered JSON-LD with a browser or Rich Results Test, not only static curl.

## 7. Publishing Workflow

1. Fill the article brief.
2. Draft the article from the standard structure.
3. Run the content verifier locally.
4. Check forbidden claims and internal links.
5. Publish/update the WordPress post by existing slug when possible.
6. Run production verifier.
7. Record evidence under the CR directory.

## 8. Acceptance Checklist

Before a new article ships:

- [ ] Brief has all required fields.
- [ ] Opening answers the query in the first 100 words.
- [ ] Quick answer exists.
- [ ] At least 4 FAQ items exist.
- [ ] At least 4 common mistakes or 5 checklist criteria exist.
- [ ] At least 3 internal links exist.
- [ ] At least 1 Scanner CTA exists.
- [ ] No promised AI ranking or precise all-AI tracking claim.
- [ ] No “SEO is dead” framing.
- [ ] Partner angle is present but does not turn UseGEO into a self-operated service agency.
