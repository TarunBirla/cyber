<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Company Registration Alert</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 40px 20px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
        <!-- Header -->
        <tr>
            <td style="padding: 30px 36px; background-color: #0f172a; color: #ffffff;">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #38bdf8; margin-bottom: 6px;">Lead Auditor Notification</div>
                <h1 style="font-size: 22px; font-weight: 800; margin: 0; color: #ffffff;">New UK Company Registered</h1>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 36px;">
                <p style="font-size: 14px; color: #475569; line-height: 1.6; margin-top: 0;">
                    Hello <strong>Mohammed Nasar</strong>,
                </p>
                <p style="font-size: 14px; color: #334155; line-height: 1.6;">
                    A new company has registered on the <strong>Cyber Essentials Information Gathering Portal</strong>. Their workbook is now in <em>Draft</em> status awaiting completion.
                </p>

                <!-- Company Details Card -->
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0;">
                    <table style="width: 100%; font-size: 13px;">
                        <tr>
                            <td style="padding: 6px 0; color: #64748b; width: 140px;">Company Name:</td>
                            <td style="padding: 6px 0; font-weight: 700; color: #0f172a;">{{ $companyUser->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Registered Email:</td>
                            <td style="padding: 6px 0; font-family: monospace; color: #0284c7;">{{ $companyUser->email }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Phone:</td>
                            <td style="padding: 6px 0; color: #334155;">{{ $companyUser->phone ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Registration Date:</td>
                            <td style="padding: 6px 0; color: #334155;">{{ now()->toDayDateTimeString() }}</td>
                        </tr>
                    </table>
                </div>

                <div style="text-align: center; margin: 28px 0;">
                    <a href="https://cyber.nexteck.uk/admin/dashboard" style="display: inline-block; background-color: #0f172a; color: #ffffff; padding: 12px 28px; font-size: 13px; font-weight: 700; border-radius: 8px; text-decoration: none;">
                        Open Admin Command Center &rarr;
                    </a>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 16px 36px; background-color: #f1f5f9; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
                Nexteck Limited • Cyber Essentials Automated Notification
            </td>
        </tr>
    </table>
</body>
</html>
