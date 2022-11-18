<?php

namespace Mintdev\Xml\Logger\Types;

enum Step: string
{
    case FROM_CALLER_TO_SERVICE = "FROM_CALLER_TO_SERVICE";
    case FROM_SERVICE_TO_CALLER = "FROM_SERVICE_TO_CALLER";
}