<?php

//Connection to database
$host = "localhost";
$username = "root";
$password = "";
$koneksiname = "produksi";

$koneksi = mysqli_connect($host, $username, $password, $koneksiname);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


//insert new bahan
function tambahData($data)
{
    global $koneksi;

    $nama_bahan = htmlspecialchars($data['namaBahan']);
    $takaran = htmlspecialchars($data['takaran']);
    $fungsi = htmlspecialchars($data['fungsi']);

    $query = "INSERT INTO bahan (namaBahan, takaran, fungsi) 
              VALUES ('$nama_bahan', '$takaran', '$fungsi')";

    return mysqli_query($koneksi, $query);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_data'])) {
        $status = tambahData($_POST);

        if ($status) {
            header("Location: dataProduksi.php?status=sukses_tambah");
        } else {
            header("Location: dataProduksi.php?status=gagal_gagal_tambah");
        }
        exit();
    }
}

// Delete Bahan
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if (hapusData($id) > 0) {
        header("Location: dataProduksi.php?status=sukses_hapus");
    } else {
        header("Location: dataProduksi.php?status=gagal_hapus");
    }
    exit;
}

function hapusData($id) {
    global $koneksi;

    $query = "DELETE FROM bahan WHERE id = $id";

    mysqli_query($koneksi, $query);
    return mysqli_affected_rows($koneksi);
}

// Select bahan
function getDataProduksi() {
    global $koneksi;
    $query = "SELECT * FROM bahan";
    $result = mysqli_query($koneksi, $query);
    
    if (!$result) {
        die("Query gagal: " . mysqli_error($koneksi));
    }
    
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}



// Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if ($user['password'] == $password) {
            session_start();
            $_SESSION['username'] = $user['username'];
            header("Location: home.php");
        } else {
            header("Location: index.php?status=error_login");
        }
    } else {
        header("Location: index.php?status=error_login");
    }
    exit;
}


// Logout
function logout() {
    session_start();

    session_unset();

    session_destroy();

    header("Location: index.php");
    exit;
}

if (isset($_GET['logout'])) {
    logout();
}

?>

