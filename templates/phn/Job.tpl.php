<?php declare(strict_types=1); ?>
namespace <?php echo $variables['namespace']; ?>;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Job\AbstractContainerJob;

class <?php echo $variables['className']; ?> extends AbstractContainerJob
{
    image(): Image => Image::fromName('my-cool/image');

    static shortName(): string => '<?php echo $variables['manifestName']; ?>';
}
