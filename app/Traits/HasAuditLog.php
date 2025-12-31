<?php

namespace App\Traits;

use App\Models\AuditLog;

trait HasAuditLog
{
    public static function bootHasAuditLog()
    {
        static::created(function ($model) {
            AuditLog::logActivity('created', $model, null, $model->toArray());
        });
        
        static::updated(function ($model) {
            $changes = $model->getChanges();
            if (!empty($changes)) {
                AuditLog::logActivity('updated', $model, $model->getOriginal(), $changes);
            }
        });
        
        static::deleted(function ($model) {
            AuditLog::logActivity('deleted', $model, $model->toArray(), null);
        });
    }
}
