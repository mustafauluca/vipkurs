<form action="save_firma.php" method="POST"  id="firmaForm">
 
	

							<div class="form-group">
								<label for="kurumadi">Kurum Adı</label>
								<input type="text" class="form-control" id="kurumadi" name="kurumadi" oninput="checkKurumAdi(this.value)" required>
								<small id="kurumadiFeedback" class="form-text text-muted"></small>
							</div>

							<div class="form-group">
								<label for="kurumunvan">Kurum Ünvanı</label>
								<input type="text" class="form-control" id="kurumunvan" name="kurumunvan" required>
							</div>
							<div class="form-group">
								<label for="kurumadres">Kurum Adresi</label>
								<input type="text" class="form-control" id="kurumadres" name="kurumadres" required >
							</div>
							<div class="form-group">
								<label for="kurumtelefon1">Telefon 1</label>
								<input type="text" class="form-control" id="kurumtelefon1" name="kurumtelefon1" required >
							</div>
							<div class="form-group">
								<label for="kurumtelefon2">Telefon 2</label>
								<input type="text" class="form-control" id="kurumtelefon2" name="kurumtelefon2" >
							</div>
							<div class="form-group">
								<label for="kurumgsm">Mobile</label>
								<input type="text" class="form-control" id="kurumgsm" name="kurumgsm" required >
							</div>
							<div class="form-group">
								<label for="kurumeposta">E-posta</label>
								<input type="email" class="form-control" id="kurumeposta" name="kurumeposta" required >
							</div>
							<div class="form-group">
								<label for="kurumweb">Web</label>
								<input type="text" class="form-control" id="kurumweb" name="kurumweb" >
							</div>
						
							<button type="submit" class="btn btn-primary" >Sonraki Adım</button>
						</form>
	