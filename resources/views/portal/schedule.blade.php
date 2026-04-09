<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal Libur - VtuberGraphic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'] } } } }
    </script>
</head>
<body class="font-poppins bg-[#fef7ff] text-[#3d2b3a] min-h-screen pb-10">
    {{-- Background blobs --}}
    <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
        <div class="absolute -top-[60px] -right-10 w-[260px] h-[260px] bg-[#e87bb0] rounded-full blur-[100px] opacity-[0.08]"></div>
        <div class="absolute bottom-[100px] -left-[60px] w-[300px] h-[300px] bg-[#b388d9] rounded-full blur-[120px] opacity-[0.06]"></div>
    </div>

    <div class="max-w-[540px] lg:max-w-4xl xl:max-w-5xl mx-auto px-5 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center gap-3.5 py-5 pb-6">
            <a href="{{ route('portal.index') }}" class="w-10 h-10 rounded-xl bg-white/[0.92] border-none flex items-center justify-center no-underline text-[#3d2b3a] transition-all duration-200 hover:bg-[#ffe6f040] hover:-translate-y-0.5">
                <svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <div class="text-[11px] text-[#b8a0b0] uppercase tracking-[1.5px]">Departemen {{ $employee->department ?? '-' }}</div>
                <div class="text-xl font-extrabold">Jadwal Libur</div>
            </div>
        </div>

        {{-- Schedule Info --}}
        <div class="py-3 px-4 bg-[#b388d914] rounded-xl mb-4 text-xs text-[#8a6b80]">
            Jadwal libur kamu: <strong class="text-[#3d2b3a]">{{ $employee->off_day_names ?: '-' }}</strong> setiap minggu
        </div>

        {{-- Desktop: calendar and list side by side --}}
        <div class="grid lg:grid-cols-2 gap-5">
            <div>
                {{-- Month Navigation --}}
                <div class="flex items-center justify-center gap-4 mb-4 py-2.5 bg-white/[0.92] rounded-[14px]">
                    <button onclick="changeMonth(-1)" class="w-[34px] h-[34px] rounded-[10px] bg-[#ffe6f040] border-none flex items-center justify-center cursor-pointer text-[#8a6b80] transition-all duration-200 hover:bg-[#e87bb01f] hover:text-[#e87bb0]">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"></path></svg>
                    </button>
                    <div class="text-[15px] font-bold min-w-[150px] text-center" id="monthLabel"></div>
                    <button onclick="changeMonth(1)" class="w-[34px] h-[34px] rounded-[10px] bg-[#ffe6f040] border-none flex items-center justify-center cursor-pointer text-[#8a6b80] transition-all duration-200 hover:bg-[#e87bb01f] hover:text-[#e87bb0]">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"></path></svg>
                    </button>
                </div>

                {{-- Calendar --}}
                <div class="bg-white/[0.92] rounded-[18px] p-3.5 mb-4">
                    <div class="grid grid-cols-7 gap-0.5 mb-1.5">
                        <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Min</span>
                        <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Sen</span>
                        <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Sel</span>
                        <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Rab</span>
                        <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Kam</span>
                        <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Jum</span>
                        <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Sab</span>
                    </div>
                    <div class="grid grid-cols-7 gap-0.5" id="calendarBody"></div>
                </div>

                {{-- Legend --}}
                <div class="flex flex-wrap gap-3 mb-5 px-1">
                    <div class="flex items-center gap-1.5 text-[11px] text-[#8a6b80]">
                        <div class="w-3 h-3 rounded bg-[#e8707040]"></div> Libur Saya
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-[#8a6b80]">
                        <div class="w-3 h-3 rounded bg-[#f0b86e26]"></div> Libur Lain
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-[#8a6b80]">
                        <div class="w-3 h-3 rounded bg-gradient-to-br from-[#e87bb0] to-[#b388d9]"></div> Hari Ini
                    </div>
                </div>
            </div>

            {{-- Employee List --}}
            <div>
                <div class="text-[15px] font-bold mb-3.5">Tim {{ $employee->department ?? '' }}</div>
                <div id="employeeList" class="space-y-2"></div>
            </div>
        </div>
    </div>

    <script>
        const offDaysData = @json($offDaysByEmployee);
        const employeeColors = @json($employeeColors);
        const currentEmployeeId = @json($employee->employee_id);
        const today = new Date();

        let viewMonth = {{ $month }};
        let viewYear = {{ $year }};

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        function changeMonth(delta) {
            viewMonth += delta;
            if (viewMonth > 12) { viewMonth = 1; viewYear++; }
            if (viewMonth < 1) { viewMonth = 12; viewYear--; }
            window.location.href = `{{ route('portal.schedule') }}?month=${viewMonth}&year=${viewYear}`;
        }

        function renderCalendar() {
            document.getElementById('monthLabel').textContent = `${monthNames[viewMonth - 1]} ${viewYear}`;

            const firstDay = new Date(viewYear, viewMonth - 1, 1).getDay();
            const daysInMonth = new Date(viewYear, viewMonth, 0).getDate();
            const todayDate = today.getDate();
            const todayMonth = today.getMonth() + 1;
            const todayYear = today.getFullYear();

            const myDates = offDaysData[currentEmployeeId]?.dates || [];

            let html = '';

            for (let i = 0; i < firstDay; i++) {
                html += '<div class="aspect-square"></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${viewYear}-${String(viewMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const isToday = day === todayDate && viewMonth === todayMonth && viewYear === todayYear;
                const isMyOff = myDates.includes(dateStr);

                let othersOff = [];
                for (const empId in offDaysData) {
                    if (empId === currentEmployeeId) continue;
                    if ((offDaysData[empId].dates || []).includes(dateStr)) {
                        othersOff.push(offDaysData[empId].name);
                    }
                }

                let bgClass = 'hover:bg-[#ffe6f040]';
                if (isToday) bgClass = 'bg-gradient-to-br from-[#e87bb0] to-[#b388d9] text-white font-bold';
                else if (isMyOff && othersOff.length > 0) bgClass = 'bg-[#e8707040] text-[#b54e4e] font-bold';
                else if (isMyOff) bgClass = 'bg-[#e870702e] text-[#b54e4e] font-bold';
                else if (othersOff.length > 0) bgClass = 'bg-[#f0b86e1f] text-[#a86d2b] font-semibold';

                let tooltipParts = [];
                if (isMyOff) tooltipParts.push('Libur Anda');
                if (othersOff.length > 0) tooltipParts.push(othersOff.join(', '));
                const tooltip = tooltipParts.length > 0
                    ? `<div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 bg-[#3d2b3a] text-white py-1.5 px-2.5 rounded-lg text-[10px] whitespace-nowrap z-10 pointer-events-none">${tooltipParts.join(' · ')}</div>`
                    : '';

                html += `<div class="group relative aspect-square flex flex-col items-center justify-center rounded-[10px] text-xs font-medium cursor-pointer transition-all duration-200 ${bgClass}">${day}${tooltip}</div>`;
            }

            document.getElementById('calendarBody').innerHTML = html;
        }

        function renderEmployeeList() {
            let html = '';
            const avatarColors = ['#e87bb0', '#b388d9', '#7eb8e0', '#8dd4b0', '#f0b86e', '#e87070', '#6dcfcf', '#a78bfa'];

            let idx = 0;
            for (const empId in offDaysData) {
                const emp = offDaysData[empId];
                const dates = (emp.dates || []).filter(d => {
                    const [y, m] = d.split('-');
                    return parseInt(y) === viewYear && parseInt(m) === viewMonth;
                });
                const color = avatarColors[idx % avatarColors.length];
                const initials = emp.name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
                const isMe = empId === currentEmployeeId;

                html += `
                    <div class="flex items-center gap-3.5 p-3.5 rounded-[14px] transition-all duration-200 hover:shadow-[0_6px_20px_rgba(180,120,160,0.08)] ${isMe ? 'bg-[#e870700f]' : 'bg-white/[0.92]'}">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold text-white shrink-0" style="background:${color};">${initials}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-semibold">${emp.name}${isMe ? ' <span class="text-[10px] text-[#e87070] font-semibold">(Anda)</span>' : ''}</div>
                            <div class="text-[11px] text-[#b8a0b0]">Libur: ${emp.off_day_names || '-'}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-extrabold text-[#e87bb0]">${dates.length}</div>
                            <div class="text-[9px] text-[#b8a0b0] uppercase">Hari</div>
                        </div>
                    </div>
                `;
                idx++;
            }

            if (!html) {
                html = '<div class="text-center py-8 text-[#b8a0b0]"><p class="text-[13px]">Tidak ada data</p></div>';
            }

            document.getElementById('employeeList').innerHTML = html;
        }

        renderCalendar();
        renderEmployeeList();
    </script>
</body>
</html>
