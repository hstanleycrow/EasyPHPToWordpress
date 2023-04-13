<?php

namespace hcrow\EasyPHPToWordpress;

class ErrorHandler
{
    const NO_ERROR_CODE = 0;
    const NO_ERROR_MESSAGE = "No errors found";
    private int $errorCode;
    private string $errorMessage;

    public function __construct()
    {
        $this->setError(self::NO_ERROR_CODE, self::NO_ERROR_MESSAGE);
    }

    public function setError(int $errorCode, ?string $message = null): void
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $message;
    }

    /**
     * Get the value of errorCode
     */
    public function error(): string
    {
        return "Code: " . $this->errorCode . ", Message: " . $this->errorMessage;
    }
}
