@props(['url'])
<tr>
<td class="header" style="padding: 0;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" style="background-color: #1d4ed8; padding: 36px 32px 30px;">
    <a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
        {{-- Logo branca 500x500, renderizada em 60x60 --}}
        <img src="{{ asset('images/Logo_rota-segura_branco.png') }}"
             alt="Rota Segura"
             width="60"
             height="60"
             style="display: block; width: 60px; height: 60px; margin: 0 auto 14px; border: none; outline: none; text-decoration: none;">
        <p style="margin: 0 0 5px; font-size: 28px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; line-height: 1; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Rota Segura</p>
        <p style="margin: 0; font-size: 11px; color: rgba(255,255,255,0.6); letter-spacing: 2px; text-transform: uppercase; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Transporte Escolar Seguro</p>
    </a>
</td>
</tr>
{{-- Faixa decorativa que conecta o header ao card branco --}}
<tr>
<td style="background-color: #1e40af; height: 6px; font-size: 0; line-height: 0; mso-line-height-rule: exactly;">&nbsp;</td>
</tr>
</table>
</td>
</tr>
