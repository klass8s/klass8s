<?php declare(strict_types=1); ?>
namespace <?php echo $variables['namespace']; ?>;

use Klass8s\Core\Ingress\AbstractIngress;
use Klass8s\Core\Ingress\Configurator\IngressRulesConfigurator;

class <?php echo $variables['className']; ?> extends AbstractIngress
{
    rules(IngressRulesConfigurator $rules): void {}

    static shortName(): string => '<?php echo $variables['manifestName']; ?>';
}
