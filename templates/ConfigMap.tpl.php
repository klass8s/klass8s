<?php declare(strict_types=1);
echo "<?php\n\ndeclare(strict_types=1);\n"; ?>

namespace <?php echo $variables['namespace']; ?>;

use K8Schema\Collection\StringMap;
use Klass8s\Core\ConfigMap\AbstractConfigMap;

class <?php echo $variables['className']; ?> extends AbstractConfigMap
{
    public function data(StringMap $data): void {}

    public static function shortName(): string
    {
        return '<?php echo $variables['manifestName']; ?>';
    }
}
