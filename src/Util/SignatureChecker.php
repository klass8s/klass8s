<?php

declare(strict_types=1);

namespace Klass8s\Util;

final readonly class SignatureChecker
{
    /**
     * @return bool Returns true if the signatures are equal, false otherwise.
     */
    public static function check(
        \ReflectionFunctionAbstract $methodA,
        \ReflectionFunctionAbstract $methodB,
        bool $ignoreNullsInReturnTypes = true,
    ): bool {
        $returnTypeA = (string)$methodA->getReturnType();
        $returnTypeB = (string)$methodB->getReturnType();
        if ($ignoreNullsInReturnTypes) {
            $returnTypeA = ltrim($returnTypeA, '?');
            $returnTypeB = ltrim($returnTypeA, '?');
            $returnTypeA = str_replace('|null', '', $returnTypeA);
            $returnTypeB = str_replace('|null', '', $returnTypeB);
        }

        if ($returnTypeA !== $returnTypeB) {
            return false;
        }

        return $methodA->getParameters() == $methodB->getParameters();
    }
}
