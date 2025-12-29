<?php

declare(strict_types=1);

namespace Klass8s\Core;

use Klass8s\App\AppInterface;
use Klass8s\Registry\AppRegistry;
use Klass8s\Util\ManifestAppFinder;
use Klass8s\Util\ManifestShortName;
use Klass8s\Util\PropertyAccessor;

readonly class DefaultLabelsGenerator implements LabelsGeneratorInterface
{
    public function __construct(private AppRegistry $appRegistry, private ManifestAppFinder $appFinder)
    {
    }

    public function byManifestClass(string $manifestClass, string|null $appAlias = null): array
    {
        $app = null === $appAlias
            ? $this->appFinder->appByManifestClass($manifestClass)
            : $this->appRegistry->get($appAlias);

        if (!$app->ownsManifest($manifestClass)) {
            throw new \LogicException(sprintf('Manifest "%s" does not belong to app "%s"', $manifestClass, $app->alias()));
        }

        $manifest = $app->getManifest($manifestClass);

        return $this->labels($manifest, $app);
    }

    public function byManifestInstance(ManifestInterface $manifest): array
    {
        /** @var AppInterface $app */
        $app = PropertyAccessor::get($manifest, 'app');

        return $this->labels($manifest, $app);
    }

    protected function labels(ManifestInterface $manifest, AppInterface $app): array
    {
        return [
            'app' => $app->alias(),
            'component' => $manifest instanceof FullNameAwareInterface ? $manifest->fullName() : ManifestShortName::getFrom($manifest),
        ];
    }
}
