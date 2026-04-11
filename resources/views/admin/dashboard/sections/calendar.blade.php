<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false },
        theme: { extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'] } } }
    }
</script>

<div class="page-section" id="page-calendar">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-2xl font-bold mb-1">Kalender Kehadiran</h1>
        </div>
        <div style="display:flex;items-center;">
            <div class="user-portal-clock">
                <div class="user-portal-clock-time global-clock-time">--:--:--</div>
                <div class="user-portal-clock-divider"></div>
                <div class="user-portal-clock-date global-clock-date">Memuat...</div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-[minmax(0,500px)_1fr] gap-6 xl:gap-8 items-start">
        <div class="w-full">
            {{-- Month Navigation --}}
            <div class="flex items-center justify-center gap-4 mb-4 py-2.5 bg-white rounded-[14px] shadow-[0_2px_10px_rgba(219,160,190,0.06)]">
                <button onclick="changeAdminMonth(-1)" class="w-[34px] h-[34px] rounded-[10px] bg-[#ffe6f040] border-none flex items-center justify-center cursor-pointer text-[#8a6b80] transition-all duration-200 hover:bg-[#e87bb01f] hover:text-[#e87bb0]">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"></path></svg>
                </button>
                <div class="text-[15px] font-bold min-w-[150px] text-center" id="adminMonthLabel"></div>
                <button onclick="changeAdminMonth(1)" class="w-[34px] h-[34px] rounded-[10px] bg-[#ffe6f040] border-none flex items-center justify-center cursor-pointer text-[#8a6b80] transition-all duration-200 hover:bg-[#e87bb01f] hover:text-[#e87bb0]">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"></path></svg>
                </button>
            </div>

            {{-- Calendar --}}
            <div class="bg-white rounded-[18px] p-3.5 shadow-[0_2px_10px_rgba(219,160,190,0.06)] mb-4">
                <div class="grid grid-cols-7 gap-0.5 mb-1.5">
                    <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Min</span>
                    <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Sen</span>
                    <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Sel</span>
                    <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Rab</span>
                    <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Kam</span>
                    <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Jum</span>
                    <span class="text-center text-[10px] font-bold text-[#b8a0b0] uppercase py-1">Sab</span>
                </div>
                <div class="grid grid-cols-7 gap-0.5" id="adminCalendarBody"></div>
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap gap-3 px-1 relative z-0">
                <div class="flex items-center gap-1.5 text-[11px] text-[#8a6b80]">
                    <div class="w-3 h-3 rounded bg-gradient-to-br from-[#e87bb0] to-[#b388d9]"></div> Hari Ini
                </div>
                <div class="flex items-center gap-1.5 text-[11px] text-[#8a6b80]">
                    <div class="w-3 h-3 rounded bg-[#f0b86e26]"></div> Ada yang Libur
                </div>
                <div class="flex items-center gap-1.5 text-[11px] text-[#8a6b80]">
                    <div class="w-3 h-3 rounded bg-white border border-[#e87bb0] shadow-[0_0_0_2px_rgba(232,123,176,0.2)]"></div> Terpilih
                </div>
            </div>
        </div>

        {{-- Employee List based on Selection --}}
        <div class="w-full">
            <div class="bg-white rounded-[18px] p-5 lg:p-6 shadow-[0_2px_10px_rgba(219,160,190,0.06)] h-full min-h-[400px] flex flex-col">
                <div class="mb-5">
                    <div class="text-[17px] font-extrabold text-[#3d2b3a]" id="selectedDateLabel">Hari Ini</div>
                </div>

                {{-- Tabs --}}
                <div class="flex p-1.5 bg-[#fef7ff] border-2 border-[#ffe6f0] rounded-[16px] mb-6">
                    <button id="tabWorking" onclick="switchCalendarTab('working')" class="flex-1 border-none outline-none py-2 text-[13px] font-bold rounded-[10px] bg-white text-[#e87bb0] shadow-[0_4px_12px_rgba(232,123,176,0.18)] transition-all">Masuk</button>
                    <button id="tabOff" onclick="switchCalendarTab('off')" class="flex-1 border-none outline-none py-2 text-[13px] font-bold rounded-[10px] text-[#8a6b80] hover:text-[#e87bb0] transition-all bg-transparent">Libur</button>
                </div>

                {{-- List Container (Scrollable) --}}
                <div class="flex-1 overflow-y-auto pr-1 scrollbar-thin" id="adminEmployeeList">
                    {{-- Dynamically populated --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const adminOffDaysData = @json($offDaysByEmployee ?? []);
    
    // Convert to array and group by department for easier iteration later
    const employeeArray = Object.values(adminOffDaysData);
    
    let adminViewMonth = {{ $month ?? 'new Date().getMonth() + 1' }};
    let adminViewYear = {{ $year ?? 'new Date().getFullYear()' }};
    let selectedDateStr = null;
    let currentTab = 'working'; // 'working' or 'off'
    
    const adminMonthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'   
    ];

    function changeAdminMonth(delta) {
        adminViewMonth += delta;
        if (adminViewMonth > 12) { adminViewMonth = 1; adminViewYear++; }
        if (adminViewMonth < 1) { adminViewMonth = 12; adminViewYear--; }
        window.location.href = `{{ route('admin.calendar') }}?month=${adminViewMonth}&year=${adminViewYear}`;
    }

    // Initialize calendar and variables
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date();
        selectedDateStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        renderAdminCalendar();
        renderAdminEmployeeList();
    });

    function renderAdminCalendar() {
        document.getElementById('adminMonthLabel').textContent = `${adminMonthNames[adminViewMonth - 1]} ${adminViewYear}`;

        const firstDay = new Date(adminViewYear, adminViewMonth - 1, 1).getDay();     
        const daysInMonth = new Date(adminViewYear, adminViewMonth, 0).getDate();     
        const today = new Date();
        const todayDate = today.getDate();
        const todayMonth = today.getMonth() + 1;
        const todayYear = today.getFullYear();

        let html = '';

        for (let i = 0; i < firstDay; i++) {
            html += '<div class="aspect-square"></div>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${adminViewYear}-${String(adminViewMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = day === todayDate && adminViewMonth === todayMonth && adminViewYear === todayYear;
            const isSelected = dateStr === selectedDateStr;

            // Check how many people are off
            let offCount = 0;
            for (const emp of employeeArray) {
                if ((emp.dates || []).includes(dateStr)) {   
                    offCount++;
                }
            }

            let bgClass = 'hover:bg-[#ffe6f040]';
            if (isSelected) {
                bgClass = 'bg-white shadow-[0_0_0_2px_rgba(232,123,176,0.3)] z-10 text-[#e87bb0] font-bold';
            } else if (isToday) {
                bgClass = 'bg-gradient-to-br from-[#e87bb0] to-[#b388d9] text-white font-bold';
            } else if (offCount > 0) {
                bgClass = 'bg-[#f0b86e1f] text-[#a86d2b] font-semibold';
            }

            let tooltip = offCount > 0 ? `<div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 bg-[#3d2b3a] text-white py-1.5 px-2.5 rounded-lg text-[10px] whitespace-nowrap z-20 pointer-events-none">${offCount} org libur</div>` : '';

            html += `
                <div onclick="selectDate('${dateStr}')" class="group relative aspect-square flex flex-col items-center justify-center rounded-[10px] text-xs font-medium cursor-pointer transition-all duration-200 ${bgClass}">
                    ${day}
                    ${tooltip}
                </div>
            `;
        }

        document.getElementById('adminCalendarBody').innerHTML = html;
    }

    function selectDate(dateStr) {
        selectedDateStr = dateStr;
        renderAdminCalendar(); // Re-render to highlight selection
        renderAdminEmployeeList();
    }

    function switchCalendarTab(tab) {
        currentTab = tab;
        
        const btnWorking = document.getElementById('tabWorking');
        const btnOff = document.getElementById('tabOff');
        
        if (tab === 'working') {
            btnWorking.className = 'flex-1 border-none outline-none py-2 text-[13px] font-bold rounded-[10px] bg-white text-[#e87bb0] shadow-[0_4px_12px_rgba(232,123,176,0.18)] transition-all';
            btnOff.className = 'flex-1 border-none outline-none py-2 text-[13px] font-bold rounded-[10px] text-[#8a6b80] hover:text-[#e87bb0] transition-all bg-transparent';
        } else {
            btnOff.className = 'flex-1 border-none outline-none py-2 text-[13px] font-bold rounded-[10px] bg-white text-[#e87bb0] shadow-[0_4px_12px_rgba(232,123,176,0.18)] transition-all';
            btnWorking.className = 'flex-1 border-none outline-none py-2 text-[13px] font-bold rounded-[10px] text-[#8a6b80] hover:text-[#e87bb0] transition-all bg-transparent';
        }
        
        renderAdminEmployeeList();
    }

    function renderAdminEmployeeList() {
        if (!selectedDateStr) return;
        
        // Format the date label
        const dObj = new Date(selectedDateStr);
        const formatOptions = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        document.getElementById('selectedDateLabel').textContent = dObj.toLocaleDateString('id-ID', formatOptions);

        let filteredEmployees = [];
        
        employeeArray.forEach(emp => {
            const isOff = (emp.dates || []).includes(selectedDateStr);
            if (currentTab === 'working' && !isOff) {
                filteredEmployees.push(emp);
            } else if (currentTab === 'off' && isOff) {
                filteredEmployees.push(emp);
            }
        });

        // Group by department
        const grouped = {};
        filteredEmployees.forEach(emp => {
            const dept = emp.department || 'Lainnya';
            if (!grouped[dept]) grouped[dept] = [];
            grouped[dept].push(emp);
        });

        // Generate HTML
        let html = '';
        const deptNames = Object.keys(grouped).sort();
        
        if (deptNames.length === 0) {
            html = `
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="w-16 h-16 rounded-full bg-[#FFE6F0] text-[#e87bb0] flex items-center justify-center mb-3">
                        <svg viewBox="0 0 24 24" class="w-8 h-8 stroke-current fill-none stroke-2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="text-[14px] font-bold text-[#3d2b3a]">Tidak ada data</div>
                    <div class="text-[12px] text-[#8a6b80] mt-1">Tidak ada karyawan yang ${currentTab === 'working' ? 'masuk' : 'libur'} di hari ini</div>
                </div>
            `;
            document.getElementById('adminEmployeeList').innerHTML = html;
            return;
        }

        const avatarColors = ['#e87bb0', '#b388d9', '#7eb8e0', '#8dd4b0', '#f0b86e', '#e87070', '#6dcfcf', '#a78bfa'];
        let colorIdx = 0;
        
        html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2">';

        deptNames.forEach(dept => {
            html += `
                <div class="mb-5 last:mb-2">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="h-px bg-gradient-to-r from-[#e87bb040] to-transparent flex-1"></div>
                        <div class="text-[11px] font-bold text-[#e87bb0] uppercase tracking-[1px] px-2">${dept} <span class="bg-[#FFE6F0] text-[#e87bb0] ml-1 py-0.5 px-1.5 rounded-full text-[9px]">${grouped[dept].length}</span></div>
                        <div class="h-px bg-gradient-to-l from-[#e87bb040] to-transparent flex-1"></div>
                    </div>
                    <div class="space-y-3">
            `;
            
            // Sort by name inside department
            grouped[dept].sort((a, b) => a.name.localeCompare(b.name));
            
            grouped[dept].forEach(emp => {
                const color = avatarColors[colorIdx % avatarColors.length];
                const initials = emp.name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
                colorIdx++;
                
                html += `
                    <div class="flex items-center gap-3.5 p-3 rounded-[14px] bg-[#fdfafc] transition-all hover:bg-[#ffe6f020]">
                        <div class="w-10 h-10 rounded-[12px] flex items-center justify-center text-[13px] font-bold text-white shrink-0" style="background:${color};">${initials}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[14px] font-bold text-[#3d2b3a] truncate">${emp.name}</div>
                            <div class="text-[11px] text-[#b8a0b0] truncate mt-0.5">${emp.position || '-'}</div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        html += '</div>';

        document.getElementById('adminEmployeeList').innerHTML = html;
    }
</script>
