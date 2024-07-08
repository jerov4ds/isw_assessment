<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends BaseModel
{
    use HasFactory;
    protected $table = 'audit_logs';

    public function saveAudit(AuditLog $auditLog)
    {
        if(empty($auditLog->username)) $auditLog->username = "Super Admin";
        $this->save((array)$auditLog);
    }
}
