<!-- resources/views/components/tu/upload-notification-success-tu.blade.php -->
<link rel="stylesheet" href="{{ asset('css/tu/upload-notification-success-tu.css') }}">

<div id="successNotificationModal" class="success-modal-overlay hidden">
    <div class="success-modal-container">
        <!-- Checkmark Animation -->
        <div class="success-checkmark-circle">
            <svg class="success-checkmark" viewBox="0 0 52 52">
                <circle class="success-checkmark-circle-bg" cx="26" cy="26" r="25" fill="none"/>
                <path class="success-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <!-- Success Text -->
        <h2 class="success-title">Selamat!</h2>
        <p class="success-message">Dokumen berhasil diunggah</p>

        <!-- OK Button -->
        <button type="button" class="success-ok-button" onclick="closeSuccessNotification()">
            OK
        </button>
    </div>
</div>

<script src="{{ asset('js/tu/upload-notification-success-tu.js') }}"></script>