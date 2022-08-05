<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Monthy Slip of {{ $employee_name }}</title>
</head>
<style>
    @font-face {
        font-family: ipag;
        font-style: normal;
        font-weight: normal;
        src:url("{{ storage_path('fonts/ipag.ttf')}}");
    }

    body {
        font-family: ipag;
    }

    hr {
        width: 155%;
        margin-left: 0px;
    }

    table td:nth-child(1) {
        padding-right: 40px;
    }

    table td:nth-child(2) {
        text-align: right;
    }

    table td:nth-child(3) {
        text-align: left;
    }

    #tbl_data {
        width: 400px;
    }

    #tbl_data td:nth-child(2) {
        text-align: left;
    }
</style>

<body>
    <h1 style="text-align: center;"><u>Official Report for Monthly Salary</u></h1>
    <div style="float: right;">As of {{ $date }}</div>
    <h3>Received with thanks from GIC MYANMAR Co.,Ltd.</h3>
    <div>For the month of <b style="color:blue;">{{ $payMonth }}</b></div>
    <table>
        <tbody>
            <tr>
                <td>集計</td>
                <td>
                    {{ $aggregate }}
                </td>
                <td>時間</td>
            </tr>
            <tr>
                <td>
                    事前研修時間数※初期の身
                </td>
                <td>
                    {{ $pre_training_hours }}
                </td>
            </tr>
            <tr>
                <td>
                    会議出席
                </td>
                <td>
                    {{ $meeting_attendance }}
                </td>
            </tr>
            <tr>
                <td>
                    Leader 手当※会議出席含み
                </td>
                <td>
                    {{ $leader_allowance }}
                </td>
            </tr>
            <tr>
                <td>
                    作業時間数
                </td>
                <td>
                    {{ $working_hours }}
                </td>
            </tr>
            <tr>
                <td>
                    Cross Check
                </td>
                <td>
                    {{ $cross_check }}
                </td>
            </tr>
            <tr>
                <td>
                    修正作業時間
                </td>
                <td>
                    {{ $correction_work_time }}
                </td>
            </tr>
            <tr>
                <td>
                    基本時給※単価：円
                </td>
                <td>
                    {{ $basic_hourly_wage	 }}
                </td>
                <td>(￥)</td>
            </tr>
            <tr>
                <td>
                    Incentives※単価：生産性による
                </td>
                <td>
                    {{ $incentives	 }}
                </td>
                <td>(￥)</td>
            </tr>
            <tr>
                <td>
                    支給額※単価:円
                </td>
                <td>
                    {{ $payment_amount_with_yen }}
                </td>
                <td>(￥)</td>
            </tr>
            <hr />
            <tr>
                <td>
                    レード {{ $yarn_rate }}　(￥)
                </td>
                <td>
                    {{ $usd_rate }}
                </td>
                <td>USD</td>
            </tr>
            <tr>
                <td>
                    レード {{ $mmk_rate }}　MMK
                </td>
            </tr>
            <tr>
                <td>
                    支給合計額
                </td>
                <td>
                    {{ $total_payment }}
                </td>
                <td>MMK</td>
            </tr><br><br>
        </tbody>
    </table>
    <table id="tbl_data">
        <!-- <tr>
            <td> {{$addition}}</td>
        </tr> -->
        <tr>
            <td> 基本時給 :</td>
            <td> 300円</td>
        </tr>
        <tr>
            <td> Incentives Hr※7月分 :</td>
            <td> 16時間</td>
        </tr>
        <tr>
            <td> Incentives単価※7月分 :</td>
            <td> 250円</td>
        </tr>
        <tr>
            <td> Incentives Hr※6月分 :</td>
            <td> 20時間</td>
        </tr>
        <tr>
            <td> Incentives単価※6月分 :</td>
            <td> 100円</td>
        </tr>
        <br><br>
        <tr>
            <td> Name :</td>
            <td> {{ $employee_name }}</td>
        </tr>
        <tr>
            <td> Employee Number:</td>
            <td> {{ $employee_id }}</td>
        </tr>
        <tr>
            <td> NRC Number:</td>
            <td> {{ $employee_nrc_number }}</td>
        </tr>
        <tr>
            <td> Signature:</td>
        </tr>
    </table>
</body>

</html>