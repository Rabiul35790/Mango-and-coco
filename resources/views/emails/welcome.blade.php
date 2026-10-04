@extends('emails.layout')
@section('body')
<h1 style="margin:0 0 12px;font-size:24px;color:#1E3A2F;">Welcome, {{ $name }}! 🐣</h1>
<p style="margin:0 0 12px;font-size:15px;line-height:1.6;color:#33463D;">Your Mango&amp;Coco account is ready. From your account you can follow every order, re-download your sticker packs and wallpapers anytime, and rate the things you bought.</p>
<table cellpadding="0" cellspacing="0" style="margin:20px 0;"><tr>
<td style="border-radius:999px;background-color:#3E7C5B;"><a href="{{ $accountUrl }}" style="display:inline-block;padding:12px 28px;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;">Open my account</a></td>
<td width="12"></td>
<td style="border-radius:999px;border:1px solid #CBD8D0;"><a href="{{ $shopUrl }}" style="display:inline-block;padding:12px 28px;color:#1E3A2F;font-size:15px;font-weight:bold;text-decoration:none;">Browse the shop</a></td>
</tr></table>
<p style="margin:0;font-size:13px;color:#5A7268;">Past guest orders on this email were linked automatically — nothing is lost.</p>
@endsection
