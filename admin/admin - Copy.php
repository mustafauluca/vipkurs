<?php
session_start();
include '../ulc/db.php';
require_once '../ulc/mailer.php';

// Oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit;
}

$db = new DB();
$table = 'ayarlar';

// Mevcut admin bilgilerini çek
$admin_id = $_SESSION['admin_id'];
$admin = $db->getRow("SELECT * FROM kullanicilar WHERE id = ?", [$admin_id]);

// Yetki kontrolü (Örnek: 1=Super Admin, 2=Normal Admin)
if ($admin->yetki > 2) {
    header('Location: adminlogin.php?status=unauthorized');
    exit;
}


function generateVerificationCode() {
    return bin2hex(random_bytes(16));
}



if (isset($_GET['code']) && isset($_GET['id'])) {
    $code = $_GET['code'];
    $id = $_GET['id'];
    
    // Verify token
    $token = $db->getRow("SELECT * FROM reset_tokens WHERE user_id = ? AND token = ? AND expires_at > NOW()", 
        [$id, $code]);
    
    if ($token) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $db->query("UPDATE ayarlar SET adminpassword = ? WHERE id = ?", [$new_password, $id]);
            $db->query("DELETE FROM reset_tokens WHERE token = ?", [$code]);
            echo "Şifreniz başarıyla güncellendi!";
            exit;
        }
    } else {
        echo "Geçersiz veya süresi dolmuş sıfırlama bağlantısı!";
        exit;
    }
} else {
  
}



// Ekleme, Güncelleme, Silme ve Şifre Sıfırlama İşlemleri
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // [Previous add code remains the same]
        $data = [
            'adminusername' => $_POST['adminusername'],
            'adminadsoyad' => $_POST['adminadsoyad'],
            'adminpassword' => password_hash($_POST['adminpassword'], PASSWORD_DEFAULT), // Hash password
            'adminemail' => $_POST['adminemail'],
            'admingsm' => $_POST['admingsm'],
            'aktif' => $_POST['aktif'],
            'kurumadi' => $_POST['kurumadi'],
            'kurumunvan' => $_POST['kurumunvan'],
            'kurumadres' => $_POST['kurumadres'],
            'kurumtelefon1' => $_POST['kurumtelefon1'],
            'kurumtelefon2' => $_POST['kurumtelefon2'],
            'kurumgsm' => $_POST['kurumgsm'],
            'kurumeposta' => $_POST['kurumeposta'],
            'kurumweb' => $_POST['kurumweb'],
            'kurumlogo' => $_POST['kurumlogo']
        ];
        
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);
        
        $db->query("INSERT INTO $table ($columns) VALUES ($placeholders)", $values);
    } elseif (isset($_POST['update'])) {
        // [Previous update code remains the same]
    } elseif (isset($_POST['delete'])) {
        // [Previous delete code remains the same]
    } elseif (isset($_POST['reset_password'])) {
        // Manual password reset
        $id = $_POST['id'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $db->query("UPDATE $table SET adminpassword = ? WHERE id = ?", [$new_password, $id]);
    } elseif (isset($_POST['send_reset_link'])) {
    $id = $_POST['id'];
    $user = $db->getRow("SELECT adminemail, adminadsoyad FROM $table WHERE id = ?", [$id]);
    
    if ($user) {
        $code = generateVerificationCode();
        $reset_link = "https://yourdomain.com/adminresetmail.php?code=$code&id=$id";
        
        // Token kaydet
        $db->query("INSERT INTO reset_tokens ...");
        
        // Mail gönder
        $mailer = new Mailer();
        if($mailer->sendPasswordReset(
            $user->adminemail,
            $user->adminadsoyad,
            $reset_link
        )){
            $_SESSION['success'] = "Şifre sıfırlama maili gönderildi!";
        } else {
            $_SESSION['error'] = "Mail gönderilemedi!";
        }
    }
}
}



$rows = $db->getList("SELECT * FROM ayarlar");

