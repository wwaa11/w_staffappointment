<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class WebController extends Controller
{
    private $listAppointment = [
        'SAP' => [
            'code' => 'SAP',
            'name' => 'ตรวจสุขภาพ',
            'name_eng' => 'Health Checkup',
            'note' => '',
            'clinic' => '1800',
            'doctor' => 'V9999',
            'time' => '12:00',
            'exist' => false,
        ],
        'VAP' => [
            'code' => 'VAP',
            'name' => 'วัคซีนไข้หวัดใหญ่',
            'name_eng' => 'Influenza Vaccine',
            'note' => '',
            'note_eng' => '',
            'clinic' => '1800',
            'doctor' => 'V9999',
            'time' => '13:00',
            'exist' => false,
        ],
        'EVAP' => [
            'code' => 'EVAP',
            'name' => 'วัคซีนไข้หวัดใหญ่',
            'name_eng' => 'Influenza Vaccine',
            'note' => 'เฉพาะ พนง.ที่ไม่อยู่ในรอบตรวจสุขภาพปีนี้',
            'note_eng' => 'Only for those who are not in the health checkup round',
            'clinic' => '1800',
            'doctor' => 'V9999',
            'time' => '13:00',
            'exist' => false,
        ],
    ];

    public function langSelect(Request $request)
    {
        $lang = $request->lang;
        if ($lang == 'TH') {
            session()->put('langSelect', 'TH');
        } elseif ($lang == 'ENG') {
            session()->put('langSelect', 'ENG');
        }

        return response()->json('success', 200);
    }

    public function setfullDate($dateInput)
    {
        $dateTime = strtotime($dateInput);
        if (session('langSelect') == 'TH') {
            App::setLocale('th');
            $dayOfWeek = Carbon::createFromTimestamp($dateTime)->translatedFormat('l');
            $monthNames = Carbon::createFromTimestamp($dateTime)->translatedFormat('M');

            $response = (object) [
                'Day' => $dayOfWeek,
                'Month' => $monthNames,
                'Date' => date('j', $dateTime),
                'Year' => date('Y', $dateTime),
                'FullDate' => date('j', $dateTime).' '.$monthNames.' '.date('Y', $dateTime) + 543,
            ];
        } else {
            $response = (object) [
                'Day' => date('D', $dateTime),
                'Month' => date('M', $dateTime),
                'Date' => date('j', $dateTime),
                'Year' => date('Y', $dateTime),
                'FullDate' => date('j', $dateTime).' '.date('M', $dateTime).' '.date('Y', $dateTime),
            ];
        }

        return $response;
    }

    public function formatName($first, $last)
    {
        mb_internal_encoding('UTF-8');
        $setname = mb_substr($first, 1);
        $setlast = mb_substr($last, 1);
        if (str_contains($setname, '\\')) {
            $setname = explode('\\', $setname);
            $setname = $setname[1].$setname[0];
        }
        $fullname = $setname.' '.$setlast;

        return $fullname;
    }

    public function setDoctor($code)
    {
        $config = DB::connection('SSB')->table('HNDOCTOR_MASTER')->where('Doctor', $code)->first();
        $text = '';
        if ($config !== null) {
            mb_internal_encoding('UTF-8');
            if (session('langSelect') == 'ENG') {
                $text = mb_substr($config->EnglishName, 1);
            } else {
                $text = mb_substr($config->LocalName, 1);
            }
            if (str_contains($text, '\\')) {
                $temp = explode('\\', $text);
                $text = $temp[1].$temp[0];
            }
            if (str_contains($text, ',')) {
                $temp = explode(',', $text);
                $text = $temp[1].$temp[0];
            }
        }

        return $text;
    }

    public function setClinic($code)
    {
        $config = DB::connection('SSB')->table('DNSYSCONFIG')->where('CtrlCode', '42203')->where('Code', $code)->first();
        $text = $code;
        if ($config !== null) {
            mb_internal_encoding('UTF-8');
            if (session('langSelect') == 'ENG') {
                $text = mb_substr($config->EnglishName, 1);
            } else {
                $text = mb_substr($config->LocalName, 1);
            }
        }

        return $text;
    }

    public function index()
    {
        if (session('langSelect') == null) {
            session()->put('langSelect', 'TH');
        }

        return view('index');
    }

    public function checkreferance(Request $request)
    {
        $input = $request->input;
        $response = [
            'status' => 'Not Found!',
        ];

        $getHN = DB::connection('SSB')
            ->table('HNPAT_INFO')
            ->leftjoin('HNPAT_NAME', 'HNPAT_INFO.HN', '=', 'HNPAT_NAME.HN')
            ->leftjoin('HNPAT_REF', 'HNPAT_INFO.HN', '=', 'HNPAT_REF.HN')
            ->leftjoin('HNPAT_ADDRESS', 'HNPAT_INFO.HN', '=', 'HNPAT_ADDRESS.HN')
            ->whereNull('HNPAT_INFO.FileDeletedDate')
            ->where('HNPAT_ADDRESS.SuffixTiny', 1)
            ->where('HNPAT_NAME.SuffixSmall', 0)
            ->where(function ($query) use ($input) {
                $query->where('HNPAT_REF.RefNo', $input)
                    ->orWhere('HNPAT_ADDRESS.MobilePhone', $input);
            })
            ->select(
                'HNPAT_INFO.HN',
                'HNPAT_INFO.BirthDateTime',
                'HNPAT_NAME.FirstName',
                'HNPAT_NAME.LastName',
            )
            ->groupBy(
                'HNPAT_INFO.HN',
                'HNPAT_INFO.BirthDateTime',
                'HNPAT_NAME.FirstName',
                'HNPAT_NAME.LastName',
            )
            ->get();

        if (count($getHN) > 0) {
            $data = [];
            foreach ($getHN as $hn) {
                $birthDate = $this->setfullDate($hn->BirthDateTime);
                $findAppointment = DB::connection('SSB')
                    ->table('HNAPPMNT_HEADER')
                    ->where('HNAPPMNT_HEADER.HN', $hn->HN)
                    ->whereNull('HNAPPMNT_HEADER.cxlReasonCode')
                    ->where('HNAPPMNT_HEADER.AppointmentNo', 'LIKE', 'VAP%')
                    ->first();

                $data[] = [
                    'hn' => $hn->HN,
                    'name' => $this->formatName($hn->FirstName, $hn->LastName),
                    'dob' => $birthDate->FullDate,
                    'new' => ($findAppointment == null) ? true : false,
                ];

                $response['status'] = 'success';
                $response['search'] = $input;
                $response['data'] = $data;
            }

        }

        return response()->json($response, 200);
    }

    public function viewAppointment($hn)
    {
        $patientInfo = DB::connection('SSB')
            ->table('HNPAT_NAME')
            ->leftJoin('HNPAT_INFO', 'HNPAT_NAME.HN', '=', 'HNPAT_INFO.HN')
            ->where('HNPAT_NAME.HN', $hn)
            ->select('HNPAT_NAME.HN', 'FirstName', 'LastName', 'BirthDateTime')
            ->first();

        $fullname = $this->formatName($patientInfo->FirstName, $patientInfo->LastName);
        $findAppointment = DB::connection('SSB')
            ->table('HNAPPMNT_HEADER')
            ->leftJoin('HNAPPMNT_MSG', 'HNAPPMNT_HEADER.AppointmentNo', 'HNAPPMNT_MSG.AppointmentNo')
            ->where('HNAPPMNT_HEADER.HN', $hn)
            ->whereNull('HNAPPMNT_HEADER.cxlReasonCode')
            ->orderBy('HNAPPMNT_HEADER.AppointDateTime', 'asc')
            ->where('HNAPPMNT_HEADER.AppointDateTime', '>=', date('Y-m-d'))
            ->where('HNAPPMNT_MSG.HNAppointmentMsgType', 5)
            ->select(
                'HNAPPMNT_HEADER.AppointmentNo',
                'HNAPPMNT_HEADER.AppointDateTime',
                'HNAPPMNT_HEADER.HN',
                'HNAPPMNT_HEADER.Clinic',
                'HNAPPMNT_HEADER.Doctor',
                'HNAPPMNT_MSG.RemarksMemo',
            )
            ->get();

        $patient = [
            'hn' => $hn,
            'name' => $fullname,
            'dob' => $this->setfullDate($patientInfo->BirthDateTime)->FullDate,
            'appointments' => [],
        ];

        $listAppointment = $this->listAppointment;
        $showVAP = false;
        foreach ($findAppointment as $app) {
            $exits = false;
            $name = '';
            $checkAppointment = substr($app->AppointmentNo, 0, 3);
            if ($checkAppointment == 'SAP') {
                $listAppointment['SAP']['exist'] = true;
                $listAppointment['EVAP']['exist'] = true;
                if ($app->AppointDateTime >= '2026-04-20') {
                    $listAppointment['VAP']['exist'] = true;
                    $name = 'ตรวจสุขภาพ + วัคซีนไข้หวัดใหญ่';
                    $app->RemarksMemo = $app->RemarksMemo.' + วัคซีนไข้หวัดใหญ่';
                } else {
                    $showVAP = true;
                    $name = 'ตรวจสุขภาพ';
                }
                $exits = true;
            }

            if ($checkAppointment == 'VAP') {
                $listAppointment['VAP']['exist'] = true;
                $listAppointment['EVAP']['exist'] = true;
                $name = 'วัคซีนไข้หวัดใหญ่';
                $exits = true;
            }

            $checkAppointment = substr($app->AppointmentNo, 0, 4);
            if ($checkAppointment == 'EVAP') {
                $listAppointment['SAP']['exist'] = true;
                $listAppointment['VAP']['exist'] = true;
                $listAppointment['EVAP']['exist'] = true;
                $name = 'วัคซีนไข้หวัดใหญ่';
                $exits = true;
            }

            $date = date('Y-m-d', strtotime($app->AppointDateTime));
            $fulldate = $this->setfullDate($date);
            $patient['appointments'][] = [
                'Date' => $fulldate,
                'AppointmentNo' => $app->AppointmentNo,
                'Name' => $name,
                'Doctor' => $this->setDoctor($app->Doctor),
                'Clinic' => $this->setClinic($app->Clinic),
                'Remark' => $app->RemarksMemo,
                'Cancel' => ($app->Doctor == 'V9999') ? true : false,
            ];
        }

        return view('Appointment_all')->with(compact('patient', 'listAppointment', 'showVAP'));
    }

    public function newAppointment($hn, $type)
    {
        $patient = [
            'appointment_code' => $type,
            'appointment_name' => $this->listAppointment[$type],
            'hn' => $hn,
            'dates' => [],
        ];

        $response = Http::withoutVerifying()
            ->withOptions(['verify' => false])
            ->withHeaders([
                'Content-Type' => 'application/json',
                'API_KEY' => env('DOCTOR_TIME_KEY'),
            ])
            ->post('https://192.168.99.6:8089/api/appointment/doctor/doctorTime', [
                'clinicCode' => $this->listAppointment[$type]['clinic'],
                'fixDoctorCode' => [$this->listAppointment[$type]['doctor']],
                'startDate' => null,
                'days' => 120,
            ]);

        if ($response->successful()) {
            $response = $response->object();
            $dates = $response->detail[0]->time;

            $thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
            $thaiMonths = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

            foreach ($dates as $date) {
                foreach ($date->appointmentTime as $time) {
                    if ($type == 'SAP' && $time->time == '12:00' && $time->available > 0) {
                        $dt = \Carbon\Carbon::parse($date->appointmentDate);
                        $label = $thaiDays[$dt->dayOfWeek].' '.$dt->day.' '.$thaiMonths[$dt->month].' '.($dt->year + 543);
                        $patient['dates'][] = [
                            'value' => $date->appointmentDate,
                            'label' => $label,
                            'day' => $dt->day,
                            'month' => $thaiMonths[$dt->month],
                            'year' => $dt->year + 543,
                            'dayOfWeek' => $thaiDays[$dt->dayOfWeek],
                        ];
                    }
                    if (($type == 'VAP' || $type == 'EVAP') && $time->time == '13:00' && $time->available > 0 && $date->appointmentDate < '2026-04-20') {
                        $dt = \Carbon\Carbon::parse($date->appointmentDate);
                        $label = $thaiDays[$dt->dayOfWeek].' '.$dt->day.' '.$thaiMonths[$dt->month].' '.($dt->year + 543);
                        $patient['dates'][] = [
                            'value' => $date->appointmentDate,
                            'label' => $label,
                            'day' => $dt->day,
                            'month' => $thaiMonths[$dt->month],
                            'year' => $dt->year + 543,
                            'dayOfWeek' => $thaiDays[$dt->dayOfWeek],
                        ];
                    }
                }
            }
        }

        return view('Appointment_create')->with(compact('patient'));
    }

    public function AppointmentCreate(Request $request)
    {
        $response = [
            'status' => 'failed',
            'message' => 'ทำการนัดหมายไม่สำเร็จ',
            'continue' => false,
        ];

        $hn = $request->hn;
        $phone = $request->phone;
        $remark = $request->remark;

        $Referance = DB::connection('SSB')
            ->table('HNPAT_REF')
            ->where('HN', $hn)
            ->orderBy('IDCardType', 'asc')
            ->select(
                'IDCardType',
                'RefNo'
            )
            ->first();

        $patient = [
            'hn' => $hn,
            'refNo' => $Referance->RefNo,
            'isPassport' => ($Referance->IDCardType == 5) ? true : false,
            'phone' => $phone,
        ];

        $listAppointment = $this->listAppointment;
        $myAppointments = DB::connection('SSB')
            ->table('HNAPPMNT_HEADER')
            ->where('HNAPPMNT_HEADER.HN', $hn)
            ->whereNull('HNAPPMNT_HEADER.cxlReasonCode')
            ->orderBy('HNAPPMNT_HEADER.AppointDateTime', 'asc')
            ->where('HNAPPMNT_HEADER.AppointDateTime', '>=', date('Y-m-d'))
            ->select(
                'HNAPPMNT_HEADER.AppointmentNo',
                'HNAPPMNT_HEADER.AppointDateTime',
                'HNAPPMNT_HEADER.Clinic',
                'HNAPPMNT_HEADER.Doctor',
            )
            ->get();

        if ($myAppointments->count() > 0) {
            foreach ($myAppointments as $appointment) {
                $checkAppointment = substr($appointment->AppointmentNo, 0, 3);
                if (array_key_exists($checkAppointment, $listAppointment)) {
                    $listAppointment[$checkAppointment]['exist'] = true;
                }
            }
        }

        if ($request->appointment_code == 'SAP' && ! $listAppointment['SAP']['exist']) {
            $create_response = $this->createAppointment(env('APPOINTMENT_CREATE_SAP'), $patient, $request->date, '12:00', 'นัดหมายตรวจสุขภาพ', $remark);
            if ($create_response) {
                $response['status'] = 'success';
                $response['message'] = 'นัดหมายตรวจสุขภาพสำเร็จ';
                if ($request->date < '2026-04-20') {
                    $response['continue'] = true;
                }
            }
        } elseif ($request->appointment_code == 'VAP' && ! $listAppointment['VAP']['exist']) {
            $create_response = $this->createAppointment(env('APPOINTMENT_CREATE_VAP'), $patient, $request->date, '13:00', 'นัดฉีดวัคซีน', $remark);
            if ($create_response) {
                $response['status'] = 'success';
                $response['message'] = 'นัดฉีดวัคซีนสำเร็จ';
            }
        } elseif ($request->appointment_code == 'EVAP' && ! $listAppointment['EVAP']['exist']) {
            $create_response = $this->createAppointment(env('APPOINTMENT_CREATE_EVAP'), $patient, $request->date, '13:00', 'นัดฉีดวัคซีน', $remark);
            if ($create_response) {
                $response['status'] = 'success';
                $response['message'] = 'นัดฉีดวัคซีนสำเร็จ';
            }
        }

        return response()->json($response, 200);
    }

    private function createAppointment($key, $patient, $date, $time, $memo, $remark)
    {
        $response = Http::withoutVerifying()
            ->withOptions(['verify' => false])
            ->withHeaders([
                'Content-Type' => 'application/json',
                'API_KEY' => $key,
            ])
            ->post('https://192.168.99.6:8089/api/appointment/booking', [
                'hn' => $patient['hn'],
                'doctorCode' => 'V9999',
                'appointmentDateTime' => $date.' '.$time,
                'idCard' => $patient['refNo'],
                'isPassport' => $patient['isPassport'],
                'mobilePhone' => $patient['phone'],
                'remarksProcedure' => [],
                'remarksMemo' => $memo,
            ]);

        if ($response->successful()) {
            $response = $response->json();
            $appointment_no = $response['detail']['appointmentNo'];

            if ($remark !== null) {
                $remark_response = Http::withoutVerifying()
                    ->withOptions(['verify' => false])
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'API_KEY' => env('DOCTOR_TIME_KEY'),
                    ])
                    ->post('https://192.168.99.6:8089/api/appointment/appointmentMsg', [
                        'appointmentNo' => $appointment_no,
                        'appointmentMsgType' => '5',
                        'remarksMemo' => $remark,
                    ]);
            }

            $response = true;
        } else {

            $response = false;
        }

        return $response;
    }

    public function AppointmentDelete(Request $req)
    {
        $response = [
            'status' => 'failed',
        ];

        $type = substr($req->appointmentno, 0, 3) == 'SAP' ? 'VAP' : 'SAP';
        // Check appointment is check up and date > 2026-04-20 also delete vaccine appointment
        $checkAppointment = DB::connection('SSB')
            ->table('HNAPPMNT_HEADER')
            ->where('AppointmentNo', $req->appointmentno)
            ->select(
                'HN',
                'AppointDateTime'
            )
            ->first();

        if ($checkAppointment->AppointDateTime < '2026-04-20') {
            $vaccineAppointment = DB::connection('SSB')
                ->table('HNAPPMNT_HEADER')
                ->where('HN', $checkAppointment->HN)
                ->where('AppointmentNo', 'like', $type.'%')
                ->whereNull('cxlReasonCode')
                ->select(
                    'AppointmentNo',
                    'AppointDateTime',
                )
                ->first();
            if ($vaccineAppointment !== null) {
                $delete_vaccine_response = $this->deleteAppointment($vaccineAppointment->AppointmentNo);
            }
        }

        $delete_response = $this->deleteAppointment($req->appointmentno);

        if ($delete_response) {
            $response = [
                'status' => 'success',
            ];
        }

        return response()->json($response, 200);
    }

    private function deleteAppointment($appointmentno)
    {
        $response = Http::withoutVerifying()
            ->withOptions(['verify' => false])
            ->withHeaders([
                'Content-Type' => 'application/json',
                'API_KEY' => env('APPMNT_DELETE'),
            ])
            ->delete('https://192.168.99.6:8089/api/appointment/booking', [
                'appointmentNo' => $appointmentno,
            ]);

        if ($response->successful()) {

            return true;
        }

        return false;
    }
}
