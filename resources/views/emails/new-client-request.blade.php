<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <style>
    body {
        font-family: Arial, sans-serif;
    }
</style>
</head>
<body>
  <h1>New Client Request</h1>
  <p style="padding-top: 15px; padding-bottom: 15px">A client has submitted a new request.</p>
  <table>
    <tbody>
        <tr>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;"><strong>Client:</strong></td>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;">{{ $user->name }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;"><strong>Email:</strong></td>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;"><strong>Title:</strong></td>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;">{{ $clientRequest->title }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;"><strong>Needed At:</strong></td>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;">{{ date('D, F j, Y', strtotime($clientRequest->needed_at)) }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;"><strong>Created At:</strong></td>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;">{{ date('D, F j, Y', strtotime($clientRequest->created_at)) }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;"><strong>Remarks:</strong></td>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;">{!! $clientRequest->remarks !!}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;"><strong>Open Request:</strong></td>
            <td style="border-bottom: 1px solid #c0c0c0; padding: 15px 100px 15px 0;"><a href="{{ route('requests.view-request', ['client' => $user->id, 'clientRequest' => $clientRequest->id]) }}">Click here</a></td>
        </tr>
    </tbody>
</table>
<p style="margin-top: 10px; font-style: italic; color: gray;">This is a system generated email, please do not reply.</p>
</body>
</html>