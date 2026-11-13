<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Find Your Perfect Life Partner — IDLMatch.com</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f1f2;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        Matches across Pakistan, India, Russia and Thailand — verified profiles and family-first matchmaking.
    </div>

    @php
        $posterSrc = isset($message)
            ? $message->embed($posterPath)
            : ($posterUrl ?? url('storage/newsletter/idlmatch-poster.jpg'));
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f1f2;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="width:100%;max-width:640px;background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#9d174d;padding:18px 28px;">
                            <span style="display:inline-block;font-family:Georgia,'Times New Roman',serif;font-size:19px;font-weight:bold;color:#ffffff;letter-spacing:0.5px;">
                                Ideal Marriage Center
                            </span>
                            <span style="display:inline-block;font-family:Arial,sans-serif;font-size:11px;color:#fbcfe8;letter-spacing:1.5px;text-transform:uppercase;margin-left:10px;">
                                IDLMatch.com
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 28px 10px 28px;font-family:Georgia,'Times New Roman',serif;color:#1f2937;">
                            <h1 style="margin:0;font-size:26px;line-height:1.25;">Find Your Perfect Life Partner</h1>
                            <p style="margin:10px 0 0 0;font-family:Arial,sans-serif;font-size:15px;line-height:1.6;color:#4b5563;">
                                Serious, family-first matchmaking for members in Pakistan, India, Russia and Thailand — with verified profiles and human matchmakers at every step.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 28px 0 28px;">
                            <img src="{{ $posterSrc }}"
                                alt="IDLMatch.com — Find Your Perfect Life Partner. Couples from Pakistan, India, Russia and Thailand."
                                width="584"
                                style="display:block;width:100%;max-width:584px;height:auto;border:0;border-radius:8px;">
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:24px 28px 8px 28px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color:#e91e63;border-radius:999px;">
                                        <a href="{{ route('register') }}"
                                            style="display:inline-block;padding:14px 34px;font-family:Arial,sans-serif;font-size:15px;font-weight:bold;color:#ffffff;text-decoration:none;border-radius:999px;">
                                            Create Your Free Profile
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:6px 28px 26px 28px;font-family:Arial,sans-serif;font-size:14px;line-height:1.6;color:#4b5563;">
                            <p style="margin:0 0 10px 0;">Why members choose us:</p>
                            <p style="margin:0 0 6px 0;">✔ Verified profiles reviewed by real matchmakers</p>
                            <p style="margin:0 0 6px 0;">✔ Privacy-first — your photos stay hidden until you agree</p>
                            <p style="margin:0;">✔ Guidance from first conversation to nikah</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#faf5f6;padding:20px 28px;font-family:Arial,sans-serif;font-size:12px;line-height:1.6;color:#6b7280;">
                            You are receiving this email because you registered at
                            <a href="{{ url('/') }}" style="color:#9d174d;text-decoration:none;font-weight:bold;">idlmatch.com</a>.
                            Questions? <a href="{{ route('contact') }}" style="color:#9d174d;text-decoration:none;">Contact our team</a>.
                            <br><br>
                            Ideal Marriage Center · idlmatch.com
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
