<?php
namespace tests;

use jeyroik\components\attributes\THasIdString;
use jeyroik\interfaces\attributes\IHaveIdString;

class Some implements IHaveIdString
{
    use THasIdString;
}
