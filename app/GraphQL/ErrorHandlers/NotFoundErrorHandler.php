<?php

namespace App\GraphQL\ErrorHandlers;

use App\Exceptions\NotFoundException;
use GraphQL\Error\Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundErrorHandler implements ErrorHandler
{
    public function __invoke(?Error $error, \Closure $next): ?array
    {
        if ($error === null) {
            return $next(null);
        }

        $underlyingException = $error->getPrevious();

        if ($underlyingException instanceof ModelNotFoundException) {
            $notFoundException = new NotFoundException(__('errors.not_found'), $underlyingException->getModel());

            return $next(new Error(
                $notFoundException->getMessage(),
                $error->getNodes(),
                $error->getSource(),
                $error->getPositions(),
                $error->getPath(),
                $notFoundException,
            ));
        }

        if ($underlyingException instanceof NotFoundHttpException) {
            $notFoundException = new NotFoundException(__('errors.not_found'));

            return $next(new Error(
                $notFoundException->getMessage(),
                $error->getNodes(),
                $error->getSource(),
                $error->getPositions(),
                $error->getPath(),
                $notFoundException,
            ));
        }

        return $next($error);
    }
}
