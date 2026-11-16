# Security policy

## Reporting a vulnerability

Please do not report a vulnerability in a public issue. Report it privately through GitHub's
private vulnerability reporting: the repository's **Security** tab, **Report a vulnerability**
(<https://github.com/klass8s/klass8s/security/advisories/new>).

Say what is affected (a manifest type, a configurator, the bundle's configuration or commands, the
YAML rendered for a given class), the version (`composer show klass8s/klass8s`), and the class or
the steps that show the problem. The fix is coordinated in the advisory, and the advisory is
published with the release that carries it.

## Supported versions

Klass8s has no 1.0 release yet. Security fixes go into the latest release line only; upgrade to its
newest patch release to receive them.
