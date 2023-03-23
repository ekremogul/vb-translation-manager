<?php

namespace EkremOgul\VbTranslateManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \EkremOgul\VbTranslateManager\VbTranslateManager
 */
class VbTranslateManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \EkremOgul\VbTranslateManager\VbTranslateManager::class;
    }
}
