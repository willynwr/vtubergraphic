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
        <div class="bg-gradient-to-br from-[#e87bb0] to-[#b388d9] rounded-[20px] p-5 text-white mb-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[120px] h-[120px] bg-white/10 rounded-full -translate-y-1/2 translate-x-1/4"></div>
            <div class="text-xs font-semibold opacity-80 mb-2 uppercase tracking-[1px]">Status Hari Ini</div>
            <div class="text-2xl font-extrabold mb-1" id="statusText">{{ $todayStatus['text'] ?? 'Belum Absen' }}</div>
            <div class="text-sm opacity-80" id="statusTime">{{ $todayStatus['time'] ?? '-' }}</div>
        </div>

        {{-- Action Buttons --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <button type="button" onclick="startCamera('IN')" class="p-4 lg:p-5 bg-white/[0.92] rounded-[18px] border-none cursor-pointer font-poppins text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]">
                <div class="w-11 h-11 rounded-[14px] bg-[#8dd4b01f] text-[#2f7c57] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v4"></path><path d="M9 21H5a2 2 0 0 1-2-2v-4"></path><path d="M21 15v4a2 2 0 0 1-2 2h-4"></path><path d="M3 9V5a2 2 0 0 1 2-2h4"></path></svg>
                </div>
                <div class="text-sm font-semibold mb-0.5">Scan IN</div>
                <div class="text-[10px] text-[#b8a0b0]">Masuk kerja</div>
            </button>
            <button type="button" onclick="startCamera('OUT')" class="p-4 lg:p-5 bg-white/[0.92] rounded-[18px] border-none cursor-pointer font-poppins text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]">
                <div class="w-11 h-11 rounded-[14px] bg-[#7eb8e01f] text-[#4a8fb5] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v4"></path><path d="M9 21H5a2 2 0 0 1-2-2v-4"></path><path d="M21 15v4a2 2 0 0 1-2 2h-4"></path><path d="M3 9V5a2 2 0 0 1 2-2h4"></path></svg>
                </div>
                <div class="text-sm font-semibold mb-0.5">Scan OUT</div>
                <div class="text-[10px] text-[#b8a0b0]">Pulang kerja</div>
            </button>
            <button type="button" onclick="submitAttendance('IZIN')" class="p-4 lg:p-5 bg-white/[0.92] rounded-[18px] border-none cursor-pointer font-poppins text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]">
                <div class="w-11 h-11 rounded-[14px] bg-[#f0b86e1f] text-[#a86d2b] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h7l4 4v12H7z"></path><path d="M14 4v4h4"></path><path d="M9 12h6"></path><path d="M9 16h4"></path></svg>
                </div>
                <div class="text-sm font-semibold mb-0.5">Izin</div>
                <div class="text-[10px] text-[#b8a0b0]">Tidak masuk</div>
            </button>
            <button type="button" onclick="submitAttendance('SAKIT')" class="p-4 lg:p-5 bg-white/[0.92] rounded-[18px] border-none cursor-pointer font-poppins text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]">
                <div class="w-11 h-11 rounded-[14px] bg-[#e870701f] text-[#b54e4e] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 8.6c0 5.2-8.8 11.4-8.8 11.4S3.2 13.8 3.2 8.6A4.6 4.6 0 0 1 7.8 4c1.7 0 3.1.8 4.2 2.2C13.1 4.8 14.5 4 16.2 4a4.6 4.6 0 0 1 4.6 4.6z"></path><path d="M12 8v4"></path><path d="M10 10h4"></path></svg>
                </div>
                <div class="text-sm font-semibold mb-0.5">Sakit</div>
                <div class="text-[10px] text-[#b8a0b0]">Hari ini sakit</div>
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

        {{-- Note Input (for IZIN/SAKIT) --}}
        <div class="hidden mb-6 bg-white/[0.92] rounded-[20px] p-5" id="noteSection">
            <div class="text-[15px] font-bold mb-3" id="noteTitle">Catatan</div>
            <textarea id="noteInput" class="w-full py-3 px-4 border-none rounded-[14px] bg-[#ffe6f040] text-[#3d2b3a] text-sm font-poppins outline-none min-h-[100px] resize-y transition-all duration-300 focus:shadow-[0_0_0_3px_rgba(232,123,176,0.12)]" placeholder="Tuliskan alasan izin/sakit..."></textarea>
            <div class="flex gap-2.5 mt-4">
                <button type="button" onclick="cancelNote()" class="flex-1 py-3.5 bg-[#ffe6f040] rounded-[14px] border-none text-[13px] font-semibold text-[#3d2b3a] font-poppins cursor-pointer">Batal</button>
                <button type="button" onclick="confirmNote()" class="flex-[2] py-3.5 bg-gradient-to-r from-[#e87bb0] to-[#b388d9] rounded-[14px] border-none text-[13px] font-bold text-white font-poppins cursor-pointer transition-all duration-300 hover:shadow-[0_6px_20px_rgba(232,123,176,0.3)]">Kirim</button>
            </div>
            <input type="hidden" id="noteType" value="">
        </div>

        {{-- History --}}
        <div class="text-[15px] font-bold mb-3.5">Riwayat Absensi</div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5">
            @forelse($history as $record)
            <div class="flex items-center gap-3.5 p-3.5 bg-white/[0.92] rounded-[16px] transition-all duration-200 hover:shadow-[0_6px_20px_rgba(180,120,160,0.08)]">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white font-bold text-[11px]
                    {{ $record->type === 'IN' ? 'bg-[#8dd4b0]' : ($record->type === 'OUT' ? 'bg-[#7eb8e0]' : ($record->type === 'IZIN' ? 'bg-[#f0b86e]' : ($record->type === 'SAKIT' ? 'bg-[#e87070]' : 'bg-[#b388d9]'))) }}">
                    {{ $record->getTypeLabel() }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-semibold">{{ $record->date->translatedFormat('l, d M Y') }}</div>
                    <div class="text-[11px] text-[#b8a0b0]">{{ $record->time }} {{ $record->note ? '· '.$record->note : '' }}</div>
                </div>
                @if($record->distance_meters)
                <div class="text-[11px] text-[#b8a0b0] font-medium shrink-0">{{ round($record->distance_meters) }}m</div>
                @endif
            </div>
            @empty
            <div class="text-center py-8 text-[#b8a0b0] col-span-full">
                <p class="text-[13px] font-medium">Belum ada riwayat</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast" class="fixed top-5 left-1/2 -translate-x-1/2 z-[300] hidden">
        <div class="py-3.5 px-6 rounded-[14px] text-[13px] font-semibold max-w-[90vw]" id="toastInner"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const employeeId = @json($employee->employee_id);
        let cameraStream = null;
        let scanInterval = null;

        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            const inner = document.getElementById('toastInner');
            inner.textContent = msg;
            inner.className = `py-3.5 px-6 rounded-[14px] text-[13px] font-semibold max-w-[90vw] ${type === 'success' ? 'bg-[#8dd4b0f2] text-[#1a4d35]' : 'bg-[#e87070f2] text-white'}`;
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 4000);
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
            document.getElementById('noteType').value = type;
            document.getElementById('noteTitle').textContent = type === 'IZIN' ? 'Alasan Izin' : 'Keterangan Sakit';
            document.getElementById('noteSection').classList.remove('hidden');
        }

        function cancelNote() {
            document.getElementById('noteSection').classList.add('hidden');
            document.getElementById('noteInput').value = '';
        }

        function confirmNote() {
            const type = document.getElementById('noteType').value;
            const note = document.getElementById('noteInput').value;
            if (!note.trim()) { showToast('Catatan wajib diisi.', 'error'); return; }
            document.getElementById('noteSection').classList.add('hidden');
            doAttendance(type, note);
        }

        async function doAttendance(type, note) {
            try {
                const payload = { employee_id: employeeId, type: type };
                if (note) payload.note = note;

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(async (pos) => {
                        payload.latitude = pos.coords.latitude;
                        payload.longitude = pos.coords.longitude;
                        await sendAttendance(payload);
                    }, async () => {
                        await sendAttendance(payload);
                    }, { timeout: 5000 });
                } else {
                    await sendAttendance(payload);
                }
            } catch (err) { showToast('Gagal mengirim absensi.', 'error'); }
        }

        async function sendAttendance(payload) {
            const res = await fetch('{{ route("attendance.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (!res.ok) { showToast(data.message || 'Gagal.', 'error'); return; }
            showToast(data.message || 'Absensi berhasil!', 'success');
            setTimeout(() => window.location.reload(), 1500);
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
