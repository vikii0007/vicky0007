<?php
session_start();

// Set durasi waktu session timeout (1 menit)
$session_timeout = 60;

// Cek apakah ada data session yang tersimpan dan waktu session masih berlaku
if (isset($_SESSION['result_time'])) {
    $elapsed_time = time() - $_SESSION['result_time'];
    if ($elapsed_time > $session_timeout) {
        // Hapus session jika lebih dari 1 menit
        unset($_SESSION['result'], $_SESSION['result_time']);
    }
}

// Jika halaman di-refresh tanpa POST, maka hapus session
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    unset($_SESSION['result'], $_SESSION['result_time']);
}

// Jika form dikirim (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $total_belanja = $_POST['total_belanja'];
    $member = $_POST['member'];

    // Fungsi untuk menghitung diskon
    function hitungDiskon($total_belanja, $member) {
        $diskon = 0;
        $diskon_member = $total_belanja - 0.10 * $total_belanja;

        if ($member) {
            // Jika member
            if ($total_belanja > 1000000) {
                $diskon = 0.10 * $total_belanja + 0.15 * $diskon_member;
            } elseif ($total_belanja >= 500000) {
                $diskon = 0.10 * $total_belanja + 0.10 * $total_belanja;
            } else {
                $diskon = $diskon_member;
            }
        } else {
            // Jika bukan member
            if ($total_belanja > 1000000) {
                $diskon = 0.10 * $total_belanja;
            } elseif ($total_belanja >= 500000) {
                $diskon = 0.05 * $total_belanja;
            }
        }

        // Total bayar setelah diskon
        $total_bayar = $total_belanja - $diskon;
        return array('total_belanja' => $total_belanja, 'diskon' => $diskon, 'total_bayar' => $total_bayar);
    }

    // Panggil fungsi untuk menghitung diskon
    $result = hitungDiskon($total_belanja, $member);

    // Simpan hasil ke session
    $_SESSION['result'] = $result;
    $_SESSION['result_time'] = time();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hitung Diskon</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Roboto:wght@300;400;500&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: linear-gradient(to right, #667eea, #764ba2);
        }

        .container {
            max-width: 450px;
            background-color: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
        }

        .container:hover {
            transform: translateY(-10px);
            box-shadow: 0 40px 70px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 25px;
            position: relative;
            font-weight: 600;
        }

        h1:before {
            content: '';
            position: absolute;
            width: 60px;
            height: 5px;
            background-color: #764ba2;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 8px;
            text-align: left;
            width: 100%;
        }

        input[type="text"], select {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1.1rem;
            transition: all 0.2s ease-in-out;
        }

        input[type="text"]:focus, select:focus {
            border-color: #764ba2;
            outline: none;
            box-shadow: 0 0 10px rgba(118, 75, 162, 0.2);
        }

        input[type="submit"] {
            padding: 12px 20px;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 500;
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #5a67d8;
            box-shadow: 0 10px 20px rgba(90, 103, 216, 0.3);
        }

        .result {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 15px;
            margin-top: 25px;
            font-size: 1.2rem;
            color: #333;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            text-align: left;
            transition: opacity 0.3s ease-in-out;
        }

        .result p {
            margin: 8px 0;
            font-size: 1.1rem;
            font-weight: 400;
            color: #333;
        }

        .result span {
            font-weight: 500;
            color: #667eea;
        }

        @media screen and (max-width: 768px) {
            .container {
                max-width: 100%;
                padding: 30px;
            }

            h1 {
                font-size: 2rem;
            }

            input[type="submit"] {
                font-size: 1rem;
                padding: 10px 18px;
            }

            .result p {
                font-size: 1rem;
            }
        }
    </style>
    <script>
        // Fungsi untuk menyembunyikan hasil setelah 5 detik
        setTimeout(function() {
            var resultDiv = document.querySelector('.result');
            if (resultDiv) {
                resultDiv.style.opacity = '0';
            }
        }, 5000); // 5000 ms = 5 detik
    </script>
</head>
<body>

<div class="container">
    <h1>Hitung Total Bayar</h1>
    <form method="POST">
        <label for="total_belanja">Total Belanja (Rp):</label>
        <input type="text" id="total_belanja" name="total_belanja" required placeholder="Masukkan Total Belanja">

        <label for="member">Apakah Anda Member?</label>
        <select id="member" name="member">
            <option value="1">Ya</option>
            <option value="0">Tidak</option>
        </select>

        <input type="submit" value="Total Bayar">
    </form>

    <?php
    // Tampilkan hasil jika ada di session dan belum kedaluwarsa
    if (isset($_SESSION['result'])) {
        echo "<div class='result'>";
        echo "<p>Total Belanja: <span>Rp " . number_format($_SESSION['result']['total_belanja'], 0, ',', '.') . "</span></p>";
        echo "<p>Diskon: <span>Rp " . number_format($_SESSION['result']['diskon'], 0, ',', '.') . "</span></p>";
        echo "<p>Total Bayar: <span>Rp " . number_format($_SESSION['result']['total_bayar'], 0, ',', '.') . "</span></p>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>
