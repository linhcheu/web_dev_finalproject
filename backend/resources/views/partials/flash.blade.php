{{--
    Success / error messages after a form is sent.
    A page that prints these itself declares @section('handles_flash', '1')
    (and @section('handles_errors', '1') for validation errors) to avoid duplicates.
--}}
@php
    $showFlash = ! View::hasSection('handles_flash');
    $showErrors = ! View::hasSection('handles_errors') && $errors->any();
@endphp

@if(($showFlash && (session('success') || session('error'))) || $showErrors)
    <div class="flash-stack" role="status" aria-live="polite">
        @if($showFlash && session('success'))
            <div class="flash flash-success">
                <span>{{ session('success') }}</span>
                <button type="button" class="flash-close" aria-label="Dismiss" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif
        @if($showFlash && session('error'))
            <div class="flash flash-error">
                <span>{{ session('error') }}</span>
                <button type="button" class="flash-close" aria-label="Dismiss" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif
        @if($showErrors)
            <div class="flash flash-error">
                <ul>
                    @foreach($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
                <button type="button" class="flash-close" aria-label="Dismiss" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif
    </div>

    <style>
        .flash-stack { max-width: 1100px; margin: 16px auto 0; padding: 0 16px; display: grid; gap: 10px; }
        .flash { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 12px 16px;
                 border-radius: 12px; font-size: 15px; line-height: 1.4; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
        .flash ul { margin: 0; padding-left: 18px; }
        .flash-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .flash-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .flash-close { background: none; border: 0; font-size: 22px; line-height: 1; cursor: pointer; color: inherit; opacity: .6; }
        .flash-close:hover { opacity: 1; }
    </style>
@endif
