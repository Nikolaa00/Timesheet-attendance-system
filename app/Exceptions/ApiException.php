<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiException extends Exception
{
    protected array $loc = [];
    protected string $type = 'api_error';
    protected $input = null;
    protected array $ctx = [];
    protected int $status = 422;

    public function __construct(
        string $message = "An error occurred.",
        array $loc = [],
        string $type = 'api_error',
        $input = null,
        array $ctx = [],
        int $status = 422
    ) {
        parent::__construct($message);
        $this->loc = $loc;
        $this->type = $type;
        $this->input = $input;
        $this->ctx = $ctx;
        $this->status = $status; 
    }

    public function render(Request $request)
    {
        return response()->json([
            "detail" => [
                [
                    "loc" => $this->loc,
                    "msg" => $this->getMessage(),
                    "type" => $this->type,
                    "input" => $this->input,
                    "ctx" => (object) $this->ctx,
                ]
            ]
        ], $this->status);
    }
}