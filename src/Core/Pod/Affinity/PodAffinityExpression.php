<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Affinity;

use Klass8s\Core\LabelSelector\LabelSelectorExpression;

class PodAffinityExpression extends LabelSelectorExpression
{
    public const string TARGET_LABEL = 'label';
    public const string TARGET_FIELD = 'field';

    protected string $target;

    protected function __construct(string $target, string $key)
    {
        $this->target = $target;
        parent::__construct($key);
    }

    public function target(): string
    {
        return $this->target;
    }

    /**
     * @param string $key
     *
     * @return static
     */
    public static function field(string $key): self
    {
        return new static(self::TARGET_FIELD, $key);
    }

    /**
     * @param string $key
     *
     * @return static
     */
    public static function label(string $key): self
    {
        return new static(self::TARGET_LABEL, $key);
    }
}
