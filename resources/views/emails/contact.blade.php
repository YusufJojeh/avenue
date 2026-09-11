<!doctype html>
<html>
<body style="font-family: sans-serif; color: #1b1b1f; line-height: 1.6;">
    <h2 style="margin-bottom: 4px;">New message from the contact form</h2>
    <p style="color: #5b6270; margin-top: 0;">{{ config('app.name') }}</p>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="font-weight: 600;">Name</td>
            <td>{{ $data['name'] }}</td>
        </tr>
        <tr>
            <td style="font-weight: 600;">Email</td>
            <td>{{ $data['email'] }}</td>
        </tr>
        <tr>
            <td style="font-weight: 600;">Subject</td>
            <td>{{ $data['subject'] }}</td>
        </tr>
    </table>

    <p style="font-weight: 600; margin-bottom: 4px;">Message</p>
    <p style="white-space: pre-line;">{{ $data['message'] }}</p>
</body>
</html>
