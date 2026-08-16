<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        // セッション切れ状態でログアウトを押した場合
        if ($exception instanceof TokenMismatchException) {

            if ($request->is('logout')) {
                return redirect()
                    ->route('login')
                    ->with('status', 'セッションの有効期限が切れました。再度ログインしてください。');
            }
            if ($request->is('login')) {
                return redirect()
                    ->route('login')
                    ->with('status', 'ログイン画面の有効期限が切れました。もう一度ログインしてください。');
            }

        }
        return parent::render($request, $exception);
    }
}
