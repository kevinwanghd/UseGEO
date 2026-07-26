# UseGEO Timeline Lead Signal Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a required `timeline` field to the UseGEO full assessment form, validate it on submission, save it to lead records, and display readable timeline labels in the WordPress admin.

**Architecture:** Extend the existing scanner form with one more controlled select input, then thread that value through the existing `usegeo_handle_lead_capture()` flow using the same sanitize-and-whitelist pattern already used for `company_type`. Keep admin rendering centralized in `functions.php` by adding one label-mapping function and reusing the existing badge renderer for list/detail display.

**Tech Stack:** WordPress theme PHP, admin-post form handling, WordPress post meta, existing UseGEO theme helpers

---

## File structure

- **Modify:** `wordpress/theme/usegeo/template-parts/scanner/scanner.php`
  - Responsibility: render the public scanner and full assessment form fields.
- **Modify:** `wordpress/theme/usegeo/functions.php`
  - Responsibility: process form submission, define controlled-option label maps, render lead admin columns, and render lead detail meta box.
- **Create:** `docs/superpowers/plans/2026-04-29-usegeo-timeline-implementation.md`
  - Responsibility: implementation handoff document.

No new runtime PHP files are needed. Keep the change inside the existing form template and theme functions file.

---

### Task 1: Add the required timeline field to the public form

**Files:**
- Modify: `wordpress/theme/usegeo/template-parts/scanner/scanner.php`

- [ ] **Step 1: Update the full assessment form markup**

Insert this block immediately after the existing `support_level` `<label>` block and before the submit button:

```php
<label>
    <span>When would you realistically act on this?</span>
    <select name="timeline" required>
        <option value="">Select one</option>
        <option value="just_researching">Just researching</option>
        <option value="next_quarter">Planning in the next quarter</option>
        <option value="need_help_soon">Need help soon</option>
        <option value="not_sure_yet">Not sure yet</option>
    </select>
</label>
```

- [ ] **Step 2: Review the surrounding form for ordering correctness**

The relevant section should read in this order:

```php
<label>
    <span>What kind of help are you looking for?</span>
    <select name="help_intent" required>
        <option value="">Select one</option>
        <option value="exploring_geo">Just exploring GEO</option>
        <option value="prioritized_next_steps">Want prioritized next steps</option>
        <option value="open_to_specialist">Open to working with a GEO specialist</option>
    </select>
</label>
<label>
    <span>If you need outside help, what level of support would be realistic?</span>
    <select name="support_level" required>
        <option value="">Select one</option>
        <option value="not_hiring">Not planning to hire anyone</option>
        <option value="small_project">Open to a small project</option>
        <option value="ongoing_support">Open to ongoing support</option>
        <option value="not_sure">Not sure yet</option>
    </select>
</label>
<label>
    <span>When would you realistically act on this?</span>
    <select name="timeline" required>
        <option value="">Select one</option>
        <option value="just_researching">Just researching</option>
        <option value="next_quarter">Planning in the next quarter</option>
        <option value="need_help_soon">Need help soon</option>
        <option value="not_sure_yet">Not sure yet</option>
    </select>
</label>
<button type="submit" class="usegeo-report-gate__button">Get my full assessment</button>
```

- [ ] **Step 3: Commit the form change**

Run:

```bash
git -C "D:/Code/UseGEO" add "wordpress/theme/usegeo/template-parts/scanner/scanner.php"
git -C "D:/Code/UseGEO" commit -m "feat: add timeline field to full assessment form"
```

Expected: a new commit is created with only the form-template change.

---

### Task 2: Accept, validate, and persist timeline in lead submission handling

**Files:**
- Modify: `wordpress/theme/usegeo/functions.php`

- [ ] **Step 1: Add a label whitelist function for timeline options**

Insert this function near the existing option-label helpers (`usegeo_get_help_intent_labels`, `usegeo_get_support_level_labels`, `usegeo_get_lead_company_type_options`):

