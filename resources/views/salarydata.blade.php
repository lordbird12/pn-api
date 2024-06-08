
<table>
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
        <td style="text-align: left; width: 200px;">{{ $name }}</td>
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
        <th colspan="2" style="border: 1px solid black; color: red;" >รายการหัก</th>
        <th style="border: 1px solid black; color: red;">จำนวนเงิน</th>
        <th style="border: 1px solid black;">หมายเหตุ</th>
    </tr>
    @php
        $count = 0;
    @endphp
    {{-- @foreach($datasalary as $item)
    <tr>
        <td colspan="2" style="border: 1px solid black;">{{ $item['income'] }}</td>
        <td style="border: 1px solid black;">{{ $item['income_amount'] }}</td>
        <td colspan="2" style="border: 1px solid black;">{{ $item['deduction'] }}</td>
        <td style="border: 1px solid black;">{{ $item['deduction_amount'] }}</td>
        <td style="border: 1px solid black;">{{ $item['note'] }}</td>
    </tr>
    @endforeach --}}
    @foreach ($datasalary as $item)
        @php
            $count++
        @endphp
        <tr>
            <td colspan="2" style="border: 1px solid black;">เงือนนเดือน</td>
            <td style="border: 1px solid black;">100</td>
            <td colspan="2" style="border: 1px solid black; color: red;">หักสาย</td>
            <td style="border: 1px solid black; color: red;">10000000000</td>
            <td style="border: 1px solid black;"></td>
        </tr>
    @endforeach
    @for ($i = 0 ;$i < 9;$i++)
        <tr>
            <td colspan="2" style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td colspan="2" style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    @endfor
    <tr>
        <td colspan="2" rowspan="2" style="border: 1px solid black; text-align: right;">รวมรายการได้</td>
        <td rowspan="2" style="border: 1px solid black;">{{ $total_income }}</td>
        <td colspan="2" rowspan="2" style="border: 1px solid black; color: red; text-align: right;">รวมรายการหัก</td>
        <td rowspan="2" style="border: 1px solid black; color: red;">{{ $total_deduction }}</td>
        <td style="border: 1px solid black; text-align: center;">เงินได้สุทธิ</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: center;">{{ $net_income }}</td>
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
    <tr></tr>
    <tr>
        <td></td>
        <td colspan="2">ผู้อนุมัติจ่าย...................................................</td>
    </tr>
    <tr>
        <td></td>
        <td>(นางสาวนาตยา นราวัฒน์)</td>
    </tr>
</table>

