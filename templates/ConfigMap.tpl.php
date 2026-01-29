<?php declare(strict_types=1);
echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use K8Schema\Collection\StringMap;
use Klass8s\Core\ConfigMap\AbstractConfigMap;

class <?php echo $className; ?> extends AbstractConfigMap
{
    public function data(StringMap $data): void
    {
    }

    public static function manifestName(): string
    {
        return '<?php echo $manifestName; ?>';
    }
}
