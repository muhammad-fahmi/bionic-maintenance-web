<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class WorkOrderActivity
 *
 * Model yang merepresentasikan rekaman aktivitas eksekusi sebuah Work Order.
 *
 * @property int $id
 * @property int $work_order_id
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WorkOrder $workOrder
 */
class WorkOrderActivity extends Model
{
    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'description',
    ];

    /**
     * Relasi ke entitas Work Order.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
