<div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 border-l-4 border-indigo-500">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Users</div>
            <div class="mt-1 text-3xl font-semibold">{{ $userCount }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 border-l-4 border-green-500">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Work Orders</div>
            <div class="mt-1 text-3xl font-semibold">{{ $woCount }}</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
            <div class="flex gap-4">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 dark:focus:border-indigo-500 dark:focus:ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Manage Users
                </a>
                <button wire:click="$refresh" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 dark:focus:border-gray-700 disabled:opacity-25 transition ease-in-out duration-150">
                    Refresh Stats
                </button>
            </div>
        </div>
    </div>

    @if($totalRatings > 0)
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" wire:ignore>
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Statistik Kepuasan Pelapor</h3>
                <div class="flex gap-2">
                    <button id="btnExportPng" class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-sm font-semibold rounded-md border border-indigo-200 dark:border-indigo-700 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">PNG</button>
                    <button id="btnExportJpeg" class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-sm font-semibold rounded-md border border-indigo-200 dark:border-indigo-700 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">JPEG</button>
                    <button id="btnExportPdf" class="px-3 py-1 bg-red-50 dark:bg-red-900/50 text-red-700 dark:text-red-300 text-sm font-semibold rounded-md border border-red-200 dark:border-red-700 hover:bg-red-100 dark:hover:bg-red-900 transition-colors">PDF</button>
                </div>
            </div>
            <div class="relative rounded-lg overflow-hidden" style="height: 400px; background-color: var(--chart-bg, white);" x-data="{ dark: document.documentElement.classList.contains('dark') }" @theme-changed.window="dark = document.documentElement.classList.contains('dark')" :style="dark ? '--chart-bg: #1f2937;' : '--chart-bg: white;'">
                <canvas id="satisfactionChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Setup observer for dark mode changes to update chart text color
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && window.mySatisfactionChart) {
                    const isDark = document.documentElement.classList.contains('dark');
                    Chart.defaults.color = isDark ? '#d1d5db' : '#6b7280';
                    window.mySatisfactionChart.update();
                    // trigger alpine event for chart bg
                    window.dispatchEvent(new CustomEvent('theme-changed'));
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });

        document.addEventListener('livewire:initialized', () => {
            initChart();
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') {
                setTimeout(initChart, 500);
            } else {
                initChart();
            }
        });

        function initChart() {
            const canvas = document.getElementById('satisfactionChart');
            if (!canvas) return;
            
            const isDark = document.documentElement.classList.contains('dark');
            Chart.defaults.color = isDark ? '#d1d5db' : '#6b7280';

            const ctx = canvas.getContext('2d');
            
            const data = @json($satisfactionStats);
            const labels = data.map(item => 'Nilai ' + item.rating);
            const percentages = data.map(item => item.percentage);
            
            const backgroundColors = data.map(item => {
                const r = item.rating;
                if (r <= 3) return 'rgba(239, 68, 68, 0.7)';
                if (r <= 6) return 'rgba(249, 115, 22, 0.7)';
                if (r <= 8) return 'rgba(234, 179, 8, 0.7)';
                return 'rgba(34, 197, 94, 0.7)';
            });
            
            const borderColors = data.map(item => {
                const r = item.rating;
                if (r <= 3) return 'rgb(220, 38, 38)';
                if (r <= 6) return 'rgb(234, 88, 12)';
                if (r <= 8) return 'rgb(202, 138, 4)';
                return 'rgb(22, 163, 74)';
            });

            const customCanvasBackgroundColor = {
                id: 'customCanvasBackgroundColor',
                beforeDraw: (chart, args, options) => {
                    const {ctx} = chart;
                    ctx.save();
                    ctx.globalCompositeOperation = 'destination-over';
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#1f2937' : (options.color || '#ffffff');
                    ctx.fillRect(0, 0, chart.width, chart.height);
                    ctx.restore();
                }
            };

            // Destroy previous chart if exists
            if (window.mySatisfactionChart) {
                window.mySatisfactionChart.destroy();
            }

            window.mySatisfactionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase Kepuasan (%)',
                        data: percentages,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1,
                        hoverOffset: 4
                    }]
                },
                plugins: [customCanvasBackgroundColor],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        customCanvasBackgroundColor: {
                            color: 'white',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const count = data[context.dataIndex].count;
                                    return ' ' + context.label + ': ' + context.raw + '% (' + count + ' laporan)';
                                }
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: isDark ? '#d1d5db' : '#6b7280'
                            }
                        }
                    }
                }
            });

            document.getElementById('btnExportPng')?.addEventListener('click', function() {
                const link = document.createElement('a');
                link.href = window.mySatisfactionChart.toBase64Image();
                link.download = 'statistik_kepuasan.png';
                link.click();
            });

            document.getElementById('btnExportJpeg')?.addEventListener('click', function() {
                const link = document.createElement('a');
                link.href = window.mySatisfactionChart.toBase64Image('image/jpeg', 1.0);
                link.download = 'statistik_kepuasan.jpeg';
                link.click();
            });

            document.getElementById('btnExportPdf')?.addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape');
                const imgData = window.mySatisfactionChart.toBase64Image('image/jpeg', 1.0);
                
                doc.setFontSize(18);
                doc.text("Statistik Kepuasan Pelapor", 14, 22);
                doc.addImage(imgData, 'JPEG', 15, 30, 260, 130);
                doc.save('statistik_kepuasan.pdf');
            });
        }
    </script>
    @endpush
    @endif
</div>
