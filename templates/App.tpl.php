<?php declare(strict_types=1);
echo "<?php\n\ndeclare(strict_types=1);\n"; ?>

namespace <?php echo $variables['namespace']; ?>;

use Klass8s\App\AbstractApp;

class <?php echo $variables['className']; ?> extends AbstractApp
{
    public static function name(): string
    {
        return '<?php echo $variables['appName']; ?>';
    }
}
