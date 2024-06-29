<?php

include("koneksi.php");

// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
//     // Handle the AJAX request to update attendance and status
//     $id_tamu = $_GET['id'];
    
//     if ($action == 'sendiri') {
//         $query = "UPDATE tamu SET kehadiran='Hadir', waktu=NOW() WHERE id_tamu='$id_tamu'";
//     } 
//     $result = mysqli_query($koneksi, $query);

//     if ($result) {
//         echo 'success';
//         echo '<script>window.history.back()</script>';
//     } else {
//         echo 'error: ' . mysqli_error($koneksi);
//     }
//     exit;
// }

if(isset($_GET['id'])) {
    $id_tamu = $_GET['id'];
    $query = "UPDATE tamu SET kehadiran='Tidak Hadir', waktu=NOW() WHERE id_tamu='$id_tamu'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        // echo 'success';
        echo '<script>window.history.back()</script>';
    } else {
        echo 'error: ' . mysqli_error($koneksi);
    }
    exit;
}