<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oturum Aç</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
		
        <div class="card">
            <div class="card-header">
                <h3>Oturum Aç</h3>
				
            </div>
            <div class="card-body">
                <form action="login_kontrol.php" method="POST">
                    <div class="form-group">
                        <label for="adminusername">Kullanıcı Adı</label>
                        <input type="text" class="form-control" id="adminusername" name="adminusername" required>
                    </div>
                    <div class="form-group">
                        <label for="adminpassword">Şifre</label>
                        <input type="password" class="form-control" id="adminpassword" name="adminpassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Giriş Yap</button> <a href="../edusys" class="btn btn-success">EDUSYS'ye git</a> <a href="kur.php" >C</a><br><a href="#">Şifremi Unuttum</a> 
                </form>
               
                
            </div>
        </div>
    </div>
</body>
</html>