@extends('layout')
@section('content')
    <div class="m-auto py-3 text-center" id="section_search">
        <div class="text-center text-lg text-[#4db1ab]">
            @if (session('langSelect') == 'TH')
                ตรวจสอบข้อมูล
            @else
                Check information
            @endif
        </div>
        <div class="text-red-600">
            @if (session('langSelect') == 'TH')
                ( พนักงานโรงพยาบาลพระราม9 )
            @else
                ( Praram9 Staff )
            @endif
        </div>
        <div class="text-center">
            <img class="m-auto aspect-auto w-32" src="{{ asset('images/check2.jpg') }}">
        </div>
        <div class="mb-4">
            <i class="fa-regular fa-address-card mr-1 text-[#4db1ab]"></i>
            @if (session('langSelect') == 'TH')
                หมายเลขบัตรฯ / เบอร์โทรศัพท์
            @else
                ID Card / Mobile Phone
            @endif
        </div>
        <input
            class="mt-3 w-full rounded-xl border-2 border-slate-200 p-4 text-center transition-all focus:border-[#4db1ab] focus:outline-none focus:ring-2 focus:ring-[#4db1ab]/20 shadow-sm"
            id="inputSearch" type="text" placeholder="@if (session('langSelect') == 'TH') ค้นหา... @else Search... @endif"
            autocomplete="off">
        <div class="mt-5 cursor-pointer rounded-xl bg-[#4db1ab] p-4 text-center text-white font-bold shadow-lg shadow-[#4db1ab]/30 hover:bg-[#3d918c] transition-all transform active:scale-95"
            id="search" onclick="checkReferance()">
            <i class="fa-solid fa-magnifying-glass mr-2"></i>
            @if (session('langSelect') == 'TH')
                ค้นหาข้อมูล
            @else
                Search Information
            @endif
        </div>
        <div class="mt-3 hidden rounded bg-[#ddd] p-2 text-center" id="searching">
            @if (session('langSelect') == 'TH')
                กำลังค้นหา
            @else
                Searching...
            @endif
        </div>
    </div>
    <div class="m-auto hidden py-3 text-center" id="section_result">
        <div class="cursor-pointer rounded bg-[#4db1ab] p-2 text-center text-white" onclick="searchAgain()">
            <i class="fa-solid fa-angle-left"></i>
            @if (session('langSelect') == 'TH')
                ค้นหาอีกครั้ง
            @else
                Search Again
            @endif
        </div>
        <div class="mt-3 flex px-6 py-2">
            <div class="p-2">
                @if (session('langSelect') == 'TH')
                    ผลการค้นหา :
                @else
                    Result :
                @endif
            </div>
            <div class="flex-1 text-3xl font-bold text-[#4db1ab]" id="searchInput">1103702299235</div>
        </div>
        <div id="searchResult"></div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#inputSearch').focus();
            $('#inputSearch').keypress(function(e) {
                if (e.which == 13) {
                    checkReferance();
                }
            });
        });

        function checkReferance() {
            var input = $('#inputSearch').val();

            swal.fire({
                title: "Searching...",
                icon: "info",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
            });

            $('#search').hide();
            $('#searching').show();

            if ('{{ session('langSelect') }}' == "TH") {
                search = "โปรดกรอกรายละเอียด"
                notfound = "ไม่พบข้อมูล"
                confirm = "ตกลง"
            } else {
                search = "Please fill information."
                notfound = "Notfound !"
                confirm = "Confirm"
            }

            if (input == '') {
                $('#btnCheck').show();
                $('#btnChecking').hide();
                Swal.fire({
                    title: search,
                    icon: "error",
                    confirmButtonText: confirm,
                    confirmButtonColor: '#d33328',
                });

                return;
            }

            axios.post('{{ route('patient.search') }}', {
                'input': input
            }).then((res) => {
                if (res.data.status == 'success') {
                    $('#section_search').hide();
                    $('#section_result').show();
                    $('#searchInput').html(res.data.search)

                    var data = '';
                    dataArray = res.data.data
                    dataArray.forEach(hn => {
                        @php
                            $url = route('patient.appointment', ':hn');
                        @endphp
                        var viewUrl = '{{ $url }}'.replace(':hn', hn.hn);

                        data += '<a href="' + viewUrl + '" class="block group">';
                        data +=
                            '<div class="bg-white border-2 border-slate-100 rounded-2xl p-5 mb-4 text-left transition-all duration-300 group-hover:border-[#4db1ab] group-hover:shadow-xl group-hover:shadow-[#4db1ab]/10 group-hover:-translate-y-1 relative overflow-hidden">';

                        // Icon accent
                        data +=
                            '<div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">';
                        data += '<i class="fa-solid fa-user-circle fa-4x text-[#4db1ab]"></i>';
                        data += '</div>';

                        data += '<div class="flex items-start justify-between mb-4">';
                        data += '<div>';
                        data +=
                            '<div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Hospital Number</div>';
                        data += '<div class="text-xl font-bold text-slate-800">' + hn.hn + '</div>';
                        data += '</div>';
                        data +=
                            '<div class="bg-[#4db1ab]/10 text-[#4db1ab] px-3 py-1 rounded-full text-xs font-bold">';
                        data += hn.new ? (res.data.lang == 'TH' ? 'รายใหม่' : 'New') : (res.data.lang ==
                            'TH' ? 'ประวัติเดิม' : 'Existing');
                        data += '</div>';
                        data += '</div>';

                        data += '<div class="mb-4">';
                        data +=
                            '<div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Full Name</div>';
                        data += '<div class="text-lg font-semibold text-slate-700">' + hn.name + '</div>';
                        data += '</div>';

                        data +=
                            '<div class="flex items-center justify-between border-t border-slate-50 pt-4">';
                        data += '<div>';
                        data +=
                            '<div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date of Birth</div>';
                        data += '<div class="text-sm font-medium text-slate-600">' + hn.dob + '</div>';
                        data += '</div>';
                        data +=
                            '<div class="flex items-center text-[#4db1ab] font-bold text-sm group-hover:translate-x-1 transition-transform">';
                        data += '<span>' + (res.data.lang == 'TH' ? 'ตรวจสอบนัด' : 'View Appointment') +
                            '</span>';
                        data += '<i class="fa-solid fa-chevron-right ml-2 text-xs"></i>';
                        data += '</div>';
                        data += '</div>';

                        data += '</div>';
                        data += '</a>';
                    });

                    $('#searchResult').html(data)
                    swal.close();
                } else {
                    swal.close();
                    Swal.fire({
                        title: notfound,
                        icon: "error",
                        confirmButtonText: confirm,
                        confirmButtonColor: '#d33328',
                    });
                }
            })
        }

        function searchAgain() {
            $('#inputSearch').val('');
            $('#btnCheck').show();
            $('#btnChecking').hide();
            $('#section_search').show();
            $('#section_result').hide();
        }
    </script>
@endsection
