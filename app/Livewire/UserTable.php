<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class UserTable extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';

    public $role = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRole()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->role) {
            $query->where('role', $this->role);
        }

        return view('livewire.user-table', [
            'users' => $query->paginate(10),
        ]);
    }
}
