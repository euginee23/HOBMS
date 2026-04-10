<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Received — HOBMS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; color: #18181b; -webkit-font-smoothing: antialiased; }
        .wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px 40px; }
        .card { background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); padding: 40px 40px 32px; text-align: center; }
        .logo-row { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
        .logo-icon { width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; }
        .logo-text { font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; }
        .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .header-title { font-size: 24px; font-weight: 800; color: #ffffff; line-height: 1.2; margin-bottom: 6px; }
        .header-sub { font-size: 14px; color: rgba(255,255,255,0.85); }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; color: #3f3f46; margin-bottom: 12px; }
        .message { font-size: 15px; color: #52525b; line-height: 1.7; margin-bottom: 28px; }
        .section-label { font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #a1a1aa; margin-bottom: 12px; }
        .details-card { background: #fafafa; border-radius: 14px; border: 1px solid #e4e4e7; overflow: hidden; margin-bottom: 28px; }
        .detail-row { display: flex; align-items: flex-start; padding: 12px 16px; border-bottom: 1px solid #f4f4f5; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-size: 12px; font-weight: 600; color: #a1a1aa; width: 130px; flex-shrink: 0; }
        .detail-value { font-size: 13px; color: #18181b; font-weight: 500; flex: 1; }
        .status-badge { display: inline-block; padding: 4px 12px; background: #fef9c3; color: #854d0e; font-size: 12px; font-weight: 700; border-radius: 999px; border: 1px solid #fde68a; }
        .cta-button { display: inline-block; margin-top: 8px; padding: 12px 28px; background: #2563eb; color: #ffffff; font-size: 14px; font-weight: 700; border-radius: 10px; text-decoration: none; }
        .cta-wrap { text-align: center; margin-bottom: 28px; }
        .security { background: #f9fafb; border-radius: 12px; padding: 16px 20px; font-size: 13px; color: #71717a; line-height: 1.6; border: 1px solid #e4e4e7; }
        .security strong { color: #3f3f46; }
        .footer { padding: 24px 40px; background: #fafafa; border-top: 1px solid #f4f4f5; text-align: center; }
        .footer-brand { font-size: 13px; font-weight: 700; color: #2563eb; letter-spacing: 0.5px; margin-bottom: 6px; }
        .footer-text { font-size: 12px; color: #a1a1aa; line-height: 1.6; }
        @media only screen and (max-width: 480px) {
            .wrapper { margin: 0 auto; padding: 0 8px 24px; }
            .card { border-radius: 16px; }
            .header { padding: 28px 20px 24px; }
            .header-title { font-size: 22px; }
            .body { padding: 24px 20px; }
            .detail-row { flex-direction: column; gap: 2px; padding: 10px 14px; }
            .detail-label { width: auto; font-size: 11px; }
            .footer { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="logo-row">
                    <span class="logo-icon">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z"/>
                            <path d="m9 16 .348-.24c1.465-1.013 3.84-1.013 5.304 0L15 16"/>
                            <path d="M8 7h.01"/><path d="M16 7h.01"/><path d="M12 7h.01"/><path d="M12 11h.01"/><path d="M16 11h.01"/><path d="M8 11h.01"/>
                            <path d="M10 22v-6.5m4 0V22"/>
                        </svg>
                    </span>
                    <span class="logo-text">HOBMS</span>
                </div>
                <div class="header-icon">
                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="header-title">Booking Received</div>
                <div class="header-sub">Your reservation request has been submitted</div>
            </div>

            <div class="body">
                <p class="greeting">Hi, <strong>{{ $booking->guest_name }}</strong>!</p>
                <p class="message">
                    Thank you for choosing HOBMS. Your booking has been received and is currently <strong>pending confirmation</strong> by our staff. You'll receive another email once it's confirmed.
                </p>

                <div class="section-label">Booking Details</div>
                <div class="details-card">
                    <div class="detail-row">
                        <span class="detail-label">Reference #</span>
                        <span class="detail-value">{{ $booking->booking_reference }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Room Type</span>
                        <span class="detail-value">{{ $booking->room->roomCategory->name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Room</span>
                        <span class="detail-value">Room {{ $booking->room->room_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Check-in</span>
                        <span class="detail-value">{{ $booking->check_in_date->format('l, F j, Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Check-out</span>
                        <span class="detail-value">{{ $booking->check_out_date->format('l, F j, Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Duration</span>
                        <span class="detail-value">{{ $booking->nights }} {{ Str::plural('night', $booking->nights) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Guests</span>
                        <span class="detail-value">{{ $booking->num_guests }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount</span>
                        <span class="detail-value" style="font-weight: 700; color: #2563eb;">₱{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value"><span class="status-badge">Pending</span></span>
                    </div>
                </div>

                <div class="cta-wrap">
                    <a href="{{ $booking->portalUrl }}" class="cta-button">Track Your Booking</a>
                </div>

                <div class="security">
                    <strong>Save your reference number!</strong> Use reference <strong>{{ $booking->booking_reference }}</strong> to track your booking status anytime on our website.
                </div>
            </div>

            <div class="footer">
                <div class="footer-brand">HOBMS</div>
                <div class="footer-text">
                    Hotel Online Booking Management System<br />
                    This is an automated message — please do not reply to this email.
                </div>
            </div>
        </div>

        <p style="text-align:center; font-size:12px; color:#a1a1aa; margin-top:20px;">
            &copy; {{ date('Y') }} HOBMS. All rights reserved.
        </p>
    </div>
</body>
</html>
