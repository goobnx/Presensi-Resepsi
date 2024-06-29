<?php

include("koneksi.php");

if(isset($_GET['id'])) {
    $id_tamu = $_GET['id'];
    $query = "UPDATE tamu SET kehadiran='Hadir', waktu=NOW() WHERE id_tamu='$id_tamu'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo '<script>window.history.back()</script>';
    } else {
        echo 'error: ' . mysqli_error($koneksi);
    }
    exit;
}