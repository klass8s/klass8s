<?php declare(strict_types=1);
echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use Klass8s\Core\Service\AbstractService;
use Klass8s\Core\Service\Configurator\ServicePortsConfigurator;

class <?php echo $className; ?> extends AbstractService
{
    public function ports(ServicePortsConfigurator $ports): void
    {
    }

    public static function shortName(): string
    {
        return '<?php echo $manifestName; ?>';
    }
}
