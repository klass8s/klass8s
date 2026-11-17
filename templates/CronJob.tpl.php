<?php declare(strict_types=1);
echo "<?php\n\ndeclare(strict_types=1);\n"; ?>

namespace <?php echo $variables['namespace']; ?>;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\CronJob\AbstractContainerCronJob;

class <?php echo $variables['className']; ?> extends AbstractContainerCronJob
{
    public function image(): Image
    {
        return Image::fromName('my-cool/image');
    }

    public function schedule(): string
    {
        return '* * * * *'; // min hour day month dayOfWeek
    }

    public static function shortName(): string
    {
        return '<?php echo $variables['manifestName']; ?>';
    }
}
