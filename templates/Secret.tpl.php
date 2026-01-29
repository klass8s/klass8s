<?php declare(strict_types=1);
echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use K8Schema\Collection\StringMap;
use Klass8s\Core\Secret\AbstractSecret;

class <?php echo $className; ?> extends AbstractSecret
{
    public function data(StringMap $data): void
    {
    }

    public static function shortName(): string
    {
        return '<?php echo $manifestName; ?>';
    }
}
