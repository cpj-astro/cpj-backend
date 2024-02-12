
<html style="-moz-osx-font-smoothing: grayscale; -webkit-font-smoothing: antialiased; background-color: #464646; margin: 0; padding: 0;">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="format-detection" content="telephone=no">
        <title>CPJ - Success</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
        <style>
            body {
                text-align: center;
                padding: 40px 0;
                background: #EBF0F5;
            }
                h1 {
                color: red;
                font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
                font-weight: 900;
                font-size: 40px;
                margin-bottom: 10px;
                }
                p {
                color: #404F5E;
                font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
                font-size:20px;
                margin: 0;
                }
            i {
                color: red;
                font-size: 100px;
                line-height: 200px;
                font-family: monospace;
                font-style: normal;
            }
            .card {
                margin-top: 50px!important;
                background: white;
                padding: 50px;
                max-width: 230px;
                border-radius: 4px;
                box-shadow: 0 2px 3px #C8D0D8;
                display: inline-block;
                margin: 0 auto;
            }
            </style>
    </head>
    <body bgcolor="#d7d7d7" class="generic-template" style="-moz-osx-font-smoothing: grayscale; -webkit-font-smoothing: antialiased; background-color: #d7d7d7; margin: 0; padding: 0;">
    <!-- Header Start -->
    <div class="bg-white header" bgcolor="#ffffff" style="background-color: white; width: 100%;">
        <table align="center" bgcolor="#ffffff" style="border-left: 10px solid white; border-right: 10px solid white; max-width: 600px; width: 100%;">
            <tr height="80">
                <td align="center" class="vertical-align-middle" style="color: #464646; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 16px; vertical-align: middle;">
                    <a href="https://www.go.com.mt/" target="_blank" style="-webkit-text-decoration-color: #F16522; color: #F16522; text-decoration: none; text-decoration-color: #F16522;">
                        <img src="https://cricketpanditji.com/assets/images/logo.png" alt="CPJ-LOGO" style="height: 50px; width: 150px">
                    </a>
                </td>
            </tr>
        </table>
    </div>
    <!-- Header End -->

    <div class="card">
      <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
        <i class="checkmark">X</i>
      </div>
        <h1>Failed</h1> 
        @if($match_id && $merchant_transaction_id)
            @php
                $baseUrl = env('APP_ENV') === 'local' ? env('CPJ_LOCAL_URL') : env('CPJ_DEV_URL');
                $matchDetailsUrl = $baseUrl . 'match-reports/' . $match_id;
            @endphp
            <p>Transaction ID: $merchant_transaction_id</p>
            <p>Trasaction failed!, Try again after sometime <a href="{{ $matchDetailsUrl }}">click here</a></p>
        @endif
      </div>
    </body>
</html>