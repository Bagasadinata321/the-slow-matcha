<?php
// app/Models/VisitorLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class VisitorLog extends Model
{
    use Prunable;

    /**
     * Tentukan query untuk menghapus data log yang lebih tua dari 30 hari.
     */
    public function prunability()
    {
        return static::where('visited_at', '<', now()->subDays(30));
    }
}