<?php declare(strict_types=1); ?>
namespace <?php echo $variables['namespace']; ?>;

use K8Schema\Collection\StringMap;
use Klass8s\Core\Secret\AbstractSecret;

class <?php echo $variables['className']; ?> extends AbstractSecret
{
    data(StringMap $data): void {}

    static shortName(): string => '<?php echo $variables['manifestName']; ?>';
}
