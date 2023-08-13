<tr>
    <td class="esdev-adapt-off" align="left" style="padding:20px;Margin:0">
        <table class="esdev-mso-table" cellspacing="0" cellpadding="0"
               style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px">
            <tr>
                <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                    <table class="es-left" cellspacing="0" cellpadding="0" align="left"
                           style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                        <tr>
                            <td align="left" style="padding:0;Margin:0;width:258px">
                                <table
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;background-color:#e8eafb;border-radius:10px 0 0 10px"
                                    width="100%" cellspacing="0" cellpadding="0"
                                    bgcolor="#e8eafb" role="presentation">
                                    @foreach(array_keys($additional_info) as $key)
                                        <tr>
                                            <td align="right"
                                                style="padding:0;Margin:0;padding-top:10px; @if($loop->last) {{ 'padding-bottom:10px;' }} @endif">
                                                <p style="text-align: right !important;Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">
                                                    {{$key}}:</p></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                    <table class="es-left" cellspacing="0" cellpadding="0" align="left"
                           style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                        <tr>
                            <td align="left" style="padding:0;Margin:0;width:320px">
                                <table
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;background-color:#e8eafb;border-radius:0 10px 10px 0"
                                    width="100%" cellspacing="0" cellpadding="0"
                                    bgcolor="#e8eafb" role="presentation">
                                    @foreach(array_values($additional_info) as $value)
                                        <tr>
                                            <td align="left"
                                                style="padding:0;Margin:0;padding-top:10px;padding-left:10px; @if($loop->last) {{ 'padding-bottom:10px;' }} @endif">
                                                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">
                                                    <strong>{{$value}}</strong>
                                                </p></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
