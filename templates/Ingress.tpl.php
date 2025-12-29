<?php declare(strict_types=1);
echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use Klass8s\Core\Ingress\AbstractIngress;
use Klass8s\Core\Ingress\Configurator\IngressRulesConfigurator;

class <?php echo $className; ?> extends AbstractIngress
{
    public function rules(IngressRulesConfigurator $rules): void
    {
    }

    public static function shortName(): string
    {
        return '<?php echo $manifestName; ?>';
    }
}
