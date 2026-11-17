<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Cyber Essentials Portal</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 40px 20px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0;">
        <!-- Header -->
        <tr>
            <td style="padding: 36px 36px 24px 36px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%); color: #ffffff;">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #bae6fd; margin-bottom: 6px;">Nexteck Limited • UK Cyber Essentials</div>
                <h1 style="font-size: 24px; font-weight: 800; margin: 0; color: #ffffff; letter-spacing: -0.02em;">Welcome to Cyber Essentials Portal</h1>
                <p style="margin: 8px 0 0 0; font-size: 14px; color: #e0f2fe;">Information Gathering & Scope Inventory System</p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 36px;">
                <p style="font-size: 15px; color: #334155; line-height: 1.6; margin-top: 0;">
                    Hello <strong>{{ $user->name }}</strong>,
                </p>
                <p style="font-size: 14px; color: #475569; line-height: 1.6;">
                    Thank you for registering your company on the official <strong>Cyber Essentials Information Gathering Portal</strong>. Your account has been initialized and your 9-tab compliance workbook is ready to be populated.
                </p>

                <!-- Credentials Card -->
                <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin: 24px 0;">
                    <div style="font-size: 12px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                        🔐 Your Portal Access Credentials
                    </div>
                    <table style="width: 100%; font-size: 14px;">
                        <tr>
                            <td style="padding: 6px 0; color: #64748b; width: 110px;">Portal URL:</td>
                            <td style="padding: 6px 0;"><a href="{{ $portalUrl }}" style="color: #0284c7; font-weight: 600; text-decoration: none;">{{ $portalUrl }}</a></td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Login Email:</td>
                            <td style="padding: 6px 0; font-family: monospace; font-weight: 600; color: #0f172a;">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Password:</td>
                            <td style="padding: 6px 0; font-family: monospace; font-weight: 700; color: #15803d;">{{ $rawPassword }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Call to action button -->
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ $portalUrl }}" style="display: inline-block; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%); color: #ffffff; padding: 14px 32px; font-size: 14px; font-weight: 700; border-radius: 10px; text-decoration: none; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);">
                        Access Your Cyber Essentials Workbook &rarr;
                    </a>
                </div>

                <p style="font-size: 13px; color: #64748b; line-height: 1.6; border-top: 1px solid #e2e8f0; padding-top: 20px; margin-bottom: 0;">
                    <strong>Next Steps:</strong> Complete Tabs 1 through 9 (Company Details, Staff, Computers, Mobiles, Servers, Network, Cloud Services, Software, and IT Governance). Once finished, submit your workbook for audit review.
                </p>
            </td>
        </tr>

        <!-- Lead Auditor / Support Signature -->
        <tr>
            <td style="padding: 0 36px 36px 36px;">
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; font-size: 13px; color: #334155;">
                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 2px;">Mohammed Nasar</div>
                    <div style="color: #64748b; font-size: 12px;">CEO & Enterprise Architect</div>
                    <div style="color: #0284c7; font-weight: 600; margin-top: 4px;">Nexteck Limited</div>
                    <div style="color: #64748b; font-size: 12px; margin-top: 2px;">
                        London, UK • Tel: 07879 175585 • Email: <a href="mailto:info@nexteck.co.uk" style="color: #0284c7; text-decoration: none;">info@nexteck.co.uk</a>
                    </div>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 20px 36px; background-color: #f1f5f9; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
                Nexteck Limited • Registered in England & Wales • Cyber Essentials Assurance Team
            </td>
        </tr>
    </table>
</body>
</html>
