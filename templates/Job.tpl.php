<?php declare(strict_types=1);
echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Job\AbstractContainerJob;

class <?php echo $className; ?> extends AbstractContainerJob
{
    public function image(): Image
    {
        return Image::fromName('my-cool/image');
    }

    public static function shortName(): string
    {
        return '<?php echo $manifestName; ?>';
    }
}
