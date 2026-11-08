<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Work Order') }}: {{ $workOrder->wo_code }}
            </h2>
            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                {{ $workOrder->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                   ($workOrder->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' : 
                   ($workOrder->status === 'assigned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 
                   ($workOrder->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))) }}">
                {{ str_replace('_', ' ', ucfirst($workOrder->status)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300 p-4" role="alert">
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 dark:border-red-600 text-red-700 dark:text-red-300 p-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Informasi Utama -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4 border-b dark:border-gray-700 pb-2 text-gray-800 dark:text-gray-200">Informasi Pekerjaan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Judul</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $workOrder->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Prioritas</p>
                            <p class="text-lg">{{ ucfirst($workOrder->priority) }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Deskripsi Masalah</p>
                            <p class="mt-1 bg-gray-50 dark:bg-gray-700 p-4 rounded border dark:border-gray-600 text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $workOrder->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penilaian Kepuasan (Hanya untuk Pelapor jika sudah selesai) -->
            @if($workOrder->status === 'completed' && auth()->user()->isReporter())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-400 dark:border-yellow-600">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4 border-b dark:border-gray-700 pb-2 text-gray-800 dark:text-gray-200">Penilaian Kepuasan Pelanggan</h3>
                        @if($workOrder->satisfaction_rating)
                            <div class="flex items-center gap-4">
                                <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">{{ $workOrder->satisfaction_rating }}<span class="text-xl text-gray-400 dark:text-gray-500">/10</span></div>
                                <p class="text-gray-600 dark:text-gray-400">Terima kasih! Anda telah memberikan penilaian kepuasan.</p>
                            </div>
                        @else
                            <form method="POST" action="{{ route('reporter.work-orders.rate', $workOrder) }}">
                                @csrf
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Bagaimana penilaian Anda terhadap penyelesaian pekerjaan ini? (1 = Sangat Kecewa, 10 = Sangat Puas)</p>
                                <div class="flex flex-wrap justify-center sm:justify-start items-center gap-2 sm:gap-4 mb-6 mt-2">
                                    @for($i = 1; $i <= 10; $i++)
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="rating" value="{{ $i }}" class="peer sr-only" required>
                                            <span class="w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 text-lg font-bold bg-white dark:bg-gray-700 shadow-sm transform transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:border-indigo-400 dark:hover:border-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-300 hover:shadow-md peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-indigo-200 dark:peer-checked:shadow-indigo-900 peer-checked:shadow-lg peer-checked:scale-110 peer-checked:hover:bg-indigo-700 peer-checked:hover:border-indigo-700 active:scale-95">
                                                {{ $i }}
                                            </span>
                                        </label>
                                    @endfor
                                </div>
                                <div class="flex justify-end">
                                    <x-primary-button class="px-6 py-3 text-base shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200">
                                        Kirim Nilai Kepuasan
                                    </x-primary-button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @elseif($workOrder->satisfaction_rating)
                <!-- Tampilan Nilai untuk Role Lain (Penerima/Admin) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 dark:border-green-600">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4 border-b dark:border-gray-700 pb-2 text-gray-800 dark:text-gray-200">Hasil Penilaian Kepuasan</h3>
                        <div class="flex items-center gap-4">
                            <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">{{ $workOrder->satisfaction_rating }}<span class="text-xl text-gray-400 dark:text-gray-500">/10</span></div>
                            <p class="text-gray-600 dark:text-gray-400">Pelapor telah memberikan penilaian kepuasan untuk pekerjaan ini.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Personel Terkait -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4 border-b dark:border-gray-700 pb-2 text-gray-800 dark:text-gray-200">Personel Terkait</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Pelapor</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workOrder->reporter->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Penerima/Dispatcher</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workOrder->receiver->name ?? 'Belum Ditugaskan' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Pelaksana Lapangan</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workOrder->executor->name ?? 'Belum Ditugaskan' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Kegiatan Lapangan -->
            @if($workOrder->status === 'completed' || $workOrder->activities->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4 border-b dark:border-gray-700 pb-2 text-gray-800 dark:text-gray-200">Laporan Kegiatan Lapangan</h3>
                        @if($workOrder->activities->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 italic">Belum ada rincian kegiatan yang dilaporkan.</p>
                        @else
                            <ul class="space-y-4">
                                @foreach($workOrder->activities as $index => $activity)
                                    <li class="flex gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600 shadow-sm">
                                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <p class="text-gray-700 dark:text-gray-200">{{ $activity->description }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">Dilaporkan pada {{ $activity->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex justify-start">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
