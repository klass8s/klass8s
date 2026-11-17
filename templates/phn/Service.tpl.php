<?php declare(strict_types=1); ?>
namespace <?php echo $variables['namespace']; ?>;

use Klass8s\Core\Service\AbstractService;
use Klass8s\Core\Service\Configurator\ServicePortsConfigurator;

class <?php echo $variables['className']; ?> extends AbstractService
{
    ports(ServicePortsConfigurator $ports): void {}

    static shortName(): string => '<?php echo $variables['manifestName']; ?>';
}