if (isset($_GET['profile'])) {
    $profile_id = $_GET['profile'];
    $profile = $db->getRow("SELECT * FROM $table WHERE id = ?", [$profile_id]);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli | Kurum Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            min-height: 100vh;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            padding: 1rem;
            margin-bottom: 0.2rem;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
            font-weight: 700;
        }
        
        .table-responsive {
            border-radius: 0.35rem;
            overflow: hidden;
        }
        
        .table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05rem;
        }
        
        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .action-buttons .btn {
            margin: 0.2rem;
            min-width: 80px;
        }
        
        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            border-radius: 0.25rem;
        }
        
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #842029;
        }
        
        .contact-info {
            line-height: 1.4;
        }
        
        .form-section {
            transition: all 0.3s ease;
        }
        
        .topbar {
            height: 4.375rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            background-color: #fff;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
  <div class="sidebar col-md-3 col-lg-2 d-md-block">
    <div class="text-center py-4">
        <img src="<?php echo $rows[0]->kurumlogo ?? 'https://via.placeholder.com/150'; ?>" class="logo-img mb-3" alt="Kurum Logo">
        <h5 class="text-white mb-0"><?php echo htmlspecialchars($rows[0]->kurumadi ?? 'Kurum Adı'); ?></h5>
        <small class="text-white-50">Admin Paneli</small>
    </div>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="admin.php">
                <i class="fas fa-fw fa-home"></i>
                Ana Sayfa
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="admincompanies.php">
                <i class="fas fa-fw fa-building"></i>
                Kurum Yönetimi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="adminusers.php">
                <i class="fas fa-fw fa-users-cog"></i>
                Kullanıcı Yönetimi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="adminlogs.php">
                <i class="fas fa-fw fa-clipboard-list"></i>
                Sistem Logları
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="adminprofile.php">
                <i class="fas fa-fw fa-user-shield"></i>
                Profil Yönetimi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="adminreset.php">
                <i class="fas fa-fw fa-lock"></i>
                Şifre Yönetimi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="adminlogout.php">
                <i class="fas fa-fw fa-sign-out-alt"></i>
                Çıkış Yap
            </a>
        </li>
    </ul>
</div>
		

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ml-sm-auto px-md-4 py-4">
            <!-- Topbar -->
<nav class="topbar navbar navbar-expand navbar-light bg-white mb-4 static-top">
    <div class="container-fluid">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                        <?php echo htmlspecialchars($admin->adi . ' ' . $admin->sadi); ?>
                    </span>
                    <img class="img-profile rounded-circle" 
                         src="<?php echo htmlspecialchars($admin->foto ?? 'https://via.placeholder.com/60'); ?>" 
                         width="32"
                         alt="Profil Fotoğrafı">
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="adminprofile.php">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profil
                    </a>
                    <a class="dropdown-item" href="adminreset.php">
                        <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                        Şifre Değiştir
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="adminlogout.php">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Çıkış Yap
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Kurum Bilgileri Yönetimi</h1>
                <button class="btn btn-primary shadow-sm" onclick="showForm('addForm')">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Yeni Kurum Ekle
                </button>
            </div>

            <!-- Profil Sayfası -->
            <?php if (isset($_GET['profile']) && $profile): ?>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Kurum Profil Detayları</h6>
                            <a href="?" class="btn btn-sm btn-secondary">Geri Dön</a>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="<?php echo htmlspecialchars($profile->kurumlogo); ?>" class="logo-img" alt="Kurum Logo">
                                <h4 class="mt-3"><?php echo htmlspecialchars($profile->kurumadi); ?></h4>
                                <p class="text-muted"><?php echo htmlspecialchars($profile->kurumunvan); ?></p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="font-weight-bold text-primary mb-3">İletişim Bilgileri</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-map-marker-alt fa-fw mr-2 text-gray-400"></i> <?php echo htmlspecialchars($profile->kurumadres); ?></li>
                                        <li class="mb-2"><i class="fas fa-phone fa-fw mr-2 text-gray-400"></i> <?php echo htmlspecialchars($profile->kurumtelefon1); ?></li>
                                        <li class="mb-2"><i class="fas fa-mobile-alt fa-fw mr-2 text-gray-400"></i> <?php echo htmlspecialchars($profile->kurumgsm); ?></li>
                                        <li class="mb-2"><i class="fas fa-envelope fa-fw mr-2 text-gray-400"></i> <?php echo htmlspecialchars($profile->kurumeposta); ?></li>
                                        <li class="mb-2"><i class="fas fa-globe fa-fw mr-2 text-gray-400"></i> <?php echo htmlspecialchars($profile->kurumweb); ?></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="font-weight-bold text-primary mb-3">Yönetici Bilgileri</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-user fa-fw mr-2 text-gray-400"></i> <?php echo htmlspecialchars($profile->adminadsoyad); ?></li>
                                        <li class="mb-2"><i class="fas fa-at fa-fw mr-2 text-gray-400"></i> <?php echo htmlspecialchars($profile->adminemail); ?></li>
                                        <li class="mb-2"><i class="fas fa-phone fa-fw mr-2 text-gray-400"></i> <?php echo htmlspecialchars($profile->admingsm); ?></li>
                                        <li class="mb-2">
                                            <span class="status-badge <?php echo $profile->aktif ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo $profile->aktif ? 'Aktif' : 'Pasif'; ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Ekleme Formu -->
            <div id="addForm" class="form-section card shadow mb-4" style="display: none;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Yeni Kurum Ekle</h6>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold text-gray-800 mb-3">Kurum Bilgileri</h5>
                                <div class="form-group">
                                    <label for="kurumadi">Kurum Adı</label>
                                    <input type="text" class="form-control" name="kurumadi" required>
                                </div>
                                <div class="form-group">
                                    <label for="kurumunvan">Kurum Ünvanı</label>
                                    <input type="text" class="form-control" name="kurumunvan" required>
                                </div>
                                <div class="form-group">
                                    <label for="kurumlogo">Logo URL</label>
                                    <input type="text" class="form-control" name="kurumlogo">
                                </div>
                                <div class="form-group">
                                    <label for="kurumadres">Adres</label>
                                    <textarea class="form-control" name="kurumadres" rows="2" required></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="font-weight-bold text-gray-800 mb-3">İletişim Bilgileri</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kurumtelefon1">Telefon 1</label>
                                            <input type="text" class="form-control" name="kurumtelefon1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kurumtelefon2">Telefon 2</label>
                                            <input type="text" class="form-control" name="kurumtelefon2">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="kurumgsm">GSM</label>
                                    <input type="text" class="form-control" name="kurumgsm" required>
                                </div>
                                <div class="form-group">
                                    <label for="kurumeposta">E-posta</label>
                                    <input type="email" class="form-control" name="kurumeposta" required>
                                </div>
                                <div class="form-group">
                                    <label for="kurumweb">Web Sitesi</label>
                                    <input type="url" class="form-control" name="kurumweb">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mt-3">
                                <h5 class="font-weight-bold text-gray-800 mb-3">Yönetici Bilgileri</h5>
                                <div class="form-group">
                                    <label for="adminusername">Kullanıcı Adı</label>
                                    <input type="text" class="form-control" name="adminusername" required>
                                </div>
                                <div class="form-group">
                                    <label for="adminadsoyad">Ad Soyad</label>
                                    <input type="text" class="form-control" name="adminadsoyad" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mt-3">
                                <div class="form-group">
                                    <label for="adminpassword">Şifre</label>
                                    <input type="password" class="form-control" name="adminpassword" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="adminemail">E-posta</label>
                                            <input type="email" class="form-control" name="adminemail" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="admingsm">GSM</label>
                                            <input type="text" class="form-control" name="admingsm" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="aktif">Durum</label>
                                    <select class="form-control" name="aktif" required>
                                        <option value="1">Aktif</option>
                                        <option value="0">Pasif</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button type="submit" name="add" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i> Kaydet
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="hideForm('addForm')">
                                    <i class="fas fa-times mr-2"></i> İptal
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Güncelleme Formu -->
            <?php if (isset($_GET['edit'])): ?>
            <div id="editForm" class="form-section card shadow mb-4">
                <!-- Ekleme formuyla benzer yapıda düzenlenebilir -->
            </div>
            <?php endif; ?>

            <!-- Tablo Listeleme -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kurum Listesi</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Logo</th>
                                    <th>Kurum Bilgileri</th>
                                    <th>İletişim</th>
                                    <th>Yönetici</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row->id); ?></td>
                                    <td class="text-center">
                                        <img src="<?php echo htmlspecialchars($row->kurumlogo); ?>" class="logo-img" alt="Logo">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row->kurumadi); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($row->kurumunvan); ?></small><br>
                                        <small><?php echo htmlspecialchars($row->kurumadres); ?></small>
                                    </td>
                                    <td>
                                        <i class="fas fa-phone fa-fw text-gray-400"></i> <?php echo htmlspecialchars($row->kurumtelefon1); ?><br>
                                        <?php if ($row->kurumtelefon2): ?>
                                        <i class="fas fa-phone fa-fw text-gray-400"></i> <?php echo htmlspecialchars($row->kurumtelefon2); ?><br>
                                        <?php endif; ?>
                                        <i class="fas fa-mobile-alt fa-fw text-gray-400"></i> <?php echo htmlspecialchars($row->kurumgsm); ?><br>
                                        <i class="fas fa-globe fa-fw text-gray-400"></i> <?php echo htmlspecialchars($row->kurumweb); ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row->adminadsoyad); ?></strong><br>
                                        <small class="text-muted">@<?php echo htmlspecialchars($row->adminusername); ?></small><br>
                                        <small><i class="fas fa-envelope fa-fw text-gray-400"></i> <?php echo htmlspecialchars($row->adminemail); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge <?php echo $row->aktif ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo $row->aktif ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="?edit=<?php echo $row->id; ?>" class="btn btn-sm btn-primary" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?profile=<?php echo $row->id; ?>" class="btn btn-sm btn-info" title="Detay">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $row->id; ?>">
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger" title="Sil" onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        
                                        <div class="mt-2">
                                            <form method="POST" class="d-flex">
                                                <input type="hidden" name="id" value="<?php echo $row->id; ?>">
                                                <input type="password" class="form-control form-control-sm" name="new_password" placeholder="Yeni şifre" required>
                                                <button type="submit" name="reset_password" class="btn btn-sm btn-warning ml-1" title="Şifre Sıfırla">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?php echo $row->id; ?>">
                                                <button type="submit" name="send_reset_link" class="btn btn-sm btn-secondary w-100" title="Mail ile Şifre Sıfırlama Linki Gönder">
                                                    <i class="fas fa-paper-plane"></i> Mail Gönder
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showForm(formId) {
            document.getElementById(formId).style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        function hideForm(formId) {
            document.getElementById(formId).style.display = 'none';
        }
        
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>