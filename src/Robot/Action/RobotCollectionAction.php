<?php 

namespace App\Robot\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RobotCollectionAction
{
    public function __invoke(Request $request): void
    {
        echo $request->get('name');
        die();
    }
}
