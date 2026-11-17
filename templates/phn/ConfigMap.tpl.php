<?php declare(strict_types=1); ?>
namespace <?php echo $variables['namespace']; ?>;

use K8Schema\Collection\StringMap;
use Klass8s\Core\ConfigMap\AbstractConfigMap;

class <?php echo $variables['className']; ?> extends AbstractConfigMap
{
    data(StringMap $data): void {}

    static shortName(): string => '<?php echo $variables['manifestName']; ?>';
}
