<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>New Migration Enquiry — Waiting</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f4; color:#1c2a3a; font-family:Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f4f4;">
  <tr>
    <td align="center" style="padding:24px 12px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">

        @include('emails.partials.appointment-branding-header')

        <tr>
          <td style="padding:8px 24px 18px 24px;">
            <p style="margin:0 0 10px 0; font-size:14px; line-height:1.7; color:#333;">A migration enquiry form was submitted and the contact has been added to the <strong>In Person → Waiting</strong> list.</p>
            <p style="margin:0 0 6px 0; font-size:14px; line-height:1.7; color:#333;"><strong>Name:</strong> {{ trim(($lead->first_name ?? '').' '.($lead->last_name ?? '')) ?: '—' }}</p>
            <p style="margin:0 0 6px 0; font-size:14px; line-height:1.7; color:#333;"><strong>Email:</strong> {{ $lead->email ?: '—' }}</p>
            <p style="margin:0 0 6px 0; font-size:14px; line-height:1.7; color:#333;"><strong>Phone:</strong> {{ $lead->phone ?: '—' }}</p>
            <p style="margin:0; font-size:14px; line-height:1.7; color:#333;"><strong>Visit purpose:</strong> {{ $visit->visit_purpose ?: 'Migration Client Enquiry' }}</p>
          </td>
        </tr>

        <tr>
          <td style="padding:0 24px 24px 24px;" align="center">
            <a href="{{ $waitingUrl }}" target="_blank" rel="noopener" style="display:inline-block; padding:11px 18px; background:#f5a623; color:#1c2a3a; font-size:14px; font-weight:bold; text-decoration:none; border-radius:8px;">Open Waiting list</a>
          </td>
        </tr>

        <tr>
          <td style="background:#1c2a3a; padding:16px 20px; text-align:center;">
            <p style="margin:0; font-size:11px; color:#8fa3b3;">&copy; {{ date('Y') }} Bansal Immigration. All rights reserved.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
