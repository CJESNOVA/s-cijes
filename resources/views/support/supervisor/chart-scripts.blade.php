{{-- Scripts Chart.js pour le dashboard superviseur --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration globale des graphiques
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#4B5563';
    
    // Graphique 1: Répartition des tickets par statut
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['En Cours', 'Résolus', 'Ouverts'],
                datasets: [{
                    data: [
                        {{ $stats['ouverts'] }},
                        {{ $stats['resolus'] }},
                        {{ $stats['total'] - $stats['ouverts'] - $stats['resolus'] }}
                    ],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Graphique 2: Volume par plateforme
    const platformCtx = document.getElementById('platformChart');
    if (platformCtx) {
        new Chart(platformCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($parPlateforme as $plat)
                    '{{ $plat->nom }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Nombre de tickets',
                    data: [
                        @foreach($parPlateforme as $plat)
                        {{ $plat->tickets_count }},
                        @endforeach
                    ],
                    backgroundColor: '#3B82F6',
                    borderColor: '#2563EB',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E5E7EB'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Graphique 3: Tendance des tickets (7 derniers jours)
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: [
                    @php
                        $labels = [];
                        $data = [];
                        for ($i = 6; $i >= 0; $i--) {
                            $date = now()->subDays($i)->format('d/m');
                            $count = App\Models\Ticket::whereDate('created_at', now()->subDays($i))->count();
                            $labels[] = $date;
                            $data[] = $count;
                        }
                    @endphp
                    @foreach($labels as $label)
                    '{{ $label }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Tickets créés',
                    data: [
                        @foreach($data as $count)
                        {{ $count }},
                        @endforeach
                    ],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(context) {
                                return `Jour ${context[0].label}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: '#E5E7EB'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E5E7EB'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    
    // Graphique 4: Performance SLA (temps de résolution)
    const slaCtx = document.getElementById('slaChart');
    if (slaCtx) {
        new Chart(slaCtx, {
            type: 'line',
            data: {
                labels: [
                    @php
                        $slaLabels = [];
                        $slaData = [];
                        for ($i = 6; $i >= 0; $i--) {
                            $date = now()->subDays($i)->format('d/m');
                            $avgTime = App\Models\Ticket::whereDate('date_fermeture', now()->subDays($i))
                                ->whereNotNull('date_fermeture')
                                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, date_ouverture, date_fermeture)) as avg_time')
                                ->value() ?? 0;
                            $slaLabels[] = $date;
                            $slaData[] = round($avgTime, 1);
                        }
                    @endphp
                    @foreach($slaLabels as $label)
                    '{{ $label }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'SLA (heures)',
                    data: [
                        @foreach($slaData as $time)
                        {{ $time }},
                        @endforeach
                    ],
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#F59E0B',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `SLA: ${context.parsed.y}h`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: '#E5E7EB'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E5E7EB'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Animation des nombres au chargement
    function animateNumbers() {
        const numbers = document.querySelectorAll('.animate-number');
        numbers.forEach(number => {
            const final = parseInt(number.getAttribute('data-final'));
            const duration = 2000;
            const increment = final / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= final) {
                    number.textContent = final;
                    clearInterval(timer);
                } else {
                    number.textContent = Math.floor(current);
                }
            }, 16);
        });
    }
    
    // Démarrer les animations
    animateNumbers();
});
</script>
