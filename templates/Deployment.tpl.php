<?php declare(strict_types=1);
echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Deployment\AbstractContainerDeployment;
use Klass8s\Core\LabelSelector\SelectorConfigurator;

class <?php echo $className; ?> extends AbstractContainerDeployment
{
    public function image(): Image
    {
        return Image::fromName('my-cool/image');
    }

    public function selector(SelectorConfigurator $selector): void
    {
        $selector
            ->addLabel('app', $this->app->alias())
            ->addLabel('name', static::shortName())
        ;
    }

    public static function shortName(): string
    {
        return '<?php echo $manifestName; ?>';
    }
}
