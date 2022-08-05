<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeHistory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use Mail;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EmployeeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = Employee::paginate(10);

        return response()->json($employee);
    }

    public function get_history_data()
    {
        $employee = EmployeeHistory::paginate(10);

        return response()->json($employee);
    }

    public function readExcel($file)
    {
        $path = storage_path() . '/app/' . $file->store('tmp');
        $reader = new ReaderXlsx();
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $worksheetInfo = $reader->listWorksheetInfo($path);

        $totalRows = $worksheetInfo[0]['totalRows'];
        for ($rate = 1; $rate <= 1; $rate++) {
            $yarn_rate = $sheet->getCell("AG{$rate}")->getValue();
            $mmk_rate = $sheet->getCell("AI{$rate}")->getValue();
            $paymonth = $sheet->getCell("D{$rate}")->getValue();
            $addition = $sheet->getCell("AE{$rate}")->getValue();
            // return $addition;
        }

        for ($row = 4; $row <= $totalRows; $row++) {
            if ($sheet->getCell("B{$row}")->getValue() != null) {
                $employee_id = $sheet->getCell("B{$row}")->getValue();
                $employee_name = $sheet->getCell("C{$row}")->getValue();
                $employee_email = $sheet->getCell("D{$row}")->getValue();
                $employee_nrc_number = $sheet->getCell("E{$row}")->getValue();
                $add_info = $sheet->getCell("F{$row}")->getValue();
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
                    'pay_month' => $paymonth,
                    'total_payment' => $total_payment,
                    'addition' => $addition,
                    'add_info' => $add_info,
                ]);
                $data->save();
            }
        }
    }

    public function validEmail($str)
    {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $itemArray = $request->input('selectedItems');

        $valid_emails = [];
        $invalid_emails = [];
        if ($request->hasFile('file')) {
            $file = request()->file('file');
            $this->readExcel($file);
            return response("Excel File has been read successfully");
        } else if ($itemArray) {
            $result = explode(",", $itemArray);
            for ($i = 0; $i < count($result); $i++) {
                $user = Employee::find($result[$i]);
                if ($this->validEmail($user->employee_email)) {
                    $year = Carbon::parse($user->created_at)->year;
                    $month = Carbon::parse($user->created_at)->month;
                    $day = Carbon::parse($user->created_at)->day;
                    $date = $month . "/" . $day . "/" . $year;
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
                    $data["yarn_rate"] = $user->yarn_rate;
                    $data["mmk_rate"] = $user->mmk_rate;
                    $data["monthYear"] = $monthYear;
                    $data["payMonth"] = $user->pay_month;
                    $data["date"] = $date;
                    $data["month"] = $month;
                    $data["year"] = $year;
                    $data["usd_rate"] = $usd_rate;
                    $data["total_payment"] = $total_amount;
                    $data["addition"] = $user->addition;
                    $data["add_info"] = $user->add_info;

                    $pdf = PDF::loadView('pdf_template', $data);

                    Mail::send('mail_content', $data, function ($message) use ($data, $pdf) {
                        $message->to($data["employee_email"])
                            ->subject("給与明細送付_" . $data["payMonth"] . "/" . $data["year"])
                            ->attachData($pdf->output(), $data["employee_name"] . " (" . $data["payMonth"] . "," . $data["year"] . " Salary Slip).pdf");
                    });

                    Employee::query()
                        ->where('id', $user->id)
                        ->each(function ($user) {
                            $newRecord = $user->replicate();
                            $newRecord->setTable('employee_histories');
                            $newRecord->save();

                            $user->delete();
                        });
                    $valid_emails[] = $user->employee_email;
                } else {
                    $invalid_emails[] = $user->employee_email;
                }
            }
            if (count($valid_emails) > 0) {
                if (count($invalid_emails) > 0) {
                    $data = ["code" => "success", "message" => "Email Sent!But, Some Emails are Invalid!"];
                    return response($data);
                }
                $data = ["code" => "success", "message" => "All Emails has been Sent Successfully"];
                return response($data);
            } else {
                $invalid_emails[] = "These Emails are Invalid!";
                $data = ["code" => "error", "message" => $invalid_emails];
                return response($data);
            }
        } else {
            $itemArray = "hello";
            $result = Employee::all();
            foreach ($result as $value) {
                $user = Employee::find($value['id']);
                $email = $user->employee_email;
                if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
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
                    $data["addition"] = $user->addition;
                    $data["add_info"] = $user->add_info;

                    $pdf = PDF::loadView('pdf_template', $data);

                    Mail::send('mail_content', $data, function ($message) use ($data, $pdf) {
                        $message->to($data["employee_email"])
                            ->subject("給与明細送付_" . $data["payMonth"] . "/" . $data["year"])
                            ->attachData($pdf->output(), $data["employee_name"] . " (" . $data["payMonth"] . "," . $data["year"] . " Salary Slip).pdf");
                    });

                    Employee::query()
                        ->where('id', $user->id)
                        ->each(function ($user) {
                            $newRecord = $user->replicate();
                            $newRecord->setTable('employee_histories');
                            $newRecord->save();

                            $user->delete();
                        });
                    $valid_emails[] = $user->employee_email;
                } else {
                    $invalid_emails[] = $user->employee_email;
                }
            }
            if (count($valid_emails) > 0) {
                if (count($invalid_emails) > 0) {
                    $data = ["code" => "success", "message" => "Email Sent!But, Some Emails are Invalid!"];
                    return response($data);
                }
                $data = ["code" => "success", "message" => "All Emails has been Sent Successfully"];
                return response($data);
            } else {
                $invalid_emails[] = "These Emails are Invalid!";
                $data = ["code" => "error", "message" => $invalid_emails];
                return response($data);
            }
        }
    }

    public function delete(Request $request)
    {
        $itemArray = $request->input('selectedItems');
        if ($itemArray) {
            $result = explode(",", $itemArray);
            for ($i = 0; $i < count($result); $i++) {
                $employee = Employee::find($result[$i]);
                $employee->delete();
            }
            $data = ["code" => "success", "message" => "Delete Success!"];
            return response($data);
        } else {
            $itemArray = "hello";
            $result = Employee::all();
            foreach ($result as $value) {
                $user = Employee::find($value['id']);
                $user->delete();
            }
            $data = ["code" => "success", "message" => "Delete Success!"];
            return response($data);
        }
    }

    // public function delete_history()
    // {
    //     DB::table('employee_histories')->delete();

    //     $data = ["code" => "success", "message" => "Delete Success!"];
    //     return response($data);
    // }

    public function edit($id)
    {
        $user =  Employee::find($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $data = Employee::find($id);
        $data->update($request->all());
        $data = ["code" => "success", "message" => "Update Success!"];
        return response()->json($data);
    }

    public function test_send_mail($id)
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
                ->attachData($pdf->output(), $data["employee_name"] . " (" . $data["payMonth"] . "," . $data["year"] . " Salary Slip).pdf");
        });

        Employee::query()
            ->where('id', $user->id)
            ->each(function ($user) {
                $newRecord = $user->replicate();
                $newRecord->setTable('employee_histories');
                $newRecord->save();

                $user->delete();
            });

        return 'mail sent....';
    }

    public function sendMailToAll(Request $request)
    {
        // $itemArray = $request->input('selectedItems');
        // return $itemArray;
        // $result = explode(",", $itemArray);
        // return $result;
        // for ($i = 0; $i < count($result); $i++) {
        //     $user = Employee::find($result[$i]);
        // }
        // return $user;
        $result = Employee::all();


        foreach ($result as $value) {
            $user = Employee::find($value['id']);

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
                    ->attachData($pdf->output(), $data["employee_name"] . " (" . $data["payMonth"] . "," . $data["year"] . " Salary Slip).pdf");
            });

            Employee::query()
                ->where('id', $user->id)
                ->each(function ($user) {
                    $newRecord = $user->replicate();
                    $newRecord->setTable('employee_histories');
                    $newRecord->save();

                    $user->delete();
                });
        }
        return 'success';
    }
}
