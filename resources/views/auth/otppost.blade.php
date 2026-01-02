<div style="
    max-width: 420px; 
    margin: 40px auto; 
    background: #ffffff; 
    padding: 30px; 
    border-radius: 12px; 
    box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
    font-family: Arial, sans-serif;
">
    <h2 style="margin-bottom: 20px; color:#333; text-align:center;">
        Verifikasi Kode OTP
    </h2>

    <p style="font-size:14px; color:#666; margin-bottom: 20px; text-align:center;">
        Masukkan kode OTP yang telah kami kirim ke email Anda.
    </p>

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf

        <label style="font-size:14px; color:#444; font-weight:600;">
            Kode OTP
        </label>

        <input 
            type="text" 
            name="otp" 
            required 
            maxlength="6"
            style="
                width:100%;
                margin-top:8px;
                padding:14px;
                font-size:20px;
                letter-spacing:6px;
                text-align:center;
                border:2px solid #ddd;
                border-radius:8px;
                outline:none;
                transition: all .2s;
            "
            onfocus="this.style.borderColor='#2c7be5'"
            onblur="this.style.borderColor='#ddd'"
        >

        <button type="submit" style="
            width:100%;
            margin-top:25px;
            padding:14px;
            background:#2c7be5;
            color:#fff;
            border:none;
            border-radius:8px;
            font-size:16px;
            font-weight:600;
            cursor:pointer;
            transition:.2s;
        "
        onmouseover="this.style.background='#1b63c8'"
        onmouseout="this.style.background='#2c7be5'"
        >
            Verifikasi
        </button>
    </form>
</div>



