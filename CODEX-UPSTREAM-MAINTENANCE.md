# Contract for future Codex maintenance sessions

1. Identify the current upstream base and requested target Tutor release from Git history/tags.
2. Read `KITMAGE-FORK.md` before editing.
3. Work on a dedicated integration branch and merge upstream there.
4. Never mechanically preserve obsolete upstream implementation; prefer the new upstream design and reapply Kitmage behavioral intent.
5. Avoid changing `kitmage/entitlements/` unless an actual API/schema/security need exists.
6. Audit all Tutor pricing, course API, checkout, enrollment, guest-login, template and add-on changes.
7. Inspect actual available Tutor Pro dependency/version/path checks; do not guess, weaken, or fake them.
8. Rebuild source assets, never edit generated bundles manually.
9. Run PHP, TypeScript, Rspack, packaging, WordPress/Woo, atomic redemption and private Tutor Pro tests.
10. Produce an explicit compatibility/test report, including unavailable proprietary tests.
11. Update the patch manifest whenever an upstream file changes.
12. Build the `tutor/`-root artifact, create a pull request, and require human review.
13. Never automatically deploy to production.
