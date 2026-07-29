<?php

namespace App\Exceptions;

use App\Traits\ResponseFormat;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseFormat;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*') || $request->wantsJson()) {
            if ($e instanceof AuthenticationException || ($e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException && str_contains($e->getMessage(), 'login'))) {
                return $this->responseError('Unauthenticated', null, 401);
            }

            $statusCode = 500;
            $message = $e->getMessage() ?: 'Terjadi kesalahan pada server';
            $errorData = null;

            if ($e instanceof ValidationException) {
                $statusCode = 422;
                $message = 'Validasi gagal';
                $errorData = $e->errors();
            } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                $statusCode = 404;
                $message = 'Data atau Endpoint tidak ditemukan';
            } elseif ($e instanceof \Illuminate\Database\QueryException) {
                $statusCode = 500;
                $message = 'Database / SQL Error';
                $errorData = config('app.debug')
                    ? [
                        'sql_error' => $e->getMessage(),
                        'sql' => $e->getSql(),
                        'bindings' => $e->getBindings()
                    ]
                    : 'Terjadi kesalahan pada query database';
            }

            return $this->responseError($message, $errorData, $statusCode);
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('api/*') || $request->wantsJson()) {
            return $this->responseError('Unauthenticated', null, 401);
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
