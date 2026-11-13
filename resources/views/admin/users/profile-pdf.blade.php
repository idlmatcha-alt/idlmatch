<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h1 {
            font-size: 20px;
            margin: 0 0 6px;
        }
        h2 {
            font-size: 14px;
            margin: 18px 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }
        .meta {
            margin-bottom: 10px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            vertical-align: top;
            text-align: left;
        }
        th {
            width: 35%;
            background: #f9fafb;
            font-weight: 700;
        }
        .empty {
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>User Profile Export</h1>
    <div class="meta">
        User ID: {{ $user->id }} |
        Email: {{ $user->email }} |
        Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}
    </div>

    <h2>User Fields</h2>
    <table>
        <tbody>
            @foreach($userFields as $row)
                <tr>
                    <th>{{ $row['label'] }}</th>
                    <td>{{ $row['value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Profile Fields</h2>
    @if($profile)
        <table>
            <tbody>
                @foreach($profileFields as $row)
                    <tr>
                        <th>{{ $row['label'] }}</th>
                        <td>{{ $row['value'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty">No profile found.</p>
    @endif

    <h2>Partner Preference Fields</h2>
    @if($preferences)
        <table>
            <tbody>
                @foreach($preferenceFields as $row)
                    <tr>
                        <th>{{ $row['label'] }}</th>
                        <td>{{ $row['value'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty">No preferences found.</p>
    @endif

    <h2>Uploaded Photos</h2>
    @if(count($photoPaths) > 0)
        <table>
            <tbody>
                @foreach($photoPaths as $index => $path)
                    <tr>
                        <th>Photo {{ $index + 1 }}</th>
                        <td>{{ $path }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty">No photos uploaded.</p>
    @endif
</body>
</html>

