<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.5;
            padding: 30px;
        }

        h2 {
            text-align: center;
        }

        .signature {
            margin-top: 50px;
        }
    </style>
</head>

<body style="line-height: 1.3;">
    <div style="position: absolute; top: -33; left: -34; width: 113%; height: 109%; z-index: -1;">
        <img src="{{ public_path('ext_images/bg_invoice.png') }}" style="width: 100%; height: 100%; object-fit: cover;"
            alt="">
    </div>
    <p style="top: 180px; left: 250px; position: absolute; font-size: 14px;">
        Number: {{ $number }}/JRNL/UINSMDD/{{ $year }}
    </p>

    <div style="top: 200px;  position: absolute; font-size: 16px; padding: 0 28px;">
        <p >
            <table>
                <tr style="line-height: 1;">
                    <td style="width: 180px;">Name</td>
                    <td style="width: 0px;">:</td>
                    <td>FIRDAUS ANNAS, M.Kom</td>
                </tr>
                <tr style="line-height: 1;">
                    <td>Position</td>
                    <td>:</td>
                    <td>Head office of Rumah Jurnal</td>
                </tr>
                <tr style="line-height: 1;">
                    <td>Institution</td>
                    <td>:</td>
                    <td>UIN Sjech M. Djamil Djambek Bukittinggi</td>
                </tr>
            </table>
            </p>

        <p>
            With this latter I inform that the article with a title of “<strong>{{ $title }}</strong>”
        </p>

        <p style="margin-top: -10px;">
        <table>
            <tr style="line-height: 1;">
                <td style="width: 180px;">Author</td>
                <td style="width: 0px;">:</td>
                <td>{{ $name }}</td>
            </tr>
            <tr style="line-height: 1;">
                <td>Affiliation</td>
                <td>:</td>
                <td>{{ $affiliation }}</td>
            </tr>
        </table>
        </p>

        <p style="margin-top: 20px;">
            Has made an administration payment to {{ $journal }}
            with an amount of <strong> {{ $payment_percent }}% - @money($payment_amount)</strong> Please transfer your payment via <strong>Bank
                {{ $payment_account->bank }} - Nomor Rekening : {{ $payment_account->account_number }} - An.
                {{ $payment_account->account_name }}.</strong> Payment Deadline is
            <strong>{{ $payment_due_date }}.</strong>
        </p>

        <p style="margin-top: -5px;">
            If you have made a payment, please confirm the payment via the following url : <a
                href="https://rumahjurnal.uinbukittinggi.ac.id/payment">https://rumahjurnal.uinbukittinggi.ac.id/payment</a>
        </p>
        <p style="margin-top: -5px;">
            If you have anything to ask, please get in touch Mrs. Herlinda via WhatsApp
            +62 813-9675-3128.
        </p>
        <p style="margin-top: -5px;">
            With this, the latter is made for its purpose.
        </p>

    <div class="signature" style="position: absolute; bottom: 380; left: 278; ">
        <p style="margin-bottom: -5px" >Bukittinggi, {{ $date }}</p>
    </div>
    </div>

    <img style="position: absolute; bottom: -20; right: 10;  width: 100px;" src="{{ $journal_thumbnail }}"
        alt="">

</body>

</html>
