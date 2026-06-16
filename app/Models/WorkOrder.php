<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class WorkOrder
 *
 * Model yang merepresentasikan entitas Work Order / laporan pekerjaan.
 *
 * @property int $id
 * @property string $wo_code
 * @property string $title
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property int|null $satisfaction_rating
 * @property int $reporter_id
 * @property int|null $receiver_id
 * @property int|null $executor_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $reporter
 * @property-read User|null $receiver
 * @property-read User|null $executor
 * @property-read Collection|WorkOrderActivity[] $activities
 */
class WorkOrder extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wo_code',
        'title',
        'description',
        'priority',
        'status',
        'satisfaction_rating',
        'reporter_id',
        'receiver_id',
        'executor_id',
    ];

    /**
     * Event boot model untuk memicu aksi saat pembuatan.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($workOrder) {
            if (empty($workOrder->wo_code)) {
                $workOrder->wo_code = 'WO-'.date('Ymd').'-'.strtoupper(Str::random(4));
            }
        });
    }

    /**
     * Relasi ke pelapor (Reporter).
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Relasi ke penerima/dispatcher (Receiver).
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Relasi ke pelaksana (Executor).
     */
    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    /**
     * Relasi ke log aktivitas Work Order.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(WorkOrderActivity::class);
    }
}
