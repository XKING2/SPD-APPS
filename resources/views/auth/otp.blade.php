<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kode OTP Verifikasi</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4; padding:20px 0;">
        <tr>
            <td align="center">

                <!-- WRAPPER -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:500px; background-color:#ffffff; border-radius:8px;">
                    <tr>
                        <td align="center" style="padding: 25px 20px 10px 20px;">
                            <h2 style="margin:0; color:#333; font-size:22px;">Kode Verifikasi OTP</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 20px; font-size:15px; color:#555; line-height:1.6;">
                            Hai,<br><br>
                            Gunakan kode OTP berikut untuk menyelesaikan proses verifikasi:
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:20px 0;">
                            <table cellpadding="0" cellspacing="0" border="0" style="margin:auto;">
                                <tr>
                                    <td style="
                                        font-size:36px;
                                        font-weight:bold;
                                        letter-spacing:6px;
                                        color:#2c7be5;
                                        padding:15px 30px;
                                        border: 2px solid #2c7be5;
                                        border-radius:8px;
                                        text-align:center;
                                    ">
                                        {{ $otp }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 20px 20px 20px; font-size:15px; color:#555; line-height:1.6;">
                            Masukkan kode ini pada aplikasi untuk menyelesaikan verifikasi.<br><br>
                            Kode OTP berlaku selama <strong>5 menit</strong>.
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="font-size:12px; color:#aaa; padding:15px 0 20px 0;">
                            &copy; {{ date('Y') }} Aplikasi Anda. Semua hak dilindungi.
                        </td>
                    </tr>
                </table>
                <!-- END WRAPPER -->

            </td>
        </tr>
    </table>

</body>
</html>
