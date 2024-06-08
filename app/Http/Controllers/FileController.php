<?php

namespace App\Http\Controllers;

use App\Exports\salaryExport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FileController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function excel_payslip(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m'));
        $Date = Carbon::createFromFormat('Y-m', $date);

        $users = User::whereYear('created_at', $Date->year)
                    ->whereMonth('created_at', $Date->month)
                    ->get();

        return $users;

        $result = new salaryExport($users);
        return Excel::download($result, 'saraly.xlsx');
    }
    public function pdf_payslip(Request $request)
    {   //ฟิวเตอร์ name
        $user_no = $request->user_no;
        $date = $request->date; // '2024-05'
        $Date = Carbon::createFromFormat('Y-m', $date);

        $users = User::where('user_no', $user_no)
                     ->whereYear('created_at', $Date->year)
                     ->whereMonth('created_at', $Date->month)
                     ->get();


        $content = '
            <table style="width: 100%; border-collapse: collapse; font-size: 20px;">
                <tr>
                    <td colspan="7" style="text-align: center">ห้างหุ้นส่วนจำกัด ส.สปีดออโต้ปากน้ำ (สำนักงานใหญ่)</td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: center">251/2 หมู่ที่ 11 ตำบลนาป่า อำเภอเมือง จังหวัดเพชรบูรณ์ 67000 โทร. 081-2393070</td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: center">เลขประจำตัวผู้เสียภาษีอากร 0-6735-63000-95-1</td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: center">ใบแจ้งรายได้ PAY SLIP</td>
                </tr>
                <tr>
                    <td style="text-align: left; width: 70px;">ชื่อพนักงาน</td>
                    <td style="text-align: left; width: 200px;">' . $data['name'] . '</td>
                    <td style="text-align: left; width: 65px;"></td>
                    <td style="text-align: left; width: 110px;"></td>
                    <td style="text-align: left; width: 70px;"></td>
                    <td style="text-align: left; width: 70px;">วิกที่</td>
                    <td style="text-align: left; width: 125px;">16-30/9/2566</td>
                </tr>
                <tr>
                    <td style="text-align: left;">ตำแหน่ง</td>
                    <td style="text-align: left;">ผู้บริหารร้าน</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: left;">วันที่สั่งจ่าย</td>
                    <td style="text-align: left;">9/30/2566</td>
                </tr>
                <tr>
                    <th colspan="2" style="border: 1px solid black;">รายได้</th>
                    <th style="border: 1px solid black;">จำนวนเงิน</th>
                    <th colspan="2" style="border: 1px solid black; color: red;">รายการหัก</th>
                    <th style="border: 1px solid black; color: red;">จำนวนเงิน</th>
                    <th style="border: 1px solid black;">หมายเหตุ</th>
                </tr>';

            foreach ($data['datasalary'] as $item) {
                $content .= '
                <tr>
                    <td colspan="2" style="border: 1px solid black;">' . $item['income'] . '</td>
                    <td style="border: 1px solid black;">' . $item['income_amount'] . '</td>
                    <td colspan="2" style="border: 1px solid black; color: red;">' . $item['deduction'] . '</td>
                    <td style="border: 1px solid black; color: red;">' . $item['deduction_amount'] . '</td>
                    <td style="border: 1px solid black;">' . $item['note'] . '</td>
                </tr>';
            }

            for ($i = 0; $i < 9; $i++) {
                $content .= '
                <tr>
                    <td colspan="2" style="border: 1px solid black;">&nbsp;</td>
                    <td style="border: 1px solid black;"></td>
                    <td colspan="2" style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                </tr>';
            }

            $content .= '
                <tr>
                    <td colspan="2" rowspan="2" style="border: 1px solid black; text-align: right;">รวมรายการได้</td>
                    <td rowspan="2" style="border: 1px solid black;">' . $data['total_income'] . '</td>
                    <td colspan="2" rowspan="2" style="border: 1px solid black; color: red; text-align: right;">รวมรายการหัก</td>
                    <td rowspan="2" style="border: 1px solid black; color: red;">' . $data['total_deduction'] . '</td>
                    <td style="border: 1px solid black; text-align: center;">เงินได้สุทธิ</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">' . $data['net_income'] . '</td>
                </tr>
                <tr></tr>
                <tr>
                    <td></td>
                    <td colspan="2">ผู้บันทึก..........................................................</td>
                    <td></td>
                    <td colspan="2">ผู้รับเงิน..............................................................</td>
                </tr>
                <tr>
                    <td></td>
                    <td>(นางสาวอริษา แสนในเมือง)</td>
                    <td></td>
                    <td></td>
                    <td>(นางสาวนาตยา นราวัฒน์)</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">ผู้อนุมัติจ่าย...................................................</td>
                </tr>
                <tr>
                    <td></td>
                    <td>(นางสาวนาตยา นราวัฒน์)</td>
                </tr>
            </table>';

        //PDF
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path() . '/custom/font/directory',
            ]),
            'fontdata' => $fontData + [ // lowercase letters only in font key
                'th-sarabun-it' => [
                    'R' => 'THSarabunIT๙.ttf',
                    'I' => 'THSarabunIT๙ Italic.ttf',
                    'B' => 'THSarabunIT๙ Bold.ttf',
                    'BI' => 'THSarabunIT๙ BoldItalic.ttf',
                ], 'th-sarabun' => [
                    'R' => 'THSarabun.ttf',
                    'I' => 'THSarabun Italic.ttf',
                    'B' => 'THSarabun Bold.ttf',
                    'BI' => 'THSarabun BoldItalic.ttf',
                ],
            ],
            'default_font' => 'th-sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            // 'default_font_size' => 12,
            // 'default_font' => 'sarabun',
            // 'margin_left' => 5,
            // 'margin_right' => 5,
            // 'margin_top' => 5,
            // 'margin_bottom' => 5,
            // 'margin_header' => 5,
            // 'margin_footer' => 5,
        ]);
        $mpdf->SetTitle('เงินเดือน');
        $mpdf->AddPage();
        $mpdf->WriteHTML($content);
        $mpdf->Output('เงินเดือน.pdf', 'I');
    }
}
