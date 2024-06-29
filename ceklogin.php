<?php

include "koneksi.php";

if(isset($_SESSION['login'])) {
	// Kalo sudah login, boleh masuk web
} else {
	// Kalo belum login, tidak boleh masuk web
	header('location: login.php');
}

?>