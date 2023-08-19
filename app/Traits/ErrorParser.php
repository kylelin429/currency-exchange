<?php

namespace App\Traits;

trait ErrorParser
{
    public function parseException(\Throwable $exception)
    {
        $errors = $exception->getMessage();
        if (isset($exception->validator)) {
            $errors = $exception->validator->errors();
            $errors = implode(' | ', $errors->all());
        }

        return $errors;
    }
}
