<?php

namespace EkremOgul\VbTranslateManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VbTranslateManagerTranslate extends Model
{
    protected $table = "vb_translate_manager_translates";
    protected $guarded = [];
    public function key() : BelongsTo
    {
        return $this->belongsTo(VbTranslateManagerKey::class, 'id','key_id');
    }
}
