<p>Dear {{ $fullName }},</p>

<p>
    An official staff account ({{ $roleLabel }} - {{ $officeName }}) has been created for you
    in the DENR-4A RDAC Portal.
</p>

<p>
    Portal URL: <a href="{{ route('login') }}">{{ route('login') }}</a><br>
    Username / Email: <strong>{{ $username }}</strong> ({{ $email }})<br>
    Temporary Password: <strong style="font-family: monospace; font-size: 16px;">{{ $tempPassword }}</strong>
</p>

<p>
    <strong>Security Notice:</strong> You are required to change your temporary password
    immediately upon your first login.
</p>

<p>Thank you,<br>Department of Environment and Natural Resources<br>Region 4-A (CALABARZON)</p>