# Klass8s
Klass8s defines Kubernetes manifests as PHP classes. A Deployment, a Service or a whole application
is a class with typed methods, and Klass8s renders it to the YAML that `kubectl`, Helm or a GitOps
controller applies. It is a library and a Symfony bundle: manifests are services in a Symfony
container, so they are autowired, configured per environment and adjusted by event subscribers
before they are rendered.

The documentation is at [klass8s.dev](https://klass8s.dev).

## Example
The examples are in [Phaenon](https://phaenon.dev): modern, strict, expressive PHP that compiles to
the PHP you would have written by hand. Klass8s itself is written in it. Manifests can be written in
plain PHP just as well: [Phaenon and PHP](https://klass8s.dev/docs/getting-started/phaenon-and-php)
says how a project uses either, and the documentation shows every example in both.

An app groups manifests under one name; every manifest in it is named `<app>-<short name>`:

```php
namespace App\K8S\Hello;

use Klass8s\App\AbstractApp;

final class HelloApp extends AbstractApp
{
    static name(): string => 'hello';
}
```

```php
namespace App\K8S\Hello\Manifest;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Container\Ports\PortsConfigurator;
use Klass8s\Core\Deployment\AbstractContainerDeployment;

class WebDeployment extends AbstractContainerDeployment
{
    static shortName(): string => 'web';

    image(): Image => Image::fromName('nginx')->setTag('1.29');

    replicas(): int => 2;

    ports(PortsConfigurator $ports): void => $ports
        ->add(80)
        ->setName('http');
}
```

```php
namespace App\K8S\Hello\Manifest;

use Klass8s\Core\Service\AbstractService;
use Klass8s\Core\Service\Configurator\ServicePortsConfigurator;

class WebService extends AbstractService
{
    static shortName(): string => WebDeployment::shortName();

    ports(ServicePortsConfigurator $ports): void => $ports->add(80, 'http');
}
```

Register the classes as services (`autowire` and `autoconfigure` in `config/services.yaml`) and
render them:

```bash
php bin/console k8s:render:all
```

```yaml
# File: /hello/web.deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  annotations:
    klass8s.dev/app: hello
    klass8s.dev/deploy-priority: '0'
  labels:
    app.kubernetes.io/name: hello
    app.kubernetes.io/component: web
  name: hello-web
spec:
  replicas: 2
  # … the selector, the pod template and its container
---
# File: /hello/web.service.yaml
apiVersion: v1
kind: Service
metadata:
  # … the same annotations and labels
  name: hello-web
spec:
  ports:
    -
      port: 80
      targetPort: http
  selector:
    app.kubernetes.io/name: hello
    app.kubernetes.io/component: web
  trafficDistribution: PreferClose
```

Manifest classes are not `final`: Klass8s wraps each one in a proxy, which is how the events around
its methods reach your subscribers. `k8s:dump:all` writes the same YAML into files, one directory per
app.

## Installation
Klass8s requires PHP 8.5 and Symfony 8. Install it with Composer:

```bash
composer require klass8s/klass8s
```

and register its bundle and that of [Proximity](https://github.com/klass8s/proximity), the proxy
library it builds on, in `config/bundles.php`:

```php
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Proximity\Bundle\ProximityBundle::class => ['all' => true],
    Klass8s\Bundle\Klass8sBundle::class => ['all' => true],
];
```

Klass8s operates on Kubernetes API objects, which are defined in
[k8schema](https://github.com/klass8s/k8schema): classes generated from the Kubernetes OpenAPI
specification by [k8schema-generator](https://github.com/klass8s/k8schema-generator).
`klass8s/k8schema` has a minor release for every supported Kubernetes API version
(usually, four latest minor releases). Check your Kubernetes version
and install a corresponding version of `klass8s/k8schema` - for example,
if you're using Kubernetes v1.35, install `klass8s/k8schema` as follows:

```bash
composer require klass8s/k8schema:~1.35.8
```

> [!IMPORTANT]
> Always use the tilde (`~`) operator with all three version numbers (e.g., `~1.35.8`) 
> when installing `klass8s/k8schema`. 
> Using the caret (`^`) operator will automatically update the library to the newest minor Kubernetes version, 
> which may break your manifests if you're not using the newest version of Kubernetes.

## Development
Klass8s is written in [Phaenon](https://phaenon.dev): modern, strict, expressive PHP that compiles
to the PHP you would have written by hand. The sources are the `.phn` files in `src/` and `tests/`.
The package ships the PHP they compile to, in `.src/`, so a project that installs Klass8s needs
nothing beyond Composer: no compiler and no Composer plugin.

To work on Klass8s itself:

```bash
composer install        # also installs phn, the compiler, whose plugin builds .src/ and .tests/
composer test           # the suite, run on the compiled tests
composer cs             # format the .phn: Phaenon's layout, then php-cs-fixer on the compiled PHP
composer cs:check       # check the format, then that the committed .src/ is what the build writes
```

`composer check` type-checks the sources without writing anything. Commit a change to a `.phn`
together with the `.src/` the build wrote for it.

## Origins
Klass8s continues [`dealroadshow/k8s-framework`](https://github.com/petr-buchin/k8s-framework),
[`dealroadshow/k8s-bundle`](https://github.com/petr-buchin/k8s-bundle) and `dealroadshow/keda-bundle`,
MIT-licensed projects that ran in production for years before this package merged, renamed and
extended them.
Their copyright and permission notice is in [NOTICE](NOTICE).

## Related packages
| Package | What it is |
|---|---|
| [k8schema](https://github.com/klass8s/k8schema) | The Kubernetes API as PHP classes, one minor release line per Kubernetes minor |
| [k8schema-generator](https://github.com/klass8s/k8schema-generator) | The application that generates k8schema from the Kubernetes OpenAPI definitions |
| [Proximity](https://github.com/klass8s/proximity) | Runtime proxy objects with method interception; Klass8s's event layer runs on it |

## Contributing
Bug reports, questions and pull requests are welcome: [CONTRIBUTING.md](CONTRIBUTING.md) says what a
pull request needs. Report a security vulnerability privately, as [SECURITY.md](SECURITY.md)
describes.

## License
Klass8s is released under the [MIT License](LICENSE). The portions that come from the projects named
under *Origins* remain under their own MIT notice, in [NOTICE](NOTICE).
