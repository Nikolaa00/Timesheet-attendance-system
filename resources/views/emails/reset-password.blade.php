<!doctype html>
<html lang="mk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Reset Password</title>
    <style type="text/css">
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .email-content {
                padding: 30px 20px !important;
            }

            .email-title {
                font-size: 22px !important;
            }

            .email-text {
                font-size: 16px !important;
            }

            .email-button {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
                font-size: 18px !important;
                padding: 16px 24px !important;
                text-align: center !important;
            }

            .outer-padding {
                padding: 20px 10px !important;
            }
        }
    </style>
</head>
<body
    style="
        margin: 0;
        padding: 0;
        background-color: #F5F5F5;
        font-family: Arial, Helvetica, sans-serif;
    "
>
    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        bgcolor="#F5F5F5"
    >
        <tr>
            <td align="center" class="outer-padding" style="padding: 40px 20px">
                <table
                    class="email-container"
                    width="100%"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    style="max-width: 600px; background: #FFFFFF; border-radius: 8px"
                >
                    <tr>
                        <td class="email-content" style="padding: 50px">
                            <table
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                            >
                                <tr>
                                    <td
                                        class="email-title"
                                        style="font-size: 28px; color: #4B4F58; padding-bottom: 30px"
                                    >
                                        Здраво {{ $firstName }},
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        class="email-text"
                                        style="
                                            font-size: 18px;
                                            line-height: 1.6;
                                            color: #4B4F58;
                                            padding-bottom: 40px;
                                        "
                                    >
                                        Овој емаил го добивате бидејќи побаравте ресетирање на лозинката
                                        за вашата сметка.
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" style="padding-bottom: 50px">
                                        <a
                                            class="email-button"
                                            href="{{ $url }}"
                                            style="
                                                display: inline-block;
                                                padding: 18px 50px;
                                                background: linear-gradient(90deg, #FD5D47, #EA406F);
                                                color: #FFFFFF;
                                                text-decoration: none;
                                                font-size: 22px;
                                                font-weight: bold;
                                                border-radius: 40px;
                                            "
                                        >
                                            РЕСЕТИРАЈ ЛОЗИНКА
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        class="email-text"
                                        style="
                                            font-size: 18px;
                                            line-height: 1.6;
                                            color: #4B4F58;
                                            padding-bottom: 30px;
                                        "
                                    >
                                        Овој линк за ресетирање на лозинката ќе истече за 15 минути.
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        class="email-text"
                                        style="
                                            font-size: 18px;
                                            line-height: 1.6;
                                            color: #4B4F58;
                                            padding-bottom: 40px;
                                        "
                                    >
                                        Доколку вие не побаравте ресетирање на лозинката, не е потребно
                                        да преземате дополнителни активности.
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        class="email-text"
                                        style="font-size: 18px; color: #4B4F58; padding-bottom: 40px"
                                    >
                                        Со почит,<br />
                                       Timesheet
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <hr style="border: none; border-top: 1px solid #DDDDDD" />
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        class="email-text"
                                        style="
                                            padding-top: 30px;
                                            font-size: 16px;
                                            line-height: 1.6;
                                            color: #888888;
                                        "
                                    >
                                        Ако имате потешкотии при кликање на копчето „Ресетирај лозинка“,
                                        копирајте ја следната адреса и вметнете ја во вашиот веб
                                        прелистувач:
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        class="email-text"
                                        style="
                                            padding-top: 30px;
                                            font-size: 16px;
                                            line-height: 1.6;
                                            color: #888888;
                                        "
                                    >
                                        <a
                                            href="{{ $url }}"
                                            style="color: #4A78FF; word-break: break-all"
                                        >
                                            {{ $url }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
