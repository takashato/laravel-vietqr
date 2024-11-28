<?php

namespace Takashato\VietQr\Data;

use Takashato\VietQr\Utils\StringUtil;

class AdditionalInfo
{
    public function __construct(
        protected ?string $purpose = null,
        protected ?string $terminalLabel = null,
    ) {}

    public function purpose(string $purpose): self
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function terminalLabel(string $terminalLabel): self
    {
        $this->terminalLabel = $terminalLabel;

        return $this;
    }

    public function build(): string
    {
        return
            ($this->terminalLabel ? StringUtil::buildWithLength('01', $this->terminalLabel) : '')
            . ($this->purpose ? StringUtil::buildWithLength('08', $this->purpose) : null);
    }
}
