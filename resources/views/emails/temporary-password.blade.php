<p>Dear {{ $fullName }},</p>

<p>A password reset request was received for your account in the DENR-4A RDAC Portal.</p>

<p>
    Portal URL: <a href="{{ route('login') }}">{{ route('login') }}</a><br>
    Username: <strong>{{ $username }}</strong><br>
    Temporary Password: <strong style="font-family: monospace; font-size: 16px;">{{ $tempPassword }}</strong>
</p>

<p>
    <strong>Security Notice:</strong> This temporary password expires in 30 minutes and you will be
    required to change it immediately upon logging in. Your current password still works until then.
    If you did not request this reset, you can ignore this e-mail.
</p>

<p>Thank you,<br>Department of Environment and Natural Resources<br>Region 4-A (CALABARZON)</p>