<div class="text-right mb-3">
    <button type="button" id="ornekDoldurBtn" class="btn btn-outline-primary">
        <i class="fas fa-magic"></i> Örnek Şablon Doldur
    </button>
</div>

<form action="save_ayarlar.php" method="POST" id="step3">
    <!-- Form içeriği aynı şekilde kalacak -->
</form>



<form action="save_ayarlar.php" method="POST" id="step3">
    <div class="form-group">
        <label for="posbankalari">POS Bankaları</label> <span style="font-size: 10px; color:slategray">(Her kaydı yeni bir satıra giriniz)</span>
        <textarea class="form-control" id="posbankalari" name="posbankalari" rows="3" required></textarea>
    </div>
   
	

							<div class="form-group">
								<label for="kurumhesaplari">Kurum Hesapları</label><span style="font-size: 10px; color:slategray"> (Her kaydı yeni bir satıra giriniz)</span>
								<textarea class="form-control" id="kurumhesaplari" name="kurumhesaplari" rows="3" required></textarea>
							</div>
							<div class="form-group">
								<label for="sahsihesaplar">Şahsi Hesaplar</label><span style="font-size: 10px; color:slategray"> (Her kaydı yeni bir satıra giriniz)</span>
								<textarea class="form-control" id="sahsihesaplar" name="sahsihesaplar" rows="3" required></textarea>
							</div>
							<div class="form-group">
								<label for="gelirkategori">Gelir Kategorileri</label><span style="font-size: 10px; color:slategray"> (Her kaydı yeni bir satıra giriniz)</span>
								<textarea class="form-control" id="gelirkategori" name="gelirkategori" rows="5" required></textarea>
							</div>
							<div class="form-group">
								<label for="giderkategori">Gider Kategorileri</label><span style="font-size: 10px; color:slategray"> (Her kaydı yeni bir satıra giriniz)</span>
								<textarea class="form-control" id="giderkategori" name="giderkategori" rows="15" required></textarea>
							</div>
							<div class="form-group">
								<label for="borckategori">Borç Kategorileri</label><span style="font-size: 10px; color:slategray">(Her kaydı yeni bir satıra giriniz)</span>
								<textarea class="form-control" id="borckategori" name="borckategori" rows="5" required></textarea>
							</div>
							<div class="form-group">
								<label for="siniflar">Sınıflar</label><span style="font-size: 10px; color:slategray">(Her kaydı yeni bir satıra giriniz)</span>
								<textarea class="form-control" id="siniflar" name="siniflar" rows="20" required></textarea>
							</div>
							<div class="form-group">
								<label for="egitimyillari">Eğitim Yılları</label><span style="font-size: 10px; color:slategray">(Her kaydı yeni bir satıra giriniz)</span>
								<textarea class="form-control" id="egitimyillari" name="egitimyillari" rows="5" required></textarea>
							</div>
							<div class="form-group">
								<label for="taksitsayisidefault">Taksit Sayısı</label>
								<input type="number" class="form-control" id="taksitsayisidefault" name="taksitsayisidefault" min="1" max="99" required value="8">
							</div>
							<div class="form-group">
								<label for="taksitgunudefault">Taksit Günü</label>
								<input type="number" class="form-control" id="taksitgunudefault" name="taksitgunudefault" min="1" max="31" required value="20">
							</div>
							<!--div class="form-group">
								<label for="odemesekilleri"> Ödeme Şekilleri</label><span style="font-size: 10px; color:slategray">(Her kaydı yeni bir satıra giriniz)</span>
								<textarea class="form-control" id="odemesekilleri" name="odemesekilleri" rows="5" required>Nakit
Kredi kartı
Havale/Eft
Barter</textarea>
							</div-->
	
	
	
    <button type="submit" class="btn btn-success">Kurulumu Tamamla</button>
</form>


<script>
document.getElementById('ornekDoldurBtn').addEventListener('click', function() {
    // Tüm textarea ve inputları seç
    const formElements = document.querySelectorAll('#step3 textarea, #step3 input[type="number"]');
    
    // Her bir eleman için işlem yap
    formElements.forEach(element => {
        const elementId = element.id;
        
        // Örnek veriler (mevcut formdaki değerler)
        const ornekVeriler = {
            posbankalari: "Ziraat Pos\nVakifPos\nGarantiPos",
            kurumhesaplari: "Halkbank\nTEB",
            sahsihesaplar: "Ziraat Bank\nGaranti Bank",
            gelirkategori: "Eğitim\nKütüphane\nKitap\nKurumsal Borç\nŞahsi Borç",
            giderkategori: "Kira\nElektrik\nSu\nİnternet\nTelefon\nGüvenlik Kamerası Bakım\nYönetici Maaşları\nİdari Personel Maaşları\nÖğretmen Maaşları\nYemek\nBakım-Onarım\nReklam\nYazılım\nFranchasing\nMuhasebe",
            borckategori: "Kredi\nŞahsi Borç\nKurumsal Borç",
            siniflar: "5. Sınıf - Orta 1\n6. Sınıf - Orta 2\n7. Sınıf - Orta 3\n8. Sınıf - Orta 4\n9. Sınıf - Lise 1\n10. Sınıf - Lise 2\n11. Sınıf - Lise 3\n12. Sınıf - Lise 4\nMezun Grubu\nKütüphane 5. Sınıf\nKütüphane 6. Sınıf\nKütüphane 7. Sınıf\nKütüphane 8. Sınıf\nKütüphane 9. Sınıf\nKütüphane 10. Sınıf\nKütüphane 11. Sınıf\nKütüphane 12. Sınıf\nKütüphane Mezun",
            egitimyillari: "2024-2025\n2025-2026\n2026-2027",
            taksitsayisidefault: "8",
            taksitgunudefault: "20"
        };
        
        // Eğer element ID'si örnek verilerde varsa değeri ata
        if (ornekVeriler.hasOwnProperty(elementId)) {
            if (element.tagName === 'TEXTAREA') {
                element.value = ornekVeriler[elementId];
            } else {
                element.value = ornekVeriler[elementId];
            }
        }
    });
    
    // Kullanıcıyı bilgilendir
    alert('Örnek şablon verileri başarıyla yüklendi!');
});
</script>