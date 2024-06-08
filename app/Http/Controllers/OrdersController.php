<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\CC;
use App\Models\Clients;
use App\Models\Color;
use App\Models\Factory;
use App\Models\Finance;
use App\Models\OrderList;
use App\Models\Orders;
use App\Models\PaymentPeriod;
use App\Models\Products;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function getList()
    {
        $Item = Orders::get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
                $Item[$i]['items'] = OrderList::where('order_id',$Item[$i]['id'])->get();
                foreach ($Item[$i]['items'] as $key => $value) {
                    $Item[$i]['items'][$key]['product'] = Products::find($value['product_id']);
                }
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    public function getPage(Request $request)
    {
        $columns = $request->columns;
        $length = $request->length;
        $order = $request->order;
        $search = $request->search;
        $start = $request->start;
        $page = $start / $length + 1;


        $col = array('id', 'code', 'date', 'client_id', 'total_price', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'code', 'date', 'client_id', 'total_price', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = Orders::select($col);


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
                $d[$i]->client = Clients::find($d[$i]->client_id);
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
        $loginBy = $request->login_by;

        DB::beginTransaction();

        try {
            $prefix = "#OR-";
            $id = IdGenerator::generate(['table' => 'orders', 'field' => 'code', 'length' => 9, 'prefix' => $prefix]);

            $Item = new Orders();
            $Item->code = $id;
            $Item->date = $request->date;
            $Item->client_id = $request->client_id;
            $Item->total_price = $request->total_price;
            $Item->down_payment = $request->down_payment;
            $Item->finance_id = $request->finance_id;
            $Item->interest = $request->interest;
            $Item->payment_period = $request->payment_period;
            $Item->sale_type = $request->sale_type;
            $Item->payment_date = $request->payment_date;
            $Item->warranty = $request->warranty;

            $Item->save();


            $ItemL = new OrderList();
            $ItemL->order_id = $Item->id;
            $ItemL->product_id = $request->product['product_id'];
            $ItemL->cost = $request->product['cost'];
            $ItemL->price = $request->product['price'];
            $ItemL->save();


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
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Orders::where('id', $id)
            ->first();

        if ($Item) {
            $Item->client = Clients::find($Item->client_id);
            $Item->finance = Finance::find($Item->finance_id);
            $Item->orders = OrderList::where('order_id', $id)->first();
            $Item->orders->product = Products::find($Item->orders->product_id);

            $Item->orders->area = Area::find($Item->orders->product->area_id);
            $Item->orders->brand = Brand::find($Item->orders->product->brand_id);
            $Item->orders->brand_model = BrandModel::find($Item->orders->product->brand_model_id);
            $Item->orders->cc = CC::find($Item->orders->product->cc_id);
            $Item->orders->color = Color::find($Item->orders->product->color_id);

            $Item->orders->payments = PaymentPeriod::where('order_id',$id)->get();
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Orders::find($id);
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
