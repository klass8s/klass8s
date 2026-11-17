<?php declare(strict_types=1);
echo "<?php\n\ndeclare(strict_types=1);\n"; ?>

namespace <?php echo $variables['namespace']; ?>;

use Klass8s\Core\Ingress\AbstractIngress;
use Klass8s\Core\Ingress\Configurator\IngressRulesConfigurator;

class <?php echo $variables['className']; ?> extends AbstractIngress
{
    public function rules(IngressRulesConfigurator $rules): void {}

    public static function shortName(): string
    {
        return '<?php echo $variables['manifestName']; ?>';
    }
}
