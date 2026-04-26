<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use stdClass;

class ApiController extends Controller {
    /**
     * The function returns a JSON response with a status code, message, data, errors, and timestamp.
     *
     * @param int status The status parameter is an integer that represents the HTTP status code of the
     * response. It indicates the success or failure of the request.
     * @param string message The "message" parameter is a string that represents a custom message that you
     * want to include in the response. It can be used to provide additional information or instructions to
     * the client.
     * @param array data The `` parameter is an array that contains any additional data that you want
     * to include in the response. This can be any information that you want to send back to the client.
     * @param array errors The `errors` parameter is an array that contains any error messages or
     * information related to the request or operation. It can be used to provide additional details about
     * any errors that occurred during the execution of the code.
     *
     * @return JsonResponse a JsonResponse object.
     */
    public function return(int $status, string $message = '', mixed $data = [], array $errors = [], mixed $debug = []): JsonResponse {
        $response = new stdClass();
        $response->status = $status >= 200 && $status < 300 ? 'success' : 'error';
        $response->code = $status;
        $response->success = $status >= 200 && $status < 300;
        $response->message = $message;

        // Flatten data if it's wrapped in ['data' => ...] to avoid double nesting
        if (is_array($data) && count($data) === 1 && isset($data['data'])) {
            $data = $data['data'];
        }

        if ($data instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $response->data = $data->items();
            $response->meta = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ];
        } else {
            $response->data = $data;
        }

        $response->errors = $errors;
        $response->timestamp = time();
        if (env("APP_DEBUG")) $response->debug = $debug;
        return response()->json($response, $status);
    }

    /**
     * The function returns a JSON response with a status code of 403 and a message of "Unauthenticated".
     *
     * @return JsonResponse A JsonResponse object is being returned.
     */
    public function returnUnAuthenticated(): JsonResponse {
        return $this->return(403, "Unauthenticated");
    }

    /**
     * The function returns a JSON response with a status code of 401 and a message of "Unauthorized".
     *
     * @return JsonResponse A JsonResponse object is being returned.
     */
    public function returnUnAuthorized(): JsonResponse {
        return $this->return(401, "Unauthorized");
    }

    /**
     * Return a successful response with data array
     */
    public function respondWithArray(array $data, string $message = ''): JsonResponse {
        return $this->return(200, $message, $data);
    }

    /**
     * Return a 201 created response
     */
    public function respondCreated(array $data, string $message = 'Created successfully'): JsonResponse {
        return $this->return(201, $message, $data);
    }

    /**
     * Return a 204 no content response
     */
    public function respondNoContent(string $message = ''): JsonResponse {
        return $this->return(204, $message);
    }

    /**
     * Return a 404 not found response
     */
    public function respondNotFound(string $message = 'Resource not found'): JsonResponse {
        return $this->return(404, $message);
    }

    /**
     * Return an error response
     */
    public function respondError(string $message, int $status = 400): JsonResponse {
        return $this->return($status, $message);
    }
}
