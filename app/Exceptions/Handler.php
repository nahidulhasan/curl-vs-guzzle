<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use App\Enums\ApiErrorType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use ReflectionException;
use stdClass;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @var stdClass
     */
    private $errorObj;

    private function initErrorObj()
    {
        $this->errorObj = new stdClass();
        $this->errorObj->type = "";
        $this->errorObj->code = "";
        $this->errorObj->message = "";
        $this->errorObj->target = "";
    }

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return Response
     * @throws ReflectionException
     */
    public function render($request, Exception $exception)
    {
        $this->initErrorObj();
        $reflect = new \ReflectionClass($exception);
        $method = 'handle' . $reflect->getShortName();
        if (method_exists($this, $method)) {
            return $this->$method($exception, $request);
        }

        return parent::render($request, $exception);
    }

    /**
     * NotFoundHttpException
     * The requested path could not match a route in the API
     *
     * @param NotFoundHttpException $exception
     * @return void 404
     */
    protected function handleNotFoundHttpException(NotFoundHttpException $exception)
    {
        abort(403);
/*        $this->errorObj->hint = "The requested api end-point not found";
        $this->errorObj->message = "Something unexpected happened.Please try again later";
        $this->errorObj->type = ApiErrorType::NOT_FOUND_ERROR;
        $this->errorObj->code = ApiErrorCode::NOT_FOUND_ERROR;
        $this->errorObj->target = "query";*/

/*        return response()->json([
            'status' => 'FAIL',
            'status_code' => 404,
            'error' => $this->errorObj
        ], 404);*/
    }


    /**
     * handleMethodNotAllowedHttpException
     * The used HTTP Accept header is not allowed on this route in the API
     *
     * @param MethodNotAllowedHttpException $exception
     * @return JsonResponse
     */
    protected function handleMethodNotAllowedHttpException(MethodNotAllowedHttpException $exception)
    {
        $this->errorObj->hint = "The requested method not allowed";
        $this->errorObj->message = "Something unexpected happened.Please try again later";
        $this->errorObj->type = ApiErrorType::METHOD_NOT_ALLOWED_ERROR;
        $this->errorObj->code = ApiErrorCode::METHOD_NOT_ALLOWED_ERROR;
        $this->errorObj->target = "query";

        return response()->json([
            'status' => 'FAIL',
            'status_code' => 406,
            'error' => $this->errorObj
        ], 406);
    }

    /**
     * ModelNotFoundException
     * The model is not found with given identifier
     *
     * @param ModelNotFoundException $exception
     * @return JsonResponse
     */
    protected function handleModelNotFoundException(ModelNotFoundException $exception)
    {
        $fullModel = $exception->getModel();
        $choppedUpModel = explode('\\', $fullModel);
        $cleanedUpModel = array_pop($choppedUpModel);
        $this->errorObj->hint = $cleanedUpModel . " model is not found with given identifier";
        $this->errorObj->message = 'What you are looking may have been replaced';
        $this->errorObj->target = $cleanedUpModel;
        return response()->json([
            'status' => 'FAIL',
            'status_code' => 404,
            'error' => $this->errorObj
        ], 404);
    }

    /**
     * ValidationException
     * Parameters did not pass validation
     *
     * @param ValidationException $exception
     * @return JsonResponse
     */
    protected function handleValidationException(ValidationException $exception)
    {
        $this->errorObj->type = ApiErrorType::VALIDATION_FAILED_ERROR;
        $this->errorObj->code = ApiErrorCode::VALIDATION_FAILED_ERROR;
        $this->errorObj->hint = "Parameters did not pass validation.See details for more info";
        $this->errorObj->target = "parameters";
        $this->errorObj->details = [];
        foreach ($exception->validator->errors()->getMessages() as $field => $message) {
            $details = new stdClass();
            $details->message = $message[0];
            $this->errorObj->message = $message[0];
            $details->target = $field;
            $this->errorObj->details[] = $details;
        }

        return response()->json([
            'status' => 'FAIL',
            'status_code' => 422,
            'error' => $this->errorObj
        ], 422);
    }
}
