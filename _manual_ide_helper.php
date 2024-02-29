<?php

/**
 * This file is for manually adding ide helpers for various classes and methods.
 */

namespace Illuminate\Http {
    class Request
    {
        public function user(): \App\Models\User
        {
            return new \App\Models\User();
        }
    }
}
