<?php

namespace EkremOgul\VbTranslateManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VbTranslateManagerKey extends Model
{
    protected $table = "vb_translate_manager_keys";
    protected $guarded = [];
    public function translates(): HasMany
    {
        return $this->hasMany(VbTranslateManagerTranslate::class, 'key_id','id');
    }
}
