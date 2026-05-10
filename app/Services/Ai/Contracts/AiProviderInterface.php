<?php

namespace App\Services\Ai\Contracts;

use App\Services\Ai\DTO\AiResponse;

interface AiProviderInterface
{
    public function generate(string $prompt): AiResponse;
}
