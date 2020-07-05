<?php

namespace App\Exceptions;

use App\Trates\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof HttpException)
        {
            $code = $exception->getStatusCode();
            $message = Response::$statusTexts[$code];

            return $this->errorResponse($message ,$code);
        }

        if($exception instanceof ModelNotFoundException)
        {
            $model = strtolower( class_basename( $exception->getModel() ) );

            return $this->errorResponse("No existe el id indicado para el modelo {$model}" ,Response::HTTP_NOT_FOUND);
        }

        if($exception instanceof AuthorizationException)
        {
            return $this->errorResponse($exception->getMessage() ,Response::HTTP_FORBIDDEN);
        }

        if($exception instanceof AuthenticationException)
        {
            return $this->errorResponse($exception->getMessage() ,Response::HTTP_UNAUTHORIZED);
        }

        if($exception instanceof ValidationException)
        {
            $errors = $exception->validator->errors()->getMessages();

            return $this->errorResponse($errors ,Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(env('APP_DEBUG', false)){
            return $this->errorResponse('Error inesperado', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }
}
