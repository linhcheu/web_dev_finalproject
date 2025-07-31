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

        .appointments-box {
            background-color: white;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .appointments-box img {
            width: 60px;
            height: 60px;
        }

        .appointments-table {
            background-color: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .appointments-table h3 {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
        }

        .feedback-item {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
        }

        .feedback-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .user-info h4 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 3px;
        }

        .date {
            font-size: 12px;
            color: #6b7280;
        }

        .feedback-content {
            color: #374151;
            line-height: 1.6;
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .pagination {
            text-align: center;
            padding: 20px 0 0;
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
        }
        
        .pagination button {
            min-width: 36px;
            height: 36px;
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            cursor: pointer;
            font-weight: 500;
            color: #4b5563;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .pagination button:hover:not(:disabled) {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            transform: translateY(-1px);
        }
        
        .pagination button.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .pagination a {
            text-decoration: none;
        }

        @media (max-width: 640px) {
            .feedback-table {
                padding: 0.5rem !important;
            }
            table, th, td {
                font-size: 12px !important;
                padding: 6px 8px !important;
            }
            .action-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .pagination {
                gap: 4px;
            }
            
            .pagination button {
                min-width: 32px;
                height: 32px;
                padding: 4px 8px;
                font-size: 13px;
            }
        }
    </style>

    <div class="main px-2 sm:px-4 md:px-8">
        <div class="appointments-box">
            <img src="{{ asset('images/feedback_icon.png') }}" alt="Feedback">
            <div>
                <h3><strong>Total Feedback</strong></h3>
                <h1>{{ number_format($totalFeedback) }}</h1>
            </div>
        </div>

        <div class="appointments-table">
            <h3>All Feedback</h3>
            
            @if($feedback->count() > 0)
                @foreach($feedback as $item)
                    <div class="feedback-item">
                        <div class="feedback-header">
                            <div class="user-avatar">
                                {{ strtoupper(substr($item->user->first_name, 0, 1)) }}{{ strtoupper(substr($item->user->last_name, 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <h4>{{ $item->user->first_name }} {{ $item->user->last_name }}</h4>
                                <div class="date">{{ $item->created_at->format('M j, Y \a\t g:i A') }}</div>
                            </div>
                        </div>
                        <div class="feedback-content">
                            {{ $item->comments }}
                        </div>
                    </div>
                @endforeach

                @if($feedback->hasPages())
                    <div class="pagination">
                        @if($feedback->onFirstPage())
                            <button disabled>&lt;</button>
                        @else
                            <a href="{{ $feedback->previousPageUrl() }}"><button>&lt;</button></a>
                        @endif

                        @foreach($feedback->getUrlRange(1, $feedback->lastPage()) as $page => $url)
                            @if($page == $feedback->currentPage())
                                <button class="active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}"><button>{{ $page }}</button></a>
                            @endif
                        @endforeach

                        @if($feedback->hasMorePages())
                            <a href="{{ $feedback->nextPageUrl() }}"><button>&gt;</button></a>
                        @else
                            <button disabled>&gt;</button>
                        @endif
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <p>No feedback found</p>
                </div>
            @endif
        </div>
    </div>
@endsection
