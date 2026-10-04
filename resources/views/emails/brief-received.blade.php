@extends('emails.layout')
@section('body')
<h1 style="margin:0 0 12px;font-size:24px;color:#1E3A2F;">Brief received! 💌</h1>
<p style="margin:0 0 12px;font-size:15px;line-height:1.6;color:#33463D;">Thanks, {{ $name }} — your personalised <strong>{{ $product }}</strong> brief is with us. Here's your tracking ID:</p>
<p style="margin:0 0 12px;font-size:26px;font-weight:bold;letter-spacing:2px;color:#1E3A2F;">{{ $trackingId }}</p>
<p style="margin:0 0 6px;font-size:14px;color:#5A7268;">Your message in the scene:</p>
<p style="margin:0 0 16px;padding:12px 16px;background-color:#F2F6F3;border-radius:12px;font-size:14px;font-style:italic;color:#33463D;">“{{ $briefMessage }}”</p>
<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#33463D;">What happens next: we confirm the details, hand-make your piece, and deliver it to your inbox within 48 hours of payment.</p>
<table cellpadding="0" cellspacing="0" style="margin:4px 0 0;"><tr>
<td style="border-radius:999px;background-color:#3E7C5B;"><a href="{{ $trackUrl }}" style="display:inline-block;padding:12px 28px;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;">Track my order</a></td>
</tr></table>
@endsection