```php
function usegeo_get_timeline_labels() {
    return array(
        'just_researching' => __('Just researching', 'usegeo'),
        'next_quarter'     => __('Planning in the next quarter', 'usegeo'),
        'need_help_soon'   => __('Need help soon', 'usegeo'),
        'not_sure_yet'     => __('Not sure yet', 'usegeo'),
    );
}
```

- [ ] **Step 2: Extend `usegeo_handle_lead_capture()` to read and validate timeline**

Replace the top of the function body from the existing input reads through validation setup with this version:

```php
$work_email = isset($_POST['work_email']) ? sanitize_email(wp_unslash($_POST['work_email'])) : '';
$company_type = isset($_POST['company_type']) ? sanitize_key(wp_unslash($_POST['company_type'])) : '';
$help_intent = isset($_POST['help_intent']) ? sanitize_key(wp_unslash($_POST['help_intent'])) : '';
$support_level = isset($_POST['support_level']) ? sanitize_key(wp_unslash($_POST['support_level'])) : '';
$timeline = isset($_POST['timeline']) ? sanitize_key(wp_unslash($_POST['timeline'])) : '';
$scanner_query = isset($_POST['scanner_query']) ? esc_url_raw(wp_unslash($_POST['scanner_query'])) : '';
$redirect_to = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : home_url('/#scanner');
$company_type_options = usegeo_get_lead_company_type_options();
$timeline_labels = usegeo_get_timeline_labels();

if (
    !$work_email ||
    !is_email($work_email) ||
    !$company_type ||
    !isset($company_type_options[$company_type]) ||
    !$help_intent ||
    !$support_level ||
    !$timeline ||
    !isset($timeline_labels[$timeline]) ||
    !$scanner_query
) {
    wp_safe_redirect(add_query_arg('lead_status', 'invalid', $redirect_to));
    exit;
}
```

- [ ] **Step 3: Persist timeline to lead meta**

Add this line immediately after the existing `support_level` meta save:

```php
update_post_meta($post_id, 'timeline', $timeline);
```

That section should become:

```php
update_post_meta($post_id, 'work_email', $work_email);
update_post_meta($post_id, 'company_type', $company_type);
update_post_meta($post_id, 'help_intent', $help_intent);
update_post_meta($post_id, 'support_level', $support_level);
update_post_meta($post_id, 'timeline', $timeline);
update_post_meta($post_id, 'scanner_query', $scanner_query);
update_post_meta($post_id, 'submitted_at', current_time('mysql'));
update_post_meta($post_id, 'source', 'full_assessment_gate');
```

- [ ] **Step 4: Keep notification behavior unchanged**

Do **not** add `timeline` to `usegeo_send_lead_notification()` in this task. This plan keeps scope narrow: form capture, whitelist validation, admin storage, and admin display only.

The function signature should remain:

```php
function usegeo_send_lead_notification($lead_id, $work_email, $company_type, $help_intent, $support_level, $scanner_query) {
```

- [ ] **Step 5: Commit the submission-handling change**

Run:

```bash
git -C "D:/Code/UseGEO" add "wordpress/theme/usegeo/functions.php"
git -C "D:/Code/UseGEO" commit -m "feat: validate and store lead timeline"
```

Expected: a second commit is created covering only the backend capture change.

---

### Task 3: Show readable timeline values in the admin lead list

**Files:**
- Modify: `wordpress/theme/usegeo/functions.php`

- [ ] **Step 1: Add the Timeline column to the lead list**

In `usegeo_lead_admin_columns_v2()`, insert the new column between `support_level` and `scanner_query`:

```php
$columns['work_email'] = __('Work Email', 'usegeo');
$columns['company_type'] = __('Company Type', 'usegeo');
$columns['help_intent'] = __('Help Intent', 'usegeo');
$columns['support_level'] = __('Support Level', 'usegeo');
$columns['timeline'] = __('Timeline', 'usegeo');
$columns['scanner_query'] = __('Website Input', 'usegeo');
$columns['notification_status'] = __('Notification', 'usegeo');
$columns['notification_recipient'] = __('Recipient', 'usegeo');
```

