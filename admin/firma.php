<div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Firma Bilgilerini Güncelle</h3>
            </div>
            <div class="card-body">
                <!-- Bootstrap Tab Yapısı -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="firma-tab" data-toggle="tab" href="#firma" role="tab" aria-controls="firma" aria-selected="true">Firma Bilgileri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="varsayilan-tab" data-toggle="tab" href="#varsayilan" role="tab" aria-controls="varsayilan" aria-selected="false">Varsayılan Ayarlar</a> 
                    </li>
                </ul>
				  
                <div class="tab-content" id="myTabContent">
                    <!-- Firma Bilgileri Sekmesi -->
                    <div class="tab-pane fade show active" id="firma" role="tabpanel" aria-labelledby="firma-tab">
                      <form action="firma_guncelle.php?d=bilgi" method="POST" class="mt-3">
                            <div class="form-group">
                                <label for="kurumadi">Kurum Adı</label>
                                <input type="text" class="form-control" id="kurumadi" name="kurumadi" value="<?php echo $ayarlar->kurumadi; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kurumunvan">Kurum Ünvanı</label>
                                <input type="text" class="form-control" id="kurumunvan" name="kurumunvan" value="<?php echo $ayarlar->kurumunvan; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kurumadres">Kurum Adresi</label>
                                <input type="text" class="form-control" id="kurumadres" name="kurumadres" value="<?php echo $ayarlar->kurumadres; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kurumtelefon1">Telefon 1</label>
                                <input type="text" class="form-control" id="kurumtelefon1" name="kurumtelefon1" value="<?php echo $ayarlar->kurumtelefon1; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kurumtelefon2">Telefon 2</label>
                                <input type="text" class="form-control" id="kurumtelefon2" name="kurumtelefon2" value="<?php echo $ayarlar->kurumtelefon2; ?>">
                            </div>
                            <div class="form-group">
                                <label for="kurumgsm">Mobile</label>
                                <input type="text" class="form-control" id="kurumgsm" name="kurumgsm" value="<?php echo $ayarlar->kurumgsm; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kurumeposta">E-posta</label>
                                <input type="email" class="form-control" id="kurumeposta" name="kurumeposta" value="<?php echo $ayarlar->kurumeposta; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kurumweb">Web</label>
                                <input type="text" class="form-control" id="kurumweb" name="kurumweb" value="<?php echo $ayarlar->kurumweb; ?>">
                            </div>
                            	   <button type="submit" class="btn btn-primary">Firma Bilgileri Güncelle</button>
                        </form>
                    </div>

                    <!-- Varsayılan Ayarlar Sekmesi -->
                    <div class="tab-pane fade" id="varsayilan" role="tabpanel" aria-labelledby="varsayilan-tab">
						
						<div class="text-right mb-3">
    <button type="button" id="ornekDoldurBtn" class="btn btn-outline-primary">
        <i class="fas fa-magic"></i> Örnek Şablon Doldur
    </button>
</div>
                       
						 <form action="firma_guncelle.php?d=ayar" method="POST" class="mt-3" name="firmaayar">
                            <div class="form-group">
                                <label for="posbankalari">POS Bankaları</label>
                                <textarea class="form-control" id="posbankalari" name="posbankalari" rows="3" required><?php echo $ayarlar->posbankalari; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="kurumhesaplari">Kurum Hesapları</label>
                                <textarea class="form-control" id="kurumhesaplari" name="kurumhesaplari" rows="3" required><?php echo $ayarlar->kurumhesaplari; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="sahsihesaplar">Şahsi Hesaplar</label>
                                <textarea class="form-control" id="sahsihesaplar" name="sahsihesaplar" rows="3" required><?php echo $ayarlar->sahsihesaplar; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="gelirkategori">Gelir Kategorileri</label>
                                <textarea class="form-control" id="gelirkategori" name="gelirkategori" rows="3" required><?php echo $ayarlar->gelirkategori; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="giderkategori">Gider Kategorileri</label>
                                <textarea class="form-control" id="giderkategori" name="giderkategori" rows="15" required><?php echo $ayarlar->giderkategori; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="borckategori">Borç Kategorileri</label>
                                <textarea class="form-control" id="borckategori" name="borckategori" rows="3" required><?php echo $ayarlar->borckategori; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="siniflar">Sınıflar</label>
                                <textarea class="form-control" id="siniflar" name="siniflar" rows="15" required><?php echo $ayarlar->siniflar; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="egitimyillari">Eğitim Yılları</label>
                                <textarea class="form-control" id="egitimyillari" name="egitimyillari" rows="3" required><?php echo $ayarlar->egitimyillari; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="teksitsayisidefault">Taksit Sayısı</label>
                                <input type="number" class="form-control" id="taksitsayisidefault" name="taksitsayisidefault" value="<?php echo $ayarlar->taksitsayisidefault; ?>" min="1" max="99" required>
                            </div>
                            <div class="form-group">
                                <label for="taksitgunudefault">Taksit Günü</label>
                                <input type="number" class="form-control" id="taksitgunudefault" name="taksitgunudefault" value="<?php echo $ayarlar->taksitgunudefault; ?>" min="1" max="31" required>
                            </div>
                            <!--div class="form-group">
                                <label for="taksitodemesekilleri">Taksit Ödeme Şekilleri</label>
                                <textarea class="form-control" id="taksitodemesekilleri" name="taksitodemesekilleri" rows="3" required><?php echo $ayarlar->odemesekilleri; ?></textarea>
                            </div-->
						   <button type="submit" class="btn btn-primary">Firma Ayarları Güncelle</button>
                        </form>
                         
                    </div>
					
					
					
						 
                </div>
					 
            </div>
        </div>
    </div>


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