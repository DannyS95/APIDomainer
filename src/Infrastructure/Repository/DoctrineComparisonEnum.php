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
}
