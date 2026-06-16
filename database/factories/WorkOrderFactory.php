<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'assigned', 'in_progress', 'completed', 'rejected']);
        $priority = $this->faker->randomElement(['low', 'medium', 'high', 'critical']);

        $reporter = User::where('role', 'reporter')->inRandomOrder()->first() ?? User::factory()->create(['role' => 'reporter']);

        $receiver = null;
        $executor = null;
        $rating = null;

        if ($status !== 'pending' && $status !== 'rejected') {
            $receiver = User::where('role', 'receiver')->inRandomOrder()->first() ?? User::factory()->create(['role' => 'receiver']);
            $executor = User::where('role', 'executor')->inRandomOrder()->first() ?? User::factory()->create(['role' => 'executor']);
        }

        if ($status === 'completed') {
            $rating = $this->faker->numberBetween(1, 10);
        }

        return [
            'wo_code' => 'WO-'.date('Ymd').'-'.strtoupper($this->faker->lexify('????')),
            'title' => '[DUMMY] '.$this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'priority' => $priority,
            'status' => $status,
            'satisfaction_rating' => $rating,
            'reporter_id' => $reporter->id,
            'receiver_id' => $receiver?->id,
            'executor_id' => $executor?->id,
        ];
    }
}
