<?php

declare(strict_types=1);

namespace App\UI\Api\Exception;

final class ApiValidationException extends \RuntimeException
{
    /**
     * @param array<int,array{path:string,message:string}> $violations
     */
    public function __construct(
        string $message,
        private readonly array $violations = [],
    ) {
        parent::__construct($message);
    }

    /**
     * @return array<int, array{path: string, message: string}>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
