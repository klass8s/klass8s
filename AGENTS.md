# Agent guide

Klass8s is a PHP library and Symfony bundle that defines Kubernetes manifests as PHP classes and
renders them to YAML (namespace `Klass8s\`, PHP 8.5, Symfony 8). This file is for coding agents;
[CONTRIBUTING.md](CONTRIBUTING.md) says the same for people, and where the two differ, CONTRIBUTING.md
wins.

## What a pull request must satisfy

- It targets `main`, never the branch of another open pull request.
- `composer test` and `composer cs:check` pass (`composer install` installs the style tool with
  the rest; `composer tools:install` does it alone).
- A change in behaviour comes with a test that fails without it. The tests render manifests and
  compare the result; they need no cluster, and a test must not start one.
- What a change makes wrong in `README.md` or `docs/` is corrected in the same pull request.
- The title and every commit are [Conventional Commits](https://www.conventionalcommits.org)
  (`type(scope): subject`, `!` for a change that breaks existing manifest classes); each commit has a
  body that says what changed and why. Pull requests are squash-merged: the title becomes the commit
  on `main`.
- The constraint on `tequila/k8schema` keeps the tilde form (`~1.35.8`) for every Kubernetes minor
  it accepts.
- Manifests stay proxiable. Klass8s wraps every manifest in a Proximity proxy to dispatch the events
  around its methods: a `final` manifest class cannot be proxied at all, and a `final` method is
  skipped by the proxy, so no event fires around it. Making a method of a base class `final` takes
  its events away from every subscriber and is a breaking change.
