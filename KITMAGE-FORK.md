# Kitmage Tutor LMS fork manifest

## Identity and compatibility audit

The inspected tree is Tutor LMS 4.0.6. `tutor.php` defines `TUTOR_VERSION` as the upstream-compatible `4.0.6`, defines `TUTOR_FILE`, loads `Tutor\\Tutor`, exposes `tutor_lms()`, and assigns `$GLOBALS['tutor']`. The Composer map retains every Tutor namespace. The course and enrollment post types and existing hook names are unchanged. No Tutor Pro source was present under `/workspace`, so proprietary runtime compatibility is **not executed**; releases must follow the manual gate below. WordPress dependency declarations and hard-coded `tutor/tutor.php` checks are satisfied only by the release ZIP's `tutor/` root. `Update URI` gives this distribution a non-WordPress.org update identity, while `KITMAGE_TUTOR_BUILD_VERSION` identifies the downstream build without changing `TUTOR_VERSION`.

## Intentional upstream modifications (7 files)

### `tutor.php`

**Reason:** bootstrap the in-plugin subsystem, publish a separate build ID, and prevent wordpress.org replacement. **Contract:** Tutor identity remains intact. **Risk:** low; reapply headers/bootstrap after upstream header changes.

### `composer.json`

**Reason:** register the isolated Kitmage namespace. **Contract:** existing Tutor mappings are unchanged. **Risk:** low.

### `classes/Course.php`

**Reason:** declare and validate `entitlement`; guard Tutor-owned form, AJAX, and post-login self-enrollment. **Contract:** free/paid paths are unchanged and direct trusted `EnrollmentModel::do_enroll($course, 0, $user)` remains available. **Risk:** high; audit all new upstream enrollment endpoints.

### `classes/Utils.php`

**Reason:** canonical `is_course_entitlement_only()` helper. **Contract:** `is_course_purchasable()` semantics remain false for entitlement. **Risk:** low.

### `templates/single/course/course-entry-box.php`

**Reason:** replace the free/purchase controls with invitation-only state for unenrolled entitlement courses. **Contract:** enrolled rendering is unchanged. **Risk:** medium.

### `assets/src/js/v3/entries/course-builder/services/course.ts`

**Reason:** type, serialize, and restore the new pricing value without requiring a product. **Contract:** paid payload behavior remains unchanged. **Risk:** high when upstream API models change.

### `assets/src/js/v3/entries/course-builder/components/course-basic/CoursePricing.tsx`

**Reason:** add the native radio option. Existing paid-only controls already key on `paid`, so they remain hidden for entitlement. **Risk:** high when upstream pricing UI changes.

All entitlement business behavior is under `kitmage/entitlements/`.

## Tutor Pro release procedure

Install the built artifact as `wp-content/plugins/tutor/tutor.php`, then activate the licensed Tutor Pro package. Confirm WordPress reports no missing `tutor` dependency; `TUTOR_VERSION`, `TUTOR_FILE`, `tutor_lms()`, `tutor()`, `$GLOBALS['tutor']`, and representative Pro add-ons initialize; open the course builder and exercise Free, Paid, and Entitlement modes. Proprietary source must never be committed.
