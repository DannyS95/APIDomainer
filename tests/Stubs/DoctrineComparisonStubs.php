<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

final class Comparison
{
    public const EQ = '=';
    public const NEQ = '!=';
    public const GT = '>';
    public const GTE = '>=';
    public const LT = '<';
    public const LTE = '<=';
    public const CONTAINS = 'LIKE';
    public const IN = 'IN';
}
