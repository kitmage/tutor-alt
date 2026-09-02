# Updating from upstream Tutor LMS

1. Fetch official Tutor remotes/tags and identify both the current 4.0.6 base and target release.
2. Create a dedicated integration branch; never merge an upstream release directly to production.
3. Merge the target, read `KITMAGE-FORK.md`, and resolve its seven intentional upstream files.
4. Prefer the target's architecture and reapply behavioral intent rather than preserving obsolete code mechanically.
5. Normally leave `kitmage/entitlements/` untouched. Audit new pricing validation, payloads, templates, checkout and every self-enrollment route.
6. Audit Tutor Pro/add-on contracts (`Requires Plugins`, basename, constants, globals, functions, classes and hooks) against legitimately available current source.
7. Run Composer install, TypeScript checks, Rspack production build, PHP checks, WordPress/Woo integration tests, concurrency tests, and Tutor Pro private smoke tests.
8. Run `./scripts/build-release.sh`; inspect that the only ZIP root is `tutor/` and `tutor/tutor.php` exists.
9. Write a compatibility report and open a pull request. Merge only after CI and human approval. There is intentionally no automatic production deployment.
