<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Class User
 *
 * Model otentikasi untuk entitas pengguna sistem.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|WorkOrder[] $reportedWorkOrders
 * @property-read Collection|WorkOrder[] $assignedWorkOrders
 * @property-read Collection|WorkOrder[] $executedWorkOrders
 */
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mengecek apakah pengguna memiliki peran sebagai Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Mengecek apakah pengguna memiliki peran sebagai Pelapor (Reporter).
     */
    public function isReporter(): bool
    {
        return $this->role === 'reporter';
    }

    /**
     * Mengecek apakah pengguna memiliki peran sebagai Penerima/Dispatcher (Receiver).
     */
    public function isReceiver(): bool
    {
        return $this->role === 'receiver';
    }

    /**
     * Mengecek apakah pengguna memiliki peran sebagai Pelaksana (Executor).
     */
    public function isExecutor(): bool
    {
        return $this->role === 'executor';
    }

    /**
     * Relasi Work Order yang dilaporkan oleh pengguna ini.
     *
     * @return HasMany
     */
    public function reportedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'reporter_id');
    }

    /**
     * Relasi Work Order yang ditetapkan oleh pengguna ini (Dispatcher).
     *
     * @return HasMany
     */
    public function assignedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'receiver_id');
    }

    /**
     * Relasi Work Order yang harus dieksekusi oleh pengguna ini.
     *
     * @return HasMany
     */
    public function executedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'executor_id');
    }
}
