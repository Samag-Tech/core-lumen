<?php

// --------------------------------------------------------------------------


use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Helper per le risposte generiche di successo
 *
 * @access public
 * @param JsonResource|array $response
 * @param int $statusCode
 *
 * @return Response
 */
if ( ! function_exists('respond')) {
	function respond(JsonResource|array $response, int $statusCode = 200) {

        if ( $response instanceof JsonResource ) {
            $response->response()->setStatusCode($statusCode);
        }
        else {
            $response = response()->json($response, $statusCode);
        }

        return $response;
	}
}

// --------------------------------------------------------------------------

/**
 * Helper per le risposte generiche di fallimento
 *
 * @access public
 * @param string|array $messages
 * @param int $statusCode
 *
 * @return Response
 */
if ( ! function_exists('respondFail')) {
	function respondFail(array|string $messages, int $statusCode = 400 ) {

        $errors = is_string($messages) ? ['error' => $messages] : ['errors' => $messages];

        return response()->json([
           'messages' => $errors
        ], $statusCode);
	}
}