- [ ] **Step 2: Add timeline rendering logic to `usegeo_render_lead_admin_column_v3()`**

Insert this branch before the final fallback call to `usegeo_render_lead_admin_column_v2($column, $post_id);`:

```php
if ($column === 'timeline') {
    $timeline = get_post_meta($post_id, 'timeline', true);
    $timeline_labels = usegeo_get_timeline_labels();
    $timeline_label = isset($timeline_labels[$timeline]) ? $timeline_labels[$timeline] : $timeline;

    if ($timeline === 'need_help_soon') {
        echo usegeo_render_lead_priority_badge($timeline_label, 'success');
    } elseif ($timeline === 'next_quarter') {
        echo usegeo_render_lead_priority_badge($timeline_label, 'info');
    } elseif ($timeline === 'not_sure_yet') {
        echo usegeo_render_lead_priority_badge($timeline_label, 'warn');
    } else {
        echo esc_html($timeline_label ?: '-');
    }
    return;
}
```

- [ ] **Step 3: Confirm no raw timeline key leaks remain in admin list output**

After the change, the lead list renderer should contain branches for:

```php
if ($column === 'company_type') {
    // readable label
}

if ($column === 'help_intent') {
    // readable label or badge
}

if ($column === 'support_level') {
    // readable label or badge
}

if ($column === 'timeline') {
    // readable label or badge
}

usegeo_render_lead_admin_column_v2($column, $post_id);
```

- [ ] **Step 4: Commit the admin-list change**

Run:

```bash
git -C "D:/Code/UseGEO" add "wordpress/theme/usegeo/functions.php"
git -C "D:/Code/UseGEO" commit -m "feat: show timeline in lead admin list"
```

Expected: a third commit is created for the admin list enhancement.

---

### Task 4: Show readable timeline in the lead detail meta box

**Files:**
- Modify: `wordpress/theme/usegeo/functions.php`

- [ ] **Step 1: Load timeline meta and labels in `usegeo_render_lead_meta_box_v3()`**

Add these variables near the existing meta reads and label lookups:

```php
$timeline = get_post_meta($post->ID, 'timeline', true);
```

and later in the label section:

```php
$timeline_labels = usegeo_get_timeline_labels();
$timeline_label = isset($timeline_labels[$timeline]) ? $timeline_labels[$timeline] : $timeline;
```

The label setup block should include:

```php
$company_type_options = usegeo_get_lead_company_type_options();
$help_intent_labels = usegeo_get_help_intent_labels();
$support_level_labels = usegeo_get_support_level_labels();
$timeline_labels = usegeo_get_timeline_labels();
$company_type_label = isset($company_type_options[$company_type]) ? $company_type_options[$company_type] : $company_type;
$help_intent_label = isset($help_intent_labels[$help_intent]) ? $help_intent_labels[$help_intent] : $help_intent;
$support_level_label = isset($support_level_labels[$support_level]) ? $support_level_labels[$support_level] : $support_level;
$timeline_label = isset($timeline_labels[$timeline]) ? $timeline_labels[$timeline] : $timeline;
```

- [ ] **Step 2: Add a Timeline row to the detail table**

Insert this row after the existing `Support Level` row and before `Website Input`:

```php
<tr>
    <th scope="row"><?php esc_html_e('Timeline', 'usegeo'); ?></th>
    <td>
        <?php
        if ($timeline === 'need_help_soon') {
            echo usegeo_render_lead_priority_badge($timeline_label, 'success');
        } elseif ($timeline === 'next_quarter') {
            echo usegeo_render_lead_priority_badge($timeline_label, 'info');
        } elseif ($timeline === 'not_sure_yet') {
            echo usegeo_render_lead_priority_badge($timeline_label, 'warn');
        } else {
            echo esc_html($timeline_label ?: '-');
        }
        ?>
    </td>
</tr>
```

