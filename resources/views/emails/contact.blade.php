<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 20px; color: #333;">

    <div style="max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; padding: 24px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
        <h1 style="font-size: 24px; margin-bottom: 24px; color: #111;">📩 New Contact Message</h1>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="font-weight: bold; padding: 8px 0;">👤 Name:</td>
                <td style="padding: 8px 0;">{{ $data['name'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 8px 0;">📧 Email:</td>
                <td style="padding: 8px 0;">{{ $data['email'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top; padding: 8px 0;">💬 Message:</td>
                <td style="padding: 8px 0; white-space: pre-line;">{{ $data['message'] }}</td>
            </tr>
        </table>

        <p style="margin-top: 32px; font-size: 14px; color: #666;">
            You received this message from the contact form on your website.
        </p>
    </div>

</body>
</html>
