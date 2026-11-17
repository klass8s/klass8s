<?php declare(strict_types=1); ?>
namespace <?php echo $variables['namespace']; ?>;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\CronJob\AbstractContainerCronJob;

class <?php echo $variables['className']; ?> extends AbstractContainerCronJob
{
    image(): Image => Image::fromName('my-cool/image');

    schedule(): string => '* * * * *'; // min hour day month dayOfWeek

    static shortName(): string => '<?php echo $variables['manifestName']; ?>';
}