- [ ] **Step 3: Verify visual consistency with existing detail rows**

The relevant section should appear in this order:

```php
<tr>
    <th scope="row"><?php esc_html_e('Help Intent', 'usegeo'); ?></th>
    <td>...</td>
</tr>
<tr>
    <th scope="row"><?php esc_html_e('Support Level', 'usegeo'); ?></th>
    <td>...</td>
</tr>
<tr>
    <th scope="row"><?php esc_html_e('Timeline', 'usegeo'); ?></th>
    <td>...</td>
</tr>
<tr>
    <th scope="row"><?php esc_html_e('Website Input', 'usegeo'); ?></th>
    <td><?php echo esc_html($scanner_query ?: '-'); ?></td>
</tr>
```

- [ ] **Step 4: Commit the meta-box change**

Run:

```bash
git -C "D:/Code/UseGEO" add "wordpress/theme/usegeo/functions.php"
git -C "D:/Code/UseGEO" commit -m "feat: show timeline in lead detail view"
```

Expected: a fourth commit is created for the detail-view enhancement.

---

### Task 5: Manual verification

**Files:**
- Modify: none

- [ ] **Step 1: Submit a valid lead through the local form**

Use a valid website input and a valid selection for all four controlled fields.
Expected redirect:

```text
http://usegeo-local.local/?lead_status=success#scanner
```

or, if mail fails locally:

```text
http://usegeo-local.local/?lead_status=success_no_mail#scanner
```

Expected UI notice:

```text
Your full assessment request has been received.
```

or:

```text
Your request has been saved, but the email notification did not send successfully.
```

- [ ] **Step 2: Verify timeline was saved in the new lead record**

In WordPress admin, open the newest `UseGEO Lead` entry and confirm these values exist:

```text
Company Type
Help Intent
Support Level
Timeline
Website Input
```

Expected: `Timeline` shows the readable label, not the raw key.

- [ ] **Step 3: Verify the lead list column output**

In the `UseGEO Leads` list screen, confirm:

```text
Timeline
```

appears as a column header and that values render like:

```text
Need help soon   -> success badge
Planning in the next quarter -> info badge
Not sure yet -> warn badge
Just researching -> plain text
```

- [ ] **Step 4: Verify forged invalid input is rejected**

Re-submit the form after replacing the timeline field value with an invalid string such as:

```text
hacked_value
```

Expected redirect:

```text
http://usegeo-local.local/?lead_status=invalid#scanner
```

Expected UI notice:

```text
Please complete all required fields before unlocking the full assessment.
```

Expected admin result: no new valid lead should be created from that forged submission.

- [ ] **Step 5: Commit only if manual verification required code changes**

If no code changed during manual verification, do not create another commit.
If you had to adjust code, stage only the touched file(s) and commit with a focused message, for example:

```bash
git -C "D:/Code/UseGEO" add "wordpress/theme/usegeo/functions.php"
git -C "D:/Code/UseGEO" commit -m "fix: adjust timeline admin rendering"
```

---

## Self-review

### Spec coverage
- Form field addition: covered by Task 1
- Backend sanitize + whitelist validation: covered by Task 2
- Save `timeline` meta: covered by Task 2
- Admin list readable label + badge treatment: covered by Task 3
- Lead detail readable label + badge treatment: covered by Task 4
- Manual verification target list: covered by Task 5

No spec gaps found.

### Placeholder scan
- No `TODO`, `TBD`, or deferred implementation markers remain.
- Every code-edit step includes exact code.
- Every verification step includes exact expected output.

### Type consistency
- Field name is consistently `timeline`
- Saved meta key is consistently `timeline`
- Label helper is consistently `usegeo_get_timeline_labels()`
- Option keys are consistently:
  - `just_researching`
  - `next_quarter`
  - `need_help_soon`
  - `not_sure_yet`

No naming mismatches found.
