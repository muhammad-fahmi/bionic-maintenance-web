<div>
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0">
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Tiket / Judul..." class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full sm:w-auto">
            
            <select wire:model.live="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full sm:w-auto">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="assigned">Assigned</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
            </select>

            <select wire:model.live="priority" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full sm:w-auto">
                <option value="">Semua Prioritas</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
            </select>
        </div>

        <!-- Dummy Data Buttons -->
        <div class="flex gap-2">
            <button wire:click="generateDummyData" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                + Tambah Dummy
            </button>
            <button wire:click="deleteDummyData" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                Hapus Dummy
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                    <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">No. Tiket</th>
                    <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Judul</th>
                    <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Pelapor</th>
                    <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Prioritas</th>
                    <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Tanggal</th>
                    <th class="p-4 font-semibold text-gray-600 dark:text-gray-300 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                    <tr wire:key="wo-{{ $wo->id }}" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-gray-900 dark:text-gray-100">
                        <td class="p-4 text-sm">{{ $wo->wo_code }}</td>
                        <td class="p-4 font-medium">{{ $wo->title }}</td>
                        <td class="p-4 text-sm">{{ $wo->reporter->name ?? '-' }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs rounded-full font-semibold
                                {{ $wo->priority === 'low' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : 
                                   ($wo->priority === 'medium' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 
                                   ($wo->priority === 'high' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300')) }}">
                                {{ ucfirst($wo->priority) }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs rounded-full font-semibold
                                {{ $wo->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                   ($wo->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' : 
                                   ($wo->status === 'assigned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 
                                   ($wo->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))) }}">
                                {{ str_replace('_', ' ', ucfirst($wo->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-gray-500 dark:text-gray-400">{{ $wo->created_at->format('d M Y') }}</td>
                        <td class="p-4 text-center">
                            <a href="{{ route(
                                Auth::user()->isReporter() ? 'reporter.work-orders.show' : 
                                (Auth::user()->isAdmin() ? 'admin.work-orders.show' : 
                                (Auth::user()->isReceiver() ? 'receiver.work-orders.show' : 'executor.work-orders.show')), 
                                $wo) }}" 
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500 dark:text-gray-400">Belum ada data laporan yang cocok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $workOrders->links('vendor.livewire.tailwind') }}
        </div>
    </div>
</div>
