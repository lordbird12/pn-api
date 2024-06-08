<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use App\Models\Channel;
use App\Models\Floor;
use App\Models\ProductImages;
use App\Models\Products;
use App\Models\Shelf;
use App\Models\Clients;
use App\Models\SubCategoryProduct;
use App\Models\Area;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\CC;
use App\Models\Color;
use App\Models\Company;
use App\Models\ProductRaw;
use App\Models\StockTrans;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function getList($id)
    {
        $Item = Products::where('brand_model_id', $id)->get();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
                $Item[$i]['images'] = ProductImages::where('product_id', $Item[$i]['id'])->get();
                if ($Item[$i]['image']) {
                    $Item[$i]['image'] = url($Item[$i]['image']);
                }
                for ($n = 0; $n <= count($Item[$i]['images']) - 1; $n++) {
                    $Item[$i]['images'][$n]['image'] = url($Item[$i]['images'][$n]['image']);
                }
                $Item[$i]['brand'] = Brand::find($Item[$i]['brand_id']);
                $Item[$i]['brand_model'] = BrandModel::find($Item[$i]['brand_model_id']);
                $Item[$i]['supplier'] = Supplier::find($Item[$i]['supplier_id']);
                $Item[$i]['color'] = Color::find($Item[$i]['color_id']);
                $Item[$i]['cc'] = CC::find($Item[$i]['cc_id']);

                $Item[$i]['type'] = $Item[$i]['type'] == "First" ? "มือหนึ่ง" : "มือสอง";
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

        $area_id = $request->area_id;
        $type = $request->type;
        $status = $request->status;
        $brand_id = $request->brand_id;


        $col = array('id', 'status', 'mile', 'image', 'type', 'category_product_id', 'area_id', 'brand_id', 'brand_model_id', 'cc_id', 'color_id', 'name', 'detail', 'code', 'tank_no', 'engine_no', 'license_plate', 'year', 'sale_status', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('id', 'status', 'mile', 'image', 'type', 'category_product_id', 'area_id', 'brand_id', 'brand_model_id', 'cc_id', 'color_id', 'name', 'detail', 'code', 'tank_no', 'engine_no', 'license_plate', 'year', 'sale_status', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = Products::select($col);
        // $D->where('sale_status', 'N');

        if ($area_id) {
            $D->where('area_id', $area_id);
        }

        if ($type) {
            $D->where('type', $type);
        }

        if ($status) {
            $D->where('status', $status);
        }

        if ($brand_id) {
            $D->where('brand_id', $brand_id);
        }

        if (empty($order) || !isset($orderby[$order[0]['column']])) {
            $D->orderBy('created_at', 'desc');
        } else {
            $D->orderBy($orderby[$order[0]['column']], $order[0]['dir']);
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
                $d[$i]->image = url($d[$i]->image);
                $d[$i]->category_product = CategoryProduct::find($d[$i]->category_product_id);

                $d[$i]->images = ProductImages::where('product_id', $d[$i]->id)->get();

                $d[$i]->area = Area::find($d[$i]->area_id);
                if ($d[$i]->area) {
                    if ($d[$i]->area->image) {
                        $d[$i]->area->image = url($d[$i]->area->image);
                    }
                    $d[$i]->companie = Company::find($d[$i]->area->companie_id);
                }


                for ($n = 0; $n <= count($d[$i]->images) - 1; $n++) {
                    $d[$i]->images[$n]->image = url($d[$i]->images[$n]->image);
                }

                $d[$i]->brand = Brand::find($d[$i]->brand_id);
                $d[$i]->brand_model = BrandModel::find($d[$i]->brand_model_id);
                $d[$i]->cc = CC::find($d[$i]->cc_id);
                $d[$i]->color = Color::find($d[$i]->color_id);

                if ($d[$i]->type == "First") {
                    $d[$i]->type = "มือหนึ่ง";
                } else if ($d[$i]->type == "Secound") {
                    $d[$i]->type = "มือสอง";
                }


                if ($d[$i]->status == "sold") {
                    $d[$i]->status = "ขายแล้ว";
                } else if ($d[$i]->status == "free") {
                    $d[$i]->status = "ว่างอยู่";
                } else if ($d[$i]->status == "book") {
                    $d[$i]->status = "จองแล้ว";
                }
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
    }


    public function getByCode($code)
    {
        $Item = Products::where('code', $code)->first();
        if (!empty($Item)) {
            $Item['No'] = 1;
            $Item['image'] = url($Item['image']);
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
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

        if (!isset($request->category_product_id)) {
            return $this->returnErrorData('[category_product_id] Data Not Found', 404);
        }

        $check1 = CategoryProduct::find($request->category_product_id);
        if (!$check1) {
            return $this->returnErrorData('ไม่พบข้อมูล category_product_id ในระบบ', 404);
        }


        $check3 = Products::where('code', $request->code)->first();
        if ($check3) {
            return $this->returnErrorData('มี code ในระบบอยู่แล้ว', 404);
        }

        DB::beginTransaction();

        try {

            $prefix = "#" . $check1->prefix . "-";
            $id = IdGenerator::generate(['table' => 'products', 'field' => 'code', 'length' => 13, 'prefix' => $prefix]);

            $Item = new Products();
            $Item->code = $id;
            $Item->category_product_id = $request->category_product_id;
            $Item->pr_no = $request->pr_no;
            $Item->area_id = $request->area_id;
            $Item->brand_id = $request->brand_id;
            $Item->brand_model_id = $request->brand_model_id;
            $Item->cc_id = $request->cc_id;
            $Item->color_id = $request->color_id;
            $Item->name = $request->name;
            $Item->detail = $request->detail;
            $Item->tank_no = $request->tank_no;
            $Item->engine_no = $request->engine_no;
            $Item->license_plate = $request->license_plate;
            $Item->year = $request->year;
            $Item->sale_price = $request->sale_price;
            $Item->cost = $request->cost;
            $Item->type = $request->type;
            $Item->supplier_id = $request->supplier_id;
            $Item->mile = $request->mile;

            if ($request->image && $request->image != null && $request->image != 'null') {
                $Item->image = $this->uploadImage($request->image, '/images/product/');
            }

            $Item->save();

            $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
            $files = $request->file('images');
            $errors = [];

            if ($files) {

                foreach ($files as $file) {

                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();

                        $check = in_array($extension, $allowedfileExtension);

                        if ($check) {
                            $Files = new ProductImages();
                            $Files->product_id =  $Item->id;
                            $Files->image = $this->uploadImage($file, '/images/products/');
                            $Files->save();
                        }
                    }
                }
            }


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
     * @param  \App\Models\Products  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Products::where('id', $id)
            ->first();

        if ($Item) {
            $Item->area = Area::find($Item->area_id);
            $Item->area->image = url($Item->area->image);
            $Item->supplier = Supplier::find($Item->supplier_id);

            $Item->images = ProductImages::where('product_id', $Item->id)->get();

            for ($n = 0; $n <= count($Item->images) - 1; $n++) {
                $Item->images[$n]->image = url($Item->images[$n]->image);
            }

            $Item->category_product = CategoryProduct::find($Item->category_product_id);
            $Item->sub_category_product = SubCategoryProduct::find($Item->sub_category_product_id);
            // $Item->raws = ProductRaw::where('product_id', $Item->id)->get();

            // for ($n = 0; $n <= count($Item->raws) - 1; $n++) {
            //     $Item->raws[$n]->product = Products::find($Item->raws[$n]->raw_id);
            // }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Products  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Products $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Products  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Products $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Products::find($id);
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

    public function updateData(Request $request)
    {
        if (!isset($request->category_product_id)) {
            return $this->returnErrorData('[category_product_id] Data Not Found', 404);
        }

        $check = CategoryProduct::find($request->category_product_id);
        if (!$check) {
            return $this->returnErrorData('ไม่พบข้อมูล category_product_id ในระบบ', 404);
        }

        $check = SubCategoryProduct::find($request->sub_category_product_id);
        if (!$check) {
            return $this->returnErrorData('ไม่พบข้อมูล sub_category_product_id ในระบบ', 404);
        }

        DB::beginTransaction();

        try {
            $Item = Products::find($request->id);

            if (!$Item) {
                return $this->returnErrorData('ไม่พบรายการนี้ในระบบ', 404);
            }
            $Item->category_product_id = $request->category_product_id;
            $Item->sub_category_product_id = $request->sub_category_product_id;
            $Item->area_id = $request->area_id;
            $Item->shelve_id = $request->shelve_id;
            $Item->floor_id = $request->floor_id;
            $Item->channel_id = $request->channel_id;
            $Item->name = $request->name;
            $Item->detail = $request->detail;
            $Item->qty = $request->qty;
            $Item->sale_price = $request->sale_price;
            $Item->cost = $request->cost;
            $Item->type = $request->type;
            $Item->min = $request->min;
            $Item->max = $request->max;
            $Item->supplier_id = $request->supplier_id;
            $Item->mile = $request->mile;
            $Item->save();

            $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
            $files = $request->file('images');
            $errors = [];
            if ($files) {
                foreach ($files as $file) {

                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();

                        $check = in_array($extension, $allowedfileExtension);

                        if ($check) {
                            $Files = new ProductImages();
                            $Files->product_id =  $Item->id;
                            $Files->image = $this->uploadImage($file, '/images/products/');
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
}
