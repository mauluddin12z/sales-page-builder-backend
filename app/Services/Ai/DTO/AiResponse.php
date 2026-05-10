<?php

namespace App\Services\Ai\DTO;

class AiResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $content,
        public readonly string $provider,
        public readonly string $model,
    ) {}
}
