<?php declare(strict_types=1);
echo "<?php\n"; ?>

namespace <?php echo $variables['namespace']; ?>;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Deployment\AbstractContainerDeployment;

class <?php echo $variables['className']; ?> extends AbstractContainerDeployment
{
    public function image(): Image
    {
        return Image::fromName('my-cool/image');
    }

    public static function shortName(): string
    {
        return '<?php echo $variables['manifestName']; ?>';
    }
}
