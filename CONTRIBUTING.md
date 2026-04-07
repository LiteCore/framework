# Contributing to LiteCore

## Repository

- GitHub: <https://github.com/LiteCore/framework>
- Branch: `master`

## Coding Standards

Follow the guidelines in [STANDARD.md](STANDARD.md) and the philosophy in [NoNonsenseCoding.md](NoNonsenseCoding.md).

Key principles:

- Keep it simple — no overcomplicated abstractions
- No third-party dependencies unless absolutely necessary
- Use full, descriptive names — no cryptic abbreviations
- Tabs for indentation, single quotes for PHP/JS, double quotes for HTML
- Functions return data, never echo directly

## Commit Messages

    ! means critical
    + means added
    - means removed
    * means changed

Examples:

    ! Fix critical rendering issue in litebox
    + Add touch/swipe support to carousel
    - Remove deprecated panel component
    * Improve waitFor() timeout handling

Issue references:

    * Fix #12 - Carousel not responding to touch events

All commits should be production-ready. Do not commit test data or debug code.

## Pull Requests

1. Fork the repository
2. Create a feature branch from `master`
3. Make your changes following the coding standards
4. Test your changes
5. Submit a pull request against `master`

Keep PRs focused — one feature or fix per PR.
Include a clear description of what changed and why.

## What Belongs in LiteCore

LiteCore is a **generic web framework**. Contributions should be useful
for any web application, not specific to a particular project.

Good candidates:

- Bug fixes in existing components
- Performance improvements
- New generic UI components (CSS/JS)
- Framework-level PHP improvements
- Documentation improvements

Not suitable for LiteCore (contribute to LiteCart instead):

- E-commerce specific features
- Shop-specific UI components
- Payment/shipping integrations

## Security Issues

Do not open public issues for security vulnerabilities. See [SECURITY.md](SECURITY.md).
