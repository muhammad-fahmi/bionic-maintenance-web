<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Execute Work Order') }}: {{ $workOrder->wo_code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium mb-2">{{ $workOrder->title }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $workOrder->description }}</p>
                    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        <span class="mr-4"><strong>Priority:</strong> {{ ucfirst($workOrder->priority) }}</span>
                        <span><strong>Status:</strong> {{ ucfirst($workOrder->status) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Daily Activities / Field Report</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 italic">Add all activities performed at the site. This is a dynamic form to accommodate different field situations.</p>

                    <form method="POST" action="{{ route('executor.work-orders.complete', $workOrder) }}" x-data="{ activities: [''] }">
                        @csrf

                        <div class="space-y-4">
                            <template x-for="(activity, index) in activities" :key="index">
                                <div class="flex items-start gap-3">
                                    <div class="flex-grow">
                                        <x-input-label x-bind:for="'activity_' + index" x-text="'Activity #' + (index + 1)" />
                                        <textarea 
                                            x-bind:id="'activity_' + index" 
                                            name="activities[]" 
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                            rows="2" 
                                            required 
                                            x-model="activities[index]"
                                            placeholder="Describe what you did..."></textarea>
                                    </div>
                                    <div class="mt-7">
                                        <button 
                                            type="button" 
                                            @click="if(activities.length > 1) activities.splice(index, 1)" 
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 focus:outline-none"
                                            x-show="activities.length > 1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <button 
                                type="button" 
                                @click="activities.push('')" 
                                class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                + Add Activity
                            </button>

                            <x-primary-button class="bg-green-600 hover:bg-green-700 active:bg-green-900 focus:bg-green-700 focus:ring-green-500">
                                {{ __('Finish & Submit Report') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
