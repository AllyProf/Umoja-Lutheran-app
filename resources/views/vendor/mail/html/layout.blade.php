<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; margin: 0; padding: 0;">
<tr>
<td align="center" style="padding: 20px 0; background-color: #f5f5f5;">
<table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; max-width: 600px; width: 100%; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
{!! $header ?? '' !!}
<tr>
<td style="background-color: #ffffff; padding: 0 30px;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td style="padding: 30px 0; background-color: #ffffff; color: #333333; font-size: 18px;">
{!! Illuminate\Mail\Markdown::parse($slot) !!}
{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>
{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
