<?php declare(strict_types=1); ?>
namespace <?php echo $variables['namespace']; ?>;

use Klass8s\App\AbstractApp;

class <?php echo $variables['className']; ?> extends AbstractApp
{
    static name(): string => '<?php echo $variables['appName']; ?>';
}
