<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to CODECV</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:40px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,#6366f1 0%,#06b6d4 100%);border-radius:16px 16px 0 0;padding:36px 40px;text-align:center;">
              <img src="{{ $message->embed(public_path('images/codecv.png')) }}" alt="CODECV" width="160" style="display:block;margin:0 auto;" />
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="background:#ffffff;padding:40px 40px 32px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">

              <h1 style="margin:0 0 8px;font-size:26px;font-weight:800;color:#0f172a;letter-spacing:-0.02em;">
                Welcome, {{ $user->fullname }}! 🎉
              </h1>
              <p style="margin:0 0 28px;font-size:15px;color:#64748b;line-height:1.6;">
                Your account is ready. You're now part of a community of IT professionals accelerating their careers.
              </p>

              <!-- Divider -->
              <hr style="border:none;border-top:1px solid #e2e8f0;margin:0 0 28px;" />

              <!-- Features -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding-bottom:16px;">
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="width:36px;height:36px;background:#ede9fe;border-radius:8px;text-align:center;vertical-align:middle;font-size:18px;">📄</td>
                        <td style="padding-left:14px;vertical-align:middle;">
                          <span style="font-size:14px;font-weight:700;color:#0f172a;">CV Analyser</span><br/>
                          <span style="font-size:13px;color:#64748b;">Get AI-powered feedback on your CV vs real job postings</span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding-bottom:16px;">
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="width:36px;height:36px;background:#cffafe;border-radius:8px;text-align:center;vertical-align:middle;font-size:18px;">🗺️</td>
                        <td style="padding-left:14px;vertical-align:middle;">
                          <span style="font-size:14px;font-weight:700;color:#0f172a;">Learning Paths</span><br/>
                          <span style="font-size:13px;color:#64748b;">Follow a personalised roadmap curated for your goals</span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding-bottom:0;">
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="width:36px;height:36px;background:#dcfce7;border-radius:8px;text-align:center;vertical-align:middle;font-size:18px;">💼</td>
                        <td style="padding-left:14px;vertical-align:middle;">
                          <span style="font-size:14px;font-weight:700;color:#0f172a;">Job Board</span><br/>
                          <span style="font-size:13px;color:#64748b;">Explore curated IT job opportunities</span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- CTA Button -->
              <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;">
                <tr>
                  <td align="center">
                    <a href="{{ $dashboardUrl }}"
                       style="display:inline-block;background:linear-gradient(135deg,#6366f1 0%,#4f46e5 100%);color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;padding:14px 36px;border-radius:10px;letter-spacing:0.01em;">
                      Go to Your Dashboard →
                    </a>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f8fafc;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
              <p style="margin:0 0 6px;font-size:13px;color:#94a3b8;">
                Questions? Reply to this email — we're happy to help.
              </p>
              <p style="margin:0;font-size:12px;color:#cbd5e1;">
                © {{ date('Y') }} CODECV. All rights reserved.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
