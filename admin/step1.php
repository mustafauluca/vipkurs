
<h3>Sistem Yöneticisi için </h3>
<span style="font-size: 12px; color:red">(Sistem Yöneticisi Sadece Firma Bilgileri ile Varsayılan adresleri güncelleyip, Kullanıcı oluşturabilir. Kayıt, Ödeme ve Satış gibi işlem yetkisine sahip değildir.)</span>

<form action="save_admin.php" method="POST"  id="adminForm">
    <div class="form-group">
        <label for="adminusername">Kullanıcı Adı</label>
        <input type="text" class="form-control" id="adminusername" name="adminusername" oninput="checkAdminUsername(this.value)" required <?php  if ($isLoggedIn){echo 'value="'.$admin->adminusername.'"  disabled';} ?>>
								<small id="adminusernameFeedback" class="form-text text-muted"></small>
    </div>
    <div class="form-group">
        <label for="adminpassword">Şifre</label>
        <input type="password" class="form-control" id="adminpassword" name="adminpassword" required <?php  if ($isLoggedIn){echo 'value=********  disabled';} ?>>
    </div>
	
	   <div class="form-group">
        <label for="adminadsoyad">Ad Soyad</label>
        <input type="text" class="form-control" id="adminadsoyad" name="adminadsoyad" required <?php  if ($isLoggedIn){echo 'value="'.$admin->adminadsoyad.'"  disabled';} ?>>
    </div>
    <div class="form-group">
        <label for="adminemail">E-posta</label>
        <input type="email" class="form-control" id="adminemail" name="adminemail" required <?php  if ($isLoggedIn){echo 'value="'.$admin->adminemail.'"  disabled';} ?>>
    </div>
    <div class="form-group">
        <label for="admingsm">GSM</label>
        <input type="text" class="form-control" id="admingsm" name="admingsm" required <?php  if ($isLoggedIn){echo 'value="'.$admin->admingsm.'"  disabled';} ?>>
    </div>
    <button type="submit" class="btn btn-primary <?php  if ($isLoggedIn){echo 'disabled';} ?>">Sonraki Adım</button>
</form>