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
    </style>
</head>

<body>
    <table class="ticket">
        <tr>
            <th colspan="2">บัตรเข้าร่วมกิจกรรม</th>
        </tr>
        <tr>
            <td class="label">เลขที่บัตร</td>
            <td>{{ $activity->code }}-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="label">ชื่อผู้เข้าร่วม</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="label">อีเมล</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="label">กิจกรรม</td>
            <td>[{{ $activity->code }}] {{ $activity->name }}</td>
        </tr>
        <tr>
            <td class="label">หมวดหมู่</td>
            <td>{{ $activity->category->name }}</td>
        </tr>
        <tr>
            <td class="label">ผู้จัด</td>
            <td>{{ $activity->organizer->name }}</td>
        </tr>
        <tr>
            <td class="label">วันที่จัด</td>
            <td>{{ $activity->date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">สถานที่</td>
            <td>{{ $activity->location }}</td>
        </tr>
        <tr>
            <td class="label">สมัครเมื่อ</td>
            <td>{{ $registeredAt->format('d/m/Y H:i') }}</td>
        </tr>
    </table>
</body>

</html>
