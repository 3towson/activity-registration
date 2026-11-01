<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>บัตรเข้าร่วม {{ $activity->code }}</title>
    <style>
        @font-face {
            font-family: 'Sarabun';
            font-weight: normal;
            src: url('{{ resource_path('fonts/Sarabun-Regular.ttf') }}');
        }

        @font-face {
            font-family: 'Sarabun';
            font-weight: bold;
            src: url('{{ resource_path('fonts/Sarabun-Bold.ttf') }}');
        }

        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 14pt;
        }

        .ticket {
            width: 100%;
            border: 2px solid #1d4ed8;
            border-collapse: collapse;
        }

        .ticket th {
            background: #1d4ed8;
            color: #fff;
            font-size: 18pt;
            padding: 8px;
        }

        .ticket td {
            padding: 4px 12px;
            vertical-align: top;
        }

        .label {
            width: 30%;
            font-weight: bold;
        }

        .tone {
            position: relative;
            top: -0.38em;
        }
    </style>
</head>

<body>
    @php
        // dompdf วางวรรณยุกต์ทับสระบน (ที่ เมื่อ วิ่ง) → ครอบวรรณยุกต์ด้วย span แล้วยกขึ้นด้วย .tone
        // e() ก่อนเสมอ เพราะผลลัพธ์แสดงด้วย {!! !!} ซึ่งไม่ escape ให้
        $thai = static fn(string $text): string => \preg_replace(
            '/([\x{0E31}\x{0E34}-\x{0E37}])([\x{0E48}-\x{0E4C}])/u',
            '$1<span class="tone">$2</span>',
            e($text),
        );
    @endphp

    <table class="ticket">
        <tr>
            <th colspan="2">บัตรเข้าร่วมกิจกรรม</th>
        </tr>
        <tr>
            <td class="label">{!! $thai('เลขที่บัตร') !!}</td>
            <td>{{ $activity->code }}-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="label">{!! $thai('ชื่อผู้เข้าร่วม') !!}</td>
            <td>{!! $thai($user->name) !!}</td>
        </tr>
        <tr>
            <td class="label">อีเมล</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="label">กิจกรรม</td>
            <td>[{{ $activity->code }}] {!! $thai($activity->name) !!}</td>
        </tr>
        <tr>
            <td class="label">หมวดหมู่</td>
            <td>{!! $thai($activity->category->name) !!}</td>
        </tr>
        <tr>
            <td class="label">ผู้จัด</td>
            <td>{!! $thai($activity->organizer->name) !!}</td>
        </tr>
        <tr>
            <td class="label">{!! $thai('วันที่จัด') !!}</td>
            <td>{{ $activity->date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">{!! $thai('สถานที่') !!}</td>
            <td>{!! $thai($activity->location) !!}</td>
        </tr>
        <tr>
            <td class="label">{!! $thai('สมัครเมื่อ') !!}</td>
            <td>{{ $registeredAt->format('d/m/Y H:i') }}</td>
        </tr>
    </table>
</body>

</html>
