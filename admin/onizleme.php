


<div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Önizleme</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Firma Bilgileri</h4>
                        <p><strong>Kurum Adı:</strong> <?php echo $ayarlar->kurumadi; ?></p>
                        <p><strong>Kurum Ünvanı:</strong> <?php echo $ayarlar->kurumunvan; ?></p>
                        <p><strong>Kurum Adresi:</strong> <?php echo $ayarlar->kurumadres; ?></p>
                        <p><strong>Telefon 1:</strong> <?php echo $ayarlar->kurumtelefon1; ?></p>
                        <p><strong>Telefon 2:</strong> <?php echo $ayarlar->kurumtelefon2; ?></p>
                        <p><strong>Mobile:</strong> <?php echo $ayarlar->kurumgsm; ?></p>
                        <p><strong>E-posta:</strong> <?php echo $ayarlar->kurumeposta; ?></p>
                        <p><strong>Web:</strong> <?php echo $ayarlar->kurumweb; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h4>Varsayılan Ayarlar</h4>
						
                        <p><strong>POS Bankaları:</strong><br> <?php echo nl2br($ayarlar->posbankalari); ?></p>
						<p><strong>Kurum Hesapları:</strong><br> <?php echo nl2br($ayarlar->kurumhesaplari); ?></p>
				<p><strong>Şahsi Hesaplar:</strong><br> <?php echo nl2br($ayarlar->sahsihesaplar); ?></p>
				<p><strong>Gelir Kategorileri:</strong><br> <?php echo nl2br($ayarlar->gelirkategori); ?></p>
						<p><strong>Gider Kategorileri:</strong><br> <?php echo nl2br($ayarlar->giderkategori); ?></p>
				<p><strong>Borça Kategorileri:</strong><br> <?php echo nl2br($ayarlar->borckategori); ?></p>
                        <p><strong>Taksit Sayısı:</strong> <?php echo $ayarlar->taksitsayisidefault; ?></p>
                        <p><strong>Taksit Günü:</strong> <?php echo $ayarlar->taksitgunudefault; ?></p>
                    </div>
                </div>
            
            </div>
        </div>
    </div>