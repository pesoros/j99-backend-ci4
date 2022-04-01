<!DOCTYPE html>
<html lang="en">
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
<head>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto';
        }

        .container {
            margin: 15px 28px;
            margin-right: 17px;
            -webkit-transform: rotate(90deg);
            -moz-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            transform: rotate(90deg);
            margin-bottom: 240px;
        }

        .footer {
            margin: 5px 15px;
            margin-right: 10px;
            font-size: 9px;
        }

        .center-align {
            text-align: center;
        }

        @page {
            size: 2.8in 5.9in;
            margin-top: 0cm;
            margin-left: 0cm;
            margin-right: 0cm;
            margin-bottom: 0cm;
        }

        #logo img {
            width: 490px;
            height: 100px;
            margin-top: 0px;
            margin-left: -20px;
        }

        .intro {
            font-size: 9px;
            margin-top: 10px;
            margin-bottom: 20px;
            width: 440px;
        }

        .detailer {
            font-size: 9px;
            width: 180px;
        }

        .qrcode img {
            width: 80px;
            margin-left: 0px;
        }

        hr {
            width: 90%;
            border: 0.3px solid ;
        }

        .footer p {
            margin-top: 0px;
        }

    </style>
</head>

<body>
    <div class="container">
        <div id="logo" class="media" >
            <img src="https://juragan99trans.id/images/logo-1.png"/>
        </div>
        <table>
            <tr>
                <td class="detailer">
                    <table>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <!-- <td><?= $tickedData->name ?></td> -->
                        </tr>
                        <tr>
                            <td>No HP</td>
                            <td>:</td>
                            <!-- <td><?= $tickedData->phone ?></td> -->
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <!-- <td><?= $tickedData->booking_date ?></td> -->
                        </tr>
                        <tr>
                            <td>Asal</td>
                            <td>:</td>
                            <td>Agen Pare - Kediri</td>
                        </tr>
                        <tr>
                            <td>Tujuan</td>
                            <td>:</td>
                            <td>J99 Kalideres - Jakarta</td>
                        </tr>
                        <tr>
                            <td>Tarif</td>
                            <td>:</td>
                            <td>Rp. <?= $tickedData->price ?></td>
                        </tr>
                    </table>
                </td>
                <td class="detailer">
                    <table>
                        <tr>
                            <td>Kode Tiket</td>
                            <td>:</td>
                            <!-- <td><?= $tickedData->ticket_number ?></td> -->
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>:</td>
                            <!-- <td><?= $tickedData->type ?></</td> -->
                        </tr>
                        <tr>
                            <td>No Kursi</td>
                            <td>:</td>
                            <!-- <td><?= $tickedData->seat_number ?></</td> -->
                        </tr>
                        <tr>
                            <td>Menu Makan</td>
                            <td>:</td>
                            <!-- <td><?= $tickedData->food_name ?></</td> -->
                        </tr>
                        <tr>
                            <td>Bagasi</td>
                            <td>:</td>
                            <!-- <td><?= $tickedData->baggage ?></</td> -->
                        </tr>
                        <tr>
                            <td>No Polisi</td>
                            <td>:</td>
                            <td>B 3877 NY</td>
                        </tr>
                    </table>
                </td>
                <td class="qrcode">
                    <img src="<?= $qrcode ?>" alt="">
                </td>
            </tr>
        </table>
        <div class="center-align intro">
            penumpang diimbau untuk datang paling lambat 30 menit sebelum jadwal keberangkatan. Hal tersebut dikarenakan pada saat proses boarding ada tahapan verifikasi berkas oleh petugas.
        </div>
    </div>
    <hr>
    <div class="footer center-align">
        <p>
            web:juragan99trans.id | phone:081277755485 <br> email:humas@juragan99trans.id | wa:031847653
        </p>
    </div>
</body>

</html>