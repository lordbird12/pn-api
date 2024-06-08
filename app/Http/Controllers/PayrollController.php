<?php

namespace App\Http\Controllers;

use App\Models\DeductPaid;
use App\Models\IncomePaid;
use App\Models\Payroll;
use App\Models\TimeAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function getPage(Request $request)
    {
        $columns = $request->columns;
        $length = $request->length;
        $order = $request->order;
        $search = $request->search;
        $start = $request->start;
        $page = $start / $length + 1;

        $month = $request->month;
        $year = $request->year;

        $col = array('id', 'user_no', 'total_income', 'total_deduct', 'total_ot', 'total_late_deduct', 'salary', 'total_summary', 'month', 'year', 'status', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('',  'user_no', 'total_income', 'total_deduct', 'total_ot', 'total_late_deduct', 'salary', 'total_summary', 'month', 'year', 'status', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = Payroll::select($col);

        if ($month) {
            $D->where('month', $month);
        }

        if ($year) {
            $D->where('year', $year);
        }


        if ($orderby[$order[0]['column']]) {
            $D->orderby($orderby[$order[0]['column']], $order[0]['dir']);
        }

        if ($search['value'] != '' && $search['value'] != null) {

            $D->Where(function ($query) use ($search, $col) {

                //search datatable
                $query->orWhere(function ($query) use ($search, $col) {
                    foreach ($col as &$c) {
                        $query->orWhere($c, 'like', '%' . $search['value'] . '%');
                    }
                });

                //search with
                // $query = $this->withPermission($query, $search);
            });
        }

        $d = $D->paginate($length, ['*'], 'page', $page);

        if ($d->isNotEmpty()) {

            //run no
            $No = (($page - 1) * $length);

            for ($i = 0; $i < count($d); $i++) {

                $No = $No + 1;
                $d[$i]->No = $No;
                $d[$i]->user = User::where('user_no', $d[$i]->user_no)->first();
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function show(Payroll $payroll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function edit(Payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payroll $payroll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payroll $payroll)
    {
        //
    }

    public function payroll(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        $user = $request->user;

        $users = User::get();

        foreach ($users as $key => $value) {

            DB::beginTransaction();

            $totalIncomeAmount = IncomePaid::where('user_id', $value['id'])
                ->where('month', $month)
                ->where('year', $year)
                ->sum('paid');

            $totalDeductAmount = DeductPaid::where('user_id', $value['id'])
                ->where('month', $month)
                ->where('year', $year)
                ->sum('paid');

            $sumary_times = TimeAttendance::where('month', $month)
                ->where('year', $year)
                ->where('employee_no', $value['user_no'])
                ->first();

            // try {


            $Item = Payroll::where('user_no', $value['user_no'])
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->first();

            if (!$Item) {
                $Item = new Payroll();
            }

            $Item->user_no = $value['user_no'];
            $Item->total_income = $totalIncomeAmount ? $totalIncomeAmount : 0;
            $Item->total_deduct = $totalDeductAmount ? $totalDeductAmount : 0;
            $Item->total_ot = $sumary_times ? $sumary_times->sum_o_t : 0;
            $Item->total_late_deduct = $sumary_times ? $sumary_times->sum_late : 0;
            $Item->salary = $value['salary'] ? $value['salary'] : 0;
            $Item->total_summary = $value['salary'] ? $value['salary'] : 0;
            $Item->month = $request->month;
            $Item->year = $request->year;
            $Item->create_by = "Admin";
            $Item->updated_at = Carbon::now()->toDateTimeString();

            $Item->save();
            //

            //log
            $userId = "admin";
            $type = 'เพิ่มรายการ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $request->user_id;
            $this->Log($userId, $description, $type);
            //

            // DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
            // } catch (\Throwable $e) {


            //     DB::rollback();

            //     return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
            // }
        }
    }
}
