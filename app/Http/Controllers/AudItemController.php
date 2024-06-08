<?php

namespace App\Http\Controllers;

use App\Models\AudItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AudItemController extends Controller
{
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
        $loginBy = $request->login_by;

     

            DB::beginTransaction();

        try {
            $Item = AudItem::where('order_id', $request->order_id)->first();
            if (!$Item) {
                $Item = new AudItem();
            }

            
            $Item->order_id = $request->order_id;
            $Item->machine = $request->machine;
            $Item->yang_1 = $request->yang_1;
            $Item->yang_2 = $request->yang_2;
            $Item->hot_1 = $request->hot_1;
            $Item->hot_2 = $request->hot_2;
            $Item->time = $request->time;
            $Item->qty = $request->qty;
            $Item->weight = $request->weight;
            $Item->wang_yang_1 = $request->wang_yang_1;
            $Item->wang_yang_2 = $request->wang_yang_2;
            $Item->wang_yang_3 = $request->wang_yang_3;
            $Item->wang_yang_4 = $request->wang_yang_4;
            $Item->wang_yang_5 = $request->wang_yang_5;
            $Item->wang_yang_6 = $request->wang_yang_6;
            $Item->wang_yang_7 = $request->wang_yang_7;
            $Item->wang_yang_8 = $request->wang_yang_8;
            $Item->wang_yang_9 = $request->wang_yang_9;
            $Item->wang_yang_10 = $request->wang_yang_10;
            $Item->wang_son_1 = $request->wang_son_1;
            $Item->wang_son_2 = $request->wang_son_2;
            $Item->wang_son_3 = $request->wang_son_3;
            $Item->wang_son_4 = $request->wang_son_4;
            $Item->wang_son_5 = $request->wang_son_5;
            $Item->wang_son_6 = $request->wang_son_6;
            $Item->wang_son_7 = $request->wang_son_7;
            $Item->wang_son_8 = $request->wang_son_8;
            $Item->wang_son_9 = $request->wang_son_9;
            $Item->wang_son_10 = $request->wang_son_10;
            $Item->lock_roll = $request->lock_roll;

            $Item->save();
            //

            //log
            $userId = "admin";
            $type = 'เพิ่มรายการ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $request->name;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AudItem  $audItem
     * @return \Illuminate\Http\Response
     */
    public function show(AudItem $audItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AudItem  $audItem
     * @return \Illuminate\Http\Response
     */
    public function edit(AudItem $audItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AudItem  $audItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AudItem $audItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AudItem  $audItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = AudItem::find($id);
            $Item->delete();

            //log
            $userId = "admin";
            $type = 'ลบผู้ใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnUpdate('ดำเนินการสำเร็จ');
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }
}
