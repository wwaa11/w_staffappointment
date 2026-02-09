@extends('layout')
@section('content')
    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            @if (session('langSelect') == 'TH')
                รายการนัดหมายของคุณ
            @else
                My Appointments
            @endif
        </h2>
        <p class="text-sm text-slate-500">
            @if (session('langSelect') == 'TH')
                ตรวจสอบและจัดการนัดหมายทั้งหมดของคุณ
            @else
                View and manage all your scheduled appointments
            @endif
        </p>
    </div>

    {{-- Patient Summary Card --}}
    <div
        class="bg-gradient-to-br from-[#4db1ab] to-[#3d918c] rounded-3xl p-6 text-white shadow-xl shadow-[#4db1ab]/30 mb-8 relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
            <i class="fa-solid fa-id-card fa-9x"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center space-x-2 opacity-80 mb-1">
                <i class="fa-solid fa-user-circle text-xs"></i>
                <span class="text-[10px] font-bold uppercase tracking-widest">Patient Details</span>
            </div>
            <div class="text-2xl font-black mb-4 tracking-tight">{{ $patient['name'] }}</div>
            <div class="flex items-center space-x-3">
                <div class="bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/20">
                    <div class="text-[10px] uppercase font-bold opacity-70 mb-0.5">HN</div>
                    <div class="text-sm font-black tracking-wider">{{ $patient['hn'] }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/20">
                    <div class="text-[10px] uppercase font-bold opacity-70 mb-0.5">Date of Birth</div>
                    <div class="text-sm font-black tracking-wider">{{ $patient['dob'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Make Appointment Action (Compact) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
        @foreach ($listAppointment as $type)
            @if ($type['exist'] == false)
                <a href="{{ route('appointment.new', ['hn' => $patient['hn'], 'type' => $type['code']]) }}"
                    class="block group">
                    <div
                        class="bg-[#ffea29] hover:bg-[#ffe200] rounded-xl p-3 shadow-md shadow-[#ffea29]/20 flex items-center justify-between transition-all transform group-hover:scale-[1.02] group-active:scale-95 border-b-2 border-black/10">
                        <div class="flex items-center space-x-3">
                            <div class="bg-black/5 p-2 rounded-lg">
                                <i class="fa-solid fa-calendar-plus text-lg text-slate-800"></i>
                            </div>
                            <div class="text-sm font-bold text-slate-800">
                                @if (session('langSelect') == 'TH')
                                    ทำนัด{{ $type['name'] }}
                                @else
                                    {{ $type['name_eng'] }} Appt
                                @endif
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-800/30 text-xs"></i>
                    </div>
                </a>
            @else
                <div
                    class="bg-[#cccccc] cursor-pointer rounded-xl p-3 shadow-md shadow-[#ffea29]/20 flex items-center justify-between transition-all transform group-hover:scale-[1.02] group-active:scale-95 border-b-2 border-black/10">
                    <div class="flex items-center space-x-3">
                        <div class="bg-black/5 p-2 rounded-lg">
                            <i class="fa-solid fa-calendar-plus text-lg text-slate-800"></i>
                        </div>
                        <div class="text-sm font-bold text-slate-800">
                            @if (session('langSelect') == 'TH')
                                มีการทำนัด {{ $type['name'] }} แล้ว
                                <div class="text-xs text-red-500">กรุณายกเลิกนัดเดิมก่อน</div>
                            @else
                                Already have an appointment {{ $type['name_eng'] }}
                                <div class="text-xs text-red-500">Please cancel the previous appointment first</div>
                            @endif
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-800/30 text-xs"></i>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Appointment List --}}
    <div class="space-y-4">
        <div class="flex items-center space-x-2 text-slate-400 mb-2 px-2">
            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
            <span class="text-xs font-bold uppercase tracking-widest">Upcoming Schedules</span>
        </div>
        @foreach ($patient['appointments'] as $appointment)
            <div
                class="bg-white rounded-2xl border-2 border-slate-100 p-5 shadow-sm transition-all hover:border-[#4db1ab]/40 hover:shadow-md group relative overflow-hidden">
                <div
                    class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#4db1ab] opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="flex items-start space-x-4">
                    {{-- Date Block --}}
                    <div
                        class="bg-slate-50 rounded-2xl px-4 py-3 text-center min-w-[80px] border border-slate-100 group-hover:bg-[#4db1ab]/5 transition-colors">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                            {{ $appointment['Date']->Day }}</div>
                        <div class="text-3xl font-black text-[#4db1ab] -my-1">{{ $appointment['Date']->Date }}</div>
                        <div class="text-[10px] font-bold text-slate-500">{{ $appointment['Date']->Month }}</div>
                    </div>

                    {{-- Info Block --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg leading-tight mb-1">
                                    {{ $appointment['Doctor'] }}
                                </h3>
                                <p class="text-sm text-slate-500 font-medium mb-2">{{ $appointment['Clinic'] }} <span
                                        class="font-bold text-red-400">{{ $appointment['Remark'] }}</span></p>
                            </div>
                            @if (substr($appointment['AppointmentNo'], 0, 3) == 'VAP' || substr($appointment['AppointmentNo'], 0, 3) == 'SAP')
                                <button onclick="deleteAppointment('{{ $appointment['AppointmentNo'] }}')"
                                    class="text-slate-300 hover:text-red-500 transition-colors p-2">
                                    <i class="fa-solid fa-calendar-xmark text-lg"></i>Delete
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center justify-between mt-2 pt-3 border-t border-slate-50">
                            <div>
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Appointment
                                    No.</span>
                                <span
                                    class="text-xs font-mono font-bold text-slate-600 tracking-wider">{{ $appointment['AppointmentNo'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @if (count($patient['appointments']) == 0)
            <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-calendar-day fa-2x text-slate-200"></i>
                </div>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">No scheduled appointments</p>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        async function deleteAppointment(appno) {

            if ('{{ session('langSelect') }}' == "TH") {
                cancelText = "ยืนยันการยกเลิกนัด"
                confirm = "ยืนยัน"
            } else {
                cancelText = "Confirm cancel Appointment."
                confirm = "Confirm"
            }

            var result = await Swal.fire({
                title: cancelText,
                icon: 'info',
                confirmButtonText: confirm,
                confirmButtonColor: '#dc3545',
                showCancelButton: true,
                cancelButtonText: "ยกเลิก"
            })

            if (result.isConfirmed) {
                axios.post('{{ route('appointment.delete') }}', {
                    'appointmentno': appno,
                }).then((res) => {
                    if (res.data.status == 'success') {
                        window.location.reload();
                    }
                })
            }
        }
    </script>
@endsection
