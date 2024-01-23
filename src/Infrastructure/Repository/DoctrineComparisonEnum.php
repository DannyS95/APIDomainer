<?php

namespace App\Infrastructure;

use Doctrine\Common\Collections\Expr\Comparison;

enum DoctrineComparisonEnum: string
{
    case eq = Comparison::EQ;
    case neq = Comparison::NEQ;
    case gt = Comparison::GT;
    case gte = Comparison::GTE;
    case lt = Comparison::LT;
    case lte = Comparison::LTE;
    case contains = Comparison::CONTAINS;
    case in = Comparison::IN;

    public static function fromName(string $value): string
    {
        foreach (self::cases() as $operation) {
            if( $value === $operation->name ){
                return $operation->value;
            }
        }
        throw new \ValueError("$value is not a valid backing value for enum " . self::class );
    }
}
