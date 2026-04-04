<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * Return an integer input, treating empty string/null as missing and
     * returning the provided default in that case.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return int
     */
    protected function defaultInt(string $key, int $default): int
    {
        $val = $this->input($key);
        if ($val === '' || $val === null) {
            return $default;
        }

        return (int) $val;
    }

    /**
     * Return a float input, treating empty string/null as missing
     * and returning the provided default.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return float
     */
    protected function defaultFloat(string $key, float $default): float
    {
        $val = $this->input($key);
        if ($val === '' || $val === null) {
            return $default;
        }

        return (float) $val; // Uses float instead of int
    }

    /**
     * Return a string input, treating empty string/null as missing and
     * returning the provided default in that case.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return string
     */
    protected function defaultString(string $key, string $default): string
    {
        $val = $this->input($key);
        if ($val === '' || $val === null) {
            return $default;
        }

        return (string) $val;
    }
}
