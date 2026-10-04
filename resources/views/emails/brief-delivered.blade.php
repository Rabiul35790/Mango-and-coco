@extends('emails.layout')
@section('body')
<h1 style="margin:0 0 12px;font-size:24px;color:#1E3A2F;">It's ready! 🎉</h1>
<p style="margin:0 0 12px;font-size:15px;line-height:1.6;color:#33463D;">Great news, {{ $name }} — your personalised <strong>{{ $product }}</strong> ({{ $trackingId }}) is finished and delivered.</p>
@if($deliveryUrl)
<table cellpadding="0" cellspacing="0" style="margin:16px 0;"><tr>
<td style="border-radius:999px;background-color:#3E7C5B;"><a href="{{ $deliveryUrl }}" style="display:inline-block;padding:12px 28px;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;">Get your video</a></td>
</tr></table>
@else
<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#33463D;">You'll find it in your inbox — if it hasn't landed within a few minutes, just reply to this email and we'll resend it.</p>
@endif
<p style="margin:0;font-size:14px;color:#5A7268;">Loved it? <a href="{{ $reviewUrl }}" style="color:#3E7C5B;font-weight:bold;">Leave a quick review</a> — it means the world to two small budgies.</p>
@endsection
