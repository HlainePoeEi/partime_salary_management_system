<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeHistory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use Mail;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function getPDF($id)
    {
        $user =  Employee::find($id);
        $employee_name = $user->employee_name;
        if (!$user)
            return redirect('/')
                ->header('Content-Type', 'text/plain');

        $year = Carbon::parse($user->created_at)->year;
        $month = Carbon::parse($user->created_at)->month;
        $day = Carbon::parse($user->created_at)->day;
        $date = $month . "/" . $day . "/" . $year;
        $monthYear = date('F', strtotime($user->created_at)) . "," . $year;
        $usd_rate = round($user->usd_rate);
        $total = round($user->total_payment);
        $total_amount = number_format($total);
        $pdf = PDF::loadView('pdf_preview', compact('user', 'monthYear', 'date', 'usd_rate', 'total_amount'));
        return $pdf->stream("$employee_name ($monthYear Salary Slip).pdf");
    }

    public function get_history_data()
    {
        return view('history');
    }

    public function edit($id)
    {
        $user =  Employee::find($id);
        return view('edit', compact('user'));
    }

    public function test()
    {
        return view('test');
    }

    public function upload_excel(Request $request)
    {
        $file = $request->file('file');
        $path = storage_path() . '/app/' . $file->store('tmp');
        $reader = new ReaderXlsx();
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $worksheetInfo = $reader->listWorksheetInfo($path);

        $totalRows = $worksheetInfo[0]['totalRows'];

        for ($rate = 1; $rate <= 1; $rate++) {
            $yarn_rate = $sheet->getCell("AG{$rate}")->getValue();
            $mmk_rate = $sheet->getCell("AI{$rate}")->getValue();
            $date = $sheet->getCell("D{$rate}")->getValue();
            // return $date;
        }

        for ($row = 4; $row <= $totalRows; $row++) {
            if ($sheet->getCell("B{$row}")->getValue() != null) {
                $employee_id = $sheet->getCell("B{$row}")->getValue();
                $employee_name = $sheet->getCell("C{$row}")->getValue();
                $employee_email = $sheet->getCell("D{$row}")->getValue();
                $employee_nrc_number = $sheet->getCell("E{$row}")->getValue();
                $aggregate = $sheet->getCell("AD{$row}")->getCalculatedValue();
                $pre_training_hours = $sheet->getCell("X{$row}")->getValue();
                $meeting_attendance = $sheet->getCell("Y{$row}")->getValue();
                $leader_allowance = $sheet->getCell("Z{$row}")->getValue();
                $working_hours = $sheet->getCell("AA{$row}")->getValue();
                $cross_check = $sheet->getCell("AB{$row}")->getValue();
                $correction_work_time = $sheet->getCell("AC{$row}")->getValue();
                $basic_hourly_wage = $sheet->getCell("AE{$row}")->getValue();
                $incentives = $sheet->getCell("AF{$row}")->getValue();
                $payment_amount_with_yen = $sheet->getCell("AG{$row}")->getCalculatedValue();
                $usd_rate = $sheet->getCell("AH{$row}")->getCalculatedValue();
                $total_payment = $sheet->getCell("AI{$row}")->getCalculatedValue();

                $data = new Employee([
                    'employee_id' => $employee_id,
                    'employee_name' => $employee_name,
                    'employee_email' => $employee_email,
                    'employee_nrc_number' => $employee_nrc_number,
                    'aggregate' => $aggregate,
                    'pre_training_hours' => $pre_training_hours,
                    'meeting_attendance' => $meeting_attendance,
                    'leader_allowance' => $leader_allowance,
                    'working_hours' => $working_hours,
                    'cross_check' => $cross_check,
                    'correction_work_time' => $correction_work_time,
                    'basic_hourly_wage' => $basic_hourly_wage,
                    'incentives' => $incentives,
                    'payment_amount_with_yen' => $payment_amount_with_yen,
                    'usd_rate' => $usd_rate,
                    'yarn_rate' => $yarn_rate,
                    'mmk_rate' => $mmk_rate,
                    'total_payment' => $total_payment,
                ]);
                $data->save();
            }
        }
        // return 'ok';
    }

    public function send_mail($id)
    {
        $user = Employee::find($id);
        $year = Carbon::parse($user->created_at)->year;
        $month = Carbon::parse($user->created_at)->month;
        $day = Carbon::parse($user->created_at)->day;
        $date = $year . "/" . $month . "/" . $day;
        $monthYear = date('F', strtotime($user->created_at)) . "," . $year;
        $usd_rate = round($user->usd_rate);
        $total = round($user->total_payment);
        $total_amount = number_format($total);

        $data["employee_id"] = $user->employee_id;
        $data["employee_email"] = $user->employee_email;
        $data["employee_name"] = $user->employee_name;
        $data["employee_nrc_number"] = $user->employee_nrc_number;
        $data["aggregate"] = $user->aggregate;
        $data["pre_training_hours"] = $user->pre_training_hours;
        $data["meeting_attendance"] = $user->meeting_attendance;
        $data["leader_allowance"] = $user->leader_allowance;
        $data["working_hours"] = $user->working_hours;
        $data["cross_check"] = $user->cross_check;
        $data["correction_work_time"] = $user->correction_work_time;
        $data["basic_hourly_wage"] = $user->basic_hourly_wage;
        $data["incentives"] = $user->incentives;
        $data["payment_amount_with_yen"] = $user->payment_amount_with_yen;
        $data["usd_rate"] = $user->usd_rate;
        $data["yarn_rate"] = $user->yarn_rate;
        $data["mmk_rate"] = $user->mmk_rate;
        $data["total_payment"] = $user->total_payment;
        $data["monthYear"] = $monthYear;
        $data["payMonth"] = $user->pay_month;
        $data["date"] = $date;
        $data["month"] = $month;
        $data["year"] = $year;
        $data["usd_rate"] = $usd_rate;
        $data["total_payment"] = $total_amount;

        $pdf = PDF::loadView('pdf_template', $data);

        Mail::send('mail_content', $data, function ($message) use ($data, $pdf) {
            $message->to($data["employee_email"])
                ->subject("給与明細送付_" . $data["payMonth"] . "/" . $data["year"])
                ->attachData($pdf->output(), $data["employee_name"] . " (" . $data["payMonth"].",".$data["year"]. " Salary Slip).pdf");
        });

        return 'mail sent....';
    }
}
