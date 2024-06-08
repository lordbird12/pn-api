<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Order;
use App\Models\OrderImages;
use App\Models\ProductImages;
use App\Models\Products;
use App\Models\AudItem;
use App\Models\Channel;
use App\Models\ClearIron;
use App\Models\ComplatePrint;
use App\Models\Floor;
use App\Models\Iron;
use App\Models\Shelf;
use Facade\FlareClient\Http\Client;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function getList()
    {
        $Item = Order::get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
                $Item[$i]['client'] = Clients::find($Item[$i]['client_id']);
                $Item[$i]['product'] = Products::find($Item[$i]['product_id']);
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


        $col = array('id', 'date', 'qty', 'name', 'status', 'or_no', 'product_id', 'client_id', 'client_name', 'user_id', 'remark', 'year', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'date', 'qty', 'name', 'status', 'or_no', 'product_id', 'client_id', 'client_name', 'user_id', 'remark', 'year', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = Order::select($col);


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
                $d[$i]->product = Products::find($d[$i]->product_id);
                $d[$i]->clear = ClearIron::where('order_id', $d[$i]->id)->first();
                $d[$i]->aud = AudItem::where('order_id', $d[$i]->id)->first();
                $d[$i]->iron = Iron::where('order_id', $d[$i]->id)->first();
                $d[$i]->completes = ComplatePrint::where('order_id', $d[$i]->id)->get();

                $d[$i]->product->shelf = Shelf::find($d[$i]->product->shelve_id);
                $d[$i]->product->floor = Floor::find($d[$i]->product->floor_id);
                $d[$i]->product->channel = Channel::find($d[$i]->product->channel_id);


                foreach ($d[$i]->completes as $key => $value) {
                    $d[$i]->total_complete += intval($value['good']);
                }
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

        if (!isset($request->product_id)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลสินค้าให้เรียบร้อย', 404);
        } else if (!isset($request->client_id)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลลูกค้าให้เรียบร้อย', 404);
        } else if (!isset($request->user_id)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลผู้ดูแลให้เรียบร้อย', 404);
        } else if (!isset($request->date)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลวันที่ให้เรียบร้อย', 404);
        } else if ($request->date == "Invalid date") {
            return $this->returnErrorData('กรุณาระบุข้อมูลวันที่ให้เรียบร้อย', 404);
        } else

        $product = Products::find($request->product_id);

        if(!$product){
            return $this->returnErrorData('กรุณาเลือกแม่พิมพ์จากในระบบ', 404);
        }

            DB::beginTransaction();

        try {
            $prefix = "#OR-";
            $id = IdGenerator::generate(['table' => 'orders', 'field' => 'or_no', 'length' => 9, 'prefix' => $prefix]);

            $Item = new Order();
            $Item->or_no = $id;
            $Item->date = $request->date;
            $Item->qty = $request->qty;
            $Item->product_id = $request->product_id;
            $Item->client_id = $request->client_id;
            $Item->user_id = $request->user_id;
            $Item->year = $request->year;
            $Item->remark = $request->remark;
            $Item->name = $request->name;

            $client = Clients::find($request->client_id);

            if(!$client){
                return $this->returnErrorData('กรุณาเลือกรายชื่อลูกค้าจากในระบบ', 404);
            }
            $Item->client_name = $client->name;

            $Item->save();


            if ($request->hasFile('images')) {
                $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
                $files = $request->file('images');
                $errors = [];

                foreach ($files as $file) {

                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();

                        $check = in_array($extension, $allowedfileExtension);

                        if ($check) {
                            $Files = new OrderImages();
                            $Files->order_id =  $Item->id;
                            $Files->image = $this->uploadImage($file, '/images/orders/');
                            $Files->save();
                        }
                    }
                }
            }


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
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Order::where('id', $id)
            ->first();

        if ($Item) {
            $Item->client = Clients::find($Item->client_id);
            $Item->product = Products::find($Item->product_id);

            $Item->shelf = Shelf::find($Item->product->shelve_id);
            $Item->floor = Floor::find($Item->product->floor_id);
            $Item->channel = Channel::find($Item->product->channel_id);

            $Item->iron = Iron::where('order_id', $Item->id)->first();
            $Item->clear_iron = ClearIron::where('order_id', $Item->id)->first();
            $Item->aud_item = AudItem::where('order_id', $Item->id)->first();
            $Item->complate_print = ComplatePrint::where('order_id', $Item->id)->get();


            $Item->images = OrderImages::where('order_id', $Item->id)->get();

            for ($n = 0; $n <= count($Item->images) - 1; $n++) {
                $Item->images[$n]->image = url($Item->images[$n]->image);
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loginBy = $request->login_by;

        if (!isset($id)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลให้เรียบร้อย', 404);
        } else  if (!isset($request->product_id)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลสินค้าให้เรียบร้อย', 404);
        } else if (!isset($request->client_id)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลลูกค้าให้เรียบร้อย', 404);
        } else if (!isset($request->user_id)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลผู้ดูแลให้เรียบร้อย', 404);
        } else if (!isset($request->date)) {
            return $this->returnErrorData('กรุณาระบุข้อมูลวันที่ให้เรียบร้อย', 404);
        } else if ($request->date == "Invalid date") {
            return $this->returnErrorData('กรุณาระบุข้อมูลวันที่ให้เรียบร้อย', 404);
        } else

            DB::beginTransaction();

        try {
            $Item = Order::find($id);
            $Item->product_id = $request->product_id;
            $Item->client_id = $request->client_id;
            $Item->user_id = $request->user_id;
            $Item->year = $request->year;
            $Item->remark = $request->remark;
            $Item->date = $request->date;
            $Item->qty = $request->qty;
            $Item->name = $request->name;
            $client = Clients::find($request->client_id);
            $Item->client_name = $client->name;
            $Item->save();

            if ($request->hasFile('images')) {
                $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
                $files = $request->file('images');
                $errors = [];

                foreach ($files as $file) {

                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();

                        $check = in_array($extension, $allowedfileExtension);

                        if ($check) {
                            $Files = new OrderImages();
                            $Files->order_id =  $Item->id;
                            $Files->image = $this->uploadImage($file, '/images/orders/');
                            $Files->save();
                        }
                    }
                }
            }
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


    public function updateData(Request $request)
    {

        DB::beginTransaction();

        try {
            $Item = Order::find($request->id);

            if (!$Item) {
                return $this->returnErrorData('ไม่พบรายการนี้ในระบบ', 404);
            }
            $Item->product_id = $request->product_id;
            $Item->client_id = $request->client_id;
            $Item->user_id = $request->user_id;
            $Item->year = $request->year;
            $Item->remark = $request->remark;
            $Item->status = $request->status;
            $Item->date = $request->date;
            $Item->name = $request->name;
            $Item->qty = $request->qty;

            $Item->save();

            if ($request->hasFile('images')) {
                $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
                $files = $request->file('images');
                $errors = [];

                foreach ($files as $file) {

                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();

                        $check = in_array($extension, $allowedfileExtension);

                        if ($check) {
                            $Files = new OrderImages();
                            $Files->order_id =  $Item->id;
                            $Files->image = $this->uploadImage($file, '/images/orders/');
                            $Files->save();
                        }
                    }
                }
            }

            //log
            $userId = "admin";
            $type = 'แก้ไข';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการเพิ่ม ' . $request->username;
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Order::find($id);
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

    public function delete_order_image($id)
    {
        DB::beginTransaction();

        try {

            $Item = OrderImages::find($id);
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
