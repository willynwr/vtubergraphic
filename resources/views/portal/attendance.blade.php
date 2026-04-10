<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi - VtuberGraphic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'] } } } }
    </script>
    <style>
        @keyframes scanLine { 0%,100% { top: 10%; } 50% { top: 80%; } }
        .scan-line { animation: scanLine 2.5s ease-in-out infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .animate-blink { animation: blink 1.5s infinite; }

        .attendance-confirm-overlay {
            background: rgba(61, 43, 58, 0.42);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 220ms ease;
        }

        .attendance-confirm-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .attendance-confirm-card {
            transform: translateY(10px) scale(0.96);
            opacity: 0;
            transition: transform 260ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease;
        }

        .attendance-confirm-overlay.show .attendance-confirm-card {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="font-poppins bg-[#fef7ff] text-[#3d2b3a] min-h-screen pb-10 overflow-x-hidden">
    {{-- Background blobs --}}
    <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
        <div class="absolute -top-[60px] -right-10 w-[260px] h-[260px] bg-[#e87bb0] rounded-full blur-[100px] opacity-[0.08]"></div>
        <div class="absolute bottom-[100px] -left-[60px] w-[300px] h-[300px] bg-[#b388d9] rounded-full blur-[120px] opacity-[0.06]"></div>
    </div>

    <div class="max-w-md lg:max-w-3xl xl:max-w-5xl mx-auto px-5 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center gap-3.5 py-5 pb-2">
            <a href="{{ route('portal.index') }}" class="w-10 h-10 rounded-xl bg-white/[0.92] border-none flex items-center justify-center no-underline text-[#3d2b3a] transition-all duration-200 hover:bg-[#ffe6f040] hover:-translate-y-0.5">
                <svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <div class="text-[11px] text-[#b8a0b0] uppercase tracking-[1.5px]">{{ $employee->department ?? '-' }}</div>
                <div class="text-xl font-extrabold">Absensi</div>
            </div>
        </div>

        {{-- Status Card --}}
        <div class="mb-6 text-center">
            <!-- <div class="text-xs font-semibold text-[#b8a0b0] mb-2 uppercase tracking-[1px]">Waktu Saat Ini</div> -->
            <div class="inline-flex items-center gap-3.5 bg-white/[0.92] border border-white/80 rounded-[18px] px-4 py-3 text-[#3d2b3a] shadow-[0_8px_24px_rgba(61,43,58,0.12)] backdrop-blur">
                <div class="text-[22px] lg:text-2xl font-bold text-[#e87bb0]" style="font-variant-numeric: tabular-nums;" id="liveClock">--:--:--</div>
                <div class="w-px h-8 bg-[#ead9e4]"></div>
                <div class="text-[11px] text-[#7e6a79] leading-[1.4]" id="liveDate">Loading...</div>
            </div>
        </div>

        @php
            $hasInToday = $todayAttendances->contains('type', 'IN');
            $hasOutToday = $todayAttendances->contains('type', 'OUT');
            $hasIzinToday = $todayAttendances->contains('type', 'IZIN');
            $hasSakitToday = $todayAttendances->contains('type', 'SAKIT');
            $hasLeaveToday = $hasIzinToday || $hasSakitToday;

            $inDisabled = $hasInToday || $hasLeaveToday;
            $outDisabled = $hasOutToday || !$hasInToday || $hasLeaveToday;
            $izinSakitDisabled = $hasLeaveToday || $hasInToday;
        @endphp

        {{-- Action Buttons --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <button type="button" onclick="startInOutAttendance('IN')" @if($inDisabled) disabled @endif class="p-4 lg:p-5 bg-white/[0.92] rounded-[18px] border-none font-poppins text-left transition-all duration-300 {{ $inDisabled ? 'opacity-55 cursor-not-allowed' : 'cursor-pointer hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]' }}">
                <div class="w-11 h-11 rounded-[14px] bg-[#8dd4b01f] text-[#2f7c57] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><path d="M10 17l5-5-5-5"></path><path d="M15 12H4"></path></svg>
                </div>
                <div class="text-sm font-semibold mb-0.5">IN</div>
                <div class="text-[10px] text-[#b8a0b0]">
                    {{ $hasLeaveToday ? 'Sudah izin/sakit hari ini' : ($hasInToday ? 'Sudah absen hari ini' : 'Masuk kerja') }}
                </div>
            </button>
            <button type="button" onclick="startInOutAttendance('OUT')" @if($outDisabled) disabled @endif class="p-4 lg:p-5 bg-white/[0.92] rounded-[18px] border-none font-poppins text-left transition-all duration-300 {{ $outDisabled ? 'opacity-55 cursor-not-allowed' : 'cursor-pointer hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]' }}">
                <div class="w-11 h-11 rounded-[14px] bg-[#7eb8e01f] text-[#4a8fb5] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h4"></path><path d="M14 7l-5 5 5 5"></path><path d="M9 12h11"></path></svg>
                </div>
                <div class="text-sm font-semibold mb-0.5">OUT</div>
                <div class="text-[10px] text-[#b8a0b0]">
                    {{ $hasLeaveToday ? 'Sudah izin/sakit hari ini' : ($hasOutToday ? 'Sudah absen hari ini' : (!$hasInToday ? 'Absen IN dulu' : 'Pulang kerja')) }}
                </div>
            </button>
            <button type="button" onclick="submitAttendance('IZIN')" @if($izinSakitDisabled) disabled @endif class="p-4 lg:p-5 bg-white/[0.92] rounded-[18px] border-none font-poppins text-left transition-all duration-300 {{ $izinSakitDisabled ? 'opacity-55 cursor-not-allowed' : 'cursor-pointer hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]' }}">
                <div class="w-11 h-11 rounded-[14px] bg-[#f0b86e1f] text-[#a86d2b] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h7l4 4v12H7z"></path><path d="M14 4v4h4"></path><path d="M9 12h6"></path><path d="M9 16h4"></path></svg>
                </div>
                <div class="text-sm font-semibold mb-0.5">Izin</div>
                <div class="text-[10px] text-[#b8a0b0]">{{ $hasLeaveToday ? 'Sudah izin/sakit hari ini' : ($hasInToday ? 'Sudah absen masuk hari ini' : 'Tidak masuk') }}</div>
            </button>
            <button type="button" onclick="submitAttendance('SAKIT')" @if($izinSakitDisabled) disabled @endif class="p-4 lg:p-5 bg-white/[0.92] rounded-[18px] border-none font-poppins text-left transition-all duration-300 {{ $izinSakitDisabled ? 'opacity-55 cursor-not-allowed' : 'cursor-pointer hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]' }}">
                <div class="w-11 h-11 rounded-[14px] bg-[#e870701f] text-[#b54e4e] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 8.6c0 5.2-8.8 11.4-8.8 11.4S3.2 13.8 3.2 8.6A4.6 4.6 0 0 1 7.8 4c1.7 0 3.1.8 4.2 2.2C13.1 4.8 14.5 4 16.2 4a4.6 4.6 0 0 1 4.6 4.6z"></path><path d="M12 8v4"></path><path d="M10 10h4"></path></svg>
                </div>
                <div class="text-sm font-semibold mb-0.5">Sakit</div>
                <div class="text-[10px] text-[#b8a0b0]">{{ $hasLeaveToday ? 'Sudah izin/sakit hari ini' : ($hasInToday ? 'Sudah absen masuk hari ini' : 'Hari ini sakit') }}</div>
            </button>
        </div>

        {{-- Camera / Scanner --}}
        <div class="hidden mb-6 bg-white/[0.92] rounded-[20px] p-5 relative" id="cameraSection">
            <div class="flex items-center justify-between mb-4">
                <div class="text-[15px] font-bold">Scanner QR</div>
                <button type="button" onclick="stopCamera()" class="w-8 h-8 rounded-lg bg-[#e870701a] text-[#e87070] flex items-center justify-center border-none cursor-pointer">✕</button>
            </div>
            <div class="relative w-full max-w-[320px] mx-auto aspect-square rounded-2xl overflow-hidden bg-[#3d2b3a]">
                <video id="cameraVideo" class="w-full h-full object-cover" playsinline autoplay muted></video>
                <div class="scan-line absolute left-[10%] right-[10%] h-0.5 bg-[#e87bb0] opacity-60"></div>
            </div>
            <div class="mt-4 text-center">
                <label for="qrUpload" class="inline-flex items-center gap-2 py-3 px-5 bg-[#b388d91f] rounded-2xl text-sm font-semibold text-[#b388d9] cursor-pointer transition-all duration-300 hover:bg-[#b388d933]">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Upload Gambar QR
                </label>
                <input type="file" id="qrUpload" accept="image/*" class="hidden">
            </div>
            <input type="hidden" id="scanType" value="">
        </div>

        {{-- History --}}
        <div class="text-[15px] font-bold mb-3.5">Riwayat Absensi</div>
        <div class="bg-white/[0.92] rounded-[16px] overflow-hidden shadow-[0_6px_20px_rgba(180,120,160,0.08)]">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left border-collapse">
                    <thead>
                        <tr class="bg-[#ffeef7] text-[#7e6a79]">
                            <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-[0.8px]">No</th>
                            <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-[0.8px]">Tanggal</th>
                            <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-[0.8px]">Tipe</th>
                            <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-[0.8px]">Waktu</th>
                            <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-[0.8px]">Catatan</th>
                            <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-[0.8px]">Jarak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $record)
                        <tr class="border-t border-[#f3e8ef] hover:bg-[#fff8fc] transition-colors duration-200">
                            <td class="py-3 px-4 text-[12px] font-semibold text-[#7e6a79]">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4 text-[12px] font-semibold text-[#3d2b3a]">{{ $record->date->translatedFormat('l, d M Y') }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center justify-center min-w-[44px] py-1 px-2 rounded-lg text-white font-bold text-[10px]
                                    {{ $record->type === 'IN' ? 'bg-[#8dd4b0]' : ($record->type === 'OUT' ? 'bg-[#7eb8e0]' : ($record->type === 'IZIN' ? 'bg-[#f0b86e]' : ($record->type === 'SAKIT' ? 'bg-[#e87070]' : 'bg-[#b388d9]'))) }}">
                                    {{ $record->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-[12px] font-medium text-[#3d2b3a]">{{ $record->time }}</td>
                            <td class="py-3 px-4 text-[12px] text-[#7e6a79]">{{ $record->note ?: '-' }}</td>
                            <td class="py-3 px-4 text-[12px] font-medium text-[#7e6a79]">{{ $record->distance_meters ? round($record->distance_meters).'m' : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-[#b8a0b0] text-[13px] font-medium">Belum ada riwayat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- Confirm Modal (IN/OUT) --}}
    <div id="attendanceConfirmOverlay" class="attendance-confirm-overlay fixed inset-0 z-[350] flex items-center justify-center p-5" onclick="closeAttendanceConfirmIfBackdrop(event)">
        <div class="attendance-confirm-card w-full max-w-sm rounded-[24px] border border-white/60 bg-white/[0.94] p-5 shadow-[0_20px_60px_rgba(61,43,58,0.22)]">
            <div class="pb-2 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#e87bb0] to-[#b388d9] text-white flex items-center justify-center mx-auto mb-3">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4"></path><path d="M12 16h.01"></path><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path></svg>
                </div>
                <div class="text-[18px] font-extrabold text-[#3d2b3a]" id="attendanceConfirmTitle">Konfirmasi Absensi</div>
            </div>

            <div class="text-center py-2 mb-4">
                <div class="text-[13px] text-[#7e6a79] leading-relaxed" id="attendanceConfirmMessage">Absen masuk hari ini?</div>
            </div>

            <div class="flex items-center justify-center gap-2.5">
                <button type="button" onclick="closeAttendanceConfirm()" class="flex-1 py-3 rounded-[14px] border border-[#ead9e4] bg-[#fff7fb] text-[13px] font-semibold text-[#7e6a79] transition-all duration-200 hover:bg-[#ffeef7]">Batal</button>
                <button type="button" onclick="confirmAttendanceAction()" class="flex-[1.3] py-3 rounded-[14px] border-none bg-gradient-to-r from-[#e87bb0] to-[#b388d9] text-[13px] font-bold text-white transition-all duration-300 hover:shadow-[0_8px_20px_rgba(232,123,176,0.3)]">Ya, lanjut</button>
            </div>
        </div>
    </div>

    {{-- Note Modal (IZIN/SAKIT) --}}
    <div id="noteModalOverlay" class="attendance-confirm-overlay fixed inset-0 z-[340] flex items-center justify-center p-5" onclick="closeNoteModalIfBackdrop(event)">
        <div class="attendance-confirm-card w-full max-w-sm rounded-[24px] border border-white/60 bg-white/[0.94] p-5 shadow-[0_20px_60px_rgba(61,43,58,0.22)]">
            <div class="pb-2 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#e87bb0] to-[#b388d9] text-white flex items-center justify-center mx-auto mb-3">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h7l4 4v12H7z"></path><path d="M14 4v4h4"></path><path d="M9 12h6"></path><path d="M9 16h4"></path></svg>
                </div>
                <div class="text-[18px] font-extrabold text-[#3d2b3a]" id="noteModalTitle">Alasan Izin</div>
            </div>

            <div class="text-center py-2 mb-4">
                <textarea id="noteInput" class="w-full py-3 px-4 border border-[#f1e4ec] rounded-[14px] bg-[#fff7fb] text-[#3d2b3a] text-sm font-poppins outline-none min-h-[110px] resize-y transition-all duration-300 focus:shadow-[0_0_0_3px_rgba(232,123,176,0.12)]" placeholder="Tuliskan alasan izin/sakit..."></textarea>
            </div>

            <div class="flex items-center justify-center gap-2.5">
                <button type="button" onclick="cancelNote()" class="flex-1 py-3 rounded-[14px] border border-[#ead9e4] bg-[#fff7fb] text-[13px] font-semibold text-[#7e6a79] transition-all duration-200 hover:bg-[#ffeef7]">Batal</button>
                <button type="button" onclick="confirmNote()" class="flex-[1.3] py-3 rounded-[14px] border-none bg-gradient-to-r from-[#e87bb0] to-[#b388d9] text-[13px] font-bold text-white transition-all duration-300 hover:shadow-[0_8px_20px_rgba(232,123,176,0.3)]">Kirim</button>
            </div>
        </div>
    </div>

    {{-- Notice Modal (Location Validation) --}}
    <div id="noticeModalOverlay" class="attendance-confirm-overlay fixed inset-0 z-[370] flex items-center justify-center p-5" onclick="closeNoticeModalIfBackdrop(event)">
        <div class="attendance-confirm-card w-full max-w-sm rounded-[24px] border border-white/60 bg-white/[0.94] p-5 shadow-[0_20px_60px_rgba(61,43,58,0.22)]">
            <div class="pb-2 text-center">
                <div id="noticeModalIconWrap" class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#e87070] to-[#b388d9] text-white flex items-center justify-center mx-auto mb-3">
                    <svg id="noticeModalIconError" viewBox="0 0 24 24" class="w-6 h-6 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4"></path><path d="M12 16h.01"></path><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path></svg>
                    <svg id="noticeModalIconSuccess" viewBox="0 0 24 24" class="hidden w-6 h-6 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                </div>
                <div class="text-[18px] font-extrabold text-[#3d2b3a]" id="noticeModalTitle">Validasi Lokasi Gagal</div>
            </div>

            <div class="text-center py-2 mb-4">
                <div class="text-[13px] text-[#7e6a79] leading-relaxed" id="noticeModalMessage">Akses lokasi ditolak.</div>
            </div>

            <div class="flex items-center justify-center gap-2.5">
                <button type="button" onclick="closeNoticeModal()" class="flex-1 py-3 rounded-[14px] border-none bg-gradient-to-r from-[#e87bb0] to-[#b388d9] text-[13px] font-bold text-white transition-all duration-300 hover:shadow-[0_8px_20px_rgba(232,123,176,0.3)]">OK</button>
            </div>
        </div>
    </div>

    {{-- Location Loading Modal (IN/OUT) --}}
    <div id="locationLoadingOverlay" class="attendance-confirm-overlay fixed inset-0 z-[360] flex items-center justify-center p-5">
        <div class="attendance-confirm-card w-full max-w-sm rounded-[24px] border border-white/60 bg-white/[0.94] p-5 text-center shadow-[0_20px_60px_rgba(61,43,58,0.22)]">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#e87bb0] to-[#b388d9] text-white flex items-center justify-center mx-auto mb-3">
                <div class="w-6 h-6 border-2 border-white/40 border-t-white rounded-full" style="animation: spin 0.8s linear infinite;"></div>
            </div>
            <div class="text-[18px] font-extrabold text-[#3d2b3a]">Validasi Lokasi</div>
            <div class="text-[13px] text-[#7e6a79] mt-1">Sedang mengambil lokasi GPS Anda...</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const employeeId = @json($employee->employee_id);
        const hasInToday = @json($hasInToday);
        const hasOutToday = @json($hasOutToday);
        const hasLeaveToday = @json($hasLeaveToday);
        let cameraStream = null;
        let scanInterval = null;
        let pendingAttendanceType = null;
        let pendingAttendancePayload = null;
        let isLocating = false;
        let noticeModalShouldReload = false;

        function openNoteModal(type) {
            pendingAttendanceType = type;

            const title = document.getElementById('noteModalTitle');
            if (title) title.textContent = type === 'IZIN' ? 'Alasan Izin' : 'Keterangan Sakit';

            const noteInput = document.getElementById('noteInput');
            if (noteInput) {
                noteInput.value = '';
                noteInput.placeholder = type === 'IZIN' ? 'Tuliskan alasan izin...' : 'Tuliskan keterangan sakit...';
            }

            const overlay = document.getElementById('noteModalOverlay');
            if (!overlay) return;
            requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function showNoticeModal(message, title = 'Validasi Lokasi Gagal', variant = 'error', shouldReload = false) {
            const titleEl = document.getElementById('noticeModalTitle');
            const messageEl = document.getElementById('noticeModalMessage');
            const iconWrap = document.getElementById('noticeModalIconWrap');
            const iconError = document.getElementById('noticeModalIconError');
            const iconSuccess = document.getElementById('noticeModalIconSuccess');

            if (titleEl) titleEl.textContent = title;
            if (messageEl) messageEl.textContent = message;
            noticeModalShouldReload = shouldReload;

            if (iconWrap && iconError && iconSuccess) {
                if (variant === 'success') {
                    iconWrap.className = 'w-12 h-12 rounded-2xl bg-gradient-to-br from-[#8dd4b0] to-[#57b88b] text-white flex items-center justify-center mx-auto mb-3';
                    iconError.classList.add('hidden');
                    iconSuccess.classList.remove('hidden');
                } else {
                    iconWrap.className = 'w-12 h-12 rounded-2xl bg-gradient-to-br from-[#e87070] to-[#b388d9] text-white flex items-center justify-center mx-auto mb-3';
                    iconSuccess.classList.add('hidden');
                    iconError.classList.remove('hidden');
                }
            }

            const overlay = document.getElementById('noticeModalOverlay');
            if (!overlay) return;
            requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeNoticeModal() {
            const overlay = document.getElementById('noticeModalOverlay');
            if (!overlay) return;
            overlay.classList.remove('show');
            if (noticeModalShouldReload) {
                noticeModalShouldReload = false;
                window.location.reload();
            }
        }

        function closeNoticeModalIfBackdrop(event) {
            if (event.target.id === 'noticeModalOverlay') {
                closeNoticeModal();
            }
        }

        function showLocationLoading() {
            const overlay = document.getElementById('locationLoadingOverlay');
            if (!overlay) return;
            requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function hideLocationLoading() {
            const overlay = document.getElementById('locationLoadingOverlay');
            if (!overlay) return;
            overlay.classList.remove('show');
        }

        function requestLocation() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('GPS tidak didukung di perangkat ini.'));
                    return;
                }

                navigator.geolocation.getCurrentPosition((pos) => {
                    resolve({
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                    });
                }, (err) => {
                    if (err.code === err.PERMISSION_DENIED) {
                        reject(new Error('Akses lokasi ditolak. Aktifkan izin lokasi untuk absen IN/OUT.'));
                    } else if (err.code === err.POSITION_UNAVAILABLE) {
                        reject(new Error('Lokasi tidak tersedia. Pastikan GPS aktif lalu coba lagi.'));
                    } else if (err.code === err.TIMEOUT) {
                        reject(new Error('Pengambilan lokasi timeout. Coba lagi.'));
                    } else {
                        reject(new Error('Gagal mengambil lokasi GPS.'));
                    }
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                });
            });
        }

        async function startInOutAttendance(type) {
            if (isLocating) return;

            isLocating = true;
            showLocationLoading();

            try {
                const coords = await requestLocation();
                hideLocationLoading();
                openAttendanceConfirm(type, null, coords);
            } catch (err) {
                hideLocationLoading();
                showNoticeModal(err.message || 'Lokasi tidak terdeteksi.');
            } finally {
                isLocating = false;
            }
        }

        function closeNoteModal() {
            const overlay = document.getElementById('noteModalOverlay');
            if (!overlay) return;
            overlay.classList.remove('show');
        }

        function closeNoteModalIfBackdrop(event) {
            if (event.target.id === 'noteModalOverlay') {
                closeNoteModal();
            }
        }

        function openAttendanceConfirm(type, note = null, coords = null) {
            if (hasLeaveToday && (type === 'IN' || type === 'OUT')) {
                showToast('Anda sudah izin/sakit hari ini.', 'error');
                return;
            }

            if (type === 'OUT' && !hasInToday) {
                showToast('Anda harus absen IN terlebih dahulu.', 'error');
                return;
            }

            if ((type === 'IN' && hasInToday) || (type === 'OUT' && hasOutToday)) {
                showToast(`Absen ${type} hari ini sudah tercatat.`, 'error');
                return;
            }

            pendingAttendancePayload = { type, note, coords };

            const title = document.getElementById('attendanceConfirmTitle');
            const message = document.getElementById('attendanceConfirmMessage');
            if (title) title.textContent = `Konfirmasi Absen`;

            if (message) {
                if (type === 'IN') {
                    message.textContent = 'Absen masuk hari ini?';
                } else if (type === 'OUT') {
                    message.textContent = 'Absen pulang hari ini?';
                } else if (type === 'IZIN') {
                    message.textContent = 'Kirim izin untuk hari ini?';
                } else {
                    message.textContent = 'Kirim keterangan sakit untuk hari ini?';
                }
            }

            const overlay = document.getElementById('attendanceConfirmOverlay');
            if (!overlay) return;
            requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeAttendanceConfirm() {
            const overlay = document.getElementById('attendanceConfirmOverlay');
            if (!overlay) return;
            overlay.classList.remove('show');
        }

        function closeAttendanceConfirmIfBackdrop(event) {
            if (event.target.id === 'attendanceConfirmOverlay') {
                closeAttendanceConfirm();
            }
        }

        function confirmAttendanceAction() {
            if (!pendingAttendancePayload) return;
            const payload = pendingAttendancePayload;
            pendingAttendancePayload = null;
            closeAttendanceConfirm();
            doAttendance(payload.type, payload.note ?? null, payload.coords ?? null);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeAttendanceConfirm();
                closeNoteModal();
                closeNoticeModal();
            }
        });

        function updateRunningClock() {
            const now = new Date();
            const clock = document.getElementById('liveClock');
            const date = document.getElementById('liveDate');
            if (!clock || !date) return;

            clock.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });

            date.textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });
        }

        updateRunningClock();
        setInterval(updateRunningClock, 1000);

        function showToast(msg, type = 'success') {
            if (type === 'success') {
                showNoticeModal(msg, 'Berhasil', 'success');
            } else {
                showNoticeModal(msg, 'Gagal', 'error');
            }
        }

        async function startCamera(type) {
            document.getElementById('scanType').value = type;
            document.getElementById('cameraSection').classList.remove('hidden');
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                const video = document.getElementById('cameraVideo');
                video.srcObject = stream;
                cameraStream = stream;
                startScanning(type);
            } catch (err) {
                showToast('Tidak dapat mengakses kamera.', 'error');
            }
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(t => t.stop());
                cameraStream = null;
            }
            if (scanInterval) { clearInterval(scanInterval); scanInterval = null; }
            document.getElementById('cameraSection').classList.add('hidden');
        }

        function startScanning(type) {
            const video = document.getElementById('cameraVideo');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            scanInterval = setInterval(() => {
                if (video.readyState !== video.HAVE_ENOUGH_DATA) return;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code && code.data) {
                    stopCamera();
                    processQRCode(code.data, type);
                }
            }, 500);
        }

        function processQRCode(qrData, type) {
            if (qrData !== employeeId) {
                showToast('QR Code tidak cocok dengan akun Anda.', 'error');
                return;
            }
            doAttendance(type, null);
        }

        function submitAttendance(type) {
            if (type === 'IZIN' || type === 'SAKIT') {
                if (hasLeaveToday) {
                    showToast('Anda sudah izin/sakit hari ini.', 'error');
                    return;
                }
                if (hasInToday) {
                    showToast('Anda sudah absen masuk hari ini, tidak bisa izin/sakit.', 'error');
                    return;
                }
            }
            openNoteModal(type);
        }

        function cancelNote() {
            pendingAttendanceType = null;
            const noteInput = document.getElementById('noteInput');
            if (noteInput) noteInput.value = '';
            closeNoteModal();
        }

        function confirmNote() {
            const type = pendingAttendanceType;
            if (!type) { showToast('Tipe absensi tidak valid.', 'error'); return; }
            const note = document.getElementById('noteInput').value;
            if (!note.trim()) { showToast('Catatan wajib diisi.', 'error'); return; }
            closeNoteModal();
            openAttendanceConfirm(type, note.trim());
        }

        async function doAttendance(type, note, coords = null) {
            try {
                const payload = { employee_id: employeeId, type: type };
                if (note) payload.note = note;
                const requiresGps = ['IN', 'OUT'].includes(type);

                if (coords) {
                    payload.latitude = coords.latitude;
                    payload.longitude = coords.longitude;
                    await sendAttendance(payload);
                    return;
                }

                if (!requiresGps) {
                    await sendAttendance(payload);
                    return;
                }

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(async (pos) => {
                        payload.latitude = pos.coords.latitude;
                        payload.longitude = pos.coords.longitude;
                        await sendAttendance(payload);
                    }, async () => {
                        showNoticeModal('Lokasi tidak terdeteksi. Aktifkan GPS terlebih dahulu.');
                    }, { timeout: 5000 });
                } else {
                    showNoticeModal('Browser tidak mendukung GPS di perangkat ini.');
                }
            } catch (err) { showNoticeModal('Gagal mengirim absensi. Silakan coba lagi.'); }
        }

        async function sendAttendance(payload) {
            const res = await fetch('{{ route("attendance.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (!res.ok) {
                if (data?.distance) {
                    showNoticeModal('Lokasi Anda terlalu jauh dari kantor.', 'Di Luar Radius');
                } else {
                    showNoticeModal(data.message || 'Gagal.', 'Absensi Gagal');
                }
                return;
            }
            showNoticeModal(data.message || 'Absensi berhasil!', 'Absensi Berhasil', 'success', true);
        }

        // QR Upload handler
        document.getElementById('qrUpload')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    if (code && code.data) {
                        stopCamera();
                        const type = document.getElementById('scanType').value;
                        processQRCode(code.data, type);
                    } else {
                        showToast('QR Code tidak ditemukan.', 'error');
                    }
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
