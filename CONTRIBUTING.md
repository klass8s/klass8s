# Contributing to Klass8s

Thank you for helping. This file says what to do before you start and what a pull request must
satisfy to be merged.

## Before you start

- **A bug:** open an issue with the smallest manifest class that shows it, the command you ran, the
  YAML Klass8s rendered and the YAML you expected, and the versions of PHP, Klass8s and k8schema
  (`composer show tequila/klass8s tequila/k8schema`).
- **A feature or a change of behaviour:** open an issue to discuss it before writing the code. A
  change to what Klass8s renders for an existing class reaches every user on their next update, so it
  is decided before it is built.
- **A security issue** is reported privately, never in a public issue: see [SECURITY.md](SECURITY.md).

## Building and testing

You need PHP 8.5 and Composer.

```bash
composer install
composer install -d tools/php-cs-fixer   # the code-style tool, pinned by its own lock
composer test                            # the PHPUnit suite
composer cs:check                        # the code-style check; `composer cs` fixes what it reports
```

The suite renders manifests in memory and compares the result; it needs no cluster.

## What a pull request must satisfy

- It targets `main`, never the branch of another open pull request. A change that depends on an
  unmerged one goes into the same pull request, or waits for that one to merge.
- `composer test` and `composer cs:check` pass.
- A change in behaviour comes with a test that fails without it, and with the documentation it makes
  wrong corrected in the same pull request.
- The title is a [Conventional Commits](https://www.conventionalcommits.org) header,
  `type(scope): subject`, such as `fix(helm): …` or `feat(autoscaling): …`, with `!` after the type
  for a change that breaks existing manifest classes. Pull requests are squash-merged, so the title
  becomes the commit on `main` and the line in the changelog. Every commit in the pull request is a
  Conventional Commit too, with a body that says what changed and why.
- The constraint on `tequila/k8schema` keeps the tilde form (`~1.35.8`) for every Kubernetes minor it
  accepts: a caret would let a routine update move a project to another Kubernetes API.

## License

Contributions are licensed under the [MIT License](LICENSE), as the rest of the repository is.
