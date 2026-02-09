@extends('layout')
@section('content')
    {{-- Page Header --}}
    <div class="mb-8">
        <a href="{{ route('patient.appointment', ['hn' => $patient['hn']]) }}"
            class="inline-flex items-center text-slate-400 hover:text-[#4db1ab] font-bold text-xs uppercase tracking-widest transition-colors mb-4">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            @if (session('langSelect') == 'TH')
                กลับไปที่นัดหมายของฉัน
            @else
                Back to My Appointments
            @endif
        </a>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">
            @if (session('langSelect') == 'TH')
                ทำนัดหมาย <span class="text-[#4db1ab]">{{ $patient['appointment_name']['name'] }}</span>
            @else
                New <span class="text-[#4db1ab]">{{ $patient['appointment_name']['name_eng'] }}</span>
            @endif
        </h2>
        <p class="text-sm text-slate-400 mt-1 font-medium">
            @if (session('langSelect') == 'TH')
                กรุณาเลือกวันที่และกรอกข้อมูลการติดต่อด้านล่าง
            @else
                Please select a date and provide your contact info below
            @endif
        </p>
    </div>

    {{-- Error Message Display --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-2 border-red-100 rounded-2xl p-4 flex items-start space-x-3 animate-shake">
            <i class="fa-solid fa-circle-exclamation text-red-500 mt-1"></i>
            <div>
                <p class="text-sm font-bold text-red-700">
                    @if (session('langSelect') == 'TH')
                        เกิดข้อผิดพลาด
                    @else
                        Validation Error
                    @endif
                </p>
                <p class="text-xs text-red-600 font-medium">
                    @if (session('langSelect') == 'TH')
                        ไม่สามารถทำนัดในวันที่เลือกได้ กรุณาลองใหม่อีกครั้ง
                    @else
                        Unable to make an appointment on the selected date. Please try again.
                    @endif
                </p>
            </div>
        </div>
    @endif

    {{-- Appointment Form Card --}}
    <div class="bg-white rounded-3xl border-2 border-slate-100 p-6 shadow-xl shadow-slate-200/50">
        <input type="hidden" name="hn" value="{{ $patient['hn'] }}">

        {{-- Date Selection (Card Based) --}}
        <div class="mb-6">
            <label
                class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 px-1 flex justify-between items-center">
                <span>
                    @if (session('langSelect') == 'TH')
                        วันที่ต้องการนัด
                    @else
                        Appointment Date
                    @endif
                </span>
                <span id="page-indicator" class="text-[10px] bg-slate-100 px-2 py-0.5 rounded-full text-slate-500">Page
                    1</span>
            </label>

            <div id="date-cards-container" class="grid grid-cols-1 gap-2">
                {{-- Cards will be injected here via JS --}}
            </div>

            {{-- Pagination Controls --}}
            <div id="pagination-controls" class="flex justify-between mt-4 hidden">
                <button onclick="changePage(-1)" id="prev-btn"
                    class="text-xs font-bold text-[#4db1ab] flex items-center p-2 opacity-50 cursor-not-allowed transition-all">
                    <i class="fa-solid fa-chevron-left mr-1"></i> Prev
                </button>
                <button onclick="changePage(1)" id="next-btn"
                    class="text-xs font-bold text-[#4db1ab] flex items-center p-2 transition-all">
                    Next <i class="fa-solid fa-chevron-right ml-1"></i>
                </button>
            </div>

            <input type="hidden" id="dateSelect" name="date">
        </div>

        {{-- Phone Number --}}
        <div class="mb-6">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">
                @if (session('langSelect') == 'TH')
                    เบอร์โทรศัพท์ติดต่อ
                @else
                    Contact Number
                @endif
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <input
                    class="w-full rounded-2xl border-2 border-slate-50 bg-slate-50 p-4 pl-12 text-slate-700 font-bold focus:border-[#4db1ab] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#4db1ab]/10 transition-all placeholder:text-slate-300"
                    id="phone" type="text" pattern="\d*" name="phone" placeholder="08x-xxx-xxxx">
            </div>
        </div>

        {{-- Remark --}}
        <div class="mb-8">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Remark
                (Optional)</label>
            <div class="relative">
                <div class="absolute top-4 left-0 pl-4 flex items-start pointer-events-none text-slate-400">
                    <i class="fa-solid fa-comment-dots"></i>
                </div>
                <textarea rows="3"
                    class="w-full rounded-2xl border-2 border-slate-50 bg-slate-50 p-4 pl-12 text-slate-700 font-medium focus:border-[#4db1ab] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#4db1ab]/10 transition-all placeholder:text-slate-300"
                    id="remark" name="remark" autocomplete="off" placeholder="Any additional notes..."></textarea>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <button
            class="w-full bg-gradient-to-r from-[#4db1ab] to-[#3d918c] hover:shadow-xl hover:shadow-[#4db1ab]/30 text-white rounded-2xl p-5 font-bold shadow-lg shadow-[#4db1ab]/20 transition-all transform active:scale-[0.98] flex items-center justify-center space-x-3 group"
            id="btn-create" onclick="createAppointment()">
            <i class="fa-solid fa-check-circle text-xl group-hover:scale-110 transition-transform"></i>
            <span>
                @if (session('langSelect') == 'TH')
                    ยืนยันทำนัดหมาย
                @else
                    Confirm Appointment
                @endif
            </span>
        </button>

        <button
            class="hidden w-full bg-slate-100 text-slate-400 rounded-2xl p-5 font-bold cursor-not-allowed flex items-center justify-center space-x-3"
            id="btn-wait">
            <i class="fa-solid fa-spinner fa-spin text-xl"></i>
            <span>
                @if (session('langSelect') == 'TH')
                    กำลังดำเนินการ...
                @else
                    Processing...
                @endif
            </span>
        </button>
    </div>
@endsection
@section('scripts')
    <script>
        let currentPage = 0;
        const itemsPerPage = 5;
        const allDates = @json($patient['dates']);

        function renderDateCards() {
            const container = $('#date-cards-container');
            container.empty();

            const start = currentPage * itemsPerPage;
            const end = Math.min(start + itemsPerPage, allDates.length);
            const pagedDates = allDates.slice(start, end);

            pagedDates.forEach(date => {
                const isActive = $('#dateSelect').val() === date.value;
                const card = `
                    <div onclick="selectDateCard(this, '${date.value}')" 
                         class="date-card group cursor-pointer border-2 ${isActive ? 'border-[#4db1ab] bg-[#4db1ab]/5 transition-all' : 'border-slate-50 bg-slate-50 hover:border-[#4db1ab]/30 hover:bg-white'} rounded-2xl p-4 transition-all duration-300 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="${isActive ? 'bg-[#4db1ab] text-white' : 'bg-white text-slate-400 group-hover:bg-[#4db1ab]/10 group-hover:text-[#4db1ab]'} w-12 h-12 rounded-xl flex items-center justify-center font-black text-xl transition-colors shadow-sm">
                                ${date.day}
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">${date.dayOfWeek}</div>
                                <div class="text-sm font-bold text-slate-700">${date.month} ${date.year}</div>
                            </div>
                        </div>
                        <div class="${isActive ? 'text-[#4db1ab]' : 'text-slate-200'} transition-colors">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                `;
                container.append(card);
            });

            // Update Pagination UI
            $('#page-indicator').text(`Page ${currentPage + 1} of ${Math.ceil(allDates.length / itemsPerPage)}`);

            if (allDates.length > itemsPerPage) {
                $('#pagination-controls').removeClass('hidden');

                // Prev button state
                if (currentPage === 0) {
                    $('#prev-btn').addClass('opacity-50 cursor-not-allowed').attr('disabled', true);
                } else {
                    $('#prev-btn').removeClass('opacity-50 cursor-not-allowed').attr('disabled', false);
                }

                // Next button state
                if (end >= allDates.length) {
                    $('#next-btn').addClass('opacity-50 cursor-not-allowed').attr('disabled', true);
                } else {
                    $('#next-btn').removeClass('opacity-50 cursor-not-allowed').attr('disabled', false);
                }
            }
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 0 && newPage < Math.ceil(allDates.length / itemsPerPage)) {
                currentPage = newPage;
                renderDateCards();
            }
        }

        function selectDateCard(element, value) {
            $('.date-card').removeClass('border-[#4db1ab] bg-[#4db1ab]/5');
            $('.date-card').addClass('border-slate-50 bg-slate-50');
            $('.date-card .w-12').removeClass('bg-[#4db1ab] text-white').addClass('bg-white text-slate-400');
            $('.date-card').find('.fa-circle-check').parent().removeClass('text-[#4db1ab]').addClass('text-slate-200');

            $(element).removeClass('border-slate-50 bg-slate-50').addClass('border-[#4db1ab] bg-[#4db1ab]/5');
            $(element).find('.w-12').removeClass('bg-white text-slate-400').addClass('bg-[#4db1ab] text-white');
            $(element).find('.fa-circle-check').parent().removeClass('text-slate-200').addClass('text-[#4db1ab]');

            $('#dateSelect').val(value);
        }

        $(document).ready(function() {
            renderDateCards();
        });

        async function createAppointment() {
            $('#btn-create').hide();
            $('#btn-wait').show();

            date = $('#dateSelect').val()
            phone = $('#phone').val()

            if (date == '' || phone == '' || phone.length < 10) {
                $('#btn-create').show();
                $('#btn-wait').hide();

                return Swal.fire({
                    title: 'โปรดระบุ วันที่ และ เบอร์โทรศัพท์',
                    icon: "error",
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#d33328',
                });
            }

            await axios.post('{{ route('appointment.create') }}', {
                'appointment_code': '{{ $patient['appointment_code'] }}',
                'hn': '{{ $patient['hn'] }}',
                'date': date,
                'phone': phone,
                'remark': $('#remark').val(),
            }).then((res) => {
                if (res.data.status == 'success') {
                    Swal.fire({
                            title: res.data.status,
                            html: res.data.message,
                            icon: 'info',
                            confirmButtonText: 'ยืนยัน',
                            confirmButtonColor: '#119C92',
                            showCancelButton: false,
                        })
                        .then((result) => {
                            if (result.isConfirmed) {
                                window.location.replace(
                                    '{{ route('patient.appointment', $patient['hn']) }}');
                            }
                        });
                } else {
                    $('#btn-create').show();
                    $('#btn-wait').hide();

                    Swal.fire({
                        title: res.data.status,
                        html: res.data.message,
                        icon: "error",
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#d33328',
                    });
                }
            })
        }
    </script>
@endsection
