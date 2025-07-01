@extends('layouts.app')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .main {
            flex-grow: 1;
            padding: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.users { background-color: #dbeafe; color: #1d4ed8; }
        .stat-icon.appointments { background-color: #dcfce7; color: #16a34a; }
        .stat-icon.hospitals { background-color: #f3e8ff; color: #9333ea; }
        .stat-icon.feedback { background-color: #fef3c7; color: #d97706; }

        .stat-info h3 {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }

        .activity-section {
            background-color: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .activity-section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .activity-icon.users { background-color: #dbeafe; color: #1d4ed8; }
        .activity-icon.appointments { background-color: #dcfce7; color: #16a34a; }
        .activity-icon.hospitals { background-color: #f3e8ff; color: #9333ea; }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin-bottom: 2px;
        }

        .activity-description {
            font-size: 13px;
            color: #6b7280;
        }

        .activity-time {
            font-size: 12px;
            color: #9ca3af;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        @media (max-width: 640px) {
            .dashboard-card, .main {
                padding: 0.5rem !important;
            }
            .dashboard-stats {
                flex-direction: column !important;
                gap: 0.5rem !important;
            }
            .stat-card {
                width: 100% !important;
                margin-bottom: 0.5rem !important;
            }
        }
    </style>

    <div class="main px-2 sm:px-4 md:px-8">
        <!-- Dashboard Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users">👥</div>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <div class="stat-number">{{ number_format($totalUsers) }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon appointments">📅</div>
                <div class="stat-info">
                    <h3>Appointments</h3>
                    <div class="stat-number">{{ number_format($totalAppointments) }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon hospitals">🏥</div>
                <div class="stat-info">
                    <h3>Hospitals</h3>
                    <div class="stat-number">{{ number_format($totalHospitals) }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon feedback">💬</div>
                <div class="stat-info">
                    <h3>Feedback</h3>
                    <div class="stat-number">{{ number_format($totalFeedback) }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="activity-section">
            <h2>Recent Activity</h2>
            
            @if($recentUsers->count() > 0 || $recentAppointments->count() > 0 || $recentHospitals->count() > 0)
                @if($recentUsers->count() > 0)
                    @foreach($recentUsers->take(3) as $user)
                    <div class="activity-item">
                        <div class="activity-icon users">👤</div>
                        <div class="activity-content">
                            <div class="activity-title">New user registered</div>
                            <div class="activity-description">{{ $user->first_name }} {{ $user->last_name }} joined CareConnect</div>
                        </div>
                        <div class="activity-time">{{ $user->created_at->diffForHumans() }}</div>
                    </div>
                    @endforeach
                @endif

                @if($recentAppointments->count() > 0)
                    @foreach($recentAppointments->take(3) as $appointment)
                    <div class="activity-item">
                        <div class="activity-icon appointments">✅</div>
                        <div class="activity-content">
                            <div class="activity-title">Appointment scheduled</div>
                            <div class="activity-description">{{ $appointment->user->first_name }} {{ $appointment->user->last_name }} scheduled appointment at {{ $appointment->hospital->name }}</div>
                        </div>
                        <div class="activity-time">{{ $appointment->created_at->diffForHumans() }}</div>
                    </div>
                    @endforeach
                @endif

                @if($recentHospitals->count() > 0)
                    @foreach($recentHospitals->take(3) as $hospital)
                    <div class="activity-item">
                        <div class="activity-icon hospitals">🏥</div>
                        <div class="activity-content">
                            <div class="activity-title">New hospital added</div>
                            <div class="activity-description">{{ $hospital->name }} joined the network</div>
                        </div>
                        <div class="activity-time">{{ $hospital->created_at->diffForHumans() }}</div>
                    </div>
                    @endforeach
                @endif
            @else
                <div class="empty-state">
                    <p>No recent activity</p>
                </div>
            @endif
        </div>
    </div>
@endsection
